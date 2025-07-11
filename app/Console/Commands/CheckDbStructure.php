<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CheckDbStructure extends Command
{
    protected $signature = "db:check-structure {table}";
    protected $description = "Check the structure of a database table";

    public function handle()
    {
        $table = $this->argument("table");
        
        if (!Schema::hasTable($table)) {
            $this->error("Table {$table} does not exist");
            return 1;
        }
        
        $columns = Schema::getColumnListing($table);
        $this->info("Table {$table} has the following columns:");
        
        foreach ($columns as $column) {
            $type = DB::getSchemaBuilder()->getColumnType($table, $column);
            $this->info("- {$column} ({$type})");
        }
        
        return 0;
    }
}

