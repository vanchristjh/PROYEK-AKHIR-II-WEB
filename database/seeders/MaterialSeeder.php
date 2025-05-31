<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Get teacher users
        $teachers = User::whereHas('role', function($query) {
            $query->where('slug', 'guru');
        })->get();

        // Get subjects
        $subjects = Subject::all();

        $materials = [
            [
                'title' => 'Pengenalan Aljabar',
                'description' => 'Aljabar adalah cabang matematika yang mempelajari struktur, hubungan, dan kuantitas.',
                'is_active' => true,
                'publish_date' => now(),
            ],
            [
                'title' => 'Hukum Newton',
                'description' => 'Hukum Newton terdiri dari 3 hukum dasar yang menjelaskan tentang gerak benda.',
                'is_active' => true,
                'publish_date' => now(),
            ],
            [
                'title' => 'Sistem Pencernaan',
                'description' => 'Sistem pencernaan adalah sistem organ yang bertanggung jawab untuk mencerna makanan.',
                'is_active' => true,
                'publish_date' => now(),
            ],
            [
                'title' => 'Struktur Atom',
                'description' => 'Atom adalah unit terkecil dari materi yang memiliki sifat kimia.',
                'is_active' => true,
                'publish_date' => now(),
            ],
            [
                'title' => 'Teks Naratif',
                'description' => 'Teks naratif adalah jenis teks yang menceritakan sebuah kejadian secara berurutan.',
                'is_active' => true,
                'publish_date' => now(),
            ]
        ];

        foreach ($materials as $material) {
            $subject = $subjects->random();
            $teacher = $teachers->random();
            
            Material::create(array_merge($material, [
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id
            ]));
        }
    }
}
