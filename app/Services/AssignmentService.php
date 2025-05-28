<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Storage;

class AssignmentService
{
    /**
     * Get statistics for a specific assignment
     *
     * @param Assignment $assignment
     * @return array
     */
    public function getAssignmentStatistics(Assignment $assignment)
    {
        // Get total number of students in the assigned classes
        $totalStudents = 0;
        foreach ($assignment->classes as $class) {
            $totalStudents += User::where('class_id', $class->id)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'siswa');
                })->count();
        }

        // Get submission statistics
        $submissions = $assignment->submissions;
        $total = $submissions->count();
        $graded = $submissions->filter(function ($submission) {
            return !is_null($submission->score);
        })->count();
        $pending = $total - $graded;

        // Calculate average score
        $averageScore = null;
        if ($graded > 0) {
            $averageScore = round($submissions->whereNotNull('score')->avg('score'), 1);
        }

        return [
            'total_students' => $totalStudents,
            'submitted' => $total,
            'graded' => $graded,
            'pending' => $pending,
            'not_submitted' => $totalStudents - $total,
            'submission_percentage' => $totalStudents > 0 ? round(($total / $totalStudents) * 100) : 0,
            'grading_percentage' => $total > 0 ? round(($graded / $total) * 100) : 0,
            'average_score' => $averageScore,
        ];
    }

    /**
     * Handle file upload and get file metadata
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path Directory path to store the file
     * @return array
     */
    public function handleFileUpload($file, $path)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs($path, $fileName, 'public');
        $fileSize = $this->formatBytes($file->getSize());
        $fileExtension = $file->getClientOriginalExtension();
        $fileIcon = $this->getFileIcon($fileExtension);
        $fileColor = $this->getFileColor($fileExtension);

        return [
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_icon' => $fileIcon,
            'file_color' => $fileColor,
        ];
    }

    /**
     * Format file size in human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get appropriate icon based on file extension
     *
     * @param string $extension
     * @return string
     */
    public function getFileIcon($extension)
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
            'rar' => 'file-archive',
            'jpg' => 'file-image',
            'jpeg' => 'file-image',
            'png' => 'file-image',
            'gif' => 'file-image',
            'txt' => 'file-alt',
        ];

        return $icons[strtolower($extension)] ?? 'file';
    }

    /**
     * Get appropriate color based on file extension
     *
     * @param string $extension
     * @return string
     */
    public function getFileColor($extension)
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
            'rar' => 'bg-purple-600',
            'jpg' => 'bg-indigo-600',
            'jpeg' => 'bg-indigo-600',
            'png' => 'bg-indigo-600',
            'gif' => 'bg-pink-600',
            'txt' => 'bg-gray-600',
        ];

        return $colors[strtolower($extension)] ?? 'bg-gray-600';
    }
}
