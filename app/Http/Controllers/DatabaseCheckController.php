<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Classroom;

class DatabaseCheckController extends Controller
{
    public function checkPivotTables()
    {
        $results = [
            'status' => 'success',
            'pivot_tables' => []
        ];

        // Check assignment_class pivot table
        $assignmentClassExists = Schema::hasTable('assignment_class');
        $results['pivot_tables']['assignment_class'] = [
            'exists' => $assignmentClassExists
        ];

        if ($assignmentClassExists) {
            $results['pivot_tables']['assignment_class']['sample_data'] = DB::table('assignment_class')->take(5)->get();
            $results['pivot_tables']['assignment_class']['count'] = DB::table('assignment_class')->count();
        }

        // Check classroom_assignment pivot table
        $classroomAssignmentExists = Schema::hasTable('classroom_assignment');
        $results['pivot_tables']['classroom_assignment'] = [
            'exists' => $classroomAssignmentExists
        ];

        if ($classroomAssignmentExists) {
            $results['pivot_tables']['classroom_assignment']['sample_data'] = DB::table('classroom_assignment')->take(5)->get();
            $results['pivot_tables']['classroom_assignment']['count'] = DB::table('classroom_assignment')->count();
        }

        // Check if assignment relationships work correctly
        $sampleAssignment = Assignment::with(['schoolClasses', 'classrooms'])->first();
        $results['sample_assignment'] = null;
        
        if ($sampleAssignment) {
            $results['sample_assignment'] = [
                'id' => $sampleAssignment->id,
                'title' => $sampleAssignment->title,
                'is_active' => $sampleAssignment->is_active,
                'classes' => $sampleAssignment->schoolClasses->map(function($class) {
                    return [
                        'id' => $class->id,
                        'name' => $class->name
                    ];
                }),
                'classrooms' => $sampleAssignment->classrooms->map(function($classroom) {
                    return [
                        'id' => $classroom->id,
                        'name' => $classroom->name
                    ];
                })
            ];
        }

        // Check if we can find users in classes that have assignments
        $assignments = Assignment::take(5)->get();
        $results['assignments_sample'] = $assignments->map(function($assignment) {
            return [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'class_count' => $assignment->classes->count(),
                'classroom_count' => $assignment->classrooms->count(),
            ];
        });
        
        // Check if students can find their assignments
        $siswa = User::where('role_id', 3)->first(); // Assuming role_id 3 is for 'siswa'
        $results['student_check'] = null;
        
        if ($siswa) {
            $studentClass = $siswa->class_id;
            $studentClassroom = $siswa->classroom_id;
            
            $results['student_check'] = [
                'student_id' => $siswa->id,
                'student_name' => $siswa->name,
                'class_id' => $studentClass,
                'classroom_id' => $studentClassroom,
                'assignments_for_class' => $studentClass ? Assignment::whereHas('classes', function($q) use ($studentClass) {
                    $q->where('school_classes.id', $studentClass);
                })->count() : 'No class assigned',
                'assignments_for_classroom' => $studentClassroom ? Assignment::whereHas('classrooms', function($q) use ($studentClassroom) {
                    $q->where('classrooms.id', $studentClassroom);
                })->count() : 'No classroom assigned',
            ];
        }

        // Check all student assignments
        $studentAssignments = [];
        $students = User::where('role_id', 3)->take(5)->get();
        foreach($students as $student) {
            $assignmentsCount = Assignment::where(function($q) use ($student) {
                if($student->classroom_id) {
                    $q->whereHas('classrooms', function($query) use ($student) {
                        $query->where('classrooms.id', $student->classroom_id);
                    });
                }
                
                if($student->class_id) {
                    $q->orWhereHas('classes', function($query) use ($student) {
                        $query->where('school_classes.id', $student->class_id);
                    });
                }
            })->count();
            
            $studentAssignments[] = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'assignments_count' => $assignmentsCount
            ];
        }
        $results['student_assignments'] = $studentAssignments;

        return response()->json($results);
    }

    public function createTestData()
    {
        try {
            // Create test classes if they don't exist
            if (SchoolClass::count() == 0) {
                $classes = [
                    ['name' => 'X-1', 'grade' => 10],
                    ['name' => 'X-2', 'grade' => 10],
                    ['name' => 'XI-1', 'grade' => 11],
                    ['name' => 'XI-2', 'grade' => 11],
                ];

                foreach ($classes as $classData) {
                    SchoolClass::create($classData);
                }
            }

            // Create test classroom if they don't exist
            if (Classroom::count() == 0) {
                $classrooms = [
                    ['name' => 'Kelas 10-A'],
                    ['name' => 'Kelas 10-B'],
                    ['name' => 'Kelas 11-A'],
                    ['name' => 'Kelas 11-B'],
                ];

                foreach ($classrooms as $classroomData) {
                    Classroom::create($classroomData);
                }
            }

            // Link SchoolClass with Classroom
            $schoolClasses = SchoolClass::all();
            $classrooms = Classroom::all();

            foreach ($schoolClasses as $index => $class) {
                if (isset($classrooms[$index])) {
                    $class->classroom_id = $classrooms[$index]->id;
                    $class->save();
                }
            }

            // Create test assignments linking to classes
            $teacher = User::where('role_id', 2)->first(); // Assuming role_id 2 is for 'guru'
            
            if (!$teacher) {
                return response()->json(['status' => 'error', 'message' => 'No teacher found in the system']);
            }
            
            $assignment = Assignment::create([
                'title' => 'Test Assignment for Database Check',
                'description' => 'This is a test assignment created to verify database relationships',
                'subject_id' => 1, // Assuming subject 1 exists
                'teacher_id' => $teacher->id,
                'deadline' => now()->addDays(7),
                'is_active' => true,
            ]);
            
            // Attach all classes to this assignment
            $assignment->classes()->attach($schoolClasses->pluck('id')->toArray());
            
            // Attach all classrooms to this assignment
            $assignment->classrooms()->attach($classrooms->pluck('id')->toArray());

            return response()->json([
                'status' => 'success',
                'message' => 'Test data created successfully',
                'assignment_id' => $assignment->id,
                'classes_attached' => $schoolClasses->pluck('id')->toArray(),
                'classrooms_attached' => $classrooms->pluck('id')->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
