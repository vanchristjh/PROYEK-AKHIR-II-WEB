<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, ensure the table exists
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('action');
                $table->text('description')->nullable();
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->json('properties')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
            
            return; // Table created, no need to continue
        }
        
        // Table exists, so we need to modify it
        // Check for each column and add if missing
        if (!Schema::hasColumn('activity_logs', 'model_type')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('model_type')->nullable();
            });
        }
        
        if (!Schema::hasColumn('activity_logs', 'model_id')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->unsignedBigInteger('model_id')->nullable();
            });
        }
        
        if (!Schema::hasColumn('activity_logs', 'properties')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->json('properties')->nullable();
            });
        }
        
        // Remove columns that are no longer needed
        $columnsToRemove = ['ip_address', 'user_agent'];
        foreach ($columnsToRemove as $column) {
            if (Schema::hasColumn('activity_logs', $column)) {
                Schema::table('activity_logs', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
          // Create an index on model_type and model_id if they both exist
        if (Schema::hasColumn('activity_logs', 'model_type') && 
            Schema::hasColumn('activity_logs', 'model_id')) {
            // For SQLite, we can just try to create the index directly
            // SQLite handles duplicate index creation gracefully
            try {
                Schema::table('activity_logs', function (Blueprint $table) {
                    $table->index(['model_type', 'model_id']);
                });
            } catch (\Exception $e) {
                // Index might already exist, just ignore the error
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Since this is a cleanup migration, we don't need to do anything in down()
        // The table structure is now standardized
    }
};
