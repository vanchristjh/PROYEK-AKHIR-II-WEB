<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map teacher names to their subjects
        $teacherSubjects = [
            'Ahmad Matematika' => 'Matematika',
            'Budi Fisika' => 'Fisika',
            'Citra Biologi' => 'Biologi',
            'Dewi Kimia' => 'Kimia',
            'Eko B.Indonesia' => 'Bahasa Indonesia',
            'Farah B.Inggris' => 'Bahasa Inggris',
        ];

        foreach ($teacherSubjects as $teacherName => $subjectName) {
            $teacher = User::where('name', $teacherName)->where('role_id', 2)->first();
            $subject = Subject::where('name', $subjectName)->first();

            if ($teacher && $subject) {
                // Ensure unique combinations in subject_teacher table
                DB::table('subject_teacher')->updateOrInsert(
                    [
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
