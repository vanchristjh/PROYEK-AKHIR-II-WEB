<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        // Check if table exists before modifying
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                // Add any columns that don't exist in the original migration
                if (!Schema::hasColumn('activity_logs', 'model_type')) {
                    $table->string('model_type')->nullable();
                }
                if (!Schema::hasColumn('activity_logs', 'model_id')) {
                    $table->unsignedBigInteger('model_id')->nullable();
                }
                if (!Schema::hasColumn('activity_logs', 'properties')) {
                    $table->json('properties')->nullable();
                }
                
                // Drop columns that we want to replace with different ones
                if (Schema::hasColumn('activity_logs', 'ip_address')) {
                    $table->dropColumn('ip_address');
                }
                if (Schema::hasColumn('activity_logs', 'user_agent')) {
                    $table->dropColumn('user_agent');
                }
            });
        } else {
            // This shouldn't happen, but just in case the table doesn't exist
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('action');
                $table->text('description')->nullable();
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->json('properties')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
