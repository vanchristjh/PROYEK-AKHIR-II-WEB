<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;    protected $fillable = [
        'content',
        'question_type',
        'points',
        'correct_answer',
        'explanation',
        'difficulty_level',
        'quiz_id',
        'exam_id',
    ];

    /**
     * The quiz this question belongs to (if any).
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * The exam this question belongs to (if any).
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * The options for this question.
     */
    public function options()
    {
        return $this->hasMany(Option::class);
    }

    /**
     * The student answers for this question.
     */
    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Check if this is a multiple-choice question.
     */
    public function getIsMultipleChoiceAttribute()
    {        return $this->question_type === 'multiple_choice';
    }

    /**
     * Check if this is a true/false question.
     */
    public function getIsTrueFalseAttribute()
    {
        return $this->question_type === 'true_false';
    }

    /**
     * Check if this is an essay question.
     */
    public function getIsEssayAttribute()
    {
        return $this->question_type === 'essay';
    }

    /**
     * Check if this is a short answer question.
     */
    public function getIsShortAnswerAttribute()
    {
        return $this->question_type === 'short_answer';
    }
}
