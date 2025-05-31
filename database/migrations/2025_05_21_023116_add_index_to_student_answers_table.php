<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToStudentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_answers', function (Blueprint $table) {
            // Add indices for quiz and exam attempts
            $table->index(['quiz_attempt_id', 'question_id'], 'student_answers_quiz_attempt_index');
            $table->index(['exam_attempt_id', 'question_id'], 'student_answers_exam_attempt_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_answers', function (Blueprint $table) {
            // First drop foreign keys that reference this index
            // Get all foreign keys that reference student_answers
            $foreignKeys = $this->getForeignKeysUsingIndex();
            
            foreach ($foreignKeys as $foreignKey) {
                Schema::table($foreignKey['table'], function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey['name']);
                });
            }
            
            // Drop both indices
            $table->dropIndex('student_answers_quiz_attempt_index');
            $table->dropIndex('student_answers_exam_attempt_index');
        });
    }
    
    /**
     * Get foreign keys that use the student_answers_attempt_question_index
     * 
     * @return array
     */
    private function getForeignKeysUsingIndex()
    {
        // This is a placeholder. You'd need to determine which tables have foreign keys 
        // referencing the student_answers table with this specific index.
        // For a proper implementation, you might need to query the information_schema.
        
        return [
            // Example format:
            // ['table' => 'some_table', 'name' => 'some_table_foreign']
        ];
    }
}
