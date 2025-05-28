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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->integer('duration')->comment('Duration in minutes');
            $table->boolean('is_active')->default(true);
            $table->integer('max_attempts')->default(1);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_result')->default(true);
            $table->float('passing_score')->nullable();
            $table->string('exam_type')->comment('midterm, final, daily, etc');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exam_classroom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['exam_id', 'classroom_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_classroom');
        Schema::dropIfExists('exams');
    }
};
