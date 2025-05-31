<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if we're using SQLite
        $isSQLite = DB::connection()->getDriverName() === 'sqlite';

        if ($isSQLite) {
            // SQLite approach - create new table and copy data
            Schema::create('submissions_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_type')->nullable();
                $table->string('file_size')->nullable();
                $table->string('file_icon')->nullable();
                $table->string('file_color')->nullable();
                $table->text('notes')->nullable();
                $table->text('feedback')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('graded_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                // Add the new unique constraint
                $table->unique(['assignment_id', 'user_id']);
            });

            // Copy data from old table to new table
            DB::statement('
                INSERT INTO submissions_new (
                    id, assignment_id, user_id, file_path, file_name, file_type, file_size,
                    file_icon, file_color, notes, feedback, score, submitted_at, graded_at,
                    created_at, updated_at, deleted_at
                )
                SELECT 
                    id, assignment_id, student_id, file_path, file_name, file_type, file_size,
                    file_icon, file_color, notes, feedback, score, submitted_at, graded_at,
                    created_at, updated_at, deleted_at
                FROM submissions
            ');

            // Drop old table and rename new table
            Schema::drop('submissions');
            Schema::rename('submissions_new', 'submissions');
        } else {
            // MySQL/PostgreSQL approach
            // Drop all foreign keys and indexes first
            Schema::table('submissions', function (Blueprint $table) {
                // Drop any foreign keys that might reference this table
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = array_map(function($key) {
                    return $key->getName();
                }, $sm->listTableForeignKeys('submissions'));
                
                foreach ($foreignKeys as $foreignKey) {
                    $table->dropForeign($foreignKey);
                }

                // Drop the unique index
                $table->dropIndex('submissions_assignment_id_student_id_unique');
            });

            // Then rename the column
            Schema::table('submissions', function (Blueprint $table) {
                if (Schema::hasColumn('submissions', 'student_id')) {
                    $table->renameColumn('student_id', 'user_id');
                }
            });

            // Finally, add back the constraints
            Schema::table('submissions', function (Blueprint $table) {
                // Add back foreign keys
                $table->foreign('assignment_id')
                    ->references('id')
                    ->on('assignments')
                    ->onDelete('cascade');
                
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                // Add back the unique constraint
                $table->unique(['assignment_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $isSQLite = DB::connection()->getDriverName() === 'sqlite';

        if ($isSQLite) {
            // Create the old table structure
            Schema::create('submissions_old', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_type')->nullable();
                $table->string('file_size')->nullable();
                $table->string('file_icon')->nullable();
                $table->string('file_color')->nullable();
                $table->text('notes')->nullable();
                $table->text('feedback')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('graded_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['assignment_id', 'student_id']);
            });

            // Copy the data back
            DB::statement('
                INSERT INTO submissions_old (
                    id, assignment_id, student_id, file_path, file_name, file_type, file_size,
                    file_icon, file_color, notes, feedback, score, submitted_at, graded_at,
                    created_at, updated_at, deleted_at
                )
                SELECT 
                    id, assignment_id, user_id, file_path, file_name, file_type, file_size,
                    file_icon, file_color, notes, feedback, score, submitted_at, graded_at,
                    created_at, updated_at, deleted_at
                FROM submissions
            ');

            // Drop new table and rename old table
            Schema::drop('submissions');
            Schema::rename('submissions_old', 'submissions');
        } else {
            // MySQL/PostgreSQL approach
            // Drop all foreign keys and indexes first
            Schema::table('submissions', function (Blueprint $table) {
                // Drop any foreign keys that might reference this table
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = array_map(function($key) {
                    return $key->getName();
                }, $sm->listTableForeignKeys('submissions'));
                
                foreach ($foreignKeys as $foreignKey) {
                    $table->dropForeign($foreignKey);
                }

                // Drop the unique index
                $table->dropIndex('submissions_assignment_id_user_id_unique');
            });

            // Then rename the column back
            Schema::table('submissions', function (Blueprint $table) {
                $table->renameColumn('user_id', 'student_id');
            });

            // Finally, add back the original constraints
            Schema::table('submissions', function (Blueprint $table) {
                // Add back foreign keys
                $table->foreign('assignment_id')
                    ->references('id')
                    ->on('assignments')
                    ->onDelete('cascade');
                
                $table->foreign('student_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                // Add back the unique constraint
                $table->unique(['assignment_id', 'student_id']);
            });
        }
    }
};
