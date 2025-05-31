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
        // Create assignment_class table if it doesn't exist
        if (!Schema::hasTable('assignment_class')) {
            Schema::create('assignment_class', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
                $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['assignment_id', 'class_id']);
            });
        }

        // Create assignment_classroom table if it doesn't exist
        if (!Schema::hasTable('assignment_classroom')) {
            Schema::create('assignment_classroom', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
                $table->foreignId('classroom_id')->constrained('school_classes')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['assignment_id', 'classroom_id']);
            });
        }

        // Drop old tables if they exist
        Schema::dropIfExists('classroom_assignment');
        Schema::dropIfExists('assignment_classes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_classroom');
        Schema::dropIfExists('assignment_class');
    }
};
