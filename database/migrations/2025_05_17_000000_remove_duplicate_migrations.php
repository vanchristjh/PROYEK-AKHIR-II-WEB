<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateMigrations extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        // List of duplicate migrations to remove
        $migrationsToRemove = [
            // subject_teacher table duplicates
            '2023_02_15_000000_create_subject_teacher_table',
            '2023_05_15_000000_create_subject_teacher_table',
            '2023_05_15_000002_create_subject_teacher_table',
            '2024_07_02_000003_create_subject_teacher_table',
            
            // schedules table duplicates
            '2023_05_01_000000_create_schedules_table',
            '2023_05_15_000000_create_schedules_table',
            '2023_05_15_000001_create_schedules_table',
            
            // classroom_subject table duplicates
            '2023_05_01_999999_create_classroom_subject_relationship_table',
            
            // users table duplicates
            '2024_07_02_000002_create_users_table',
            
            // role_user table duplicates
            '2024_06_17_000000_create_role_user_table',
            
            // materials table duplicates
            '2024_07_01_100000_create_materials_table',
            
            // roles table duplicates
            '2024_07_03_000001_create_roles_table',
        ];

        // Remove the entries from the migrations table
        foreach ($migrationsToRemove as $migration) {
            DB::table('migrations')->where('migration', $migration)->delete();
        }

        echo "Removed " . count($migrationsToRemove) . " duplicate migrations from the migrations table.\n";
        
        // This doesn't affect the actual physical files or database tables
        echo "Note: Migration files should be manually removed from the filesystem.\n";
        echo "Database tables remain unchanged as they were already created.\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be reversed as it would require detailed information
        // about the removed migrations. It's a cleaning operation.
        echo "This migration cannot be reverted. Please restore from a backup if needed.\n";
    }
}
