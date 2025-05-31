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
    {
        // Check if assignments table exists and has the old column
        if (Schema::hasTable('assignments') && Schema::hasColumn('assignments', 'due_date')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'deadline')) {
                    $table->renameColumn('due_date', 'deadline');
                }
            });
        }

        // Check if other tables that might have due_date columns
        if (Schema::hasTable('submissions') && Schema::hasColumn('submissions', 'due_date')) {
            Schema::table('submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('submissions', 'deadline')) {
                    $table->renameColumn('due_date', 'deadline');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the column renaming for assignments table
        if (Schema::hasTable('assignments') && Schema::hasColumn('assignments', 'deadline')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'due_date')) {
                    $table->renameColumn('deadline', 'due_date');
                }
            });
        }

        // Reverse for other tables
        if (Schema::hasTable('submissions') && Schema::hasColumn('submissions', 'deadline')) {
            Schema::table('submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('submissions', 'due_date')) {
                    $table->renameColumn('deadline', 'due_date');
                }
            });
        }
    }
};