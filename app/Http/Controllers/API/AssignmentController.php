<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Submission;
use App\Models\Classroom;
use App\Models\Subject;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for students.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentIndex()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || !$student->classroom) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile or classroom not found'
            ], 404);
        }
        
        $assignments = Assignment::whereHas('classrooms', function ($query) use ($student) {
                $query->where('classroom_id', $student->classroom->id);
            })
            ->with(['teacher.user', 'subject', 'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($assignment) {
                $submission = $assignment->submissions->first();
                
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date,
                    'teacher' => $assignment->teacher->user->name,
                    'subject' => $assignment->subject->name,
                    'has_submission' => $submission ? true : false,
                    'submission_status' => $submission ? $submission->status : null,
                    'submission_score' => $submission ? $submission->score : null,
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }
    
    /**
     * Display a specific assignment for students.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentShow(Assignment $assignment)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || !$student->classroom) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile or classroom not found'
            ], 404);
        }
        
        // Check if this assignment belongs to the student's classroom
        $hasAccess = $assignment->classrooms()->where('classroom_id', $student->classroom->id)->exists();
        
        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();
        
        $assignment->load(['teacher.user', 'subject']);
        
        $data = [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'description' => $assignment->description,
            'due_date' => $assignment->due_date,
            'teacher' => $assignment->teacher->user->name,
            'subject' => $assignment->subject->name,
            'attachment' => $assignment->attachment,
            'attachment_url' => $assignment->attachment ? Storage::url($assignment->attachment) : null,
            'submission' => $submission ? [
                'id' => $submission->id,
                'content' => $submission->content,
                'status' => $submission->status,
                'score' => $submission->score,
                'feedback' => $submission->feedback,
                'attachment' => $submission->attachment,
                'attachment_url' => $submission->attachment ? Storage::url($submission->attachment) : null,
                'submitted_at' => $submission->created_at,
                'updated_at' => $submission->updated_at,
            ] : null,
            'created_at' => $assignment->created_at,
            'updated_at' => $assignment->updated_at,
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Display a listing of assignments for teachers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function teacherIndex()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher profile not found'
            ], 404);
        }
        
        $assignments = Assignment::where('teacher_id', $teacher->id)
            ->with(['subject', 'classrooms'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($assignment) {
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'due_date' => $assignment->due_date,
                    'subject' => $assignment->subject->name,
                    'classrooms' => $assignment->classrooms->pluck('name'),
                    'submission_count' => $assignment->submissions()->count(),
                    'created_at' => $assignment->created_at,
                    'updated_at' => $assignment->updated_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }
    
    /**
     * Display a specific assignment for teachers.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function teacherShow(Assignment $assignment)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher || $assignment->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $assignment->load(['subject', 'classrooms']);
        
        $data = [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'description' => $assignment->description,
            'due_date' => $assignment->due_date,
            'subject' => $assignment->subject,
            'classrooms' => $assignment->classrooms,
            'attachment' => $assignment->attachment,
            'attachment_url' => $assignment->attachment ? Storage::url($assignment->attachment) : null,
            'created_at' => $assignment->created_at,
            'updated_at' => $assignment->updated_at,
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Store a newly created assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_ids' => 'required|array',
            'classroom_ids.*' => 'exists:classrooms,id',
            'due_date' => 'required|date|after:now',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher profile not found'
            ], 404);
        }
        
        // Check if teacher teaches the subject
        $validSubject = $teacher->subjects()->where('subject_id', $request->subject_id)->exists();
        if (!$validSubject) {
            return response()->json([
                'success' => false,
                'message' => 'You do not teach this subject'
            ], 403);
        }
        
        // Create assignment
        $assignment = new Assignment();
        $assignment->title = $request->title;
        $assignment->description = $request->description;
        $assignment->due_date = $request->due_date;
        $assignment->subject_id = $request->subject_id;
        $assignment->teacher_id = $teacher->id;
        
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('assignments');
            $assignment->attachment = $path;
        }
        
        $assignment->save();
        
        // Attach classrooms
        $assignment->classrooms()->attach($request->classroom_ids);
        
        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully',
            'data' => $assignment
        ], 201);
    }
    
    /**
     * Update the specified assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'classroom_ids' => 'sometimes|required|array',
            'classroom_ids.*' => 'exists:classrooms,id',
            'due_date' => 'sometimes|required|date',
            'attachment' => 'nullable|file|max:10240',
        ]);
        
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher || $assignment->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Check if teacher teaches the subject if subject_id is being changed
        if ($request->has('subject_id') && $request->subject_id != $assignment->subject_id) {
            $validSubject = $teacher->subjects()->where('subject_id', $request->subject_id)->exists();
            if (!$validSubject) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not teach this subject'
                ], 403);
            }
        }
        
        // Update assignment
        if ($request->has('title')) $assignment->title = $request->title;
        if ($request->has('description')) $assignment->description = $request->description;
        if ($request->has('due_date')) $assignment->due_date = $request->due_date;
        if ($request->has('subject_id')) $assignment->subject_id = $request->subject_id;
        
        if ($request->hasFile('attachment')) {
            // Delete old attachment if it exists
            if ($assignment->attachment) {
                Storage::delete($assignment->attachment);
            }
            
            $path = $request->file('attachment')->store('assignments');
            $assignment->attachment = $path;
        }
        
        $assignment->save();
        
        // Update classrooms if provided
        if ($request->has('classroom_ids')) {
            $assignment->classrooms()->sync($request->classroom_ids);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Assignment updated successfully',
            'data' => $assignment
        ]);
    }
    
    /**
     * Remove the specified assignment from storage.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Assignment $assignment)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher || $assignment->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        // Delete attachment if it exists
        if ($assignment->attachment) {
            Storage::delete($assignment->attachment);
        }
        
        // Delete assignment (related records will be deleted via cascading)
        $assignment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully'
        ]);
    }
}
