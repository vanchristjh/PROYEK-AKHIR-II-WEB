<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'started_at',
        'completed_at',
        'score',
        'max_score',
        'score_percentage',
        'passed',
        'attempt_number',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean',
    ];

    /**
     * The exam this attempt belongs to.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * The student who made this attempt.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * The student answers for this attempt.
     */
    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Check if this attempt is completed.
     */
    public function getIsCompletedAttribute()
    {
        return !is_null($this->completed_at);
    }

    /**
     * Calculate the time taken for this attempt.
     */
    public function getTimeTakenAttribute()
    {
        if ($this->is_completed) {
            return $this->completed_at->diffInMinutes($this->started_at);
        }
        
        return now()->diffInMinutes($this->started_at);
    }
}
