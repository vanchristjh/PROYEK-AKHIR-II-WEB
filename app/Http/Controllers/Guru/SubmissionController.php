<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Models\AssignmentNotification;
use App\Exports\SubmissionsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SubmissionController extends Controller
{
    public function index(Assignment $assignment, Request $request)
    {
        // Security check
        if ($assignment->teacher_id !== Auth::id()) {
            return redirect()->route('guru.assignments.index')->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }

        $query = $assignment->submissions()->with('student');

        // Apply student name filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status')) {
            switch ($request->status) {
                case 'graded':
                    $query->whereNotNull('score');
                    break;
                case 'pending':
                    $query->whereNull('score');
                    break;
            }
        }

        $submissions = $query->latest()->paginate(20);

        return view('guru.submissions.index', compact('assignment', 'submissions'));
    }

    /**
     * Download a submission file.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function download(Submission $submission)
    {
        $teacher = Auth::user();
        
        // Check if the submission belongs to an assignment by this teacher
        $assignment = Assignment::find($submission->assignment_id);
        
        if (!$assignment || $assignment->teacher_id != $teacher->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }
        
        // Check if file exists
        if (!$submission->file_path || !Storage::exists($submission->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }
        
        // Get original filename
        $filename = $submission->file_name ?? basename($submission->file_path);
        
        // Return file for download
        return Storage::download($submission->file_path, $filename);
    }
    
    /**
     * Grade a submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function grade(Request $request, Submission $submission)
    {
        $teacher = Auth::user();
        
        // Check if the submission belongs to an assignment by this teacher
        $assignment = Assignment::find($submission->assignment_id);
        
        if (!$assignment || $assignment->teacher_id != $teacher->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menilai tugas ini.');
        }
        
        // Validate request
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
        ]);
        
        try {
            // Update submission
            $submission->score = $request->score;
            $submission->feedback = $request->feedback;
            $submission->graded_at = now();
            $submission->graded_by = $teacher->id;
            $submission->save();
            
            return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Send a reminder to a student about assignment submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function sendReminder(Request $request, Assignment $assignment)
    {
        // Security check
        if ($assignment->teacher_id !== Auth::id()) {
            return redirect()->route('guru.assignments.index')->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:500',
        ]);

        // Get the student
        $student = User::findOrFail($request->student_id);
        
        // Default message if not provided
        $message = $request->message ?? "Jangan lupa untuk mengumpulkan tugas {$assignment->title} sebelum tenggat waktu.";
        
        // Create notification record
        AssignmentNotification::create([
            'user_id' => $student->id,
            'title' => 'Pengingat Tugas',
            'message' => $message,
            'type' => 'assignment_reminder',
            'data' => json_encode(['assignment_id' => $assignment->id]),
            'sender_id' => Auth::id(),
            'is_read' => false,
        ]);
        
        // You could also implement email notification here if needed
        
        return redirect()->back()->with('success', "Pengingat telah dikirim ke {$student->name}");
    }

    /**
     * Display a specific submission.
     *
     * @param  int  $assignment
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function show($assignment, Submission $submission)
    {
        $teacher = Auth::user();
        
        // Check if the submission belongs to an assignment by this teacher
        $assignment = Assignment::find($submission->assignment_id);
        
        if (!$assignment || $assignment->teacher_id != $teacher->id) {
            return redirect()->route('guru.assignments.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat pengumpulan tugas ini.');
        }
        
        return view('guru.submissions.show', compact('assignment', 'submission'));
    }

    /**
     * Export submissions for a specific assignment to Excel.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function exportSubmissions(Assignment $assignment)
    {
        // Security check
        if ($assignment->teacher_id !== Auth::id()) {
            return redirect()->route('guru.assignments.index')->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }
        
        $filename = 'pengumpulan_tugas_' . $assignment->id . '_' . str_slug($assignment->title) . '.xlsx';
        return Excel::download(new SubmissionsExport($assignment->id), $filename);
    }

    /**
     * Send reminders to all students who haven't submitted the assignment yet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function sendBulkReminders(Request $request, Assignment $assignment)
    {
        // Security check
        if ($assignment->teacher_id !== Auth::id()) {
            return redirect()->route('guru.assignments.index')->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }

        $request->validate([
            'message' => 'nullable|string|max:500',
        ]);

        // Get assigned classes
        $classIds = $assignment->classes->pluck('id')->toArray();
        
        // Get students who are assigned to these classes but haven't submitted
        $studentsWithoutSubmissions = User::whereHas('student', function($query) use ($classIds) {
            $query->whereIn('class_id', $classIds);
        })->whereDoesntHave('submissions', function($query) use ($assignment) {
            $query->where('assignment_id', $assignment->id);
        })->get();
          // Get students who are assigned to the classes but haven't submitted
        $classes = $assignment->classes;
        if ($classes->count() > 0) {
            // Get all students from classes assigned to this assignment
            $classroomStudents = User::whereIn('classroom_id', $classes->pluck('id'))
                ->whereDoesntHave('submissions', function($query) use ($assignment) {
                    $query->where('assignment_id', $assignment->id);
                })->get();
                
            // Merge the collections (avoiding duplicates)
            $studentsWithoutSubmissions = $studentsWithoutSubmissions->merge($classroomStudents)->unique('id');
        }
        
        // Default message if not provided
        $message = $request->message ?? "Jangan lupa untuk mengumpulkan tugas {$assignment->title} sebelum tenggat waktu.";
        
        // Count of students who received reminders
        $reminderCount = 0;
        
        // Send notifications to each student
        foreach ($studentsWithoutSubmissions as $student) {
            AssignmentNotification::create([
                'user_id' => $student->id,
                'title' => 'Pengingat Tugas',
                'message' => $message,
                'type' => 'assignment_reminder',
                'data' => json_encode(['assignment_id' => $assignment->id]),
                'sender_id' => Auth::id(),
                'is_read' => false,
            ]);
            
            $reminderCount++;
        }
        
        return redirect()->back()->with('success', "Pengingat telah dikirim ke {$reminderCount} siswa yang belum mengumpulkan tugas.");
    }
    
    /**
     * Update multiple submissions at once (batch grading).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateBatch(Request $request)
    {
        $teacher = Auth::user();
        
        // Validate request
        $validated = $request->validate([
            'submissions' => 'required|array',
            'submissions.*.id' => 'required|exists:submissions,id',
            'submissions.*.score' => 'required|numeric|min:0|max:100',
            'submissions.*.feedback' => 'nullable|string|max:1000',
        ]);
        
        try {
            $updated = 0;
            
            foreach ($validated['submissions'] as $submissionData) {
                $submission = Submission::find($submissionData['id']);
                
                // Check if submission exists and belongs to an assignment by this teacher
                $assignment = Assignment::find($submission->assignment_id);
                
                if (!$assignment || $assignment->teacher_id != $teacher->id) {
                    continue; // Skip submissions that don't belong to this teacher
                }
                
                // Update submission
                $submission->score = $submissionData['score'];
                $submission->feedback = $submissionData['feedback'] ?? null;
                $submission->graded_at = now();
                $submission->graded_by = $teacher->id;
                $submission->save();
                
                // If you have a Grade model you might want to update that as well here
                if (class_exists('\App\Models\Grade')) {
                    \App\Models\Grade::updateOrCreate(
                        [
                            'student_id' => $submission->user_id,
                            'teacher_id' => $teacher->id,
                            'assignment_id' => $assignment->id,
                        ],
                        [
                            'subject_id' => $assignment->subject_id,
                            'score' => $submissionData['score'],
                            'max_score' => 100,
                            'feedback' => $submissionData['feedback'] ?? null,
                            'type' => 'assignment',
                            'semester' => $this->getCurrentSemester(),
                            'academic_year' => $this->getCurrentAcademicYear(),
                        ]
                    );
                }
                
                $updated++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "{$updated} pengumpulan tugas berhasil dinilai.",
                'updated' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Get current semester.
     *
     * @return string
     */
    protected function getCurrentSemester()
    {
        $month = now()->month;
        // In Indonesia, even semester is January-June, odd semester is July-December
        return $month >= 7 ? 'Ganjil' : 'Genap';
    }
    
    /**
     * Get current academic year.
     *
     * @return string
     */
    protected function getCurrentAcademicYear()
    {
        $year = now()->year;
        $month = now()->month;
        
        // Academic year format: YYYY/YYYY
        return $month >= 7 
            ? $year . '/' . ($year + 1) 
            : ($year - 1) . '/' . $year;
    }
}
