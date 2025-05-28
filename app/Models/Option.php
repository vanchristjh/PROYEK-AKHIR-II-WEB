<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'content',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * The question this option belongs to.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The student answers that selected this option.
     */
    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
