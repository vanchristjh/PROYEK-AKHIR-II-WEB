<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Assignment;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        // Get students with their classrooms
        $students = User::whereHas('role', function($query) {
            $query->where('slug', 'siswa');
        })->with('classroom')->get();

        // Get subjects with their teachers
        $subjects = Subject::with(['teachers' => function($query) {
            $query->where('role_id', 2); // Ensure only teacher role
        }])->get();

        // Current academic year and semester
        $academic_years = ['2023/2024'];
        $semesters = ['Ganjil', 'Genap'];

        foreach ($students as $student) {
            // Skip if student has no classroom assigned
            if (!$student->classroom_id) {
                $this->command->warn("Student {$student->name} (ID: {$student->id}) has no classroom assigned. Skipping...");
                continue;
            }

            foreach ($subjects as $subject) {
                // Skip if subject has no teachers
                if ($subject->teachers->isEmpty()) {
                    $this->command->warn("Subject {$subject->name} has no teachers assigned. Skipping...");
                    continue;
                }

                foreach ($academic_years as $year) {
                    foreach ($semesters as $semester) {
                        // Get a random teacher that teaches this subject
                        $teacher = $subject->teachers->random();
                        if (!$teacher) {
                            $this->command->warn("Could not find a teacher for subject {$subject->name}. Skipping...");
                            continue;
                        }

                        try {
                            // Generate random scores between 60 and 100
                            $assignment = rand(60, 100);
                            $midterm = rand(60, 100);
                            $final = rand(60, 100);
                            
                            // Calculate total score
                            $total = round(($assignment + $midterm + $final) / 3, 2);
                            
                            // Set max score
                            $max_score = 100;
                            
                            // Determine grade based on total score
                            $grade = match(true) {
                                $total >= 90 => 'A',
                                $total >= 80 => 'B',
                                $total >= 70 => 'C',
                                $total >= 60 => 'D',
                                default => 'E'
                            };
                            
                            // Create grade with error handling
                            Grade::create([
                                'student_id' => $student->id,
                                'teacher_id' => $teacher->id,
                                'subject_id' => $subject->id,
                                'classroom_id' => $student->classroom_id,
                                'semester' => $semester,
                                'academic_year' => $year,
                                'assignment_score' => $assignment,
                                'midterm_score' => $midterm,
                                'final_score' => $final,
                                'total_score' => $total,
                                'max_score' => $max_score,
                                'grade' => $grade,
                                'comments' => "Grade for {$subject->name} - {$semester} {$year}"
                            ]);
                        } catch (\Exception $e) {
                            // Log any errors but continue seeding
                            $this->command->warn("Error creating grade for student {$student->id}, subject {$subject->id}: {$e->getMessage()}");
                            continue;
                        }
                    }
                }
            }
        }

        $assignments = Assignment::with(['teacher', 'subject', 'classrooms'])->get();
        $currentYear = date('Y');
        $semester = (date('n') > 6) ? 1 : 2; // 1st semester starts in July

        foreach ($assignments as $assignment) {
            // Get students from the assignment's classrooms
            $students = User::whereIn('classroom_id', $assignment->classrooms->pluck('id'))
                ->where('role_id', 3) // Students only
                ->get();

            foreach ($students as $student) {
                // Generate a random score between 60 and 100
                $score = rand(60, 100);

                Grade::create([
                    'student_id' => $student->id,
                    'teacher_id' => $assignment->teacher_id,
                    'subject_id' => $assignment->subject_id,
                    'classroom_id' => $student->classroom_id,
                    'assignment_id' => $assignment->id,
                    'score' => $score,
                    'max_score' => 100,
                    'type' => 'assignment',
                    'feedback' => $this->generateFeedback($score),
                    'semester' => $semester,
                    'academic_year' => $currentYear . '/' . ($currentYear + 1),
                ]);
            }
        }
    }

    /**
     * Generate feedback based on score
     */
    private function generateFeedback($score): string
    {
        if ($score >= 90) {
            return "Sangat baik! Pertahankan prestasi Anda.";
        } elseif ($score >= 80) {
            return "Baik. Terus tingkatkan untuk hasil yang lebih baik.";
        } elseif ($score >= 70) {
            return "Cukup baik. Masih ada ruang untuk peningkatan.";
        } else {
            return "Perlu belajar lebih giat. Jangan ragu untuk bertanya jika ada kesulitan.";
        }
    }
}
