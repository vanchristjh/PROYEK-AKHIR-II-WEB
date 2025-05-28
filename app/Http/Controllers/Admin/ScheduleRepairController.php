<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleRepairController extends Controller
{
    public function index()
    {
        // Find schedules with broken relationships
        $brokenSchedules = Schedule::whereHas('teacher', function($query) {
                return $query;
            }, '<', 1)
            ->whereNotNull('teacher_id')
            ->orWhereHas('classroom', function($query) {
                return $query;
            }, '<', 1)
            ->whereNotNull('classroom_id')
            ->orWhereHas('subject', function($query) {
                return $query;
            }, '<', 1)
            ->whereNotNull('subject_id')
            ->get();
            
        // Get available teachers, classrooms, and subjects for repair options
        $teachers = Teacher::all();
        $classrooms = Classroom::all();
        $subjects = Subject::all();
            
        return view('admin.schedule.repair', compact('brokenSchedules', 'teachers', 'classrooms', 'subjects'));
    }
    
    public function repair(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $scheduleId = $request->input('schedule_id');
            $teacherId = $request->input('teacher_id');
            $classroomId = $request->input('classroom_id');
            $subjectId = $request->input('subject_id');
            
            $schedule = Schedule::findOrFail($scheduleId);
            
            // Update relationships as needed
            if ($teacherId) {
                $schedule->teacher_id = $teacherId;
            }
            
            if ($classroomId) {
                $schedule->classroom_id = $classroomId;
            }
            
            if ($subjectId) {
                $schedule->subject_id = $subjectId;
            }
            
            $schedule->save();
            
            DB::commit();
            
            return redirect()->route('admin.schedule.repair')
                ->with('success', 'Jadwal berhasil diperbaiki!');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error repairing schedule: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal memperbaiki jadwal: ' . $e->getMessage());
        }
    }
    
    public function cleanNullRelations()
    {
        try {
            DB::beginTransaction();
            
            // For schedules with non-existent teacher IDs, set to null
            $updated = Schedule::whereHas('teacher', function($query) {
                return $query;
            }, '<', 1)
            ->whereNotNull('teacher_id')
            ->update(['teacher_id' => null]);
            
            DB::commit();
            
            return redirect()->route('admin.schedule.repair')
                ->with('success', 'Berhasil membersihkan ' . $updated . ' referensi ID guru yang tidak valid.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error cleaning null relations: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal membersihkan relasi: ' . $e->getMessage());
        }
    }
    
    public function convertUserTeacherIds()
    {
        try {
            DB::beginTransaction();
            
            $fixed = 0;
            $errors = [];
            
            // Get all schedules
            $schedules = Schedule::all();
            
            foreach ($schedules as $schedule) {
                // Check if current teacher_id is a User ID
                $user = User::find($schedule->teacher_id);
                if ($user && $user->hasRole('guru')) {
                    // Find corresponding Teacher record
                    $teacher = Teacher::where('user_id', $user->id)->first();
                    
                    if ($teacher) {
                        try {
                            $oldId = $schedule->teacher_id;
                            $schedule->teacher_id = $teacher->id;
                            $schedule->save();
                            $fixed++;
                            Log::info("Fixed schedule ID {$schedule->id}: changed teacher_id from {$oldId} to {$teacher->id}");
                        } catch (\Exception $e) {
                            $errors[] = "Error fixing schedule {$schedule->id}: " . $e->getMessage();
                            Log::error("Error fixing schedule {$schedule->id}: " . $e->getMessage());
                        }
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.schedule.repair')
                ->with('success', "Successfully fixed {$fixed} schedules. " . 
                    (count($errors) > 0 ? "Errors: " . implode(", ", $errors) : ""));
                    
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error converting teacher IDs: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to convert teacher IDs: ' . $e->getMessage());
        }
    }
}
