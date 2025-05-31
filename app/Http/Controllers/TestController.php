<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function testAssignmentRelations()
    {
        try {
            // Check the assignments table structure
            $columns = Schema::getColumnListing('assignments');
            
            // Check relationships
            $assignments = Assignment::all();
            $assignmentCount = $assignments->count();
            
            // Get a sample assignment if available
            $sampleAssignment = $assignments->first();
            $sampleAssignmentData = $sampleAssignment ? [
                'id' => $sampleAssignment->id,
                'title' => $sampleAssignment->title,
                'is_active' => $sampleAssignment->is_active,
                'teacher_id' => $sampleAssignment->teacher_id,
                'has_teacher' => $sampleAssignment->teacher ? true : false,
                'classes_count' => $sampleAssignment->schoolClasses ? $sampleAssignment->schoolClasses->count() : 0,
                'classrooms_count' => $sampleAssignment->classrooms ? $sampleAssignment->classrooms->count() : 0,
            ] : null;
            
            // Get all teacher users
            $teachers = User::where('role_id', 2)->get();
            
            // Check for any guru role users with assignments
            $teachersWithAssignments = User::where('role_id', 2)
                ->whereHas('teacherAssignments')
                ->get();
            
            return response()->json([
                'success' => true,
                'table_exists' => Schema::hasTable('assignments'),
                'columns' => $columns,
                'has_is_active' => in_array('is_active', $columns),
                'total_assignments' => $assignmentCount,
                'sample_assignment' => $sampleAssignmentData,
                'total_teachers' => $teachers->count(),
                'teachers_with_assignments' => $teachersWithAssignments->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
