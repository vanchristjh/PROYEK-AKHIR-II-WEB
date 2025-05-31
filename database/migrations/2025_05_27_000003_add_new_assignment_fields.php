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
        Schema::table('assignments', function (Blueprint $table) {
            // Add new fields only if they don't exist
            if (!Schema::hasColumn('assignments', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            
            if (!Schema::hasColumn('assignments', 'is_draft')) {
                $table->boolean('is_draft')->default(false);
            }
            
            if (!Schema::hasColumn('assignments', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
            
            if (!Schema::hasColumn('assignments', 'attachment_type')) {
                $table->string('attachment_type')->nullable();
            }
            
            if (!Schema::hasColumn('assignments', 'attachment_size')) {
                $table->bigInteger('attachment_size')->nullable();
            }
            
            if (!Schema::hasColumn('assignments', 'last_edited_at')) {
                $table->timestamp('last_edited_at')->nullable();
            }
            
            if (!Schema::hasColumn('assignments', 'grading_method')) {
                $table->string('grading_method')->default('manual'); // Could be 'manual', 'auto', 'peer', etc.
            }
            
            if (!Schema::hasColumn('assignments', 'allow_late_submission')) {
                $table->boolean('allow_late_submission')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn([
                'is_active',
                'is_draft',
                'published_at',
                'attachment_type',
                'attachment_size',
                'last_edited_at',
                'grading_method',
                'allow_late_submission'
            ]);
        });
    }
};