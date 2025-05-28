<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'subject_id',
        'teacher_id',
        'day',
        'start_time',
        'end_time',
        'room',
        'created_by',
        'school_year',
        'notes',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($schedule) {
            // Set default values for school year if not provided
            if (empty($schedule->school_year)) {
                $currentYear = date('Y');
                $nextYear = $currentYear + 1;
                $schedule->school_year = "$currentYear/$nextYear";
            }
            
            // Record creator if not already set
            if (empty($schedule->created_by) && auth()->check()) {
                $schedule->created_by = auth()->id();
            }
        });
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }    /**
     * Relationship with teacher - handles both Teacher model and User model
     * to support both implementation strategies
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        // This optimized relationship will first check if the ID exists in the Teacher model,
        // and if not found will assume it's a User model ID. This ensures schedules created
        // by administrators with User IDs as teacher_id are still properly related.
        return $this->belongsTo(Teacher::class)->withDefault(function($teacher, $schedule) {
            // If no Teacher record found, try to get User information
            $user = User::find($schedule->teacher_id);
            if ($user) {
                $teacher->name = $user->name;
                $teacher->id = $user->id;
                $teacher->fromUser = true;
            }
        });
    }    /**
     * Get teacher name regardless of where it's stored
     * 
     * @return string|null
     */
    public function getTeacherNameAttribute()
    {
        // First try to get from teacher relation with withDefault handling
        if ($this->teacher && isset($this->teacher->name)) {
            return $this->teacher->name;
        }
        
        // If relation didn't work, do a direct check
        if ($this->teacher_id) {
            // First check User model (more likely for admin-created schedules)
            $user = User::find($this->teacher_id);
            if ($user) {
                return $user->name;
            }
            
            // Then check Teacher model
            $teacher = Teacher::find($this->teacher_id);
            if ($teacher && $teacher->name) {
                return $teacher->name;
            }
            
            return 'ID: ' . $this->teacher_id;
        }
        
        return 'Tidak Ada';
    }

    // Handle day as both string and number
    public function getDayNameAttribute()
    {
        if (is_numeric($this->day)) {
            $days = [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Minggu'
            ];
            
            return $days[$this->day] ?? $this->day;
        }
        
        return $this->day;
    }
    
    /**
     * Get formatted time for display
     * 
     * @return string
     */
    public function getFormattedTimeAttribute()
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }
    
    /**
     * Check if this schedule is currently ongoing
     * 
     * @return bool
     */
    public function getIsOngoingAttribute()
    {
        $currentDay = strtolower(date('l'));
        $dayMapping = [
            'monday' => '1',
            'tuesday' => '2',
            'wednesday' => '3',
            'thursday' => '4',
            'friday' => '5',
            'saturday' => '6',
            'sunday' => '7',
            'senin' => '1',
            'selasa' => '2',
            'rabu' => '3',
            'kamis' => '4',
            'jumat' => '5',
            'sabtu' => '6',
            'minggu' => '7',
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];
        
        $scheduleDay = is_numeric($this->day) ? $this->day : array_search($this->day, $dayMapping);
        $currentDayNumber = array_key_exists($currentDay, $dayMapping) ? $dayMapping[$currentDay] : null;
        
        if ($scheduleDay != $currentDayNumber) {
            return false;
        }
        
        $currentTime = date('H:i:s');
        return ($currentTime >= $this->start_time && $currentTime <= $this->end_time);
    }
    
    /**
     * Check if this schedule is upcoming today
     * 
     * @return bool
     */
    public function getIsUpcomingAttribute()
    {
        $currentDay = strtolower(date('l'));
        $dayMapping = [
            'monday' => '1',
            'tuesday' => '2',
            'wednesday' => '3',
            'thursday' => '4',
            'friday' => '5',
            'saturday' => '6',
            'sunday' => '7',
            'senin' => '1',
            'selasa' => '2',
            'rabu' => '3',
            'kamis' => '4',
            'jumat' => '5',
            'sabtu' => '6',
            'minggu' => '7',
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];
        
        $scheduleDay = is_numeric($this->day) ? $this->day : array_search($this->day, $dayMapping);
        $currentDayNumber = array_key_exists($currentDay, $dayMapping) ? $dayMapping[$currentDay] : null;
        
        if ($scheduleDay != $currentDayNumber) {
            return false;
        }
        
        $currentTime = date('H:i:s');
        return ($currentTime < $this->start_time);
    }
    
    /**
     * Get creator user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
