<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = ['X', 'XI', 'XII'];
        $classes = ['A', 'B', 'C'];
        $teachers = User::where('role_id', 2)->get(); // Get all teachers
        $currentYear = date('Y');
        $academicYear = $currentYear . '/' . ($currentYear + 1);

        $teacherIndex = 0;
        foreach ($grades as $grade) {
            foreach ($classes as $class) {
                // Create classroom
                $classroom = Classroom::firstOrCreate(
                    ['name' => "$grade-$class"],
                    [
                        'grade_level' => $grade,
                        'academic_year' => $academicYear,
                        'homeroom_teacher_id' => $teachers[$teacherIndex % count($teachers)]->id,
                        'capacity' => 35,
                        'room_number' => "$grade$class",
                    ]
                );

                // Assign students to classroom
                User::where('role_id', 3) // Students
                    ->where('name', 'like', "Siswa $grade-$class%")
                    ->update(['classroom_id' => $classroom->id]);

                $teacherIndex++;
            }
        }
    }
}
