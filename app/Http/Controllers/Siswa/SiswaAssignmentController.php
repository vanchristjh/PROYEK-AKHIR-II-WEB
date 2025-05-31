<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SiswaAssignmentController extends Controller
{
    public function __construct()
    {
        // Middleware is now handled in route definitions
    }

    /**
     * Display all assignments for the student
     */     
    public function index(Request $request)
    {
        // Get the student's current user
        $student = Auth::user();
        $classroomId = $student->classroom_id;

        // Get all active assignments
        $query = Assignment::query()
            ->where(function($q) use ($classroomId, $student) {
                // Get assignments for student's classroom if it exists
                if ($classroomId) {
                    $q->whereHas('classrooms', function($query) use ($classroomId) {
                        $query->where('classrooms.id', $classroomId);
                    });
                }
                
                // Get assignments for student's class/grade level
                if ($student->class_id) {
                    $q->orWhereHas('classes', function($query) use ($student) {
                        $query->where('school_classes.id', $student->class_id);
                    });
                }
                
                // Get assignments for student's subjects
                $q->orWhereHas('subject', function($query) use ($student) {
                    $query->whereHas('classrooms', function($q) use ($student) {
                        $q->where('classrooms.id', $student->classroom_id);
                    });
                });

                // Get assignments by teacher subject matches
                if ($classroomId) {
                    $q->orWhereHas('subject.teachers', function($query) use ($student) {
                        $query->whereHas('classrooms', function($q) use ($student) {
                            $q->where('classrooms.id', $student->classroom_id);
                        });
                    });
                }
            })
            ->with(['subject', 'teacher', 'submissions' => function($query) use ($student) {
                $query->where('user_id', $student->id);
            }]);
            
        // Filter by subject if provided
        if ($request->has('subject') && !empty($request->subject)) {
            $query->where('subject_id', $request->subject);
        }
        
        // Filter by status from request parameters
        $status = $request->input('status');
        if ($status === 'completed') {
            $query->whereHas('submissions', function($query) {
                $query->where('user_id', Auth::id());
            });
        } elseif ($status === 'pending') {
            $query->whereDoesntHave('submissions', function($query) {
                $query->where('user_id', Auth::id());
            })->where('deadline', '>', now());
        } elseif ($status === 'overdue') {
            $query->where('deadline', '<', now())
                ->whereDoesntHave('submissions', function($query) {
                    $query->where('user_id', Auth::id());
                });
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
        
        $assignmentsCollection = $query->latest()->get(); // Use get() to retrieve all data
        
        // Calculate statistics for assignment header
        $completedCount = $assignmentsCollection->filter(function($assignment) {
            return $assignment->submissions->isNotEmpty();
        })->count();
        
        $pendingCount = $assignmentsCollection->filter(function($assignment) {
            return $assignment->submissions->isEmpty() && !$assignment->isExpired();
        })->count();
        
        $overdueCount = $assignmentsCollection->filter(function($assignment) {
            return $assignment->submissions->isEmpty() && $assignment->isExpired();
        })->count();
        
        // Get current page from request
        $page = $request->get('page', 1);
        
        // Define how many items per page
        $perPage = 10;
        
        // Slice the collection to get the items for the current page
        $items = $assignmentsCollection->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Create a paginator instance
        $assignments = new LengthAwarePaginator(
            $items,
            $assignmentsCollection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
        
        // Get subjects for filter dropdown
        $subjects = Subject::whereHas('classrooms', function($query) use ($classroomId) {
            $query->where('classroom_id', $classroomId);
        })->get();
        
        return view('siswa.assignments.index', compact(
            'assignments', 
            'status', 
            'subjects', 
            'completedCount', 
            'pendingCount', 
            'overdueCount'
        ));
    }
      /**
     * Show details of an assignment and submission form
     */    
    public function show(Assignment $assignment)
    {
        // Get the current student
        $student = Auth::user();
        $studentClassroomId = $student->classroom_id;
        $studentClassId = $student->class_id;

        // Check if the student has access via classroom or class
        $hasAccess = false;

        // Check classroom access
        if ($studentClassroomId) {
            $hasAccess = $assignment->classrooms()
                ->where('classrooms.id', $studentClassroomId)
                ->exists();
        }

        // If no classroom access, check class access
        if (!$hasAccess && $studentClassId) {
            $hasAccess = $assignment->schoolClasses()
                ->where('school_classes.id', $studentClassId)
                ->exists();
        }

        // If no access through either classroom or class, check subject access
        if (!$hasAccess) {
            $hasAccess = $assignment->subject()
                ->whereHas('classrooms', function($query) use ($studentClassroomId) {
                    $query->where('classrooms.id', $studentClassroomId);
                })->exists();
        }

        // If still no access, check if assignment is for any subject taught in student's classroom
        if (!$hasAccess) {
            $hasAccess = $assignment->subject()
                ->whereHas('teachers', function($query) use ($studentClassroomId) {
                    $query->whereHas('classrooms', function($q) use ($studentClassroomId) {
                        $q->where('classrooms.id', $studentClassroomId);
                    });
                })->exists();
        }

        // If no access through any means, deny access
        if (!$hasAccess) {
            abort(403, 'Unauthorized action.');
        }

        // Get student's submission for this assignment if it exists
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $student->id)
            ->first();

        return view('siswa.assignments.show', compact('assignment', 'submission'));
    }
      /**
     * Submit a solution for an assignment
     */    public function submit(Request $request, Assignment $assignment)
    {
        // Get the current student
        $student = Auth::user();
        $studentClassroomId = $student->classroom_id;
        $studentClassId = $student->class_id;

        // Check if the student has access via classroom or class
        $hasAccess = false;

        // Check classroom access
        if ($studentClassroomId) {
            $hasAccess = $assignment->classrooms()
                ->where('classrooms.id', $studentClassroomId)
                ->exists();
        }

        // If no classroom access, check class access
        if (!$hasAccess && $studentClassId) {
            $hasAccess = $assignment->schoolClasses()
                ->where('school_classes.id', $studentClassId)
                ->exists();
        }

        // If no access through either classroom or class, check subject access
        if (!$hasAccess) {
            $hasAccess = $assignment->subject()
                ->whereHas('classrooms', function($query) use ($studentClassroomId) {
                    $query->where('classrooms.id', $studentClassroomId);
                })->exists();
        }

        // If still no access, check if assignment is for any subject taught in student's classroom
        if (!$hasAccess) {
            $hasAccess = $assignment->subject()
                ->whereHas('teachers', function($query) use ($studentClassroomId) {
                    $query->whereHas('classrooms', function($q) use ($studentClassroomId) {
                        $q->where('classrooms.id', $studentClassroomId);
                    });
                })->exists();
        }

        // If no access through any means, deny access
        if (!$hasAccess) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses untuk mengumpulkan tugas ini.');
        }

        // Check if deadline has passed and late submissions are not allowed
        if ($assignment->isExpired() && !$assignment->allow_late_submission) {
            return redirect()->route('siswa.assignments.show', $assignment->id)
                ->with('error', 'Deadline untuk tugas ini telah terlewat.');
        }

        // Validate the request
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:102400'], // 100MB max
            'notes' => ['nullable', 'string'],
        ]);

        try {
            // Check if student already submitted
            $existingSubmission = Submission::where('assignment_id', $assignment->id)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingSubmission) {
                // Delete old file if it exists
                if ($existingSubmission->file_path && Storage::exists('public/' . $existingSubmission->file_path)) {
                    Storage::delete('public/' . $existingSubmission->file_path);
                }

                // Get file information
                $file = $request->file('file');
                $fileExtension = $file->getClientOriginalExtension();
                $filePath = $file->store('submissions/' . $assignment->id, 'public');
                $fileSize = $this->formatFileSize($file->getSize());

                // Update existing submission
                $existingSubmission->update([
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $fileSize,
                    'file_icon' => $this->getFileIconClass($fileExtension),
                    'file_color' => $this->getFileColorClass($fileExtension),
                    'notes' => $validated['notes'] ?? null,
                    'submitted_at' => now(),
                ]);

                return redirect()->route('siswa.assignments.show', $assignment->id)
                    ->with('success', 'Pengumpulan tugas berhasil diperbarui.');
            }

            // Get file information for new submission
            $file = $request->file('file');
            $fileExtension = $file->getClientOriginalExtension();
            $filePath = $file->store('submissions/' . $assignment->id, 'public');
            $fileSize = $this->formatFileSize($file->getSize());

            // Create new submission
            Submission::create([
                'assignment_id' => $assignment->id,
                'user_id' => Auth::id(),
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $fileSize,
                'file_icon' => $this->getFileIconClass($fileExtension),
                'file_color' => $this->getFileColorClass($fileExtension),
                'notes' => $validated['notes'] ?? null,
                'submitted_at' => now(),
            ]);

            return redirect()->route('siswa.assignments.show', $assignment->id)
                ->with('success', 'Tugas berhasil dikumpulkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengumpulkan tugas: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing submission
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
     * Format file size for display
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
     */
    private function getFileColorClass($extension)
    {
        switch (strtolower($extension)) {
            case 'pdf':
                return 'bg-red-500';
            case 'doc':
            case 'docx':
                return 'bg-blue-500';
            case 'xls':
            case 'xlsx':
                return 'bg-green-500';
            case 'ppt':
            case 'pptx':
                return 'bg-orange-500';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'bg-purple-500';
            case 'zip':
            case 'rar':
                return 'bg-yellow-500';
            default:
                return 'bg-gray-500';
        }
    }
}
