<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the schedules for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        $schedules = [];
        
        if ($user->hasRole('siswa')) {
            // Get student's classroom
            $classroom = $user->student->classroom;
            
            if ($classroom) {
                $schedules = Schedule::where('classroom_id', $classroom->id)
                    ->with(['subject', 'teacher.user', 'classroom'])
                    ->orderBy('day')
                    ->orderBy('start_time')
                    ->get();
            }
        } elseif ($user->hasRole('guru')) {
            // Get teacher's schedules
            $schedules = Schedule::where('teacher_id', $user->teacher->id)
                ->with(['subject', 'classroom'])
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'data' => $schedules
        ]);
    }    /**
     * Check for schedule conflicts
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkConflict(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day' => 'required|string|min:1|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required',
            'schedule_id' => 'nullable|exists:schedules,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'conflicts' => [],
                'hasConflicts' => false,
                'errors' => $validator->errors()->toArray()
            ]);
        }

        $conflictsList = [];
        
        // Find conflicting schedules for the classroom
        $classroomQuery = Schedule::where('classroom_id', $request->classroom_id)
            ->where('day', $request->day)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '>=', $request->start_time)
                      ->where('start_time', '<', $request->end_time);
                })->orWhere(function ($q) use ($request) {
                    $q->where('end_time', '>', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
                })->orWhere(function ($q) use ($request) {
                    $q->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>=', $request->end_time);
                });
            });

        // Exclude current schedule if editing
        if ($request->schedule_id) {
            $classroomQuery->where('id', '!=', $request->schedule_id);
        }

        $classroomConflicts = $classroomQuery->with(['subject', 'teacher', 'classroom'])->get();
        
        // Find conflicting schedules for the teacher
        $teacherQuery = Schedule::where('teacher_id', $request->teacher_id)
            ->where('day', $request->day)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '>=', $request->start_time)
                      ->where('start_time', '<', $request->end_time);
                })->orWhere(function ($q) use ($request) {
                    $q->where('end_time', '>', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
                })->orWhere(function ($q) use ($request) {
                    $q->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>=', $request->end_time);
                });
            });
            
        // Exclude current schedule if editing
        if ($request->schedule_id) {
            $teacherQuery->where('id', '!=', $request->schedule_id);
        }
        
        $teacherConflicts = $teacherQuery->with(['subject', 'teacher', 'classroom'])->get();

        // Process classroom conflicts
        foreach ($classroomConflicts as $conflict) {
            $classroom = $conflict->classroom ? $conflict->classroom->name : 'Kelas ID: ' . $request->classroom_id;
            $conflictsList[] = [
                'type' => 'classroom',
                'message' => "Konflik dengan kelas {$classroom}: Jadwal sudah ada pada hari {$conflict->day} pukul " . 
                             substr($conflict->start_time, 0, 5) . " - " . substr($conflict->end_time, 0, 5) . 
                             " (" . ($conflict->subject ? $conflict->subject->name : 'Tidak ada mata pelajaran') . ")"
            ];
        }
        
        // Process teacher conflicts
        foreach ($teacherConflicts as $conflict) {
            $teacherName = $conflict->teacher ? $conflict->teacher->name : 'Guru ID: ' . $request->teacher_id;
            $conflictsList[] = [
                'type' => 'teacher',
                'message' => "Konflik dengan guru {$teacherName}: Sudah mengajar di kelas " .
                             ($conflict->classroom ? $conflict->classroom->name : 'Tidak diketahui') . 
                             " pada hari {$conflict->day} pukul " . 
                             substr($conflict->start_time, 0, 5) . " - " . substr($conflict->end_time, 0, 5)
            ];
        }

        // Return all found conflicts
        return response()->json([
            'conflicts' => $conflictsList,
            'hasConflicts' => count($conflictsList) > 0
        ]);
    }

    /**
     * Check for bulk schedule conflicts
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkBulkConflicts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classroom_id' => 'required|exists:classrooms,id',
            'schedules' => 'required|array',
            'schedules.*.day' => 'required|string',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i',
            'schedules.*.teacher_id' => 'required|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'conflicts' => [],
                'errors' => $validator->errors()
            ]);
        }

        $classroom = Classroom::find($request->classroom_id);
        $conflicts = [];
        $schedules = $request->schedules;
        
        // Check for internal conflicts between submitted schedules
        for ($i = 0; $i < count($schedules); $i++) {
            for ($j = $i + 1; $j < count($schedules); $j++) {
                if ($schedules[$i]['day'] === $schedules[$j]['day']) {
                    // Check time overlap
                    $startA = strtotime($schedules[$i]['start_time']);
                    $endA = strtotime($schedules[$i]['end_time']);
                    $startB = strtotime($schedules[$j]['start_time']);
                    $endB = strtotime($schedules[$j]['end_time']);
                    
                    if (($startA < $endB) && ($startB < $endA)) {
                        $conflicts[] = [
                            'row_index' => $schedules[$j]['row_index'],
                            'message' => "Konflik waktu dengan jadwal lain pada baris " . ($schedules[$i]['row_index'] + 1)
                        ];
                    }
                }
            }
        }
        
        // Check for conflicts with existing schedules
        foreach ($schedules as $schedule) {
            $existingConflicts = Schedule::where('classroom_id', $request->classroom_id)
                ->where('day', $schedule['day'])
                ->where(function ($query) use ($schedule) {
                    $query->where(function ($q) use ($schedule) {
                        $q->where('start_time', '>=', $schedule['start_time'])
                          ->where('start_time', '<', $schedule['end_time']);
                    })->orWhere(function ($q) use ($schedule) {
                        $q->where('end_time', '>', $schedule['start_time'])
                          ->where('end_time', '<=', $schedule['end_time']);
                    })->orWhere(function ($q) use ($schedule) {
                        $q->where('start_time', '<=', $schedule['start_time'])
                          ->where('end_time', '>=', $schedule['end_time']);
                    });
                })
                ->with(['subject'])
                ->get();
                
            if ($existingConflicts->count() > 0) {
                foreach ($existingConflicts as $conflict) {
                    $conflicts[] = [
                        'row_index' => $schedule['row_index'],
                        'message' => "Konflik dengan jadwal {$conflict->subject->name} yang sudah ada pada {$conflict->day} ({$conflict->start_time} - {$conflict->end_time})"
                    ];
                }
            }
            
            // Check for teacher conflicts
            $teacherConflicts = Schedule::where('teacher_id', $schedule['teacher_id'])
                ->where('day', $schedule['day'])
                ->where('classroom_id', '!=', $request->classroom_id)
                ->where(function ($query) use ($schedule) {
                    $query->where(function ($q) use ($schedule) {
                        $q->where('start_time', '>=', $schedule['start_time'])
                          ->where('start_time', '<', $schedule['end_time']);
                    })->orWhere(function ($q) use ($schedule) {
                        $q->where('end_time', '>', $schedule['start_time'])
                          ->where('end_time', '<=', $schedule['end_time']);
                    })->orWhere(function ($q) use ($schedule) {
                        $q->where('start_time', '<=', $schedule['start_time'])
                          ->where('end_time', '>=', $schedule['end_time']);
                    });
                })
                ->with(['classroom'])
                ->get();
                
            if ($teacherConflicts->count() > 0) {
                $teacher = Teacher::find($schedule['teacher_id']);
                foreach ($teacherConflicts as $conflict) {
                    $conflicts[] = [
                        'row_index' => $schedule['row_index'],
                        'message' => "Guru {$teacher->name} sudah mengajar di kelas {$conflict->classroom->name} pada waktu tersebut ({$conflict->day}, {$conflict->start_time} - {$conflict->end_time})"
                    ];
                }
            }
        }
        
        return response()->json(['conflicts' => $conflicts]);
    }

    /**
     * Get teachers for a specific subject
     *
     * @param  int  $subjectId
     * @return \Illuminate\Http\Response
     */
    public function getTeachersBySubject($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        $teachers = Teacher::whereHas('subjects', function($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })->get(['id', 'name']);
        
        return response()->json($teachers);
    }

    /**
     * Get schedule details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        return response()->json($schedule);
    }
}
