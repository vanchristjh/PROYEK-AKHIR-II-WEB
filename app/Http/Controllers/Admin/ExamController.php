<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Display a listing of the exams.
     */
    public function index()
    {
        $exams = Exam::with(['teacher', 'subject', 'classrooms'])
            ->latest()
            ->paginate(10);
            
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get(); // Assuming 2 is the teacher role
        $classrooms = Classroom::all();
        $examTypes = ['uts' => 'UTS (Ujian Tengah Semester)', 'uas' => 'UAS (Ujian Akhir Semester)', 'daily' => 'Ujian Harian'];
        
        return view('admin.exams.create', compact('subjects', 'teachers', 'classrooms', 'examTypes'));
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'max_attempts' => 'required|integer|min:1',
            'randomize_questions' => 'boolean',
            'show_result' => 'boolean',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'exam_type' => 'required|in:uts,uas,daily',
            'classrooms' => 'required|array',
            'classrooms.*' => 'exists:classrooms,id',
        ]);

        DB::beginTransaction();
        try {
            // Create the exam
            $exam = Exam::create([
                'title' => $request->title,
                'description' => $request->description,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => $request->duration,
                'is_active' => $request->has('is_active'),
                'max_attempts' => $request->max_attempts,
                'randomize_questions' => $request->has('randomize_questions'),
                'show_result' => $request->has('show_result'),
                'passing_score' => $request->passing_score,
                'exam_type' => $request->exam_type,
            ]);

            // Attach selected classrooms
            $exam->classrooms()->attach($request->classrooms);

            DB::commit();
            return redirect()->route('admin.exams.index')
                ->with('success', 'Ujian berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified exam.
     */
    public function show(Exam $exam)
    {
        $exam->load(['teacher', 'subject', 'classrooms', 'questions.options']);
        
        return view('admin.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get(); // Assuming 2 is the teacher role
        $classrooms = Classroom::all();
        $examTypes = ['uts' => 'UTS (Ujian Tengah Semester)', 'uas' => 'UAS (Ujian Akhir Semester)', 'daily' => 'Ujian Harian'];
        
        return view('admin.exams.edit', compact('exam', 'subjects', 'teachers', 'classrooms', 'examTypes'));
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'max_attempts' => 'required|integer|min:1',
            'randomize_questions' => 'boolean',
            'show_result' => 'boolean',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'exam_type' => 'required|in:uts,uas,daily',
            'classrooms' => 'required|array',
            'classrooms.*' => 'exists:classrooms,id',
        ]);

        DB::beginTransaction();
        try {
            // Update the exam
            $exam->update([
                'title' => $request->title,
                'description' => $request->description,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration' => $request->duration,
                'is_active' => $request->has('is_active'),
                'max_attempts' => $request->max_attempts,
                'randomize_questions' => $request->has('randomize_questions'),
                'show_result' => $request->has('show_result'),
                'passing_score' => $request->passing_score,
                'exam_type' => $request->exam_type,
            ]);

            // Sync selected classrooms
            $exam->classrooms()->sync($request->classrooms);

            DB::commit();
            return redirect()->route('admin.exams.index')
                ->with('success', 'Ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        try {
            $exam->delete();
            return redirect()->route('admin.exams.index')
                ->with('success', 'Ujian berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
