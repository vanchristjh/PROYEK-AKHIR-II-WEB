<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('school_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('school_classes', 'classroom_id')) {
                $table->foreignId('classroom_id')->nullable()->constrained()->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn('classroom_id');
        });
    }
};