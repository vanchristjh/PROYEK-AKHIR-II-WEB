<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'teacher_id',
        'start_time',
        'end_time',
        'duration',
        'is_active',
        'max_attempts',
        'randomize_questions',
        'show_result',
        'passing_score',
        'exam_type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'randomize_questions' => 'boolean',
        'show_result' => 'boolean',
    ];

    /**
     * Get the subject this exam belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher who created this exam.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the classrooms this exam is assigned to.
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'exam_classroom')
            ->withTimestamps();
    }

    /**
     * Get the questions for this exam.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the student attempts for this exam.
     */
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
    
    /**
     * Check if the exam is active and within the valid time period.
     */
    public function getIsAvailableAttribute()
    {
        return $this->is_active && 
            now()->greaterThanOrEqualTo($this->start_time) && 
            now()->lessThanOrEqualTo($this->end_time);
    }
    
    /**
     * Get the total points available in this exam.
     */
    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }
    
    /**
     * Get the total number of questions in this exam.
     */
    public function getQuestionCountAttribute()
    {
        return $this->questions->count();
    }
}
