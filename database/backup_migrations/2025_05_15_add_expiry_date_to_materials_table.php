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
        if (Schema::hasTable('materials') && !Schema::hasColumn('materials', 'expiry_date')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->timestamp('expiry_date')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('materials') && Schema::hasColumn('materials', 'expiry_date')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->dropColumn('expiry_date');
            });
        }
    }
};