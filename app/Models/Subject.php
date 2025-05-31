<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description'
    ];    
    
    /**
     * The teachers that teach this subject.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher', 'subject_id', 'teacher_id')
                    ->where('role_id', 2) // Ensure only teacher role
                    ->withTimestamps();
    }

    /**
     * The classrooms where this subject is taught.
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_subject');
    }

    /**
     * The school classes where this subject is taught.
     */
    public function schoolClasses()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')->withTimestamps();
    }

    /**
     * The assignments for this subject.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * The teaching materials for this subject.
     */
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    /**
     * The schedules for this subject.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * The attendance records for this subject.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get active assignments for this subject
     */
    public function activeAssignments()
    {
        return $this->assignments()->where('is_active', true);
    }
}
