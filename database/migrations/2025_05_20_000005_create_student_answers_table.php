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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_attempt_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('exam_attempt_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('option_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('text_answer')->nullable()->comment('For essay/short answer questions');
            $table->integer('score')->nullable()->comment('For manually graded questions');
            $table->text('feedback')->nullable();            $table->boolean('is_correct')->nullable();
            $table->timestamps();
            // Index will be added in a separate migration to avoid issues with naming
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
