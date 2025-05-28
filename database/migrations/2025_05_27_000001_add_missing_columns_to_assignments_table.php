<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToAssignmentsTable extends Migration
{
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('assignments', 'max_score')) {
                $table->integer('max_score')->nullable();
            }
            if (!Schema::hasColumn('assignments', 'allow_late_submission')) {
                $table->boolean('allow_late_submission')->default(false);
            }
            if (!Schema::hasColumn('assignments', 'late_submission_penalty')) {
                $table->integer('late_submission_penalty')->default(0);
            }
            if (!Schema::hasColumn('assignments', 'visibility')) {
                $table->string('visibility')->default('visible');
            }
        });
    }

    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn([
                'is_active',
                'max_score', 
                'allow_late_submission',
                'late_submission_penalty',
                'visibility'
            ]);
        });
    }
}
