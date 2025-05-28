<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Display a listing of the exams for the authenticated teacher.
     */
    public function index()
    {
        $teacherId = Auth::id();
        $exams = Exam::with(['subject', 'classrooms'])
            ->where('teacher_id', $teacherId)
            ->latest()
            ->paginate(10);
            
        return view('guru.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        $teacherId = Auth::id();
        $subjects = Subject::all();
        $classrooms = Classroom::all();
        $examTypes = ['uts' => 'UTS (Ujian Tengah Semester)', 'uas' => 'UAS (Ujian Akhir Semester)', 'daily' => 'Ujian Harian'];
        
        return view('guru.exams.create', compact('subjects', 'classrooms', 'examTypes'));
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $teacherId = Auth::id();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
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
                'teacher_id' => $teacherId,
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
            return redirect()->route('guru.exams.index')
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
        $this->authorizeOwnership($exam);
        $exam->load(['subject', 'classrooms', 'questions.options']);
        
        return view('guru.exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        $this->authorizeOwnership($exam);
        $subjects = Subject::all();
        $classrooms = Classroom::all();
        $examTypes = ['uts' => 'UTS (Ujian Tengah Semester)', 'uas' => 'UAS (Ujian Akhir Semester)', 'daily' => 'Ujian Harian'];
        
        return view('guru.exams.edit', compact('exam', 'subjects', 'classrooms', 'examTypes'));
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $this->authorizeOwnership($exam);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
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
            return redirect()->route('guru.exams.index')
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
        $this->authorizeOwnership($exam);
        
        try {
            $exam->delete();
            return redirect()->route('guru.exams.index')
                ->with('success', 'Ujian berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the questions for an exam.
     */
    public function questions(Exam $exam)
    {
        $this->authorizeOwnership($exam);
        $exam->load('questions.options');
        
        return view('guru.questions.exam', compact('exam'));
    }
    
    /**
     * Show the form for creating a new question for an exam.
     */
    public function createQuestion(Exam $exam)
    {
        $this->authorizeOwnership($exam);
        $questionTypes = ['multiple_choice' => 'Pilihan Ganda', 'true_false' => 'Benar/Salah', 'essay' => 'Essay'];
        
        return view('guru.questions.create', compact('exam', 'questionTypes'));
    }
    
    /**
     * View exam results and student attempts.
     */
    public function results(Exam $exam)
    {
        $this->authorizeOwnership($exam);
        $exam->load('attempts.student', 'questions');
        
        $attempts = ExamAttempt::with(['student', 'answers'])
            ->where('exam_id', $exam->id)
            ->latest()
            ->paginate(20);
            
        return view('guru.exams.results', compact('exam', 'attempts'));
    }
    
    /**
     * View a specific student's exam attempt.
     */
    public function viewAttempt(Exam $exam, ExamAttempt $attempt)
    {
        $this->authorizeOwnership($exam);
        
        if ($attempt->exam_id !== $exam->id) {
            return redirect()->route('guru.exams.results', $exam)
                ->with('error', 'Percobaan ujian tidak ditemukan.');
        }
        
        $attempt->load(['student', 'answers.question.options']);
        $exam->load('questions.options');
        
        return view('guru.exams.view-attempt', compact('exam', 'attempt'));
    }
    
    /**
     * Grade an essay question in a student's exam attempt.
     */
    public function gradeEssay(Request $request, Exam $exam, ExamAttempt $attempt, StudentAnswer $answer)
    {
        $this->authorizeOwnership($exam);
        
        $request->validate([
            'points_earned' => 'required|numeric|min:0',
            'teacher_comment' => 'nullable|string',
        ]);
        
        try {
            $answer->update([
                'points_earned' => $request->points_earned,
                'teacher_comment' => $request->teacher_comment,
                'is_graded' => true,
            ]);
            
            // Recalculate the total score for the attempt
            $totalPointsEarned = $attempt->answers()->sum('points_earned');
            $totalPossiblePoints = $exam->questions->sum('points');
            $score = $totalPossiblePoints > 0 ? ($totalPointsEarned / $totalPossiblePoints) * 100 : 0;
            
            $attempt->update([
                'score' => $score,
                'is_graded' => $this->checkIfAllQuestionsGraded($attempt),
            ]);
            
            return redirect()->back()
                ->with('success', 'Jawaban essay berhasil dinilai.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if all essay questions in an attempt have been graded.
     */
    private function checkIfAllQuestionsGraded(ExamAttempt $attempt)
    {
        $ungraded = $attempt->answers()
            ->whereHas('question', function ($query) {
                $query->where('question_type', 'essay');
            })
            ->where('is_graded', false)
            ->count();
            
        return $ungraded === 0;
    }
    
    /**
     * Ensure the teacher owns the exam.
     */
    private function authorizeOwnership(Exam $exam)
    {
        if ($exam->teacher_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke ujian ini.');
        }
    }
}
