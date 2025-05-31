<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateAssignmentsTable extends Migration
{
    /**
     * Helper method to get all foreign key constraint names for a given column.
     */
    private function getForeignKeysForColumn($table, $column) {
        $database = DB::connection()->getDatabaseName();
        $keys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$database, $table, $column]);
        return array_map(function($key) {
            return $key->CONSTRAINT_NAME;
        }, $keys);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */    
    public function up()
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
                    try {
                        // Check existing foreign keys on this column
                        $foreignKeys = $this->getForeignKeysForColumn('assignments', 'teacher_id');
                        
                        // Drop any existing foreign keys on teacher_id
                        foreach ($foreignKeys as $key) {
                            try {
                                $table->dropForeign($key);
                            } catch (\Exception $e) {
                                // Ignore errors when dropping constraints that might not exist
                            }
                        }
                        
                        // Add the foreign key with a new name
                        $table->foreign('teacher_id')
                              ->references('id')
                              ->on('users')
                              ->onDelete('cascade');
                    } catch (\Exception $e) {
                        // Foreign key operation failed - log or handle error
                        error_log("Error handling teacher_id foreign key: " . $e->getMessage());
                    }
                }
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
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                // Safely try to drop foreign keys and columns
                try {
                    if (Schema::hasColumn('assignments', 'classroom_id')) {
                        $classroomKeys = $this->getForeignKeysForColumn('assignments', 'classroom_id');
                        foreach ($classroomKeys as $key) {
                            $table->dropForeign($key);
                        }
                        $table->dropColumn('classroom_id');
                    }
                    if (Schema::hasColumn('assignments', 'max_score')) {
                        $table->dropColumn('max_score');
                    }
                    if (Schema::hasColumn('assignments', 'file_name')) {
                        $table->dropColumn('file_name');
                    }
                    
                    // Drop teacher_id foreign key but keep the column
                    if (Schema::hasColumn('assignments', 'teacher_id')) {
                        $teacherKeys = $this->getForeignKeysForColumn('assignments', 'teacher_id');
                        foreach ($teacherKeys as $key) {
                            try {
                                $table->dropForeign($key);
                            } catch (\Exception $e) {
                                // Ignore errors when dropping constraints that might not exist
                            }
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Error in down migration: " . $e->getMessage());
                }
            });
        }
    }
}