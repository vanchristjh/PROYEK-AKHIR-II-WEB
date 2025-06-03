<?php
require 'vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Enable query logging
Illuminate\Support\Facades\DB::enableQueryLog();

// Get classroom count
$classrooms = Illuminate\Support\Facades\DB::table('classrooms')->get();
echo "Classrooms count: ".$classrooms->count().PHP_EOL;

// Show classroom data
if ($classrooms->count() > 0) {
    print_r($classrooms->first());
}

// Show the table structure
$columns = Illuminate\Support\Facades\Schema::getColumnListing('classrooms');
echo "Classrooms table has columns: ".implode(', ', $columns).PHP_EOL;

// Print queries executed
$queries = Illuminate\Support\Facades\DB::getQueryLog();
echo "Queries:".PHP_EOL;
foreach ($queries as $query) {
    echo $query['query'].PHP_EOL;
}
