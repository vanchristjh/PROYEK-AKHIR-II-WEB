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
        Schema::table('student_answers', function (Blueprint $table) {
            // Add a properly named index to keep it under MySQL's 64 character limit
            $table->index(['quiz_attempt_id', 'exam_attempt_id', 'question_id'], 'student_answers_attempt_question_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            // Drop the index if it exists
            $table->dropIndex('student_answers_attempt_question_index');
        });
    }
};
