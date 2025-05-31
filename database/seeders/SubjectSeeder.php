<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'code' => 'MTK',
                'name' => 'Matematika',
                'description' => 'Pelajaran Matematika',
            ],
            [
                'code' => 'FIS',
                'name' => 'Fisika',
                'description' => 'Pelajaran Fisika',
            ],
            [
                'code' => 'BIO',
                'name' => 'Biologi',
                'description' => 'Pelajaran Biologi',
            ],
            [
                'code' => 'KIM',
                'name' => 'Kimia',
                'description' => 'Pelajaran Kimia',
            ],
            [
                'code' => 'BIN',
                'name' => 'Bahasa Indonesia',
                'description' => 'Pelajaran Bahasa Indonesia',
            ],
            [
                'code' => 'BIG',
                'name' => 'Bahasa Inggris',
                'description' => 'Pelajaran Bahasa Inggris',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
    }
}