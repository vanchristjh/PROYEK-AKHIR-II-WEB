<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'quiz_attempt_id',
        'exam_attempt_id',
        'option_id',
        'text_answer',
        'score',
        'feedback',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * The question this answer belongs to.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The quiz attempt this answer belongs to (if any).
     */
    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    /**
     * The exam attempt this answer belongs to (if any).
     */
    public function examAttempt()
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    /**
     * The selected option for this answer (if any).
     */
    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    /**
     * Get the attempt (either quiz or exam) this answer belongs to.
     */
    public function getAttemptAttribute()
    {
        return $this->quiz_attempt_id 
            ? $this->quizAttempt 
            : $this->examAttempt;
    }
}
