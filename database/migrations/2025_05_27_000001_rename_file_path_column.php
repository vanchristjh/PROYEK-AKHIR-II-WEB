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
        // Check if materials table exists and has the old column
        if (Schema::hasTable('materials') && Schema::hasColumn('materials', 'file_path')) {
            Schema::table('materials', function (Blueprint $table) {
                if (!Schema::hasColumn('materials', 'attachment_path')) {
                    $table->renameColumn('file_path', 'attachment_path');
                }
            });
        }

        // Check if assignments table exists and has the old column
        if (Schema::hasTable('assignments') && Schema::hasColumn('assignments', 'file_path')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'attachment_path')) {
                    $table->renameColumn('file_path', 'attachment_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the column renaming for materials table
        if (Schema::hasTable('materials') && Schema::hasColumn('materials', 'attachment_path')) {
            Schema::table('materials', function (Blueprint $table) {
                if (!Schema::hasColumn('materials', 'file_path')) {
                    $table->renameColumn('attachment_path', 'file_path');
                }
            });
        }

        // Reverse the column renaming for assignments table
        if (Schema::hasTable('assignments') && Schema::hasColumn('assignments', 'attachment_path')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('assignments', 'file_path')) {
                    $table->renameColumn('attachment_path', 'file_path');
                }
            });
        }
    }
};