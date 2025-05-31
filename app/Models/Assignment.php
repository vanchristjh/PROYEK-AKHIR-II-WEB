<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use App\Models\SchoolClass;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use App\Models\Grade;
use App\Models\Submission;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;
      protected $fillable = [
        'title',
        'description',
        'subject_id',
        'teacher_id',
        'deadline',
        'file',
        'is_active',
        'max_score',
        'allow_late_submission',
        'late_submission_penalty'
    ];
      protected $casts = [
        'deadline' => 'datetime',
        'is_active' => 'boolean',
        'allow_late_submission' => 'boolean',
        'late_submission_penalty' => 'integer'
    ];
    
    protected $appends = [
        'status',
        'formatted_deadline',
        'submission_status',
        'file_url',
        'has_file'
    ];

    /**
     * Get the subject this assignment belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher who created this assignment
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }    /**
     * Get the classrooms this assignment is assigned to
     */    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'assignment_classroom', 'assignment_id', 'classroom_id')
            ->withTimestamps();
    }

    /**
     * Get the classes (grade levels) this assignment is assigned to
     */
    public function schoolClasses()
    {
        return $this->belongsToMany(SchoolClass::class, 'assignment_class', 'assignment_id', 'class_id')
            ->withTimestamps();
    }

    /**
     * Get the submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the grades for this assignment
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Check if a student has submitted this assignment
     */
    public function isSubmittedBy($studentId)
    {
        return $this->submissions()
            ->where('user_id', $studentId)
            ->exists();
    }

    /**
     * Get submission status: 'submitted', 'late', 'not_submitted'
     */
    public function getSubmissionStatusAttribute()
    {
        if (!auth()->check() || !auth()->user()->isStudent()) {
            return null;
        }

        $submission = $this->submissions()
            ->where('user_id', auth()->id())
            ->first();

        if (!$submission) {
            return 'not_submitted';
        }

        return $submission->created_at > $this->deadline ? 'late' : 'submitted';
    }

    /**
     * Get formatted deadline
     */
    public function getFormattedDeadlineAttribute()
    {
        return $this->deadline ? $this->deadline->format('d M Y H:i') : null;
    }

    /**
     * Check if assignment has a file attachment
     */
    public function getHasFileAttribute()
    {
        return !empty($this->file);
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute()
    {
        return $this->has_file ? asset('storage/' . $this->file) : null;
    }
      /**
     * Check if the assignment deadline has passed.
     */
    public function isExpired()
    {
        $deadlineDate = $this->deadline ?? $this->due_date;
        if (!$deadlineDate) {
            return false; // If no deadline set, consider it not expired
        }
        
        return now()->gt($deadlineDate);
        return $deadlineDate && now()->gt($deadlineDate);
    }
    
    /**
     * Get remaining time until deadline as a human-readable string.
     */
    public function timeRemaining()
    {
        $deadlineDate = $this->deadline ?? $this->due_date;
        
        if (!$deadlineDate) {
            return 'No deadline';
        }
        
        if (now()->gt($deadlineDate)) {
            return 'Deadline passed';
        }
        
        return now()->diffForHumans($deadlineDate, true) . ' remaining';
    }
    
    /**
     * Get the status of the assignment.
     */
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if ($this->isExpired()) {
            return 'expired';
        }
        
        return 'active';
    }
    
    /**
     * Scope a query to only include active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include assignments for a specific class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->whereHas('classes', function($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }
    
    /**
     * Scope a query to only include assignments for a specific subject.
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }
    
    /**
     * Scope a query to only include assignments for a specific teacher.
     */
    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }
    
    /**
     * Get the classroom that this assignment is associated with.
     */
    public function classes()
    {
        // Return an empty collection since the pivot table doesn't exist
        // This ensures the view won't encounter null when trying to iterate
        return $this->hasOne(Classroom::class, 'id', 'class_id')
            ->withDefault(function () {
                return collect();
            });
    }
}
