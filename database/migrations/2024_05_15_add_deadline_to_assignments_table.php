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
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'deadline')) {
                // Check if due_date exists before adding deadline after it
                if (Schema::hasColumn('assignments', 'due_date')) {
                    $table->timestamp('deadline')->nullable()->after('due_date');
                } else {
                    $table->timestamp('deadline')->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'deadline')) {
                $table->dropColumn('deadline');
            }
        });
    }
};
