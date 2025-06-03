<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */    public function run(): void
    {
        $this->call([
            // Comment out the existing seeders if you want to use only the dummy data seeder
            // RoleSeeder::class,            // Create roles (admin, teacher, student)
            // UserSeeder::class,            // Create users
            // RoleUserSeeder::class,        // Assign roles to users
            // SchoolClassSeeder::class,     // Create school classes
            // SubjectSeeder::class,         // Create subjects
            // SubjectTeacherSeeder::class,  // Assign subjects to teachers
            // ClassroomSeeder::class,       // Create classrooms
            // AssignmentSeeder::class,      // Create assignments
            // MaterialSeeder::class,        // Create learning materials
            // QuizSeeder::class,           // Create quizzes
            // ExamSeeder::class,           // Create exams
            // AttendanceSeeder::class,     // Create attendance records
            // ScheduleSeeder::class,       // Create class schedules
            // GradeSeeder::class,         // Create student grades
            
            // Use simple data seeder for basic entities
            SimpleDataSeeder::class,     // Create basic dummy data with minimal dependencies
            
            // Additional data seeder for more complex entities and relationships
            AdditionalDummySeeder::class, // Create additional data like assignments, materials, etc.
        ]);
    }
}
