<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the submissions for the authenticated student.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found'
            ], 404);
        }
        
        $submissions = Submission::where('student_id', $student->id)
            ->with(['assignment.subject', 'assignment.teacher.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'assignment' => [
                        'id' => $submission->assignment->id,
                        'title' => $submission->assignment->title,
                        'subject' => $submission->assignment->subject->name,
                        'teacher' => $submission->assignment->teacher->user->name,
                    ],
                    'content' => $submission->content,
                    'status' => $submission->status,
                    'score' => $submission->score,
                    'feedback' => $submission->feedback,
                    'created_at' => $submission->created_at,
                    'updated_at' => $submission->updated_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $submissions
        ]);
    }
    
    /**
     * Display the specified submission.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Submission $submission)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || $submission->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $submission->load(['assignment.subject', 'assignment.teacher.user']);
        
        $data = [
            'id' => $submission->id,
            'assignment' => [
                'id' => $submission->assignment->id,
                'title' => $submission->assignment->title,
                'subject' => $submission->assignment->subject->name,
                'teacher' => $submission->assignment->teacher->user->name,
            ],
            'content' => $submission->content,
            'status' => $submission->status,
            'score' => $submission->score,
            'feedback' => $submission->feedback,
            'attachment' => $submission->attachment,
            'attachment_url' => $submission->attachment ? Storage::url($submission->attachment) : null,
            'created_at' => $submission->created_at,
            'updated_at' => $submission->updated_at,
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Store a newly created submission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Assignment $assignment)
    {
        $request->validate([
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found'
            ], 404);
        }
        
        // Check if assignment belongs to student's classroom
        $hasAccess = $assignment->classrooms()->where('classroom_id', $student->classroom->id)->exists();
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Check if submission already exists
        $existing = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();
            
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Submission already exists'
            ], 400);
        }
        
        // Create submission
        $submission = new Submission();
        $submission->assignment_id = $assignment->id;
        $submission->student_id = $student->id;
        $submission->content = $request->content;
        $submission->status = 'submitted';
        
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('submissions');
            $submission->attachment = $path;
        }
        
        $submission->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Submission created successfully',
            'data' => $submission
        ], 201);
    }
    
    /**
     * Update an existing submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Submission $submission)
    {
        $request->validate([
            'content' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || $submission->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Check if past due date
        if ($submission->assignment->due_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update submission after due date'
            ], 400);
        }
        
        $submission->content = $request->input('content', $submission->content);
        $submission->status = 'updated';
        
        if ($request->hasFile('attachment')) {
            // Delete old attachment if it exists
            if ($submission->attachment) {
                Storage::delete($submission->attachment);
            }
            
            $path = $request->file('attachment')->store('submissions');
            $submission->attachment = $path;
        }
        
        $submission->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Submission updated successfully',
            'data' => $submission
        ]);
    }
    
    /**
     * Display submissions for a specific assignment (teacher view).
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignmentSubmissions(Assignment $assignment)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher || $assignment->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $submissions = Submission::where('assignment_id', $assignment->id)
            ->with(['student.user'])
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'student' => [
                        'id' => $submission->student->id,
                        'name' => $submission->student->user->name,
                        'student_id' => $submission->student->student_id, // Student ID/NIS
                    ],
                    'status' => $submission->status,
                    'score' => $submission->score,
                    'has_attachment' => $submission->attachment ? true : false,
                    'submitted_at' => $submission->created_at,
                    'updated_at' => $submission->updated_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $submissions
        ]);
    }
    
    /**
     * Show a specific submission (teacher view).
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\JsonResponse
     */
    public function teacherShow(Submission $submission)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        // Check if submission belongs to an assignment made by this teacher
        if (!$teacher || $submission->assignment->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $submission->load(['assignment', 'student.user']);
        
        $data = [
            'id' => $submission->id,
            'student' => [
                'id' => $submission->student->id,
                'name' => $submission->student->user->name,
                'student_id' => $submission->student->student_id, // Student ID/NIS
            ],
            'content' => $submission->content,
            'status' => $submission->status,
            'score' => $submission->score,
            'feedback' => $submission->feedback,
            'attachment' => $submission->attachment,
            'attachment_url' => $submission->attachment ? Storage::url($submission->attachment) : null,
            'submitted_at' => $submission->created_at,
            'updated_at' => $submission->updated_at,
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Grade a submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\JsonResponse
     */
    public function grade(Request $request, Submission $submission)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        $teacher = $user->teacher;
        
        // Check if submission belongs to an assignment made by this teacher
        if (!$teacher || $submission->assignment->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $submission->score = $request->score;
        $submission->feedback = $request->feedback;
        $submission->status = 'graded';
        $submission->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Submission graded successfully',
            'data' => $submission
        ]);
    }
    
    /**
     * Download a submission attachment.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function download(Submission $submission)
    {
        $user = Auth::user();
        
        // Check if user is the student who submitted or the teacher of the assignment
        $isStudent = $user->hasRole('siswa') && $user->student && $submission->student_id === $user->student->id;
        $isTeacher = $user->hasRole('guru') && $user->teacher && $submission->assignment->teacher_id === $user->teacher->id;
        
        if (!$isStudent && !$isTeacher) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        if (!$submission->attachment) {
            return response()->json([
                'success' => false,
                'message' => 'No attachment found'
            ], 404);
        }
        
        return Storage::download($submission->attachment);
    }
}
