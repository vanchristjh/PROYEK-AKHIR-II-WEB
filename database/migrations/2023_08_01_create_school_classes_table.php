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
        // Check if the table exists before modifying it
        if (Schema::hasTable('school_classes')) {
            Schema::table('school_classes', function (Blueprint $table) {
                // Add any changes to the existing table here if needed
                // For example, you can add columns that don't exist yet
                if (!Schema::hasColumn('school_classes', 'grade') && Schema::hasColumn('school_classes', 'grade_level')) {
                    $table->renameColumn('grade_level', 'grade');
                } else if (!Schema::hasColumn('school_classes', 'grade')) {
                    $table->string('grade')->nullable();
                }
                
                if (!Schema::hasColumn('school_classes', 'year') && Schema::hasColumn('school_classes', 'academic_year')) {
                    $table->renameColumn('academic_year', 'year');
                } else if (!Schema::hasColumn('school_classes', 'year')) {
                    $table->string('year')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't drop the table in down() since we're only modifying it
        if (Schema::hasTable('school_classes')) {
            Schema::table('school_classes', function (Blueprint $table) {
                // Reverse any changes made in the up method
                if (Schema::hasColumn('school_classes', 'grade')) {
                    $table->dropColumn('grade');
                }                if (Schema::hasColumn('school_classes', 'year')) {
                    $table->dropColumn('year');
                }
            });
        }
    }
};
