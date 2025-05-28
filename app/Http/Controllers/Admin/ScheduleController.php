<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $classroomFilter = $request->input('classroom');
        $dayFilter = $request->input('day');

        $query = Schedule::query()
            ->orderBy('day', 'asc')
            ->orderBy('start_time', 'asc');

        if ($classroomFilter) {
            $query->where('classroom_id', $classroomFilter);
        }

        if ($dayFilter) {
            $query->where('day', $dayFilter);
        }

        $schedules = $query->paginate(15);
        $classrooms = Classroom::orderBy('name')->get();
        $days = $this->getDays();

        return view('admin.schedule.index', compact('schedules', 'classrooms', 'days', 'classroomFilter', 'dayFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classrooms = Classroom::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        // Get teachers from both Teacher model and User model with teacher role
        $teachersFromModel = Teacher::all();
        
        // If we have no teachers in the Teacher model, get them from the User model
        $teachers = $teachersFromModel;
        if ($teachers->isEmpty()) {
            // Find the teacher role ID
            $teacherRole = Role::where('name', 'teacher')->orWhere('name', 'like', '%guru%')->first();
            
            if ($teacherRole) {
                $teacherUsers = User::where('role_id', $teacherRole->id)->get();
                
                if ($teacherUsers->isNotEmpty()) {
                    // Convert User objects to teacher-like objects
                    $teachers = $teacherUsers;
                }
            }
        }
        
        $days = $this->getDays();

        return view('admin.schedule.create', compact('classrooms', 'subjects', 'teachers', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     */ 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string|max:255',
            'school_year' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        \Log::info('Creating new schedule', [
            'classroom_id' => $validated['classroom_id'],
            'teacher_id' => $validated['teacher_id'],
            'teacher_type' => Teacher::find($validated['teacher_id']) ? 'Teacher Model' : (User::find($validated['teacher_id']) ? 'User Model' : 'Unknown'),
        ]);

        // Verify teacher exists either in Teacher model or User model
        $teacherExists = Teacher::where('id', $validated['teacher_id'])->exists() || 
                        User::where('id', $validated['teacher_id'])->exists();
        
        if (!$teacherExists) {
            \Log::warning('Invalid teacher ID provided', ['teacher_id' => $validated['teacher_id']]);
            return back()->withInput()->with('error', 'ID Guru tidak valid.');
        }

        // Check for schedule conflicts
        $conflicts = $this->checkConflicts(
            $validated['classroom_id'],
            $validated['teacher_id'],
            $validated['day'],
            $validated['start_time'],
            $validated['end_time']
        );
        
        if (!empty($conflicts['conflicts'])) {
            return back()->withInput()->with('error', 'Terdapat konflik jadwal: ' . implode(', ', $conflicts['conflicts']));
        }

        try {
            $validated['created_by'] = Auth::id();
            
            // Ensure we have a school year
            if (empty($validated['school_year'])) {
                $validated['school_year'] = date('Y').'/'.((int)date('Y')+1); 
            }
            
            $schedule = Schedule::create($validated);
            
            \Log::info('Schedule created successfully', [
                'schedule_id' => $schedule->id,
                'classroom_id' => $schedule->classroom_id,
                'teacher_id' => $schedule->teacher_id,
            ]);

            return redirect()->route('admin.schedule.index')
                ->with('success', 'Jadwal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $classrooms = Classroom::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        
        // Get teachers from both Teacher model and User model with teacher role
        $teachersFromModel = Teacher::all();
        
        // If we have no teachers in the Teacher model, get them from the User model
        $teachers = $teachersFromModel;
        if ($teachers->isEmpty()) {
            // Find the teacher role ID
            $teacherRole = Role::where('name', 'teacher')->orWhere('name', 'like', '%guru%')->first();
            
            if ($teacherRole) {
                $teacherUsers = User::where('role_id', $teacherRole->id)->get();
                
                if ($teacherUsers->isNotEmpty()) {
                    // Use the Users as teachers
                    $teachers = $teacherUsers;
                }
            }
        }
        
        // Make sure we load the current teacher even if it's from User model
        if (!$schedule->teacher && $schedule->teacher_id) {
            $user = User::find($schedule->teacher_id);
            if ($user) {
                // Add this user to the teachers collection if not already there
                $userExists = $teachers->contains(function ($teacher) use ($user) {
                    return $teacher->id == $user->id;
                });
                
                if (!$userExists) {
                    $teachers->push($user);
                }
            }
        }
        
        $days = $this->getDays();

        return view('admin.schedule.edit', compact('schedule', 'classrooms', 'subjects', 'teachers', 'days'));
    }

    /**
     * Update the specified resource in storage.
     */    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string|max:255',
            'school_year' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Verify teacher exists either in Teacher model or User model
        $teacherExists = Teacher::where('id', $validated['teacher_id'])->exists() || 
                         User::where('id', $validated['teacher_id'])->exists();
        
        if (!$teacherExists) {
            return back()->withInput()->with('error', 'ID Guru tidak valid.');
        }
        
        // Check for schedule conflicts excluding current schedule
        $conflicts = $this->checkConflicts(
            $request->classroom_id,
            $request->teacher_id,
            $request->day,
            $request->start_time,
            $request->end_time,
            $schedule->id
        );
        
        if (!empty($conflicts['conflicts'])) {
            $conflictMessages = [];
            foreach ($conflicts['conflicts'] as $conflict) {
                $conflictMessages[] = $conflict['message'];
            }
            
            return back()->withInput()->with('error', 'Terdapat konflik jadwal: ' . implode(', ', $conflictMessages));
        }

        try {
            $schedule->update($validated);
            return redirect()->route('admin.schedule.index')->with('success', 'Jadwal berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();
            return redirect()->route('admin.schedule.index')->with('success', 'Jadwal berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
      /**
     * Check for schedule conflicts
     * 
     * @param mixed $classroomId The classroom ID
     * @param mixed $teacherId The teacher ID
     * @param mixed $day The day of the schedule
     * @param mixed $startTime The start time of the schedule
     * @param mixed $endTime The end time of the schedule
     * @param mixed $excludeId Optional schedule ID to exclude from conflict check (for updates)
     * @return array Array with conflicts information
     */
    public function checkConflicts($classroomId = null, $teacherId = null, $day = null, $startTime = null, $endTime = null, $excludeId = null)
    {
        // If this was called as an API endpoint with a Request
        if ($classroomId instanceof Request) {
            $request = $classroomId;
            $classroomId = $request->classroom_id;
            $teacherId = $request->teacher_id;
            $day = $request->day;
            $startTime = $request->start_time;
            $endTime = $request->end_time;
            $excludeId = $request->schedule_id; // For edit case
            
            $isApiCall = true;
        } else {
            $isApiCall = false;
        }
        
        $conflicts = [];
        
        // Check classroom schedule conflicts (same classroom, same day, overlapping time)
        $classroomConflicts = Schedule::where('classroom_id', $classroomId)
            ->where('day', $day)
            ->when($excludeId, function($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->where(function($query) use ($startTime, $endTime) {
                // Time overlaps
                $query->where(function($q) use ($startTime, $endTime) {
                    // New schedule starts during existing schedule
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    // New schedule ends during existing schedule
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    // New schedule contains existing schedule
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->get();
        
        foreach ($classroomConflicts as $conflict) {
            $conflicts[] = [
                'type' => 'classroom',
                'message' => "Konflik dengan kelas {$conflict->classroom->name}: Jadwal sudah ada pada hari {$conflict->day} pukul " . 
                             substr($conflict->start_time, 0, 5) . " - " . substr($conflict->end_time, 0, 5)
            ];
        }
        
        // Check teacher schedule conflicts, accounting for both User and Teacher IDs
        $teacherQuery = Schedule::where('day', $day)
            ->when($excludeId, function($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            });

        // Check conflicts for either a User teacher or a Teacher model
        $teacherQuery->where(function($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
            
            // If the teacherId corresponds to a User, also check for any Teacher model with that user_id
            if ($teacherUser = User::find($teacherId)) {
                if ($teacher = Teacher::where('user_id', $teacherUser->id)->first()) {
                    $query->orWhere('teacher_id', $teacher->id);
                }
            }
            // If the teacherId corresponds to a Teacher model, also check for the associated User
            elseif ($teacher = Teacher::find($teacherId)) {
                if ($teacher->user_id) {
                    $query->orWhere('teacher_id', $teacher->user_id);
                }
            }
        });
            
        $teacherConflicts = $teacherQuery->with(['subject', 'classroom'])->get();
        
        foreach ($teacherConflicts as $conflict) {
            $teacherName = $conflict->teacher_name;
            $conflicts[] = [
                'type' => 'teacher',
                'message' => "Konflik dengan guru {$teacherName}: Sudah mengajar di kelas {$conflict->classroom->name} pada hari {$conflict->day} pukul " . 
                             substr($conflict->start_time, 0, 5) . " - " . substr($conflict->end_time, 0, 5)
            ];
        }
        
        $result = [
            'conflicts' => $conflicts,
            'hasConflicts' => !empty($conflicts)
        ];
        
        // If this was called via API, return a JSON response
        if ($isApiCall) {
            return response()->json($result);
        }
        
        // Otherwise return an array for internal use
        return $result;
    }

    /**
     * Helper method to check if two time ranges overlap
     */
    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        return ($start1 < $end2 && $end1 > $start2);
    }
    
    /**
     * Get days of the week.
     */
    private function getDays()
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    }
    
    /**
     * API endpoint to check for schedule conflicts
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkConflictsApi(Request $request)
    {
        return $this->checkConflicts($request);
    }
}
