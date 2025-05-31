<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the foreign key already exists
        $keyExists = $this->checkIfForeignKeyExists('announcements', 'announcements_author_id_foreign');
        
        Schema::table('announcements', function (Blueprint $table) use ($keyExists) {
            // If the foreign key already exists, drop it first
            if ($keyExists) {
                $table->dropForeign('announcements_author_id_foreign');
            }
            
            // Add the foreign key with a different name to avoid conflicts
            $table->foreign('author_id', 'announcements_author_id_foreign_new')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Add any other modifications needed for the announcements table
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            // Remove the foreign key we added
            $table->dropForeign('announcements_author_id_foreign_new');
            
            // Re-add the original foreign key if needed
            // $table->foreign('author_id', 'announcements_author_id_foreign')
            //       ->references('id')
            //       ->on('users')
            //       ->onDelete('cascade');
        });
    }
    
    /**
     * Check if a foreign key exists on a table
     *
     * @param string $table
     * @param string $foreignKey
     * @return bool
     */
    private function checkIfForeignKeyExists($table, $foreignKey)
    {
        $database = DB::connection()->getDatabaseName();
        
        $result = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = ?
            AND TABLE_NAME = ?
            AND CONSTRAINT_NAME = ?
        ", [$database, $table, $foreignKey]);
        
        return $result[0]->count > 0;
    }
}