<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MarkMigrationAsRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-migration-as-run {migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark a migration as run without running it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migration = $this->argument('migration');
        
        // Check if the migration exists in the filesystem
        $migrationPath = database_path("migrations/{$migration}.php");
        if (!file_exists($migrationPath)) {
            $this->error("Migration file {$migration}.php does not exist.");
            return 1;
        }

        // Insert into the migrations table
        \Illuminate\Support\Facades\DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => \Illuminate\Support\Facades\DB::table('migrations')->max('batch') + 1
        ]);

        $this->info("Migration {$migration} has been marked as run.");
        return 0;
    }
}
