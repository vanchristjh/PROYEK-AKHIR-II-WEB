<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the table already exists to avoid errors
        if (!Schema::hasTable('classroom_assignment')) {
            Schema::create('classroom_assignment', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('classroom_id');
                $table->unsignedBigInteger('assignment_id');
                $table->timestamps();
                
                $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('cascade');
                $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
                
                $table->unique(['classroom_id', 'assignment_id']);
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
        Schema::dropIfExists('classroom_assignment');
    }
}
