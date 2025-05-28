<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    /**
     * Store a newly created submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Assignment $assignment)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if deadline has passed
        if ($assignment->isExpired()) {
            return redirect()->back()->with('error', 'Deadline tugas telah berakhir. Anda tidak dapat mengumpulkan tugas ini.');
        }

        // Check if student has already submitted
        $existingSubmission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', Auth::id())
            ->first();

        if ($existingSubmission) {
            return redirect()->back()->with('error', 'Anda sudah mengumpulkan tugas ini sebelumnya.');
        }

        // Process file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug(Auth::user()->name) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('submissions/' . $assignment->id, $fileName, 'public');
            
            // Create submission
            $submission = new Submission();
            $submission->assignment_id = $assignment->id;
            $submission->student_id = Auth::id();
            $submission->file_path = $filePath;
            $submission->file_name = $file->getClientOriginalName();
            $submission->file_size = $file->getSize();
            $submission->notes = $request->notes;
            $submission->save();

            return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan.');
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file.');
    }

    /**
     * Update the specified submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Submission $submission)
    {
        // Validate the request
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB
        ]);

        // Check if current user owns this submission
        if ($submission->student_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit pengumpulan ini.');
        }

        // Check if assignment deadline has passed
        if ($submission->assignment->isExpired()) {
            return redirect()->back()->with('error', 'Deadline tugas telah berakhir. Anda tidak dapat mengubah pengumpulan.');
        }

        // Check if submission has been graded
        if (!is_null($submission->score)) {
            return redirect()->back()->with('error', 'Tugas yang sudah dinilai tidak dapat diubah.');
        }

        // Process file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }

            // Upload new file
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug(Auth::user()->name) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('submissions/' . $submission->assignment_id, $fileName, 'public');
            
            // Update submission
            $submission->file_path = $filePath;
            $submission->file_name = $file->getClientOriginalName();
            $submission->file_size = $file->getSize();
            $submission->updated_at = now();
            $submission->save();

            return redirect()->back()->with('success', 'Pengumpulan tugas berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file.');
    }

    /**
     * Remove the specified submission.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Submission $submission)
    {
        // Check if current user owns this submission
        if ($submission->student_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus pengumpulan ini.');
        }

        // Check if assignment deadline has passed
        if ($submission->assignment->isExpired()) {
            return redirect()->back()->with('error', 'Deadline tugas telah berakhir. Anda tidak dapat menghapus pengumpulan.');
        }

        // Check if submission has been graded
        if (!is_null($submission->score)) {
            return redirect()->back()->with('error', 'Tugas yang sudah dinilai tidak dapat dihapus.');
        }

        // Delete file
        if ($submission->file_path && Storage::disk('public')->exists($submission->file_path)) {
            Storage::disk('public')->delete($submission->file_path);
        }

        // Delete submission
        $submission->delete();

        return redirect()->back()->with('success', 'Pengumpulan tugas berhasil dihapus.');
    }

    /**
     * Download the submission file.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function download(Submission $submission)
    {
        // Check if current user has access to this submission
        $user = Auth::user();
        
        if ($user->hasRole('siswa') && $submission->student_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        if ($user->hasRole('guru') && $submission->assignment->teacher_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengunduh file ini.');
        }

        // Check if file exists
        if (!$submission->file_path || !Storage::disk('public')->exists($submission->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Return file for download
        return Storage::disk('public')->download($submission->file_path, $submission->file_name);
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
        // Validate request
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000',
        ]);

        // Check if current user is the teacher of this assignment
        if (Auth::user()->id !== $submission->assignment->teacher_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menilai pengumpulan ini.');
        }

        // Update submission with grade
        $submission->score = $request->score;
        $submission->feedback = $request->feedback;
        $submission->graded_at = now();
        $submission->save();

        return redirect()->back()->with('success', 'Nilai berhasil diberikan.');
    }
}
