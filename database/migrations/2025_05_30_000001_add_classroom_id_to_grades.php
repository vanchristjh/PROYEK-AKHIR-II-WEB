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
        if (Schema::hasTable('grades') && !Schema::hasColumn('grades', 'classroom_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->foreignId('classroom_id')
                    ->nullable()
                    ->constrained()
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('grades') && Schema::hasColumn('grades', 'classroom_id')) {
            Schema::table('grades', function (Blueprint $table) {
                $table->dropForeign(['classroom_id']);
                $table->dropColumn('classroom_id');
            });
        }
    }
};
