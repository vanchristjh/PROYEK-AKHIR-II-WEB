<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'file_name',
        'file_size',
        'notes',
        'score',
        'feedback',
        'submitted_at',
        'graded_at',
        'graded_by',
        'status',
        'late_penalty_applied',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'late_penalty_applied' => 'boolean',
    ];
    
    protected $appends = [
        'is_late',
        'formatted_submitted_at',
        'status_label',
        'file_icon',
        'file_color',
        'human_file_size',
        'file_url',
    ];

    /**
     * Get the assignment associated with the submission.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the user (student) who made the submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Alias for user relation to maintain compatibility.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user (teacher) who graded the submission.
     */
    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
      /**
     * Check if the submission was late.
     */
    public function getIsLateAttribute()
    {
        $deadline = $this->assignment->deadline ?? $this->assignment->due_date;
        return $deadline && $this->created_at->gt($deadline);
    }
    
    /**
     * Check if the submission was late.
     *
     * @return bool
     */
    public function isLate(): bool
    {
        return $this->getIsLateAttribute();
    }
    
    /**
     * Get the appropriate file icon based on extension.
     */
    public function getFileIconAttribute()
    {
        if (!$this->file_path) {
            return 'file';
        }
        
        $extension = Str::afterLast($this->file_path, '.');
        
        switch ($extension) {
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
            case 'zip':
            case 'rar':
                return 'file-archive';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'file-image';
            default:
                return 'file';
        }
    }
    
    /**
     * Get the appropriate file color based on extension.
     */
    public function getFileColorAttribute()
    {
        if (!$this->file_path) {
            return 'bg-gray-500';
        }
        
        $extension = Str::afterLast($this->file_path, '.');
        
        switch ($extension) {
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
            case 'zip':
            case 'rar':
                return 'bg-yellow-500';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'bg-purple-500';
            default:
                return 'bg-gray-500';
        }
    }
    
    /**
     * Get a human-readable file size.
     */
    public function getHumanFileSizeAttribute()
    {
        if (empty($this->file_size)) {
            return 'Unknown size';
        }
        
        // Convert to numeric value and ensure it's not negative
        $bytes = is_numeric($this->file_size) ? max(0, floatval($this->file_size)) : 0;
        
        if ($bytes === 0) {
            return '0 B';
        }
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' B';
    }
    
    /**
     * Get the formatted submitted time.
     */
    public function getFormattedSubmittedAtAttribute()
    {
        return $this->submitted_at 
            ? $this->submitted_at->format('d M Y H:i') 
            : $this->created_at->format('d M Y H:i');
    }
    
    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        if ($this->score !== null) {
            return 'Graded';
        }
        
        if ($this->getIsLateAttribute()) {
            return 'Late';
        }
        
        return 'Submitted';
    }
    
    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) return null;
        
        return url('storage/' . $this->file_path);
    }
      /**
     * Check if this submission has been graded.
     *
     * @return bool
     */
    public function isGraded(): bool
    {
        return $this->score !== null;
    }
    
    /**
     * Scope a query to only include submissions that have been graded.
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('score');
    }
    
    /**
     * Scope a query to only include submissions that haven't been graded.
     */
    public function scopeUngraded($query)
    {
        return $query->whereNull('score');
    }
    
    /**
     * Scope a query to only include late submissions.
     */
    public function scopeLate($query)
    {
        return $query->whereHas('assignment', function($q) {
            $q->whereRaw('submissions.created_at > assignments.deadline');
        });
    }
    
    /**
     * Scope a query to only include submissions for a specific assignment.
     */
    public function scopeForAssignment($query, $assignmentId)
    {
        return $query->where('assignment_id', $assignmentId);
    }
    
    /**
     * Scope a query to only include submissions by a specific student.
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('user_id', $studentId);
    }
}
