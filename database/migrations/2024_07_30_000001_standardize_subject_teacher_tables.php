<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First check if the old tables exist
        $hasSubjectUser = Schema::hasTable('subject_user');
        $hasTeacherSubjects = Schema::hasTable('teacher_subjects');
        
        // Then make sure we have subject_teacher table
        if (!Schema::hasTable('subject_teacher')) {
            Schema::create('subject_teacher', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
                $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                
                $table->unique(['subject_id', 'teacher_id']);
            });
        }

        // If other tables exist, migrate their data 
        if ($hasSubjectUser) {
            // Migrate subject_user data
            $relations = DB::table('subject_user')->get();
            foreach ($relations as $relation) {
                DB::table('subject_teacher')->insertOrIgnore([
                    'subject_id' => $relation->subject_id,
                    'teacher_id' => $relation->user_id,
                    'created_at' => $relation->created_at,
                    'updated_at' => $relation->updated_at
                ]);
            }
            Schema::dropIfExists('subject_user');
        }

        if ($hasTeacherSubjects) {
            // Migrate teacher_subjects data
            $relations = DB::table('teacher_subjects')->get();
            foreach ($relations as $relation) {
                DB::table('subject_teacher')->insertOrIgnore([
                    'subject_id' => $relation->subject_id,
                    'teacher_id' => $relation->teacher_id,
                    'created_at' => $relation->created_at,
                    'updated_at' => $relation->updated_at
                ]);
            }
            Schema::dropIfExists('teacher_subjects');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No going back - this is a one-way migration
    }
};
