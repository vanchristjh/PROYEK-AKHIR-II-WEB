<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveUnusedTablesAndFixModels extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        // Check if student_users table exists and drop it
        if (Schema::hasTable('student_users')) {
            // Check if there's any data first
            $count = DB::table('student_users')->count();
            if ($count === 0) {
                Schema::dropIfExists('student_users');
                DB::table('migrations')
                    ->where('migration', 'like', '%_create_student_users_table')
                    ->delete();
                echo "Removed unused 'student_users' table.\n";
            } else {
                echo "Warning: 'student_users' table contains {$count} rows. Not removing.\n";
            }
        }

        // Remove empty model files
        $materiFilePath = app_path('Models/Materi.php');
        if (file_exists($materiFilePath)) {
            if (filesize($materiFilePath) === 0 || trim(file_get_contents($materiFilePath)) === '') {
                unlink($materiFilePath);
                echo "Removed empty 'Materi.php' model file.\n";
            }
        }

        // Fix other issues
        $submissionAliasesPath = app_path('Models/SubmissionAliases.php');
        if (file_exists($submissionAliasesPath)) {
            unlink($submissionAliasesPath);
            echo "Removed 'SubmissionAliases.php' as it's just an alias file.\n";
        }

        // Remove duplicate Activity model (keep ActivityLog instead)
        $activityModelPath = app_path('Models/Activity.php');
        if (file_exists($activityModelPath)) {
            unlink($activityModelPath);
            echo "Removed 'Activity.php' model as it's redundant with ActivityLog.\n";
        }

        // Clean up migrations related to non-existent tables
        DB::table('migrations')
            ->where('migration', 'like', '%_create_activitys_table')
            ->orWhere('migration', 'like', '%_create_materis_table')
            ->orWhere('migration', 'like', '%_create_submission_aliasess_table')
            ->delete();
        
        echo "Cleaned up migration entries for non-existent tables.\n";
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
