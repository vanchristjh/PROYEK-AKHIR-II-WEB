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
        // Remove the parent role from the roles table
        DB::table('roles')->where('name', 'parent')->delete();
        
        // If there's a separate users table with parent role_id, update those users
        // Check if role_user pivot table exists (many-to-many relationship)
        if (Schema::hasTable('role_user')) {
            // Get parent role ID
            $parentRole = DB::table('roles')->where('slug', 'parent')->first();
            if ($parentRole) {
                // Delete all associations with parent role
                DB::table('role_user')->where('role_id', $parentRole->id)->delete();
            }
        }
        
        // Check if the students table still has the parent_id column and drop it if it exists
        if (Schema::hasTable('students') && Schema::hasColumn('students', 'parent_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }
        
        // Drop the parents table if it still exists
        Schema::dropIfExists('parents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to restore parent functionality, so this is intentionally empty
    }
};
