<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Grade;

class GradeController extends Controller
{
    /**
     * Display a listing of grades for the authenticated student.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentIndex()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found'
            ], 404);
        }
        
        // Get grades for the student
        $grades = Grade::where('student_id', $student->id)
            ->with(['subject', 'teacher.user'])
            ->get()
            ->groupBy('subject_id')
            ->map(function ($subjectGrades) {
                $subject = $subjectGrades->first()->subject;
                $teacher = $subjectGrades->first()->teacher;
                
                return [
                    'subject' => $subject,
                    'teacher' => $teacher ? $teacher->user->name : null,
                    'grades' => $subjectGrades->map(function ($grade) {
                        return [
                            'id' => $grade->id,
                            'type' => $grade->type,
                            'score' => $grade->score,
                            'notes' => $grade->notes,
                            'created_at' => $grade->created_at,
                        ];
                    })
                ];
            })
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => $grades
        ]);
    }
}
