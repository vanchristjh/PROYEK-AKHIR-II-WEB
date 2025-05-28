<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('role', 'classroom')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        
        // Debug classrooms
        \Log::debug('Available classrooms: ' . $classrooms->count());
        
        return view('admin.users.create', compact('roles', 'classrooms', 'subjects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'id_number' => 'nullable|string|max:50',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'avatar' => 'nullable|image|max:2048',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        try {
            DB::beginTransaction();

            // Handle password
            $validated['password'] = Hash::make($validated['password']);
            
            // Set classroom_id to null if not a student
            if ($validated['role_id'] != 3) { // Assuming 3 is student role_id
                $validated['classroom_id'] = null;
            }

            // Process avatar if uploaded
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $avatarPath;
            }

            // Create user
            $user = User::create($validated);

            // Assign subjects if user is a teacher
            if ($validated['role_id'] == 2 && isset($validated['subjects'])) { // Assuming 2 is teacher role_id
                $user->subjects()->sync($validated['subjects']);
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Pengguna berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded avatar if exists
            if (isset($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat pengguna: ' . $e->getMessage()])
                ->withInput();
        }
    }    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Only eager load relationships that exist in the database
        $user = User::with(['role', 'classroom', 'subjects'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        $teacherSubjects = $user->subjects->pluck('id')->toArray();
        
        // Debug classrooms
        \Log::debug('Available classrooms for edit: ' . $classrooms->count());
        \Log::debug('Current user classroom: ' . ($user->classroom_id ?? 'null'));
        
        return view('admin.users.edit', compact('user', 'roles', 'classrooms', 'subjects', 'teacherSubjects'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'id_number' => 'nullable|string|max:50',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'avatar' => 'nullable|image|max:2048',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id'
        ]);

        // Process avatar if uploaded
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Set classroom_id to null if not a student
        if ($request->role_id != 3) {
            $validated['classroom_id'] = null;
        }
        
        // Debug classroom assignment
        \Log::debug('Update - Role ID: ' . $request->role_id . ', Classroom ID: ' . ($request->classroom_id ?? 'null'));

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Assign subjects to teacher or clear them
        if ($request->role_id == 2 && $request->has('subjects')) {
            $user->subjects()->sync($request->subjects);
        } elseif ($request->role_id != 2) {
            $user->subjects()->sync([]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Delete user
        $user->subjects()->detach(); // Remove subject associations
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
