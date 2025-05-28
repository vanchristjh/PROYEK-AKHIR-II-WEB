<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
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
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
        'randomize_questions' => 'boolean',
        'show_result' => 'boolean',
    ];

    /**
     * Get the subject this quiz belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher who created this quiz.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the classrooms this quiz is assigned to.
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'quiz_classroom')
            ->withTimestamps();
    }

    /**
     * Get the questions for this quiz.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Get the student attempts for this quiz.
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    /**
     * Check if the quiz is active and within the valid time period.
     */
    public function getIsAvailableAttribute()
    {
        return $this->is_active && 
            now()->greaterThanOrEqualTo($this->start_time) && 
            now()->lessThanOrEqualTo($this->end_time);
    }
    
    /**
     * Get the total points available in this quiz.
     */
    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }
    
    /**
     * Get the total number of questions in this quiz.
     */
    public function getQuestionCountAttribute()
    {
        return $this->questions->count();
    }

    /**
     * Check if the quiz is ongoing (has started but not ended)
     */
    public function getIsOngoingAttribute()
    {
        return now()->between($this->start_time, $this->end_time);
    }

    /**
     * Check if the quiz has ended
     */
    public function getHasEndedAttribute()
    {
        return now()->greaterThan($this->end_time);
    }

    /**
     * Check if a student can attempt this quiz
     */
    public function canAttempt($studentId)
    {
        if (!$this->is_available) {
            return false;
        }
        
        $attemptsCount = $this->attempts()
            ->where('student_id', $studentId)
            ->count();
            
        return $attemptsCount < $this->max_attempts;
    }

    /**
     * Get the last attempt by a student
     */
    public function getLastAttempt($studentId)
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->latest()
            ->first();
    }

    /**
     * Calculate class average score
     */
    public function getClassAverageScore()
    {
        $attempts = $this->attempts()
            ->where('is_submitted', true)
            ->where('is_graded', true)
            ->get();
            
        if ($attempts->isEmpty()) {
            return 0;
        }
        
        return $attempts->avg('score');
    }

    /**
     * Get passing students count
     */
    public function getPassingStudentsCount()
    {
        return $this->attempts()
            ->where('is_submitted', true)
            ->where('is_graded', true)
            ->where('score', '>=', $this->passing_score)
            ->count();
    }

    /**
     * Get count of students who have not attempted the quiz yet
     */
    public function getNotAttemptedCount()
    {
        $classroomStudentCount = $this->classrooms()
            ->withCount('students')
            ->get()
            ->sum('students_count');
            
        $attemptedStudentCount = $this->attempts()
            ->distinct('student_id')
            ->count('student_id');
            
        return max(0, $classroomStudentCount - $attemptedStudentCount);
    }
}
