<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['name' => 'X IPA 1', 'grade' => 'X', 'year' => '2023-2024'],
            ['name' => 'X IPA 2', 'grade' => 'X', 'year' => '2023-2024'],
            ['name' => 'X IPS 1', 'grade' => 'X', 'year' => '2023-2024'],
            ['name' => 'XI IPA 1', 'grade' => 'XI', 'year' => '2023-2024'],
            ['name' => 'XI IPS 1', 'grade' => 'XI', 'year' => '2023-2024'],
            ['name' => 'XII IPA 1', 'grade' => 'XII', 'year' => '2023-2024'],
            ['name' => 'XII IPS 1', 'grade' => 'XII', 'year' => '2023-2024'],
        ];

        foreach ($classes as $class) {
            SchoolClass::create($class);
        }
    }
}
