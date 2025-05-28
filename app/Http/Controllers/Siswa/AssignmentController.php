<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for the student.
     *
     * @return \Illuminate\Http\Response
     */    public function index(Request $request)
    {
        $student = Auth::user();
        
        // Get the student's classes and classrooms
        $studentClassroomIds = [];
        $studentClassIds = [];
        
        // Add direct classroom_id if it exists
        if ($student->classroom_id) {
            $studentClassroomIds[] = $student->classroom_id;
        }
        
        // Add classrooms from many-to-many relationship if it exists
        if (method_exists($student, 'classrooms')) {
            $manyToManyClassroomIds = $student->classrooms()->pluck('classrooms.id')->toArray();
            $studentClassroomIds = array_merge($studentClassroomIds, $manyToManyClassroomIds);
        }
        
        // Add direct class_id if it exists
        if (property_exists($student, 'class_id') && $student->class_id) {
            $studentClassIds[] = $student->class_id;
        }
        
        // Add classes from many-to-many relationship if it exists
        if (method_exists($student, 'classes')) {
            $manyToManyClassIds = $student->classes()->pluck('school_classes.id')->toArray();
            $studentClassIds = array_merge($studentClassIds, $manyToManyClassIds);
        }
        
        // Filter out any null values
        $studentClassroomIds = array_filter($studentClassroomIds);
        $studentClassIds = array_filter($studentClassIds);
        
        // Log for debugging
        \Log::debug('Student Class IDs: ' . implode(', ', $studentClassIds));
        \Log::debug('Student Classroom IDs: ' . implode(', ', $studentClassroomIds));
        
        if (empty($studentClassroomIds) && empty($studentClassIds)) {
            return view('siswa.assignments.index', [
                'assignments' => collect(),
                'subjects' => collect(),
                'completedCount' => 0,
                'pendingCount' => 0,
                'overdueCount' => 0,
            ])->with('warning', 'Anda belum terdaftar di kelas manapun.');
        }
        
        // Base query for active assignments
        $query = Assignment::where('is_active', true);
        
        // Important: For consistency with dashboard behavior, use classroom_id as fallback in both places
        // If student has classroom_id, ensure we search for assignments linked to classes with that ID
        if ($student->classroom_id) {
            $query->where(function($q) use ($studentClassroomIds, $studentClassIds, $student) {
                // Check classroom assignments
                if (!empty($studentClassroomIds)) {
                    $q->whereHas('classrooms', function($subQ) use ($studentClassroomIds) {
                        $subQ->whereIn('classrooms.id', $studentClassroomIds);
                    });
                }
                
                // Check class assignments
                if (!empty($studentClassIds)) {
                    $q->orWhereHas('classes', function($subQ) use ($studentClassIds) {
                        $subQ->whereIn('school_classes.id', $studentClassIds);
                    });
                }
                
                // Add this condition to match dashboard behavior
                $q->orWhereHas('classes', function($subQ) use ($student) {
                    $subQ->where('school_classes.id', $student->classroom_id);
                });
            });
        } else {
            // Original query logic when student doesn't have classroom_id
            $query->where(function($q) use ($studentClassroomIds, $studentClassIds) {
                // Check classroom assignments
                if (!empty($studentClassroomIds)) {
                    $q->whereHas('classrooms', function($subQ) use ($studentClassroomIds) {
                        $subQ->whereIn('classrooms.id', $studentClassroomIds);
                    });
                }
                
                // Check class assignments
                if (!empty($studentClassIds)) {
                    $q->orWhereHas('classes', function($subQ) use ($studentClassIds) {
                        $subQ->whereIn('school_classes.id', $studentClassIds);
                    });
                }
            });
        }
        
        // Eager load relasi yang dibutuhkan
        $query->with([
            'subject',
            'teacher',
            'submissions' => function($q) use ($student) {
                $q->where('user_id', $student->id);
            }
        ]);
        
        // Debug query
        \Log::debug('Assignment Query: ' . $query->toSql());
        \Log::debug('Query Bindings: ' . json_encode($query->getBindings()));
        
        // Apply filters
        $status = $request->status;
        
        // Filter by subject if provided
        if ($request->has('subject') && !empty($request->subject)) {
            $query->where('subject_id', $request->subject);
        }
        
        // Filter by status
        if ($status === 'completed') {
            $query->whereHas('submissions', function($q) use ($student) {
                $q->where('user_id', $student->id);
            });
        } elseif ($status === 'pending') {
            $query->whereDoesntHave('submissions', function($q) use ($student) {
                $q->where('user_id', $student->id);
            })->where('deadline', '>', now());
        } elseif ($status === 'overdue') {
            $query->whereDoesntHave('submissions', function($q) use ($student) {
                $q->where('user_id', $student->id);
            })->where('deadline', '<', now());
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('subject', function($subQ) use ($request) {
                     $subQ->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $assignments = $query->latest()->paginate(10);
          // Ambil semua tugas untuk statistik dengan query yang sama tapi tanpa pagination
        $allQuery = clone $query;
        $allAssignments = $allQuery->get();
        
        // Log jumlah tugas yang ditemukan
        \Log::debug('Total assignments found: ' . $allAssignments->count());
        
        // Eksekusi query utama dengan pagination
        $assignments = $query->paginate(10);
        
        // Log jumlah tugas yang ditampilkan di halaman saat ini
        \Log::debug('Assignments in current page: ' . $assignments->count());
        
        // Calculate statistics
        $completedCount = $allAssignments->filter(function($assignment) {
            return $assignment->submissions->isNotEmpty();
        })->count();
        
        $pendingCount = $allAssignments->filter(function($assignment) {
            return $assignment->submissions->isEmpty() && !$assignment->isExpired();
        })->count();
        
        $overdueCount = $allAssignments->filter(function($assignment) {
            return $assignment->submissions->isEmpty() && $assignment->isExpired();
        })->count();
        
        // Get subjects for filter dropdown
        $subjects = $student->classroom ? $student->classroom->subjects : collect();
        
        return view('siswa.assignments.index', compact(
            'assignments',
            'subjects',
            'status',
            'completedCount',
            'pendingCount',
            'overdueCount'
        ));
    }

    /**
     * Display the specified assignment.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */    public function show(Assignment $assignment)
    {
        $student = Auth::user();
        
        // Get the student's class and classroom IDs
        $studentClassroomIds = collect();
        $studentClassIds = collect();
        
        // Add direct classroom relationship
        if ($student->classroom_id) {
            $studentClassroomIds->push($student->classroom_id);
        }
        
        // Add many-to-many classroom relationships
        if (method_exists($student, 'classrooms')) {
            $studentClassroomIds = $studentClassroomIds->merge(
                $student->classrooms()->pluck('classrooms.id')
            );
        }
        
        // Add direct class relationship
        if (property_exists($student, 'class_id') && $student->class_id) {
            $studentClassIds->push($student->class_id);
        }
        
        // Add many-to-many class relationships
        if (method_exists($student, 'classes')) {
            $studentClassIds = $studentClassIds->merge(
                $student->classes()->pluck('school_classes.id')
            );
        }
        
        // Check if student has any class assignments
        if ($studentClassroomIds->isEmpty() && $studentClassIds->isEmpty()) {
            return redirect()->route('siswa.assignments.index')
                ->with('error', 'Anda belum terdaftar di kelas manapun.');
        }
        
        // Check if student has access to this assignment
        $hasAccess = $assignment->classes()->whereIn('class_id', $studentClassIds)->exists() ||
                    $assignment->classrooms()->whereIn('classroom_id', $studentClassroomIds)->exists();
        
        if (!$hasAccess) {
            return redirect()->route('siswa.assignments.index')
                ->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }
        
        // Get the student's submission if it exists
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $student->id)
            ->first();
        
        // Calculate remaining time if deadline exists
        $hoursRemaining = null;
        if ($assignment->deadline) {
            $now = now();
            if ($now->lt($assignment->deadline)) {
                $hoursRemaining = $now->diffInHours($assignment->deadline);
            }
        }
        
        return view('siswa.assignments.show', [
            'assignment' => $assignment,
            'submission' => $submission,
            'hoursRemaining' => $hoursRemaining,
        ]);
    }

    /**
     * Submit an assignment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */    public function submit(Request $request, Assignment $assignment)
    {
        $student = Auth::user();
        
        // Verify student has access to this assignment
        $studentClassroomIds = [];
        $studentClassIds = [];
        
        // Add direct classroom_id if it exists
        if ($student->classroom_id) {
            $studentClassroomIds[] = $student->classroom_id;
        }
        
        // Add classrooms from many-to-many relationship if it exists
        if (method_exists($student, 'classrooms')) {
            $manyToManyClassroomIds = $student->classrooms()->pluck('classrooms.id')->toArray();
            $studentClassroomIds = array_merge($studentClassroomIds, $manyToManyClassroomIds);
        }
        
        // Add direct class_id if it exists
        if (property_exists($student, 'class_id') && $student->class_id) {
            $studentClassIds[] = $student->class_id;
        }
        
        // Add classes from many-to-many relationship if it exists
        if (method_exists($student, 'classes')) {
            $manyToManyClassIds = $student->classes()->pluck('school_classes.id')->toArray();
            $studentClassIds = array_merge($studentClassIds, $manyToManyClassIds);
        }
        
        $hasAccess = $assignment->classes()->whereIn('class_id', $studentClassIds)->exists() ||
                    $assignment->classrooms()->whereIn('classroom_id', $studentClassroomIds)->exists();
                
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }
        
        // Check if the assignment is expired and late submissions are not allowed
        if ($assignment->isExpired() && !$assignment->allow_late_submission) {
            return redirect()->back()->with('error', 'Deadline pengumpulan tugas telah berakhir.');
        }
        
        // Check if the student has already submitted this assignment
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $student->id)
            ->first();
            
        if ($existingSubmission) {
            return redirect()->back()->with('error', 'Anda sudah mengumpulkan tugas ini. Silakan edit submission yang ada.');
        }
        
        // Validate request
        $request->validate([
            'file' => 'required|file|max:102400|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Upload file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileExtension = $file->getClientOriginalExtension();
                $filePath = $file->store('submissions/' . $assignment->id, 'public');
                $fileSize = $this->formatFileSize($file->getSize());
                
                // Create submission
                $submission = new Submission();
                $submission->assignment_id = $assignment->id;
                $submission->user_id = $student->id;
                $submission->student_id = $student->id; // For compatibility
                $submission->file_path = $filePath;
                $submission->file_name = $file->getClientOriginalName();
                $submission->file_type = $file->getMimeType();
                $submission->file_size = $fileSize;
                $submission->file_icon = $this->getFileIconClass($fileExtension);
                $submission->file_color = $this->getFileColorClass($fileExtension);
                $submission->notes = $request->notes;
                $submission->submitted_at = now();
                $submission->save();
                
                // Create notification for teacher
                if (class_exists('App\Models\AssignmentNotification')) {
                    $notification = new \App\Models\AssignmentNotification();
                    $notification->user_id = $assignment->teacher_id;
                    $notification->title = 'Tugas Baru Dikumpulkan';
                    $notification->message = $student->name . ' telah mengumpulkan tugas "' . $assignment->title . '"';
                    $notification->type = 'submission';
                    $notification->reference_id = $submission->id;
                    $notification->save();
                }
                
                return redirect()->route('siswa.assignments.show', $assignment)
                    ->with('success', 'Tugas berhasil dikumpulkan.');
            }
            
            return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function updateSubmission(Request $request, Assignment $assignment)
    {
        $student = Auth::user();
        
        // Verify student has access to this assignment
        $studentClassroomIds = $student->classrooms()->pluck('classrooms.id');
        $studentClassIds = $student->classes()->pluck('school_classes.id');
        
        $hasAccess = $assignment->classes()->whereIn('class_id', $studentClassIds)->exists() ||
                    $assignment->classrooms()->whereIn('classroom_id', $studentClassroomIds)->exists();
                    
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }
        
        // Check if the assignment is expired and late submissions are not allowed
        if ($assignment->isExpired() && !$assignment->allow_late_submission) {
            return redirect()->back()->with('error', 'Deadline pengumpulan tugas telah berakhir.');
        }
        
        // Find existing submission
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $student->id)
            ->first();
            
        if (!$submission) {
            return redirect()->back()->with('error', 'Anda belum mengumpulkan tugas ini.');
        }
        
        // Validate request
        $request->validate([
            'file' => 'required|file|max:102400|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Upload file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileExtension = $file->getClientOriginalExtension();
                
                // Delete old file
                if ($submission->file_path && Storage::exists('public/' . $submission->file_path)) {
                    Storage::delete('public/' . $submission->file_path);
                }
                
                // Store new file
                $filePath = $file->store('submissions/' . $assignment->id, 'public');
                $fileSize = $this->formatFileSize($file->getSize());
                
                // Update submission
                $submission->file_path = $filePath;
                $submission->file_name = $file->getClientOriginalName();
                $submission->file_type = $file->getMimeType();
                $submission->file_size = $fileSize;
                $submission->file_icon = $this->getFileIconClass($fileExtension);
                $submission->file_color = $this->getFileColorClass($fileExtension);
                $submission->notes = $request->notes;
                $submission->submitted_at = now();
                
                // Check if submission is late
                if ($assignment->deadline && now()->gt($assignment->deadline)) {
                    $submission->is_late = true;
                }
                
                $submission->save();
                
                return redirect()->route('siswa.assignments.show', $assignment)
                    ->with('success', 'Tugas berhasil diupdate.');
            }
            
            return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Format file size to human readable format
     *
     * @param int $size File size in bytes
     * @return string Formatted file size
     */
    private function formatFileSize($size)
    {
        if ($size < 1024) {
            return $size . ' B';
        } elseif ($size < 1048576) {
            return round($size / 1024, 2) . ' KB';
        } else {
            return round($size / 1048576, 2) . ' MB';
        }
    }
    
    /**
     * Get icon class based on file extension
     *
     * @param string $extension File extension
     * @return string Font Awesome icon class
     */
    private function getFileIconClass($extension)
    {
        switch (strtolower($extension)) {
            case 'pdf':
                return 'file-pdf';
            case 'doc':
            case 'docx':
                return 'file-word';
            case 'xls':
            case 'xlsx':
                return 'file-excel';
            case 'ppt':
            case 'pptx':
                return 'file-powerpoint';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'file-image';
            case 'zip':
            case 'rar':
                return 'file-archive';
            default:
                return 'file';
        }
    }
    
    /**
     * Get color class based on file extension
     *
     * @param string $extension File extension
     * @return string CSS color class
     */
    private function getFileColorClass($extension)
    {
        switch (strtolower($extension)) {
            case 'pdf':
                return 'text-red-500';
            case 'doc':
            case 'docx':
                return 'text-blue-500';
            case 'xls':
            case 'xlsx':
                return 'text-green-500';
            case 'ppt':
            case 'pptx':
                return 'text-orange-500';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'text-purple-500';
            case 'zip':
            case 'rar':
                return 'text-yellow-500';
            default:
                return 'text-gray-500';
        }
    }
}
