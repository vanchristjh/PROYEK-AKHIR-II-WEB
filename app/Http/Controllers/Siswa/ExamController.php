<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Display a listing of available exams for the student.
     */
    public function index()
    {
        $student = Auth::user();
        
        // Get classrooms for the student, both from direct relationship and many-to-many
        $classroomIds = collect();
        
        // Add the direct classroom relationship if it exists
        if ($student->classroom_id) {
            $classroomIds->push($student->classroom_id);
        }
        
        // Add classrooms from many-to-many relationship
        $manyToManyClassroomIds = $student->classrooms()->pluck('classrooms.id');
        if ($manyToManyClassroomIds->isNotEmpty()) {
            $classroomIds = $classroomIds->merge($manyToManyClassroomIds);
        }
        
        // If student has no classroom, return empty list
        if ($classroomIds->isEmpty()) {
            return view('siswa.exams.index', ['exams' => collect()]);
        }
        
        // Get all exams assigned to the student's classrooms
        $exams = Exam::with(['subject', 'teacher'])
            ->whereHas('classrooms', function($query) use ($classroomIds) {
                $query->whereIn('classrooms.id', $classroomIds);
            })
            ->latest()
            ->paginate(10);
            
        return view('siswa.exams.index', compact('exams'));
    }

    /**
     * Show the exam details and attempt status.
     */
    public function show(Exam $exam)
    {
        $student = Auth::user();
        
        // Get classrooms for the student, both from direct relationship and many-to-many
        $studentClassroomIds = collect();
        
        // Add the direct classroom relationship if it exists
        if ($student->classroom_id) {
            $studentClassroomIds->push($student->classroom_id);
        }
        
        // Add classrooms from many-to-many relationship
        $manyToManyClassroomIds = $student->classrooms()->pluck('classrooms.id');
        if ($manyToManyClassroomIds->isNotEmpty()) {
            $studentClassroomIds = $studentClassroomIds->merge($manyToManyClassroomIds);
        }
        
        // If student has no classroom, redirect back
        if ($studentClassroomIds->isEmpty()) {
            return redirect()->route('siswa.exams.index')
                ->with('error', 'Anda tidak terdaftar di kelas manapun.');
        }
        
        // Check if this exam is assigned to the student's classroom
        if (!$exam->classrooms()->whereIn('classrooms.id', $studentClassroomIds)->exists()) {
            return redirect()->route('siswa.exams.index')
                ->with('error', 'Ujian ini tidak tersedia untuk kelas Anda.');
        }
        
        // Get previous attempts by this student
        $attempts = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->latest()
            ->get();
            
        $attemptsCount = $attempts->count();
        $lastAttempt = $attempts->first();
        $canAttempt = $attemptsCount < $exam->max_attempts && $exam->is_available;
        
        return view('siswa.exams.show', compact('exam', 'attempts', 'attemptsCount', 'lastAttempt', 'canAttempt'));
    }

    /**
     * Start a new exam attempt.
     */
    public function start(Exam $exam)
    {
        $student = Auth::user();
        $studentClassroomIds = $student->classrooms()->pluck('classrooms.id');
        
        // Check if this exam is assigned to the student's classroom
        if (!$exam->classrooms()->whereIn('classrooms.id', $studentClassroomIds)->exists()) {
            return redirect()->route('siswa.exams.index')
                ->with('error', 'Ujian ini tidak tersedia untuk kelas Anda.');
        }
        
        // Check if the exam is available
        if (!$exam->is_available) {
            return redirect()->route('siswa.exams.show', $exam)
                ->with('error', 'Ujian ini tidak tersedia saat ini.');
        }
        
        // Check if the student has reached the maximum number of attempts
        $attemptsCount = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->count();
            
        if ($attemptsCount >= $exam->max_attempts) {
            return redirect()->route('siswa.exams.show', $exam)
                ->with('error', 'Anda telah mencapai jumlah maksimum percobaan untuk ujian ini.');
        }
        
        // Check if the student already has an ongoing attempt
        $ongoingAttempt = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->where('is_submitted', false)
            ->first();
            
        if ($ongoingAttempt) {
            // If there's an ongoing attempt, continue it
            $timeLeft = Carbon::parse($ongoingAttempt->end_time)->diffInSeconds(now(), false);
            
            if ($timeLeft <= 0) {
                // If time has expired, auto-submit the attempt
                $this->submitTimeExpired($ongoingAttempt);
                
                return redirect()->route('siswa.exams.show', $exam)
                    ->with('warning', 'Waktu percobaan sebelumnya telah habis dan telah otomatis dikumpulkan.');
            }
            
            $exam->load(['questions' => function($query) use ($exam) {
                if ($exam->randomize_questions) {
                    $query->inRandomOrder();
                }
            }, 'questions.options' => function($query) {
                $query->inRandomOrder();
            }]);
            
            return view('siswa.exams.take', compact('exam', 'ongoingAttempt', 'timeLeft'));
        }
        
        // Create a new attempt
        DB::beginTransaction();
        try {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'start_time' => now(),
                'end_time' => now()->addMinutes($exam->duration),
                'is_submitted' => false,
            ]);
            
            DB::commit();
            
            $timeLeft = $exam->duration * 60; // Convert to seconds
            
            $exam->load(['questions' => function($query) use ($exam) {
                if ($exam->randomize_questions) {
                    $query->inRandomOrder();
                }
            }, 'questions.options' => function($query) {
                $query->inRandomOrder();
            }]);
            
            return view('siswa.exams.take', compact('exam', 'attempt', 'timeLeft'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('siswa.exams.show', $exam)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Save answers during an exam attempt.
     */
    public function saveAnswer(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        // Validate that this attempt belongs to the authenticated student
        if ($attempt->student_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Validate that the attempt is for the specified exam
        if ($attempt->exam_id !== $exam->id) {
            return response()->json(['error' => 'Invalid exam attempt'], 400);
        }
        
        // Validate that the attempt is not yet submitted
        if ($attempt->is_submitted) {
            return response()->json(['error' => 'Exam already submitted'], 400);
        }
        
        // Validate the request
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_type' => 'required|in:option,text',
            'option_id' => 'required_if:answer_type,option|nullable|exists:options,id',
            'text_answer' => 'required_if:answer_type,text|nullable|string',
        ]);
        
        $question = Question::findOrFail($request->question_id);
        
        // Check that the question belongs to the exam
        if ($question->exam_id !== $exam->id) {
            return response()->json(['error' => 'Question does not belong to this exam'], 400);
        }
        
        try {
            // Check if an answer already exists for this question and attempt
            $existingAnswer = StudentAnswer::where('exam_attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->first();
                
            $data = [
                'exam_attempt_id' => $attempt->id,
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
     * Submit an exam attempt.
     */
    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        // Validate that this attempt belongs to the authenticated student
        if ($attempt->student_id !== Auth::id()) {
            return redirect()->route('siswa.exams.show', $exam)
                ->with('error', 'Anda tidak memiliki akses ke percobaan ini.');
        }
        
        // Validate that the attempt is for the specified exam
        if ($attempt->exam_id !== $exam->id) {
            return redirect()->route('siswa.exams.index')
                ->with('error', 'Percobaan tidak valid.');
        }
        
        // Validate that the attempt is not yet submitted
        if ($attempt->is_submitted) {
            return redirect()->route('siswa.exams.result', ['exam' => $exam->id, 'attempt' => $attempt->id])
                ->with('warning', 'Ujian sudah dikumpulkan sebelumnya.');
        }
        
        DB::beginTransaction();
        try {
            // Load all questions and the student's answers
            $questions = Question::where('exam_id', $exam->id)->get();
            $answers = StudentAnswer::where('exam_attempt_id', $attempt->id)->get();
            
            $answeredQuestionIds = $answers->pluck('question_id')->toArray();
            
            // For each unanswered question, create a blank answer
            foreach ($questions as $question) {
                if (!in_array($question->id, $answeredQuestionIds)) {
                    $data = [
                        'exam_attempt_id' => $attempt->id,
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
            $totalPointsEarned = StudentAnswer::where('exam_attempt_id', $attempt->id)
                ->where('is_graded', true)
                ->sum('points_earned');
                
            $totalPossiblePoints = $questions->sum('points');
            
            // Check if there are any essay questions that need to be graded
            $hasUngraded = StudentAnswer::where('exam_attempt_id', $attempt->id)
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
            
            return redirect()->route('siswa.exams.result', ['exam' => $exam->id, 'attempt' => $attempt->id])
                ->with('success', 'Ujian berhasil dikumpulkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Auto-submit an exam attempt when time expires.
     */
    private function submitTimeExpired(ExamAttempt $attempt)
    {
        DB::beginTransaction();
        try {
            $exam = Exam::findOrFail($attempt->exam_id);
            
            // Load all questions and the student's answers
            $questions = Question::where('exam_id', $exam->id)->get();
            $answers = StudentAnswer::where('exam_attempt_id', $attempt->id)->get();
            
            $answeredQuestionIds = $answers->pluck('question_id')->toArray();
            
            // For each unanswered question, create a blank answer
            foreach ($questions as $question) {
                if (!in_array($question->id, $answeredQuestionIds)) {
                    $data = [
                        'exam_attempt_id' => $attempt->id,
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
            $totalPointsEarned = StudentAnswer::where('exam_attempt_id', $attempt->id)
                ->where('is_graded', true)
                ->sum('points_earned');
                
            $totalPossiblePoints = $questions->sum('points');
            
            // Check if there are any essay questions that need to be graded
            $hasUngraded = StudentAnswer::where('exam_attempt_id', $attempt->id)
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
     * Display the result of an exam attempt.
     */
    public function result(Exam $exam, ExamAttempt $attempt)
    {
        // Validate that this attempt belongs to the authenticated student
        if ($attempt->student_id !== Auth::id()) {
            return redirect()->route('siswa.exams.show', $exam)
                ->with('error', 'Anda tidak memiliki akses ke percobaan ini.');
        }
        
        // Validate that the attempt is for the specified exam
        if ($attempt->exam_id !== $exam->id) {
            return redirect()->route('siswa.exams.index')
                ->with('error', 'Percobaan tidak valid.');
        }
        
        $attempt->load('answers.question.options', 'answers.option');
        
        // If exam settings don't allow showing results immediately, check if the student should see results
        if (!$exam->show_result && now()->lessThan($exam->end_time)) {
            return view('siswa.exams.result-pending', compact('exam', 'attempt'));
        }
        
        // Group answers by question for easier display
        $answers = $attempt->answers->groupBy('question_id');
        
        return view('siswa.exams.result', compact('exam', 'attempt', 'answers'));
    }
}
