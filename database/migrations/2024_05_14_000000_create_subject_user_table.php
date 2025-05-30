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
        if (!Schema::hasTable('subject_user')) {
            Schema::create('subject_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('subject_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                // Foreign keys - using try/catch to prevent errors if constraints already exist
                try {
                    $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
                
                try {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                } catch (\Exception $e) {
                    // Foreign key already exists
                }
                
                // Unique constraint
                $table->unique(['subject_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_user');
    }
};
