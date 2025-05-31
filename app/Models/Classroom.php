<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'grade_level',
        'academic_year',
        'homeroom_teacher_id',
        'capacity',
        'room_number',
    ];

    /**
     * Get the homeroom teacher for the classroom.
     */
    public function homeroomTeacher()
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Get the students in this classroom.
     */
    public function students()
    {
        return $this->hasMany(User::class, 'classroom_id')->where('role_id', 3); // Assuming 3 is student role ID
    }    /**
     * Get all assignments for this classroom.
     */
    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'assignment_classroom', 'classroom_id', 'assignment_id');
    }

    /**
     * Get the active assignments for this classroom.
     */
    public function activeAssignments()
    {
        return $this->assignments()->where('is_active', true);
    }

    /**
     * Get the schedules for this classroom.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get the subjects taught in this classroom.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'classroom_subject');
    }

    /**
     * Get the teaching materials for this classroom.
     */
    public function materials()
    {
        return $this->belongsToMany(Material::class, 'classroom_material')
            ->where('is_active', true)
            ->withTimestamps();
    }

    /**
     * Get the attendance records for this classroom.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
