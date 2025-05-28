<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */    public function up()
    {
        // Update the existing assignments table with new columns if they don't exist
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                // Check if columns don't exist before adding them
                if (!Schema::hasColumn('assignments', 'classroom_id')) {
                    $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('cascade');
                }
                if (!Schema::hasColumn('assignments', 'max_score')) {
                    $table->integer('max_score')->default(100);
                }
                if (!Schema::hasColumn('assignments', 'file_name')) {
                    $table->string('file_name')->nullable();
                }
                // Make sure teacher_id references users table
                if (Schema::hasColumn('assignments', 'teacher_id')) {
                    // Drop the existing foreign key first if it exists
                    // Note: In a real scenario you might need to determine the actual constraint name
                    try {
                        $table->dropForeign(['teacher_id']);
                    } catch (\Exception $e) {
                        // Constraint might not exist or have a different name
                    }
                    // Add the foreign key pointing to users table
                    $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */    public function down()
    {
        // We can't easily undo these changes without knowing what was there before
        // So this is a safe no-op down method
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                // You could drop specific columns if needed
                // If you do, be careful about dependencies
            });
        }
    }
}