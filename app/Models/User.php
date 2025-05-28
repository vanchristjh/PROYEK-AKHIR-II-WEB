<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Classroom;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role_id',
        'nisn',
        'nip',
        'phone',
        'address',
        'gender',
        'birth_date',
        'birth_place',
        'avatar',
        'status',
        'classroom_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        // If you have a direct relationship with a single role
        return $this->belongsTo(Role::class);
        
        // If you have a many-to-many relationship with roles
        // return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Get the roles associated with the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Note: If this should be a many-to-many, add this method and use it instead:
    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'role_user');
    // }

    /**
     * Get the classroom that this user belongs to.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get the classrooms that this student belongs to (many-to-many).
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student', 'user_id', 'classroom_id');
    }

    /**
     * Get the subjects taught by this teacher.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
                    ->wherePivot('teacher_id', '=', $this->id) // Ensure correct teacher
                    ->select('subjects.*'); // Explicitly select from subjects table
    }

    /**
     * Get the subjects that the teacher teaches
     * Alias for subjects() for backward compatibility
     */
    public function teacherSubjects()
    {
        return $this->subjects();
    }

    /**
     * Get the classrooms that the user teaches.
     */
    public function teachingClassrooms()
    {
        // Looking at your existing relationships, it seems that a teacher
        // might be related to classrooms through schedules instead
        return $this->hasManyThrough(
            Classroom::class,
            Schedule::class,
            'teacher_id', // Foreign key on the schedules table
            'id', // Foreign key on the classrooms table
            'id', // Local key on the users table
            'classroom_id' // Local key on the schedules table
        );
    }

    /**
     * Get the classrooms that this teacher is homeroom teacher of.
     */
    public function homeroomClasses()
    {
        return $this->hasMany(Classroom::class, 'homeroom_teacher_id');
    }

    /**
     * Get the schedules where this user is the teacher.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    /**
     * Get the announcements created by this user
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    /**
     * Get the assignments created by the user (teacher).
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id');
    }

    /**
     * Get the student submissions
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the student grades
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /**
     * Get the user's active assignments
     */
    public function activeAssignments()
    {
        if ($this->role_id === 2) { // Teacher
            return $this->assignments()->where('is_active', true);
        }
        return $this->classroom->assignments()->where('is_active', true);
    }

    /**
     * Check if the user has a specific role
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->role->slug === $role;
        }
        return $this->role_id === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher()
    {
        return $this->hasRole('guru');
    }

    /**
     * Check if user is student
     */
    public function isStudent()
    {
        return $this->hasRole('siswa');
    }

    /**
     * Get the user's ID number based on their role.
     *
     * @return string|null
     */
    public function getIdNumberAttribute()
    {
        if ($this->role_id == 2) { // Teacher role
            return $this->nip;
        } elseif ($this->role_id == 3) { // Student role
            return $this->nisn;
        }
        return null; // For admin or other roles
    }

    /**
     * Set the user's ID number based on their role.
     *
     * @param string|null $value
     * @return void
     */
    public function setIdNumberAttribute($value)
    {
        if ($this->role_id == 2) { // Teacher role
            $this->attributes['nip'] = $value;
            $this->attributes['nisn'] = null;
        } elseif ($this->role_id == 3) { // Student role
            $this->attributes['nisn'] = $value;
            $this->attributes['nip'] = null;
        }
        // For admin or other roles, we don't set any ID number
    }

    /**
     * Get all classes (SchoolClass) that this student belongs to.
     */
    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id');
    }

    /**
     * Define a self-referencing relationship if needed
     * This allows a User to be related to other User instances
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
