<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchGradeController extends Controller
{
    /**
     * Save batch grading
     */
    public function save(Request $request, Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'selected' => 'required|array',
            'selected.*' => 'exists:submissions,id',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100',
        ], [
            'selected.required' => 'Pilih minimal satu pengumpulan untuk dinilai',
            'scores.*.numeric' => 'Nilai harus berupa angka',
            'scores.*.min' => 'Nilai minimal adalah 0',
            'scores.*.max' => 'Nilai maksimal adalah 100',
        ]);
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            $count = 0;
            
            foreach ($request->selected as $submissionId) {
                if (isset($request->scores[$submissionId])) {
                    $submission = Submission::findOrFail($submissionId);
                    
                    // Check if submission belongs to the assignment
                    if ($submission->assignment_id !== $assignment->id) {
                        continue;
                    }
                    
                    // Update score
                    $submission->score = $request->scores[$submissionId];
                    $submission->graded_at = now();
                    $submission->graded_by = Auth::id();
                    $submission->save();
                    
                    $count++;
                }
            }
            
            DB::commit();
            
            return redirect()->route('guru.assignments.show', $assignment)
                ->with('success', "Berhasil memberi nilai pada {$count} pengumpulan tugas.");
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
