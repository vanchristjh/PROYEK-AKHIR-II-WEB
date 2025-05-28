<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Submission;
use App\Models\Assignment;
use App\Exports\SubmissionsExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AllSubmissionsController extends Controller
{
    /**
     * Display a listing of all submissions across all assignments.
     */    
    public function index(Request $request)
    {
        // Get the authenticated teacher
        $teacher = Auth::user();
        
        // Get all assignments created by this teacher
        $assignments = Assignment::where('teacher_id', $teacher->id)->pluck('id');
        
        // Initialize the query
        $query = Submission::whereIn('assignment_id', $assignments)
            ->with(['assignment', 'student']);
            
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('assignment', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status === 'graded') {
                $query->whereNotNull('score');
            } elseif ($request->status === 'pending') {
                $query->whereNull('score');
            }
        }
        
        // Apply assignment filter if provided
        if ($request->has('assignment_id') && !empty($request->assignment_id)) {
            $query->where('assignment_id', $request->assignment_id);
        }
        
        // Get submissions with pagination
        $submissions = $query->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Count statistics
        $totalSubmissions = Submission::whereIn('assignment_id', $assignments)->count();
        $gradedCount = Submission::whereIn('assignment_id', $assignments)->whereNotNull('score')->count();
        $pendingCount = $totalSubmissions - $gradedCount;
        
        // Calculate average score for graded submissions
        $averageScore = Submission::whereIn('assignment_id', $assignments)
            ->whereNotNull('score')
            ->avg('score');
            
        // Get all assignments for filter dropdown
        $teacherAssignments = Assignment::where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('guru.submissions.all', compact(
            'submissions', 
            'totalSubmissions', 
            'gradedCount', 
            'pendingCount', 
            'averageScore',
            'teacherAssignments'
        ));
    }
    
    /**
     * Bulk grade multiple submissions at once
     */
    public function bulkGrade(Request $request)
    {
        $request->validate([
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'exists:submissions,id',
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string'
        ]);
        
        $teacher = Auth::user();
        $updatedCount = 0;
        
        foreach ($request->submission_ids as $submissionId) {
            $submission = Submission::find($submissionId);
            
            // Verify teacher owns this assignment
            if ($submission && $submission->assignment->teacher_id == $teacher->id) {
                $submission->update([
                    'score' => $request->score,
                    'feedback' => $request->feedback,
                    'status' => 'graded',
                    'graded_at' => now()
                ]);
                
                $updatedCount++;
            }
        }
        
        return redirect()->route('guru.all-submissions.index')
            ->with('success', "{$updatedCount} pengumpulan tugas berhasil dinilai.");
    }

    /**
     * Export all submissions to Excel
     */
    public function export(Request $request)
    {
        $teacher = Auth::user();
        
        // Optionally filter by assignment ID if provided
        $assignmentId = $request->assignment_id;
        
        if ($assignmentId) {
            // Verify this assignment belongs to the teacher
            $assignment = Assignment::find($assignmentId);
            if (!$assignment || $assignment->teacher_id != $teacher->id) {
                return redirect()->route('guru.all-submissions.index')
                    ->with('error', 'Anda tidak memiliki akses ke tugas ini.');
            }
        }
        
        $filename = $assignmentId 
            ? 'pengumpulan_tugas_' . $assignmentId . '.xlsx' 
            : 'semua_pengumpulan_tugas.xlsx';
            
        return Excel::download(new SubmissionsExport($assignmentId), $filename);
    }
}
