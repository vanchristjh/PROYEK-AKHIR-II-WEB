<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Subject;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();
        $grades = ['X', 'XI', 'XII'];

        foreach ($subjects as $subject) {
            // Get teacher for this subject
            $teacher = User::whereHas('subjects', function($query) use ($subject) {
                $query->where('subjects.id', $subject->id);
            })->first();

            if (!$teacher) continue;

            foreach ($grades as $grade) {
                // Get all classrooms for this grade
                $classrooms = Classroom::where('grade_level', $grade)->get();

                // Create 2 assignments per subject per grade
                for ($i = 1; $i <= 2; $i++) {
                    $assignment = Assignment::create([
                        'title' => "Tugas {$i} {$subject->name} Kelas {$grade}",
                        'description' => "Deskripsi tugas {$i} untuk mata pelajaran {$subject->name} kelas {$grade}",
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'deadline' => Carbon::now()->addDays(rand(5, 14)),
                        'is_active' => true,
                        'max_score' => 100,
                        'allow_late_submission' => true,
                        'late_submission_penalty' => 10,
                    ]);

                    // Attach classrooms to assignment
                    $assignment->classrooms()->attach($classrooms->pluck('id')->toArray());
                }
            }
        }
    }
}
