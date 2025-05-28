<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Display a listing of the quizzes for the authenticated teacher.
     */
    public function index()
    {
        $teacherId = Auth::id();
        $quizzes = Quiz::with(['subject', 'classrooms'])
            ->where('teacher_id', $teacherId)
            ->latest()
            ->paginate(10);
            
        return view('guru.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create()
    {
        $teacherId = Auth::id();
        $subjects = Subject::all();
        $classrooms = Classroom::all();
        
        return view('guru.quizzes.create', compact('subjects', 'classrooms'));
    }

    /**
     * Store a newly created quiz in storage.
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
            'max_attempts' => 'required|integer|min:1',
            'randomize_questions' => 'boolean',
            'show_result' => 'boolean',
            'passing_score' => 'nullable|numeric|min:0|max:100',
            'classroom_id' => 'required|array',
            'classroom_id.*' => 'exists:classrooms,id'
        ], [
            'title.required' => 'Judul kuis harus diisi',
            'subject_id.required' => 'Mata pelajaran harus dipilih', 
            'start_time.required' => 'Waktu mulai harus diisi',
            'end_time.required' => 'Waktu berakhir harus diisi',
            'end_time.after' => 'Waktu berakhir harus setelah waktu mulai',
            'duration.required' => 'Durasi kuis harus diisi',
            'duration.min' => 'Durasi minimum 1 menit',
            'max_attempts.required' => 'Jumlah percobaan harus diisi',
            'max_attempts.min' => 'Minimal 1 kali percobaan',
            'classroom_id.required' => 'Minimal satu kelas harus dipilih',
        ]);

        DB::beginTransaction();
        try {
            // Create the quiz
            $quiz = Quiz::create([
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
            ]);

            // Attach selected classrooms
            $quiz->classrooms()->attach($request->classroom_id);

            DB::commit();
            return redirect()->route('guru.quizzes.questions.create', $quiz)
                ->with('success', 'Kuis berhasil dibuat. Silakan tambahkan pertanyaan untuk kuis ini.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified quiz.
     */
    public function show(Quiz $quiz)
    {
        $this->authorizeOwnership($quiz);
        $quiz->load(['subject', 'classrooms', 'questions.options']);
        
        return view('guru.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified quiz.
     */
    public function edit(Quiz $quiz)
    {
        $this->authorizeOwnership($quiz);
        $subjects = Subject::all();
        $classrooms = Classroom::all();
        
        return view('guru.quizzes.edit', compact('quiz', 'subjects', 'classrooms'));
    }

    /**
     * Update the specified quiz in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $this->authorizeOwnership($quiz);
        
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
            'classrooms' => 'required|array',
            'classrooms.*' => 'exists:classrooms,id',
        ]);

        DB::beginTransaction();
        try {
            // Update the quiz
            $quiz->update([
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
            ]);

            // Sync selected classrooms
            $quiz->classrooms()->sync($request->classrooms);

            DB::commit();
            return redirect()->route('guru.quizzes.index')
                ->with('success', 'Kuis berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified quiz from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $this->authorizeOwnership($quiz);
        
        try {
            $quiz->delete();
            return redirect()->route('guru.quizzes.index')
                ->with('success', 'Kuis berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the questions for a quiz.
     */
    public function questions(Quiz $quiz)
    {
        $this->authorizeOwnership($quiz);
        $quiz->load('questions.options');
        
        return view('guru.questions.quiz', compact('quiz'));
    }
    
    /**
     * Show the form for creating a new question for a quiz.
     */
    public function createQuestion(Quiz $quiz)
    {
        $this->authorizeOwnership($quiz);
        $questionTypes = ['multiple_choice' => 'Pilihan Ganda', 'true_false' => 'Benar/Salah', 'essay' => 'Essay'];
        
        return view('guru.questions.create', compact('quiz', 'questionTypes'));
    }
    
    /**
     * View quiz results and student attempts.
     */
    public function results(Quiz $quiz)
    {
        $this->authorizeOwnership($quiz);
        $quiz->load('attempts.student', 'questions');
        
        $attempts = QuizAttempt::with(['student', 'answers'])
            ->where('quiz_id', $quiz->id)
            ->latest()
            ->paginate(20);
            
        return view('guru.quizzes.results', compact('quiz', 'attempts'));
    }
    
    /**
     * View a specific student's quiz attempt.
     */
    public function viewAttempt(Quiz $quiz, QuizAttempt $attempt)
    {
        $this->authorizeOwnership($quiz);
        
        if ($attempt->quiz_id !== $quiz->id) {
            return redirect()->route('guru.quizzes.results', $quiz)
                ->with('error', 'Percobaan kuis tidak ditemukan.');
        }
        
        $attempt->load(['student', 'answers.question.options']);
        $quiz->load('questions.options');
        
        return view('guru.quizzes.view-attempt', compact('quiz', 'attempt'));
    }
    
    /**
     * Grade an essay question in a student's quiz attempt.
     */
    public function gradeEssay(Request $request, Quiz $quiz, QuizAttempt $attempt, StudentAnswer $answer)
    {
        $this->authorizeOwnership($quiz);
        
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
            $totalPossiblePoints = $quiz->questions->sum('points');
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
    private function checkIfAllQuestionsGraded(QuizAttempt $attempt)
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
     * Ensure the teacher owns the quiz.
     */
    private function authorizeOwnership(Quiz $quiz)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kuis ini.');
        }
    }
}
