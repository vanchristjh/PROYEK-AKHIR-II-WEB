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
        // First, drop any existing foreign key constraints on class_id
        Schema::table('students', function (Blueprint $table) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_NAME = 'students' 
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_SCHEMA = DATABASE()
            ");

            foreach ($foreignKeys as $foreignKey) {
                if (str_contains(strtolower($foreignKey->CONSTRAINT_NAME), 'class')) {
                    $table->dropForeign($foreignKey->CONSTRAINT_NAME);
                }
            }
        });

        // Make sure class_id is the correct type
        Schema::table('students', function (Blueprint $table) {
            // Modify class_id to match the type in school_classes table
            $table->unsignedBigInteger('class_id')->nullable()->change();
            
            // Add the foreign key constraint with a unique name
            $table->foreign('class_id', 'students_class_id_foreign_new')
                  ->references('id')
                  ->on('school_classes')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign('students_class_id_foreign_new');
        });
    }
};
