<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the announcements table exists
        if (Schema::hasTable('announcements')) {            // Get the column names using Schema instead of DB::select
            $columnNames = Schema::getColumnListing('announcements');
            
            Schema::table('announcements', function (Blueprint $table) use ($columnNames) {                // Check audience column - in SQLite, we'll just use string instead of enum
                if (!in_array('audience', $columnNames)) {
                    // Add audience column if it doesn't exist - use string instead of enum for SQLite
                    $table->string('audience')->default('all')->after('content');
                }
                
                // Check for is_important column
                if (!in_array('is_important', $columnNames)) {
                    $table->boolean('is_important')->default(false);
                }
                
                // Check if there's an attachment or attachment_path column
                if (!in_array('attachment', $columnNames) && !in_array('attachment_path', $columnNames)) {
                    $table->string('attachment')->nullable();
                }
                  // Check if author_id has a foreign key
                if (in_array('author_id', $columnNames)) {
                    // For SQLite, we'll just try to add the foreign key directly
                    // SQLite will silently ignore if it already exists
                    try {
                        $table->foreign('author_id')
                            ->references('id')
                            ->on('users')
                            ->onDelete('cascade');
                    } catch (\Exception $e) {
                        // Foreign key might already exist or there's another issue
                        // We'll just continue
                    }
                }
                
                // Make sure required columns exist
                if (!in_array('title', $columnNames)) {
                    $table->string('title');
                }
                
                if (!in_array('content', $columnNames)) {
                    $table->text('content');
                }
                
                if (!in_array('publish_date', $columnNames)) {
                    $table->timestamp('publish_date')->nullable();
                }
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
        // This migration makes additive changes, no need to reverse them
    }
};