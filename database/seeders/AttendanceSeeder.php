<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get classrooms
        $classrooms = Classroom::all();
        
        // Get subjects with their teachers
        $subjects = Subject::with('teachers')->get();
        
        // Create attendance records for the past 30 days
        $startDate = now()->subDays(30);
        
        foreach ($classrooms as $classroom) {
            // For each available subject in this classroom
            foreach ($subjects as $subject) {
                // Skip if subject has no teachers
                if ($subject->teachers->isEmpty()) {
                    continue;
                }
                
                // Create 5 random attendance dates for this classroom-subject combination
                $dates = [];
                for ($i = 0; $i < 5; $i++) {
                    // Generate a random weekday (Mon-Fri) between start date and now
                    do {
                        $date = Carbon::createFromTimestamp(rand($startDate->timestamp, now()->timestamp))
                            ->setTime(0, 0, 0);
                    } while ($date->isWeekend() || in_array($date->format('Y-m-d'), $dates));
                    
                    $dates[] = $date->format('Y-m-d');
                    
                    // Get a random teacher that teaches this subject
                    $teacher = $subject->teachers->random();
                    
                    // Create attendance record
                    $attendance = Attendance::create([
                        'classroom_id' => $classroom->id,
                        'subject_id' => $subject->id,
                        'recorded_by' => $teacher->id,
                        'date' => $date
                    ]);
                    
                    // Get students from the classroom
                    $students = User::whereHas('role', function($query) {
                        $query->where('slug', 'siswa');
                    })
                    ->where('classroom_id', $classroom->id)
                    ->get();
                    
                    // Create attendance records for each student
                    foreach ($students as $student) {
                        // Randomly assign attendance status with weighted probabilities
                        $status = $this->getRandomStatus();
                        
                        AttendanceRecord::create([
                            'attendance_id' => $attendance->id,
                            'student_id' => $student->id,
                            'status' => $status,
                            'notes' => $status == 'present' ? null : 'Keterangan untuk ' . $status
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Get a random attendance status with weighted probabilities
     */
    private function getRandomStatus(): string 
    {
        $rand = rand(1, 100);
        
        // 70% chance of present
        if ($rand <= 70) return 'present';
        // 10% chance each of sick, permitted or late
        if ($rand <= 80) return 'sick';
        if ($rand <= 90) return 'permitted';
        if ($rand <= 95) return 'late';
        // 5% chance of absent
        return 'absent';
    }
}
