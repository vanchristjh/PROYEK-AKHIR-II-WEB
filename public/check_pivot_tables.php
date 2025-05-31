<?php
require "../vendor/autoload.php";
$app = require_once __DIR__."/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// Check assignment-class pivot
$assignmentClassPivot = Schema::hasTable("assignment_class");
$classroomAssignmentPivot = Schema::hasTable("classroom_assignment");

echo "Assignment-class pivot exists: " . ($assignmentClassPivot ? "Yes" : "No") . "<br>";
echo "Classroom-assignment pivot exists: " . ($classroomAssignmentPivot ? "Yes" : "No") . "<br>";

// Get sample data
if($assignmentClassPivot) {
    $assignmentClassData = DB::table("assignment_class")->take(5)->get();
    echo "Assignment-class sample data:<br>";
    echo json_encode($assignmentClassData, JSON_PRETTY_PRINT);
    echo "<br>";
    echo "Total records: " . DB::table("assignment_class")->count();
    echo "<br><br>";
}

if($classroomAssignmentPivot) {
    $classroomAssignmentData = DB::table("classroom_assignment")->take(5)->get();
    echo "Classroom-assignment sample data:<br>";
    echo json_encode($classroomAssignmentData, JSON_PRETTY_PRINT);
    echo "<br>";
    echo "Total records: " . DB::table("classroom_assignment")->count();
}

