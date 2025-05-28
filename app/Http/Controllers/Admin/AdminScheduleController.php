<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AdminScheduleController extends Controller
{
    /**
     * Display a listing of schedules
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {        if (!Schema::hasColumn('schedules', 'day')) {
            return view('admin.schedule.index', [
                'schedules' => collect(),
                'message' => 'Database schema perlu diperbarui. Silakan jalankan perintah "php artisan migrate" atau hubungi administrator.'
            ]);
        }

        try {
            $query = Schedule::with(['subject', 'classroom', 'teacher']);
            
            if ($request->filled('classroom')) {
                $query->where('classroom_id', $request->classroom);
            }
            
            if ($request->filled('teacher')) {
                $query->where('teacher_id', $request->teacher);
            }
            
            if ($request->filled('subject')) {
                $query->where('subject_id', $request->subject);
            }
              if ($request->filled('day')) {
                $query->where('day', $request->day);
            }
            
            $schedules = $query->orderBy('day')
                ->orderBy('start_time')
                ->paginate(15);

            $classrooms = Classroom::all();
            $subjects = Subject::all();
            $teachers = User::whereHas('roles', function($query) {
                $query->where('name', 'guru');
            })->get();
            
            $dayNames = [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Minggu'
            ];
            
            return view('admin.schedule.index', compact('schedules', 'classrooms', 'subjects', 'teachers', 'dayNames'));
        } catch (\Exception $e) {
            return view('admin.schedule.index', [
                'message' => 'Terjadi kesalahan saat mengambil data jadwal. Detail: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new schedule
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teachers = User::whereHas('roles', function($query) {
            $query->where('name', 'guru');
        })->get();
        $subjects = Subject::all();
        $classrooms = Classroom::all();
        
        return view('admin.schedule.create', compact('teachers', 'subjects', 'classrooms'));
    }

    /**
     * Store a newly created schedule
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */    public function store(Request $request)
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

        // Check for schedule conflicts
        $conflicts = Schedule::where('day', $request->day)
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_time', '<=', $request->start_time)
                        ->where('end_time', '>', $request->start_time);
                })->orWhere(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>=', $request->end_time);
                });
            })
            ->where(function($query) use ($request) {
                $query->where('classroom_id', $request->classroom_id)
                    ->orWhere('teacher_id', $request->teacher_id);
            })
            ->exists();

        if ($conflicts) {
            return redirect()->back()
                ->withErrors(['schedule' => 'There is a scheduling conflict with this time slot.'])
                ->withInput();
        }

        // Create the schedule
        $schedule = Schedule::create([
            'classroom_id' => $validated['classroom_id'],
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room' => $validated['room'] ?? null,
            'school_year' => $validated['school_year'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dibuat.');
    }

    /**
     * Show details of a specific schedule
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $schedule = Schedule::with(['subject', 'classroom', 'teacher'])->findOrFail($id);
        
        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu'
        ];
        
        return view('admin.schedule.show', compact('schedule', 'dayNames'));
    }

    /**
     * Show the form for editing a schedule
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        $teachers = User::whereHas('roles', function($query) {
            $query->where('name', 'guru');
        })->get();
        $subjects = Subject::all();
        $classrooms = Classroom::all();
        
        return view('admin.schedule.edit', compact('schedule', 'teachers', 'subjects', 'classrooms'));
    }

    /**
     * Update a schedule
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Schedule $schedule
     * @return \Illuminate\Http\Response
     */    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day' => 'required|integer|min:1|max:7',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'notes' => 'nullable|string',
        ]);

        // Convert teacher_id from User to Teacher model if possible
        $data = $request->all();
        if ($user = User::find($data['teacher_id'])) {
            if ($teacher = Teacher::where('user_id', $user->id)->first()) {
                $data['teacher_id'] = $teacher->id;
            }
        }

        $conflict = Schedule::where('id', '!=', $schedule->id)
            ->where('day', $data['day'])
            ->where(function($query) use ($data) {
                $query->where(function($q) use ($data) {
                    $q->where('start_time', '<=', $data['start_time'])
                        ->where('end_time', '>', $data['start_time']);
                })->orWhere(function($q) use ($data) {
                    $q->where('start_time', '<', $data['end_time'])
                        ->where('end_time', '>=', $data['end_time']);
                })->orWhere(function($q) use ($data) {
                    $q->where('start_time', '>=', $data['start_time'])
                        ->where('end_time', '<=', $data['end_time']);
                });
            })
            ->where(function($query) use ($data) {
                $query->where('classroom_id', $data['classroom_id'])
                    ->orWhere('teacher_id', $data['teacher_id']);
            })
            ->first();

        if ($conflict) {
            return redirect()->back()->withInput()->with('error', 'Schedule conflict detected for the selected time slot!');
        }

        $schedule->update($data);

        return redirect()->route('admin.schedule.index')
                ->with('success', 'Schedule updated successfully!');
    }

    /**
     * Delete a schedule
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('admin.schedule.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Check for schedule conflicts
     * 
     * @param int $scheduleId ID of the schedule to exclude (0 for new schedules)
     * @param int $day Day of the week
     * @param string $startTime Start time (format: HH:MM)
     * @param string $endTime End time (format: HH:MM)
     * @param int $classroomId Classroom ID
     * @param int $teacherId Teacher ID
     * @return Schedule|null Returns conflicting schedule or null if no conflict
     */
    private function getScheduleConflict($scheduleId = 0, $day, $startTime, $endTime, $classroomId, $teacherId)
    {
        $query = Schedule::where('day', $day);
        
        if ($scheduleId > 0) {
            $query->where('id', '!=', $scheduleId);
        }
        
        return $query->where(function($query) use ($startTime, $endTime) {
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
            })
            ->where(function($query) use ($classroomId, $teacherId) {
                $query->where('classroom_id', $classroomId)
                    ->orWhere('teacher_id', $teacherId);
            })
            ->first();
    }
    
    /**
     * Get detailed information about a schedule conflict
     * 
     * @param int $scheduleId ID of the schedule to exclude (0 for new schedules)
     * @param int $day Day of the week
     * @param string $startTime Start time (format: HH:MM)
     * @param string $endTime End time (format: HH:MM)
     * @param int $classroomId Classroom ID
     * @param int $teacherId Teacher ID
     * @return string|null Returns conflict message or null if no conflict
     */
    private function getScheduleConflictDetails($scheduleId = 0, $day, $startTime, $endTime, $classroomId, $teacherId)
    {
        $conflict = $this->getScheduleConflict($scheduleId, $day, $startTime, $endTime, $classroomId, $teacherId);
        
        if (!$conflict) {
            return null;
        }
        
        $conflictType = [];
        
        if ($conflict->classroom_id == $classroomId) {
            $conflictType[] = 'Classroom';
        }
        
        if ($conflict->teacher_id == $teacherId) {
            $conflictType[] = 'Teacher';
        }
        
        $subject = Subject::find($conflict->subject_id)->name ?? 'Unknown Subject';
        $teacher = User::find($conflict->teacher_id)->name ?? 'Unknown Teacher';
        $classroom = Classroom::find($conflict->classroom_id)->name ?? 'Unknown Classroom';
        
        return 'Schedule conflict detected: ' . implode(' and ', $conflictType) . 
               ' already scheduled at this time. Conflicting schedule: ' .
               $subject . ' with ' . $teacher . ' in ' . $classroom . 
               ' (' . $conflict->formatted_time . ')';
    }
}