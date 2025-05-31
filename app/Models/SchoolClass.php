<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Assignment;

class SchoolClass extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'grade',
        'year',
        'classroom_id',
    ];
    
    /**
     * Get the classroom associated with this class.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }
    
    /**
     * Get the students that belong to the class.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }    /**
     * Get the subjects for this class.
     */    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')->withTimestamps();
    }

    /**
     * Get the assignments associated with this class.
     */
    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'assignment_class', 'class_id', 'assignment_id')
            ->withTimestamps();
    }
}
