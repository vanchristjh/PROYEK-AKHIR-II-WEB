<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of the quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::with(['teacher', 'subject', 'classrooms'])
            ->latest()
            ->paginate(10);
            
        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create()
    {
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get(); // Assuming 2 is the teacher role
        $classrooms = Classroom::all();
        
        return view('admin.quizzes.create', compact('subjects', 'teachers', 'classrooms'));
    }

    /**
     * Store a newly created quiz in storage.
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
            'classrooms' => 'required|array',
            'classrooms.*' => 'exists:classrooms,id',
        ]);

        DB::beginTransaction();
        try {
            // Create the quiz
            $quiz = Quiz::create([
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
            ]);

            // Attach selected classrooms
            $quiz->classrooms()->attach($request->classrooms);

            DB::commit();
            return redirect()->route('admin.quizzes.index')
                ->with('success', 'Kuis berhasil dibuat.');
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
        $quiz->load(['teacher', 'subject', 'classrooms', 'questions.options']);
        
        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified quiz.
     */
    public function edit(Quiz $quiz)
    {
        $quiz->load(['classrooms']);
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get(); // Assuming 2 is the teacher role
        $classrooms = Classroom::all();
        $selectedClassrooms = $quiz->classrooms->pluck('id')->toArray();
        
        return view('admin.quizzes.edit', compact('quiz', 'subjects', 'teachers', 'classrooms', 'selectedClassrooms'));
    }

    /**
     * Update the specified quiz in storage.
     */
    public function update(Request $request, Quiz $quiz)
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
                'teacher_id' => $request->teacher_id,
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
            return redirect()->route('admin.quizzes.index')
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
        try {
            $quiz->delete();
            return redirect()->route('admin.quizzes.index')
                ->with('success', 'Kuis berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
