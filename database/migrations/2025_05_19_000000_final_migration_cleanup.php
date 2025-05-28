<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FinalMigrationCleanup extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        // Fix migrations that create the same table multiple times by keeping only one creation entry

        // 1. Fix users table migrations
        $usersMigrations = DB::table('migrations')
            ->where('migration', 'like', '%create_users_table')
            ->orWhere('migration', 'like', '2024_06_10_000005_add_foreign_keys')
            ->get();
        
        if ($usersMigrations->count() > 1) {
            $oldestMigration = $usersMigrations->sortBy('batch')->first();
            
            // Keep the oldest, remove others
            foreach ($usersMigrations as $migration) {
                if ($migration->id != $oldestMigration->id) {
                    DB::table('migrations')->where('id', $migration->id)->delete();
                    echo "Removed duplicate migration: {$migration->migration}\n";
                }
            }
        }

        // 2. Fix classroom_student table migrations
        $classroomStudentMigrations = DB::table('migrations')
            ->where('migration', 'like', '%create_classroom_student_table')
            ->get();
        
        if ($classroomStudentMigrations->count() > 1) {
            $oldestMigration = $classroomStudentMigrations->sortBy('batch')->first();
            
            foreach ($classroomStudentMigrations as $migration) {
                if ($migration->id != $oldestMigration->id) {
                    DB::table('migrations')->where('id', $migration->id)->delete();
                    echo "Removed duplicate migration: {$migration->migration}\n";
                }
            }
        }

        // 3. Fix subject_teacher table migrations
        $subjectTeacherMigrations = DB::table('migrations')
            ->where('migration', 'like', '%create_subject_teacher_table')
            ->get();
        
        if ($subjectTeacherMigrations->count() > 1) {
            $oldestMigration = $subjectTeacherMigrations->sortBy('batch')->first();
            
            foreach ($subjectTeacherMigrations as $migration) {
                if ($migration->id != $oldestMigration->id) {
                    DB::table('migrations')->where('id', $migration->id)->delete();
                    echo "Removed duplicate migration: {$migration->migration}\n";
                }
            }
        }

        // 4. Fix roles table migrations
        $rolesMigrations = DB::table('migrations')
            ->where('migration', 'like', '%create_roles_table')
            ->get();
        
        if ($rolesMigrations->count() > 1) {
            $oldestMigration = $rolesMigrations->sortBy('batch')->first();
            
            foreach ($rolesMigrations as $migration) {
                if ($migration->id != $oldestMigration->id) {
                    DB::table('migrations')->where('id', $migration->id)->delete();
                    echo "Removed duplicate migration: {$migration->migration}\n";
                }
            }
        }

        // 5. Fix classroom_material table migrations
        $classroomMaterialMigrations = DB::table('migrations')
            ->where('migration', 'like', '%create_classroom_material_table')
            ->get();
        
        if ($classroomMaterialMigrations->count() > 1) {
            $oldestMigration = $classroomMaterialMigrations->sortBy('batch')->first();
            
            foreach ($classroomMaterialMigrations as $migration) {
                if ($migration->id != $oldestMigration->id) {
                    DB::table('migrations')->where('id', $migration->id)->delete();
                    echo "Removed duplicate migration: {$migration->migration}\n";
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be reversed
        echo "This migration cannot be reverted. Please restore from a backup if needed.\n";
    }
}
