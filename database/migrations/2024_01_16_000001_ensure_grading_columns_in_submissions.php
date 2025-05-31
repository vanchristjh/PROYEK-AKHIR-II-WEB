<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureGradingColumnsInSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'score')) {
                $table->decimal('score', 5, 2)->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't want to remove these columns in case they're being used
        // If you need to remove them, do it in a separate migration
    }
}
