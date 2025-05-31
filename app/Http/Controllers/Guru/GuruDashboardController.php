<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Subject;
use App\Models\Submission;
use App\Models\Schedule;
use App\Models\Announcement;

class GuruDashboardController extends Controller
{
    /**
     * Display the guru dashboard.
     *
     * @return \Illuminate\Http\Response
     */    
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        // Get all assignments for the teacher
        $assignments = Assignment::where('teacher_id', $user->id)->get();
        
        // Get upcoming assignments
        try {
            // Add error handling around Carbon date operations
            $upcomingAssignments = Assignment::where('teacher_id', $user->id)
                ->where('deadline', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                ->count();
        } catch (\Exception $e) {
            // Fallback if date parsing fails
            $upcomingAssignments = 0;
        }
        
        // Get subjects taught by the teacher - fixing the query and variable name
        $subjects = $user->subjects()->count();
        
        // Get student count for the teacher's homeroom class
        $homeroomStudentsCount = User::whereHas('classroom', function ($query) use ($user) {
            $query->where('homeroom_teacher_id', $user->id);
        })->where('role_id', 3)->count();
        
        // Get all students that are taught by the teacher
        $studentsCount = User::whereHas('classroom', function ($query) use ($user) {
            $query->whereIn('id', function ($subquery) use ($user) {
                $subquery->select('classroom_id')
                    ->from('schedules')
                    ->where('teacher_id', $user->id);
            });
        })->where('role_id', 3)->count();
        
        // Get recent submissions
        $recentSubmissions = Submission::whereHas('assignment', function ($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })->orderBy('submitted_at', 'desc')->take(5)->get();
        
        // Get today's schedule
        try {
            $day = Carbon::now()->format('l'); // Get current day name
            $today = Carbon::now()->locale('id')->isoFormat('dddd'); // Get localized day name
        } catch (\Exception $e) {
            // Fallback if date parsing fails
            $day = date('l'); // Fallback to PHP's date function
            $today = date('l'); // Using English day name as fallback
        }
        
        $todaySchedules = Schedule::where('teacher_id', $user->id)
            ->where('day', $day)
            ->orderBy('start_time', 'asc')
            ->with(['subject', 'classroom'])
            ->get();
        
        // Get important announcements
        try {
            $importantAnnouncements = Announcement::where('is_important', true)
                ->where('publish_date', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                ->where(function($query) {
                    $query->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>', Carbon::now()->format('Y-m-d H:i:s'));
                })
                ->where(function($query) use ($user) {
                    $query->where('audience', 'all')
                        ->orWhere('audience', 'guru');
                })
                ->where('publish_date', '>=', Carbon::now()->subWeek()->format('Y-m-d H:i:s'))
                ->count();
        } catch (\Exception $e) {
            // Fallback if date parsing fails
            $importantAnnouncements = 0;
        }
        
        return view('guru.dashboard', compact(
            'assignments',
            'upcomingAssignments',
            'subjects',
            'homeroomStudentsCount',
            'studentsCount',
            'recentSubmissions',
            'todaySchedules',
            'today',
            'importantAnnouncements'
        ));
    }
    
    /**
     * Refresh dashboard data.
     *
     * @return \Illuminate\Http\Response
     */
    public function refresh()
    {
        $user = Auth::user();
        
        // Count upcoming assignments (those with deadlines in the future)
        $upcomingAssignments = $user->assignments()
            ->where('deadline', '>=', Carbon::now())
            ->count();
        
        // Count the number of subjects taught by this teacher
        $subjectsCount = $user->subjects()->count();
        
        // Get students count
        $homeroomStudents = User::whereHas('classroom', function($query) use ($user) {
            $query->where('homeroom_teacher_id', $user->id);
        })->where('role_id', 3)->count();
        
        $teachingStudents = User::whereHas('classroom', function($query) use ($user) {
            $query->whereIn('id', function($subquery) use ($user) {
                $subquery->select('classroom_id')
                        ->from('schedules')
                        ->where('teacher_id', $user->id);
            });
        })->where('role_id', 3)->count();
        
        $studentsCount = $homeroomStudents + $teachingStudents;
        
        // Get pending grading count
        $pendingGradingCount = \App\Models\Submission::whereHas('assignment', function($query) use ($user) {
            $query->where('teacher_id', $user->id);
        })
        ->whereNotNull('submitted_at')
        ->whereNull('score')
        ->count();
        
        // Get recent submissions
        $recentSubmissions = \App\Models\Submission::with(['student', 'assignment.subject'])
            ->whereHas('assignment', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })
            ->latest('submitted_at')
            ->take(5)
            ->get()
            ->map(function($submission) {
                return [
                    'id' => $submission->id,
                    'student' => [
                        'name' => $submission->student->name,
                        'id' => $submission->student->id
                    ],
                    'assignment' => [
                        'id' => $submission->assignment->id,
                        'title' => $submission->assignment->title
                    ],                    'score' => $submission->score,
                    'submitted_at' => $submission->submitted_at,                    'time_ago' => $submission->submitted_at ? $submission->submitted_at->diffForHumans() : 'Belum dikumpulkan',
                    'detail_url' => route('guru.assignments.submissions.show', ['assignment' => $submission->assignment_id, 'submission' => $submission->id])
                ];
            });
        
        // Get today's schedule with status
        try {
            $todayIndonesian = now()->locale('id')->dayName;
        } catch (\Exception $e) {
            $todayIndonesian = date('l'); // Fallback to English day
        }
        
        $schedules = \App\Models\Schedule::with(['subject', 'classroom'])
            ->where('teacher_id', $user->id)
            ->where('day', $todayIndonesian)
            ->orderBy('start_time')
            ->get()
            ->map(function($schedule) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time);
                    $end = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time);
                    $currentTime = \Carbon\Carbon::createFromFormat('H:i:s', now()->format('H:i:s'));
                    
                    $isOngoing = $currentTime->between($start, $end);
                    $isUpcoming = $currentTime->lt($start);
                    $isPast = $currentTime->gt($end);
                } catch (\Exception $e) {
                    $isOngoing = false;
                    $isUpcoming = false;
                    $isPast = false;
                }
                
                return [
                    'id' => $schedule->id,
                    'subject' => [
                        'id' => $schedule->subject->id,
                        'name' => $schedule->subject->name
                    ],
                    'classroom' => [
                        'id' => $schedule->classroom->id,
                        'name' => $schedule->classroom->name
                    ],
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'start_time_formatted' => $schedule->start_time ? substr($schedule->start_time, 0, 5) : '',                    'end_time_formatted' => $schedule->end_time ? substr($schedule->end_time, 0, 5) : '',
                    'room' => $schedule->room,
                    'isOngoing' => $isOngoing,
                    'isUpcoming' => $isUpcoming,
                    'isPast' => $isPast
                ];
            });
        
        // Final response
        return response()->json([
            'success' => true,
            'stats' => [
                'subjects' => $subjectsCount,
                'students' => $studentsCount,
                'assignments' => $user->assignments()->count(),
                'upcoming' => $upcomingAssignments,
                'pendingGrading' => $pendingGradingCount
            ],
            'recentSubmissions' => $recentSubmissions,
            'todaySchedule' => $schedules
        ]);
    }
}
