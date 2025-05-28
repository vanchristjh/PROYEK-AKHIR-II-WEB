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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->integer('max_score')->nullable();
            $table->float('score_percentage')->nullable();
            $table->boolean('passed')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamps();
            
            // A student can have multiple attempts for a quiz
            $table->index(['quiz_id', 'student_id', 'attempt_number']);
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->integer('max_score')->nullable();
            $table->float('score_percentage')->nullable();
            $table->boolean('passed')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamps();
            
            // A student can have multiple attempts for an exam
            $table->index(['exam_id', 'student_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('exam_attempts');
    }
};
