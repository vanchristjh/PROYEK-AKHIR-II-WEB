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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->string('type')->comment('multiple_choice, true_false, essay, short_answer');
            $table->integer('points')->default(1);
            $table->text('correct_answer')->nullable()->comment('For multiple choice and true/false questions');
            $table->text('explanation')->nullable();
            $table->string('difficulty_level')->nullable()->comment('easy, medium, hard');
            $table->foreignId('quiz_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // A question must belong to either a quiz or an exam
            $table->index(['quiz_id', 'exam_id']);
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
    }
};
