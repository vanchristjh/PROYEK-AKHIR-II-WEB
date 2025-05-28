<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Subject;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Helpers\ActivityLogger;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $query = Material::where('teacher_id', $teacher->id);
        
        // Filter by subject if provided
        if ($request->has('subject') && !empty($request->subject)) {
            $query->where('subject_id', $request->subject);
        }
        
        // Filter by search term if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $materials = $query->with(['subject', 'classrooms'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);
        
        // Get all subjects for the filter dropdown
        $subjects = $teacher->teacherSubjects;
        
        return view('guru.materials.index', [
            'materials' => $materials,
            'subjects' => $subjects,
            'selectedSubject' => $request->subject,
            'searchTerm' => $request->search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */    public function create()
    {
        $teacher = Auth::user();
        $subjects = $teacher->teacherSubjects;
        
        // Check if the teacher has any subjects
        if ($subjects->isEmpty()) {
            // Instead of blocking access, get all subjects
            $subjects = Subject::all();
            
            // Show a warning message without blocking functionality
            session()->flash('warning', 'Anda belum memiliki mata pelajaran yang ditugaskan. Silahkan pilih mata pelajaran yang tersedia atau hubungi admin untuk penambahan mata pelajaran yang sesuai.');
        }
        
        $classrooms = Classroom::all();
        
        return view('guru.materials.create', [
            'subjects' => $subjects,
            'classrooms' => $classrooms,
        ]);
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|array|exists:classrooms,id',
            'file' => 'nullable|file|max:20480', // 20MB max
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'is_active' => 'sometimes|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Get teacher's subjects
            $teacherSubjects = auth()->user()->subjects()->pluck('id')->toArray();

            // Check if teacher has access to the subject
            if (!in_array($validated['subject_id'], $teacherSubjects) && !auth()->user()->isAdmin()) {
                throw new \Exception('Anda tidak memiliki akses untuk mata pelajaran ini.');
            }

            $materialData = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'subject_id' => $validated['subject_id'],
                'teacher_id' => auth()->id(),
                'publish_date' => $validated['publish_date'] ?? now(),
                'expiry_date' => $validated['expiry_date'] ?? null,
                'is_active' => $request->has('is_active'),
            ];

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('materials', $fileName, 'public');
                $materialData['file_path'] = $filePath;
                $materialData['file_name'] = $file->getClientOriginalName();
            }

            // Create material
            $material = Material::create($materialData);

            // Attach classrooms
            $material->classrooms()->sync($validated['classroom_id']);

            // Log activity
            activity()
                ->performedOn($material)
                ->causedBy(auth()->user())
                ->withProperties(['title' => $material->title])
                ->log('created material');

            DB::commit();

            return redirect()->route('guru.materials.index')
                ->with('success', 'Materi berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if exists
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat materi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function show(Material $material)
    {
        // Ensure the teacher owns this material
        if ($material->teacher_id !== Auth::id()) {
            return redirect()->route('guru.materials.index')
                ->with('error', 'Anda tidak memiliki akses ke materi ini.');
        }
        
        // Eager load the classrooms and subject relationships
        $material->load(['classrooms', 'subject']);
        
        return view('guru.materials.show', [
            'material' => $material
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function edit(Material $material)
    {
        // Ensure the teacher owns this material
        if ($material->teacher_id !== Auth::id()) {
            return redirect()->route('guru.materials.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit materi ini.');
        }
          $teacher = Auth::user();
        $subjects = $teacher->teacherSubjects;
        
        // Check if the teacher has any subjects
        if ($subjects->isEmpty()) {
            // Instead of blocking access, get all subjects
            $subjects = Subject::all();
            
            // Show a warning message without blocking functionality
            session()->flash('warning', 'Anda belum memiliki mata pelajaran yang ditugaskan. Silahkan pilih mata pelajaran yang tersedia atau hubungi admin untuk penambahan mata pelajaran yang sesuai.');
        }
        
        $classrooms = Classroom::all();
        
        return view('guru.materials.edit', [
            'material' => $material,
            'subjects' => $subjects,
            'classrooms' => $classrooms,
            'selectedClassrooms' => $material->classrooms->pluck('id')->toArray()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Material $material)
    {
        // Authorization check
        if ($material->teacher_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|array|exists:classrooms,id',
            'file' => 'nullable|file|max:20480', // 20MB max
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
            'is_active' => 'sometimes|boolean',
            'remove_file' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'subject_id' => $validated['subject_id'],
                'publish_date' => $validated['publish_date'] ?? $material->publish_date,
                'expiry_date' => $validated['expiry_date'],
                'is_active' => $request->has('is_active'),
            ];

            // Handle file removal
            if ($request->has('remove_file') && $request->remove_file) {
                if ($material->file_path) {
                    Storage::disk('public')->delete($material->file_path);
                    $updateData['file_path'] = null;
                    $updateData['file_name'] = null;
                }
            }

            // Handle new file upload
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($material->file_path) {
                    Storage::disk('public')->delete($material->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('materials', $fileName, 'public');
                $updateData['file_path'] = $filePath;
                $updateData['file_name'] = $file->getClientOriginalName();
            }

            // Update material
            $material->update($updateData);

            // Update classroom relationships
            $material->classrooms()->sync($validated['classroom_id']);

            // Log activity
            activity()
                ->performedOn($material)
                ->causedBy(auth()->user())
                ->withProperties(['title' => $material->title])
                ->log('updated material');

            DB::commit();

            return redirect()->route('guru.materials.index')
                ->with('success', 'Materi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if exists
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Gagal memperbarui materi: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function destroy(Material $material)
    {
        // Authorization check
        if ($material->teacher_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Log before deletion
            activity()
                ->performedOn($material)
                ->causedBy(auth()->user())
                ->withProperties(['title' => $material->title])
                ->log('deleted material');

            // Delete file if exists
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            // Delete material
            $material->delete();

            DB::commit();

            return redirect()->route('guru.materials.index')
                ->with('success', 'Materi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus materi: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Download the material file.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function download(Material $material)
    {
        // Ensure the teacher owns this material
        if ($material->teacher_id !== Auth::id()) {
            return redirect()->route('guru.materials.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengunduh materi ini.');
        }
        
        // Check if file exists
        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            return redirect()->back()
                ->withErrors(['error' => 'File tidak ditemukan.']);
        }        // Generate filename for download (original or based on title)
        $filename = pathinfo($material->file_path, PATHINFO_FILENAME) . '.' . pathinfo($material->file_path, PATHINFO_EXTENSION);
        
        // Log download activity using the improved ActivityLogger
        ActivityLogger::logDownload($material);
        
        // Return the file for download
        return Storage::disk('public')->download($material->file_path, $filename);
    }
    
    /**
     * Toggle the active status of a material.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Material $material)
    {
        // Ensure the teacher owns this material
        if ($material->teacher_id !== Auth::id()) {
            return redirect()->route('guru.materials.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengubah status materi ini.');
        }
        
        try {
            // Toggle the is_active status
            $material->is_active = !$material->is_active;
            $material->save();
            
            $status = $material->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()
                ->with('success', "Materi berhasil {$status}.");
                
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error toggling material status: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal mengubah status materi: ' . $e->getMessage()]);
        }
    }
}
