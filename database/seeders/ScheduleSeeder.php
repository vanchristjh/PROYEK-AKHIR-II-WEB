<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classrooms = Classroom::all();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $currentYear = date('Y');
        $academicYear = $currentYear . '/' . ($currentYear + 1);

        foreach ($classrooms as $classroom) {
            // Get subjects and their teachers
            $subjects = Subject::whereHas('teachers')->with('teachers')->get();

            // Create schedule for each day
            foreach ($days as $dayIndex => $day) {
                // Create 4 periods per day
                for ($period = 0; $period < 4; $period++) {
                    // Calculate time slots (7:00 - 14:30)
                    $startHour = 7 + (2 * $period);
                    $endHour = $startHour + 1;
                    
                    // Get random subject and its teacher
                    $subject = $subjects->random();
                    $teacher = $subject->teachers->first();

                    if ($teacher) {
                        Schedule::create([
                            'classroom_id' => $classroom->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'day' => $day,
                            'start_time' => sprintf('%02d:00:00', $startHour),
                            'end_time' => sprintf('%02d:00:00', $endHour),
                            'room' => $classroom->room_number,
                            'school_year' => $academicYear,
                        ]);
                    }
                }
            }
        }
    }
}
