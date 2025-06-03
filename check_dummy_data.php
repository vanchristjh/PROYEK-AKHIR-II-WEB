<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use Illuminate\Support\Facades\DB;

// Function to check record count for a table
function checkTableCount($tableName) {
    $count = DB::table($tableName)->count();
    echo "{$tableName}: {$count} records\n";
    return $count;
}

// Function to print a sample from a table
function printSample($tableName, $limit = 3) {
    $records = DB::table($tableName)->limit($limit)->get();
    echo "\n{$tableName} sample data:\n";
    echo json_encode($records, JSON_PRETTY_PRINT);
    echo "\n";
}

echo "===== SCHOOL MANAGEMENT SYSTEM DATA CHECK =====\n\n";

echo "==== BASIC ENTITIES ====\n";
$roleCount = checkTableCount('roles');
$userCount = checkTableCount('users');
$classCount = checkTableCount('school_classes');
$subjectCount = checkTableCount('subjects');
$classroomCount = checkTableCount('classrooms');

echo "\n==== EDUCATIONAL MATERIALS ====\n";
$materialCount = checkTableCount('materials');
$assignmentCount = checkTableCount('assignments');
$quizCount = checkTableCount('quizzes');
$questionCount = checkTableCount('questions');
$optionCount = checkTableCount('options');
$announcementCount = checkTableCount('announcements');
$scheduleCount = checkTableCount('schedules');

echo "\n==== SAMPLE DATA ====\n";
echo "\nUsers by Role:\n";
$usersByRole = DB::table('users')
    ->select(DB::raw('role_id, count(*) as count'))
    ->groupBy('role_id')
    ->get();
echo json_encode($usersByRole, JSON_PRETTY_PRINT);

// Print samples from main tables
printSample('users');
printSample('assignments');
printSample('materials');
printSample('quizzes');
printSample('questions');
printSample('options', 5);
printSample('announcements');
printSample('schedules');

echo "\n===== DATA VERIFICATION COMPLETE =====\n";
