<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SiswaScheduleController extends Controller
{
    /**
     * Display a listing of the schedules.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get current logged in student
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || !$student->classroom) {
            return view('siswa.schedule.index', [
                'schedules' => [], // Empty array instead of null
                'subjects' => Subject::all(),
                'classroom' => null,
            ]);
        }
        
        $classroom = $student->classroom;
        
        // Get available subjects for the filter
        $subjects = Subject::whereHas('classrooms', function($query) use ($classroom) {
            $query->where('classrooms.id', $classroom->id);
        })->orderBy('name')->get();
        
        // Base query with eager loading
        $query = Schedule::where('classroom_id', $classroom->id)
            ->with(['subject', 'teacher' => function($query) {
                $query->withDefault(); // Handle missing teacher gracefully
            }, 'classroom'])
            ->orderBy('day')
            ->orderBy('start_time');
        
        // Apply day filter if provided
        if ($request->has('day') && $request->day) {
            $query->where('day', $request->day);
        }
        
        // Apply subject filter if provided
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Get schedules and group by day
        $schedules = $query->get()->groupBy('day');
        
        // Create weekly schedule view for printing
        $timeSlots = Schedule::distinct()
            ->where('classroom_id', $classroom->id)
            ->orderBy('start_time')
            ->pluck('start_time')
            ->map(function ($time) {
                return substr($time, 0, 5);
            })
            ->unique()
            ->toArray();
        
        $weeklySchedule = [];
        
        foreach ($schedules as $day => $daySchedules) {
            foreach ($daySchedules as $schedule) {
                $startTime = substr($schedule->start_time, 0, 5);
                $weeklySchedule[$day][$startTime] = $schedule;
            }
        }
        
        return view('siswa.schedule.index', [
            'schedules' => $schedules,
            'subjects' => $subjects,
            'classroom' => $classroom,
            'timeSlots' => $timeSlots,
            'weeklySchedule' => $weeklySchedule,
        ]);
    }    public function show($id)
    {
        $schedule = Schedule::with(['subject', 'teacher', 'classroom'])->findOrFail($id);
        
        // Check if student belongs to this classroom
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || $student->classroom_id !== $schedule->classroom_id) {
            return abort(403, 'You do not have access to view this schedule');
        }
        
        return view('siswa.schedule.show', [
            'schedule' => $schedule
        ]);
    }
    
    /**
     * Display schedules for a specific day.
     *
     * @param  string  $day
     * @return \Illuminate\Http\Response
     */
    public function showDay($day)
    {
        // Validate day number
        if (!in_array($day, ['1', '2', '3', '4', '5', '6', '7'])) {
            return redirect()->route('siswa.schedule.index')->withErrors('Hari tidak valid.');
        }
        
        // Convert day number to name
        $dayNames = [
            '1' => 'Senin',
            '2' => 'Selasa', 
            '3' => 'Rabu', 
            '4' => 'Kamis', 
            '5' => 'Jumat', 
            '6' => 'Sabtu',
            '7' => 'Minggu'
        ];
        
        $dayName = $dayNames[$day];
        
        // Get student's classroom
        $user = Auth::user();
        $student = $user->student;
        
        // Make sure we have a classroom, even if it might be null
        $classroom = $student ? $student->classroom : null;
        
        // Get schedules for this day
        $schedules = $classroom ? Schedule::with(['subject', 'teacher'])
            ->where('classroom_id', $classroom->id)
            ->where('day', $day)
            ->orderBy('start_time')
            ->get() : collect([]);
            
        return view('siswa.schedule.day', compact('schedules', 'dayName', 'day', 'classroom'));
    }
    
    /**
     * Export schedule to iCalendar format.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportIcal()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student || !$student->classroom) {
            return back()->with('error', 'Tidak dapat mengekspor jadwal: Kelas tidak ditemukan.');
        }
        
        $classroom = $student->classroom;
        $schedules = Schedule::where('classroom_id', $classroom->id)
            ->with(['subject', 'teacher'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();
            
        // Generate iCal content (simplified version)
        $fileName = 'jadwal-kelas-' . $classroom->name . '.ics';
        $icalContent = "BEGIN:VCALENDAR\r\n";
        $icalContent .= "VERSION:2.0\r\n";
        $icalContent .= "PRODID:-//SMAN1-GIRSIP//Schedule//EN\r\n";
        $icalContent .= "CALSCALE:GREGORIAN\r\n";
        $icalContent .= "METHOD:PUBLISH\r\n";
        
        $dayMap = [
            'Senin' => 'MO',
            'Selasa' => 'TU',
            'Rabu' => 'WE',
            'Kamis' => 'TH',
            'Jumat' => 'FR',
            'Sabtu' => 'SA',
            'Minggu' => 'SU',
            '1' => 'MO',
            '2' => 'TU',
            '3' => 'WE',
            '4' => 'TH',
            '5' => 'FR',
            '6' => 'SA',
            '7' => 'SU',
        ];
        
        // Current timestamp for UID generation
        $now = Carbon::now();
        
        foreach ($schedules as $schedule) {            $subject = $schedule->subject ? $schedule->subject->name : 'Mata Pelajaran';
            $teacher = $schedule->teacher_name;
            $day = $dayMap[$schedule->day] ?? 'MO';
            
            // Format times for iCal
            $startTime = substr($schedule->start_time, 0, 5);
            $endTime = substr($schedule->end_time, 0, 5);
            
            $icalContent .= "BEGIN:VEVENT\r\n";
            $icalContent .= "UID:" . md5($schedule->id . $now) . "@sman1-girsip\r\n";
            $icalContent .= "SUMMARY:" . $subject . "\r\n";
            $icalContent .= "DESCRIPTION:Guru: " . $teacher . "\\n" . ($schedule->notes ?? '') . "\r\n";
            $icalContent .= "LOCATION:" . ($schedule->room ?? 'Ruang Kelas') . "\r\n";
            $icalContent .= "RRULE:FREQ=WEEKLY;BYDAY=" . $day . "\r\n";
            $icalContent .= "DTSTART;TZID=Asia/Jakarta:" . date('Ymd') . 'T' . str_replace(':', '', $startTime) . "00\r\n";
            $icalContent .= "DTEND;TZID=Asia/Jakarta:" . date('Ymd') . 'T' . str_replace(':', '', $endTime) . "00\r\n";
            $icalContent .= "END:VEVENT\r\n";
        }
        
        $icalContent .= "END:VCALENDAR";
        
        return response($icalContent)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Utility method to check for schedule conflicts (for API calls from frontend)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkConflicts(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'classroom_id' => 'required|exists:classrooms,id',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'conflicts' => [],
                'hasConflicts' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $classroomId = $request->classroom_id;
        $teacherId = $request->teacher_id ?? null;
        $day = $request->day;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $excludeId = $request->schedule_id; // For edit case
        
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
            ->with(['subject', 'teacher'])
            ->get();
        
        foreach ($classroomConflicts as $conflict) {
            $conflicts[] = [
                'type' => 'classroom',
                'schedule_id' => $conflict->id,
                'message' => "Konflik dengan kelas {$conflict->classroom->name}: Jadwal {$conflict->subject->name} sudah ada pada hari {$conflict->day} pukul " . 
                            substr($conflict->start_time, 0, 5) . " - " . substr($conflict->end_time, 0, 5)
            ];
        }
          // If teacher ID is provided, also check for teacher conflicts
        if ($teacherId) {
            $teacherConflicts = Schedule::where('teacher_id', $teacherId)
                ->where('day', $day)
                ->when($excludeId, function($query) use ($excludeId) {
                    return $query->where('id', '!=', $excludeId);
                })
                ->where(function($query) use ($startTime, $endTime) {
                    // Time overlaps (same logic as above)
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
                ->with(['subject', 'classroom'])
                ->get();            foreach ($teacherConflicts as $conflict) {
                $teacher = $conflict->teacher_name;
                $conflicts[] = [
                    'type' => 'teacher',
                    'schedule_id' => $conflict->id,
                    'message' => "Konflik dengan guru {$teacher} ({$conflict->teacher_id}): Sudah mengajar {$conflict->subject->name} di kelas {$conflict->classroom->name} pada hari {$conflict->day} pukul " . 
                                substr($conflict->start_time, 0, 5) . " - " . substr($conflict->end_time, 0, 5)
                ];
            }
        }
        
        return response()->json([
            'conflicts' => $conflicts,
            'hasConflicts' => !empty($conflicts)
        ]);
    }
}
