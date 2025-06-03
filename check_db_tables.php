<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__)
);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$classrooms = DB::table('classrooms')->get();
echo "Count classrooms: " . $classrooms->count() . "\n";
if ($classrooms->count() > 0) {
    echo "First classroom ID: " . $classrooms->first()->id . "\n";
}

$schoolClasses = DB::table('school_classes')->get();
echo "Count school_classes: " . $schoolClasses->count() . "\n";
if ($schoolClasses->count() > 0) {
    echo "First school class ID: " . $schoolClasses->first()->id . "\n";
}

$columns = Schema::getColumnListing('schedules');
echo "\nTable 'schedules' has the following columns:\n";
foreach ($columns as $column) {
    $type = DB::getSchemaBuilder()->getColumnType('schedules', $column);
    echo "- {$column} ({$type})\n";
}
