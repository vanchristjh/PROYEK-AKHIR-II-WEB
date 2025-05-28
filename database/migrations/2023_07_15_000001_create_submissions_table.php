<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if table exists before attempting to create it
        if (!Schema::hasTable('submissions')) {
            Schema::create('submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assignment_id');
                $table->unsignedBigInteger('student_id');
                $table->string('file_path');
                $table->string('file_name')->nullable();
                $table->string('file_size')->nullable();
                $table->string('file_icon')->nullable();
                $table->string('file_color')->nullable();
                $table->text('notes')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->text('feedback')->nullable();
                $table->string('status')->default('submitted'); // submitted, updated, graded, late
                $table->timestamp('graded_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
                
                $table->unique(['assignment_id', 'student_id']);
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
        Schema::dropIfExists('submissions');
    }
}
