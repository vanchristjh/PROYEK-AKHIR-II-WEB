<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendances for the authenticated student.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentIndex()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found'
            ], 404);
        }
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        return $this->studentMonth($currentMonth, $currentYear);
    }
    
    /**
     * Display a listing of attendances for a specific month and year.
     *
     * @param  int  $month
     * @param  int  $year
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentMonth($month, $year)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found'
            ], 404);
        }
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $attendances = Attendance::where('student_id', $student->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['subject'])
            ->orderBy('date')
            ->get()
            ->groupBy(function ($attendance) {
                return Carbon::parse($attendance->date)->format('Y-m-d');
            })
            ->map(function ($dayAttendances, $date) {
                return [
                    'date' => $date,
                    'day_name' => Carbon::parse($date)->format('l'),
                    'subjects' => $dayAttendances->map(function ($attendance) {
                        return [
                            'id' => $attendance->id,
                            'subject' => $attendance->subject->name,
                            'status' => $attendance->status,
                            'notes' => $attendance->notes
                        ];
                    })
                ];
            })
            ->values();
        
        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'year' => $year,
                'attendances' => $attendances
            ]
        ]);
    }
}
