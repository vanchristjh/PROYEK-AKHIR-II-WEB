<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateTeacherIdInAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the foreign key constraint already exists
        $keyExists = $this->checkIfForeignKeyExists('assignments', 'assignments_teacher_id_foreign');
        
        Schema::table('assignments', function (Blueprint $table) use ($keyExists) {
            // If the foreign key already exists, drop it first
            if ($keyExists) {
                $table->dropForeign('assignments_teacher_id_foreign');
            }
            
            // Add a new foreign key with a different name
            $table->foreign('teacher_id', 'assignments_teacher_id_foreign_new')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            // Add any other modifications needed for the assignments table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Remove the foreign key we added
            if (Schema::hasTable('assignments')) {
                $table->dropForeign('assignments_teacher_id_foreign_new');
            }
        });
    }
    
    /**
     * Check if a foreign key exists on a table
     *
     * @param string $table
     * @param string $foreignKey
     * @return bool
     */
    private function checkIfForeignKeyExists($table, $foreignKey)
    {
        $database = DB::connection()->getDatabaseName();
        
        $result = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = ?
            AND TABLE_NAME = ?
            AND CONSTRAINT_NAME = ?
        ", [$database, $table, $foreignKey]);
        
        return $result[0]->count > 0;
    }
}
