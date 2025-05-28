<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the submissions.
     *
     * @return \Illuminate\View\View
     */    public function index()
    {
        $submissions = Submission::where('student_id', auth()->id())
            ->with(['assignment.subject', 'assignment.teacher'])
            ->latest()
            ->paginate(10);

        return view('siswa.submissions.index', compact('submissions'));
    }

    /**
     * Display the specified submission.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */    public function show($id)
    {
        $submission = Submission::findOrFail($id);

        if ($submission->student_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment = $submission->assignment;
        return view('siswa.assignments.show', compact('submission', 'assignment'));
    }

    /**
     * Store a new submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max
            'assignment_id' => 'required|exists:assignments,id'
        ]);

        $assignment = Assignment::findOrFail($request->assignment_id);
        if ($assignment->deadline && Carbon::now() > $assignment->deadline) {
            return redirect()->back()->with('error', 'Tidak dapat mengumpulkan tugas yang telah melewati deadline.');
        }

        $existingSubmission = Submission::where('assignment_id', $request->assignment_id)
            ->where('student_id', Auth::id())
            ->first();

        if ($existingSubmission) {
            return redirect()->back()->with('error', 'Anda sudah mengumpulkan tugas ini. Silakan edit pengumpulan yang ada.');
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('submissions/' . $assignment->id, 'public');

            $fileSize = $this->formatFileSize($file->getSize());
            $fileExtension = $file->getClientOriginalExtension();

            $submission = new Submission();
            $submission->assignment_id = $request->assignment_id;
            $submission->student_id = Auth::id();
            $submission->file_path = $filePath;
            $submission->file_name = $file->getClientOriginalName();
            $submission->file_type = $file->getMimeType();
            $submission->file_size = $fileSize;
            $submission->file_icon = $this->getFileIconClass($fileExtension);
            $submission->file_color = $this->getFileColorClass($fileExtension);
            $submission->submitted_at = now();
            $submission->save();

            return redirect()->route('siswa.assignments.show', $assignment)->with('success', 'Tugas berhasil dikumpulkan.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * Update the specified submission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Submission $submission)
    {
        // Check if the submission belongs to the current user
        if (Auth::id() != $submission->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit tugas ini.');
        }
        
        // Check if the assignment deadline has passed
        $assignment = Assignment::find($submission->assignment_id);
        if ($assignment->isExpired()) {
            return redirect()->back()->with('error', 'Deadline pengumpulan tugas telah berakhir.');
        }
        
        // Check if submission is already graded
        if (!is_null($submission->score)) {
            return redirect()->back()->with('error', 'Tugas yang sudah dinilai tidak dapat diedit.');
        }
        
        // Validate request
        $request->validate([
            'file' => 'required|file|max:102400|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png',
        ]);
        
        try {
            // Upload new file
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($submission->file_path && Storage::exists($submission->file_path)) {
                    Storage::delete($submission->file_path);
                }
                
                $file = $request->file('file');
                $path = $file->store('submissions');
                
                $submission->file_path = $path;
                $submission->file_name = $file->getClientOriginalName();
                $submission->file_size = $file->getSize();
                $submission->updated_at = now();
                $submission->save();
                
                return redirect()->back()->with('success', 'Pengumpulan tugas berhasil diperbarui.');
            }
            
            return redirect()->back()->with('error', 'Tidak ada file yang diunggah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified submission from storage.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Submission $submission)
    {
        // Check if the submission belongs to the current user
        if (Auth::id() != $submission->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus tugas ini.');
        }
        
        // Check if the assignment deadline has passed
        $assignment = Assignment::find($submission->assignment_id);
        if ($assignment->isExpired()) {
            return redirect()->back()->with('error', 'Deadline pengumpulan tugas telah berakhir.');
        }
        
        // Check if submission is already graded
        if (!is_null($submission->score)) {
            return redirect()->back()->with('error', 'Tugas yang sudah dinilai tidak dapat dihapus.');
        }
        
        try {
            // Delete file if exists
            if ($submission->file_path && Storage::exists($submission->file_path)) {
                Storage::delete($submission->file_path);
            }
            
            // Delete submission
            $submission->delete();
            
            return redirect()->back()->with('success', 'Pengumpulan tugas berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download the submitted file.
     *
     * @param  \App\Models\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function download(Submission $submission)
    {
        // Check if the submission belongs to the current user
        if (Auth::id() != $submission->user_id) {
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
     * Format file size for display.
     *
     * @param  int  $size
     * @return string
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
     * Format file size for display.
     *
     * @param  int  $bytes
     * @param  int  $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get appropriate icon class based on file extension.
     *
     * @param  string  $extension
     * @return string
     */
    private function getFileIconClass($extension)
    {
        switch (strtolower($extension)) {
            case 'pdf':
                return 'fa-file-pdf';
            case 'doc':
            case 'docx':
                return 'fa-file-word';
            case 'xls':
            case 'xlsx':
                return 'fa-file-excel';
            case 'ppt':
            case 'pptx':
                return 'fa-file-powerpoint';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'fa-file-image';
            case 'zip':
            case 'rar':
                return 'fa-file-archive';
            default:
                return 'fa-file';
        }
    }

    /**
     * Get appropriate icon class based on file extension.
     *
     * @param  string  $extension
     * @return string
     */
    private function getFileIcon($extension)
    {
        $icons = [
            'pdf' => 'file-pdf',
            'doc' => 'file-word',
            'docx' => 'file-word',
            'xls' => 'file-excel',
            'xlsx' => 'file-excel',
            'ppt' => 'file-powerpoint',
            'pptx' => 'file-powerpoint',
            'zip' => 'file-archive',
            'jpg' => 'file-image',
            'jpeg' => 'file-image',
            'png' => 'file-image',
        ];

        return $icons[$extension] ?? 'file';
    }

    /**
     * Get appropriate color class based on file extension.
     *
     * @param  string  $extension
     * @return string
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

    /**
     * Get appropriate color class based on file extension.
     *
     * @param  string  $extension
     * @return string
     */
    private function getFileColor($extension)
    {
        $colors = [
            'pdf' => 'bg-red-600',
            'doc' => 'bg-blue-600',
            'docx' => 'bg-blue-600',
            'xls' => 'bg-green-600',
            'xlsx' => 'bg-green-600',
            'ppt' => 'bg-orange-600',
            'pptx' => 'bg-orange-600',
            'zip' => 'bg-purple-600',
            'jpg' => 'bg-indigo-600',
            'jpeg' => 'bg-indigo-600',
            'png' => 'bg-indigo-600',
        ];

        return $colors[$extension] ?? 'bg-gray-600';
    }
}
