<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Add columns only if they don't exist
            if (!Schema::hasColumn('submissions', 'file_name')) {
                $table->string('file_name')->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'file_size')) {
                $table->string('file_size')->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'file_icon')) {
                $table->string('file_icon')->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'file_color')) {
                $table->string('file_color')->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'notes')) {
                $table->text('notes')->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'feedback')) {
                $table->text('feedback')->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'status')) {
                $table->string('status')->default('submitted');
            }
            
            if (!Schema::hasColumn('submissions', 'graded_at')) {
                $table->timestamp('graded_at')->nullable();
            }
            
            if (!Schema::hasColumn('submissions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to reverse here as we don't want to remove columns
    }
}
