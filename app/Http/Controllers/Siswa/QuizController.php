<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * Display a listing of available quizzes for the student.
     */
    public function index()
    {
        $user = auth()->user();
        $classroomIds = $user->classrooms()->pluck('classrooms.id');
        
        $quizzes = Quiz::whereHas('classrooms', function($query) use ($classroomIds) {
            $query->whereIn('classroom_id', $classroomIds);
        })
        ->where(function($query) {
            $query->where('start_time', '<=', now())
                  ->where(function($q) {
                      $q->whereNull('end_time')
                        ->orWhere('end_time', '>=', now());
                  });
        })
        ->orderBy('start_time')
        ->paginate(10);
        
        return view('siswa.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the quiz details and attempt status.
     */
    public function show(Quiz $quiz)
    {
        $student = Auth::user();
        $studentClassroomIds = $student->classrooms()->pluck('classrooms.id');
        
        // Check if this quiz is assigned to the student's classroom
        if (!$quiz->classrooms()->whereIn('classrooms.id', $studentClassroomIds)->exists()) {
            return redirect()->route('siswa.quizzes.index')
                ->with('error', 'Kuis ini tidak tersedia untuk kelas Anda.');
        }
        
        // Get previous attempts by this student
        $attempts = QuizAttempt::where('student_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->latest()
            ->get();
            
        $attemptsCount = $attempts->count();
        $lastAttempt = $attempts->first();
        $canAttempt = $attemptsCount < $quiz->max_attempts && $quiz->is_available;
        
        return view('siswa.quizzes.show', compact('quiz', 'attempts', 'attemptsCount', 'lastAttempt', 'canAttempt'));
    }

    /**
     * Start a new quiz attempt.
     */
    public function start(Quiz $quiz)
    {
        $student = Auth::user();
        $studentClassroomIds = $student->classrooms()->pluck('classrooms.id');
        
        // Check if this quiz is assigned to the student's classroom
        if (!$quiz->classrooms()->whereIn('classrooms.id', $studentClassroomIds)->exists()) {
            return redirect()->route('siswa.quizzes.index')
                ->with('error', 'Kuis ini tidak tersedia untuk kelas Anda.');
        }
        
        // Check if the quiz is available
        if (!$quiz->is_available) {
            return redirect()->route('siswa.quizzes.show', $quiz)
                ->with('error', 'Kuis ini tidak tersedia saat ini.');
        }
        
        // Check if the student has reached the maximum number of attempts
        $attemptsCount = QuizAttempt::where('student_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->count();
            
        if ($attemptsCount >= $quiz->max_attempts) {
            return redirect()->route('siswa.quizzes.show', $quiz)
                ->with('error', 'Anda telah mencapai jumlah maksimum percobaan untuk kuis ini.');
        }
        
        // Check if the student already has an ongoing attempt
        $ongoingAttempt = QuizAttempt::where('student_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->where('is_submitted', false)
            ->first();
            
        if ($ongoingAttempt) {
            // If there's an ongoing attempt, continue it
            $timeLeft = Carbon::parse($ongoingAttempt->end_time)->diffInSeconds(now(), false);
            
            if ($timeLeft <= 0) {
                // If time has expired, auto-submit the attempt
                $this->submitTimeExpired($ongoingAttempt);
                
                return redirect()->route('siswa.quizzes.show', $quiz)
                    ->with('warning', 'Waktu percobaan sebelumnya telah habis dan telah otomatis dikumpulkan.');
            }
            
            $quiz->load(['questions' => function($query) use ($quiz) {
                if ($quiz->randomize_questions) {
                    $query->inRandomOrder();
                }
            }, 'questions.options' => function($query) {
                $query->inRandomOrder();
            }]);
            
            return view('siswa.quizzes.take', compact('quiz', 'ongoingAttempt', 'timeLeft'));
        }
        
        // Create a new attempt
        DB::beginTransaction();
        try {
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'start_time' => now(),
                'end_time' => now()->addMinutes($quiz->duration),
                'is_submitted' => false,
            ]);
            
            DB::commit();
            
            $timeLeft = $quiz->duration * 60; // Convert to seconds
            
            $quiz->load(['questions' => function($query) use ($quiz) {
                if ($quiz->randomize_questions) {
                    $query->inRandomOrder();
                }
            }, 'questions.options' => function($query) {
                $query->inRandomOrder();
            }]);
            
            return view('siswa.quizzes.take', compact('quiz', 'attempt', 'timeLeft'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('siswa.quizzes.show', $quiz)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Save answers during a quiz attempt.
     */
    public function saveAnswer(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        // Validate that this attempt belongs to the authenticated student
        if ($attempt->student_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Validate that the attempt is for the specified quiz
        if ($attempt->quiz_id !== $quiz->id) {
            return response()->json(['error' => 'Invalid quiz attempt'], 400);
        }
        
        // Validate that the attempt is not yet submitted
        if ($attempt->is_submitted) {
            return response()->json(['error' => 'Quiz already submitted'], 400);
        }
        
        // Validate the request
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_type' => 'required|in:option,text',
            'option_id' => 'required_if:answer_type,option|nullable|exists:options,id',
            'text_answer' => 'required_if:answer_type,text|nullable|string',
        ]);
        
        $question = Question::findOrFail($request->question_id);
        
        // Check that the question belongs to the quiz
        if ($question->quiz_id !== $quiz->id) {
            return response()->json(['error' => 'Question does not belong to this quiz'], 400);
        }
        
        try {
            // Check if an answer already exists for this question and attempt
            $existingAnswer = StudentAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->first();
                
            $data = [
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $question->id,
            ];
            
            if ($request->answer_type === 'option') {
                $option = Option::findOrFail($request->option_id);
                
                // Check that the option belongs to the question
                if ($option->question_id !== $question->id) {
                    return response()->json(['error' => 'Option does not belong to this question'], 400);
                }
                
                $data['option_id'] = $option->id;
                $data['text_answer'] = null;
                
                // Auto-grade for multiple choice questions
                if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
                    $data['points_earned'] = $option->is_correct ? $question->points : 0;
                    $data['is_graded'] = true;
                }
            } else {
                $data['option_id'] = null;
                $data['text_answer'] = $request->text_answer;
                
                // Essay questions need to be graded manually
                $data['points_earned'] = 0;
                $data['is_graded'] = false;
            }
            
            if ($existingAnswer) {
                $existingAnswer->update($data);
                $answer = $existingAnswer;
            } else {
                $answer = StudentAnswer::create($data);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan',
                'answer' => $answer,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a quiz attempt.
     */
    public function submit(Request $request, Quiz $quiz, QuizAttempt $attempt)
    {
        // Validate that this attempt belongs to the authenticated student
        if ($attempt->student_id !== Auth::id()) {
            return redirect()->route('siswa.quizzes.show', $quiz)
                ->with('error', 'Anda tidak memiliki akses ke percobaan ini.');
        }
        
        // Validate that the attempt is for the specified quiz
        if ($attempt->quiz_id !== $quiz->id) {
            return redirect()->route('siswa.quizzes.index')
                ->with('error', 'Percobaan tidak valid.');
        }
        
        // Validate that the attempt is not yet submitted
        if ($attempt->is_submitted) {
            return redirect()->route('siswa.quizzes.result', ['quiz' => $quiz->id, 'attempt' => $attempt->id])
                ->with('warning', 'Kuis sudah dikumpulkan sebelumnya.');
        }
        
        DB::beginTransaction();
        try {
            // Load all questions and the student's answers
            $questions = Question::where('quiz_id', $quiz->id)->get();
            $answers = StudentAnswer::where('quiz_attempt_id', $attempt->id)->get();
            
            $answeredQuestionIds = $answers->pluck('question_id')->toArray();
            
            // For each unanswered question, create a blank answer
            foreach ($questions as $question) {
                if (!in_array($question->id, $answeredQuestionIds)) {
                    $data = [
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'option_id' => null,
                        'text_answer' => null,
                        'points_earned' => 0,
                    ];
                    
                    // Multiple choice and true/false questions are auto-graded
                    if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
                        $data['is_graded'] = true;
                    } else {
                        // Essay questions need to be graded manually
                        $data['is_graded'] = false;
                    }
                    
                    StudentAnswer::create($data);
                }
            }
            
            // Calculate the score for auto-graded questions
            $totalPointsEarned = StudentAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('is_graded', true)
                ->sum('points_earned');
                
            $totalPossiblePoints = $questions->sum('points');
            
            // Check if there are any essay questions that need to be graded
            $hasUngraded = StudentAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('is_graded', false)
                ->exists();
                
            // Update the attempt with the score so far
            $score = $totalPossiblePoints > 0 ? ($totalPointsEarned / $totalPossiblePoints) * 100 : 0;
            
            $attempt->update([
                'is_submitted' => true,
                'submit_time' => now(),
                'score' => $score,
                'is_graded' => !$hasUngraded,
            ]);
            
            DB::commit();
            
            return redirect()->route('siswa.quizzes.result', ['quiz' => $quiz->id, 'attempt' => $attempt->id])
                ->with('success', 'Kuis berhasil dikumpulkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Auto-submit a quiz attempt when time expires.
     */
    private function submitTimeExpired(QuizAttempt $attempt)
    {
        DB::beginTransaction();
        try {
            $quiz = Quiz::findOrFail($attempt->quiz_id);
            
            // Load all questions and the student's answers
            $questions = Question::where('quiz_id', $quiz->id)->get();
            $answers = StudentAnswer::where('quiz_attempt_id', $attempt->id)->get();
            
            $answeredQuestionIds = $answers->pluck('question_id')->toArray();
            
            // For each unanswered question, create a blank answer
            foreach ($questions as $question) {
                if (!in_array($question->id, $answeredQuestionIds)) {
                    $data = [
                        'quiz_attempt_id' => $attempt->id,
                        'question_id' => $question->id,
                        'option_id' => null,
                        'text_answer' => null,
                        'points_earned' => 0,
                    ];
                    
                    // Multiple choice and true/false questions are auto-graded
                    if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
                        $data['is_graded'] = true;
                    } else {
                        // Essay questions need to be graded manually
                        $data['is_graded'] = false;
                    }
                    
                    StudentAnswer::create($data);
                }
            }
            
            // Calculate the score for auto-graded questions
            $totalPointsEarned = StudentAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('is_graded', true)
                ->sum('points_earned');
                
            $totalPossiblePoints = $questions->sum('points');
            
            // Check if there are any essay questions that need to be graded
            $hasUngraded = StudentAnswer::where('quiz_attempt_id', $attempt->id)
                ->where('is_graded', false)
                ->exists();
                
            // Update the attempt with the score so far
            $score = $totalPossiblePoints > 0 ? ($totalPointsEarned / $totalPossiblePoints) * 100 : 0;
            
            $attempt->update([
                'is_submitted' => true,
                'submit_time' => now(),
                'score' => $score,
                'is_graded' => !$hasUngraded,
            ]);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Display the result of a quiz attempt.
     */
    public function result(Quiz $quiz, QuizAttempt $attempt)
    {
        // Validate that this attempt belongs to the authenticated student
        if ($attempt->student_id !== Auth::id()) {
            return redirect()->route('siswa.quizzes.show', $quiz)
                ->with('error', 'Anda tidak memiliki akses ke percobaan ini.');
        }
        
        // Validate that the attempt is for the specified quiz
        if ($attempt->quiz_id !== $quiz->id) {
            return redirect()->route('siswa.quizzes.index')
                ->with('error', 'Percobaan tidak valid.');
        }
        
        $attempt->load('answers.question.options', 'answers.option');
        
        // If quiz settings don't allow showing results immediately, check if the student should see results
        if (!$quiz->show_result && now()->lessThan($quiz->end_time)) {
            return view('siswa.quizzes.result-pending', compact('quiz', 'attempt'));
        }
        
        // Group answers by question for easier display
        $answers = $attempt->answers->groupBy('question_id');
        
        return view('siswa.quizzes.result', compact('quiz', 'attempt', 'answers'));
    }
}
