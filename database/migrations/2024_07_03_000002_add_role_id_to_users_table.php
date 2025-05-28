<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */    public function up(): void
    {
        // First, check if the view exists and drop it
        DB::statement('DROP VIEW IF EXISTS student_users');
        
        Schema::table('users', function (Blueprint $table) {
            // First add the column without constraints
            $table->unsignedBigInteger('role_id')->after('id')->nullable();
            $table->string('username')->after('name')->unique();
            $table->string('avatar')->nullable()->after('email');
        });

        // Then add the foreign key constraint if the roles table exists
        if (Schema::hasTable('roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('roles');
            });
        }
        
        // Recreate the view if needed
        DB::statement('
            CREATE VIEW student_users AS
            SELECT u.*
            FROM users u
            INNER JOIN role_user ru ON u.id = ru.user_id
            INNER JOIN roles r ON ru.role_id = r.id
            WHERE r.name = "siswa" OR r.name = "student"
        ');
    }

    /**
     * Reverse the migrations.
     */    public function down(): void
    {
        // Drop the view first
        DB::statement('DROP VIEW IF EXISTS student_users');
        
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key first if it exists
            if (Schema::hasColumn('users', 'role_id') && Schema::hasTable('roles')) {
                $table->dropForeign(['role_id']);
            }
            
            // Then drop the columns
            $table->dropColumn(['role_id', 'username', 'avatar']);
        });
        
        // Recreate the view
        DB::statement('
            CREATE VIEW student_users AS
            SELECT u.*
            FROM users u
            INNER JOIN role_user ru ON u.id = ru.user_id
            INNER JOIN roles r ON ru.role_id = r.id
            WHERE r.name = "siswa" OR r.name = "student"
        ');
    }
};
