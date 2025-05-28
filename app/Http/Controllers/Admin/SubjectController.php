<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Display a listing of the subjects.
     */
    public function index()
    {
        $subjects = Subject::with('teachers')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $teachers = User::where('role_id', 2)->get(); // Assuming role_id=2 is for teachers
        return view('admin.subjects.create', compact('teachers'));
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:subjects,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            // Create the subject
            $subject = Subject::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Attach teachers if selected
            if ($request->has('teachers')) {
                $subject->teachers()->attach($request->teachers);
            }

            DB::commit();
            return redirect()->route('admin.subjects.index')
                ->with('success', 'Mata pelajaran berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $subject->load('teachers');
        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        $teachers = User::where('role_id', 2)->get();
        $selectedTeachers = $subject->teachers->pluck('id')->toArray();
        return view('admin.subjects.edit', compact('subject', 'teachers', 'selectedTeachers'));
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'code' => 'required|string|max:10|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            $subject->update([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Sync teachers
            $subject->teachers()->sync($request->teachers ?? []);

            DB::commit();
            return redirect()->route('admin.subjects.index')
                ->with('success', 'Mata pelajaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Subject $subject)
    {
        DB::beginTransaction();
        try {
            // Detach all teachers first
            $subject->teachers()->detach();
            
            // Delete the subject
            $subject->delete();
            
            DB::commit();
            return redirect()->route('admin.subjects.index')
                ->with('success', 'Mata pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
