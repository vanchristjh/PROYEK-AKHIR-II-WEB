<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                // Rename due_date to deadline if needed
                if (Schema::hasColumn('assignments', 'due_date') && !Schema::hasColumn('assignments', 'deadline')) {
                    $table->renameColumn('due_date', 'deadline');
                }
                  // Add class_id if it doesn't exist
                if (!Schema::hasColumn('assignments', 'class_id')) {
                    $table->foreignId('class_id')->nullable()->constrained('classrooms')->onDelete('cascade');
                }
                
                // Drop file_path if it exists since we might have a different approach for files
                if (Schema::hasColumn('assignments', 'file_path')) {
                    $table->dropColumn('file_path');
                }
            });
        }
    }
    
    /**
     * Reverse the migrations.
     */    public function down(): void
    {
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                // Reverse the changes
                if (Schema::hasColumn('assignments', 'deadline') && !Schema::hasColumn('assignments', 'due_date')) {
                    $table->renameColumn('deadline', 'due_date');
                }
                
                if (Schema::hasColumn('assignments', 'class_id')) {
                    $table->dropForeign(['class_id']);
                    $table->dropColumn('class_id');
                }
                
                // Add back the file_path column
                if (!Schema::hasColumn('assignments', 'file_path')) {
                    $table->string('file_path')->nullable();
                }
            });
        }
    }
};
