<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Option;
use App\Models\Quiz;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Store a newly created quiz question in storage.
     */
    public function storeQuizQuestion(Request $request, Quiz $quiz)
    {
        // Check if the teacher owns the quiz
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'content' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'points' => 'required|numeric|min:0',
            'options' => 'required_if:question_type,multiple_choice,true_false|array',
            'options.*.content' => 'required_if:question_type,multiple_choice,true_false|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:question_type,essay|nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create the question
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'content' => $request->content,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'correct_answer' => $request->question_type === 'essay' ? $request->correct_answer : null,
            ]);

            // For multiple choice and true/false, create options
            if (in_array($request->question_type, ['multiple_choice', 'true_false'])) {
                $hasCorrectOption = false;
                
                foreach ($request->options as $optionData) {
                    $option = new Option([
                        'content' => $optionData['content'],
                        'is_correct' => isset($optionData['is_correct']) ? true : false,
                    ]);
                    
                    $question->options()->save($option);
                    
                    if (isset($optionData['is_correct']) && $optionData['is_correct']) {
                        $hasCorrectOption = true;
                    }
                }
                
                // For multiple choice questions, ensure at least one option is marked as correct
                if (!$hasCorrectOption && $request->question_type === 'multiple_choice') {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Setidaknya satu opsi harus dipilih sebagai jawaban yang benar.')
                        ->withInput();
                }
            }

            DB::commit();
            return redirect()->route('guru.quizzes.questions', $quiz)
                ->with('success', 'Pertanyaan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Store a newly created exam question in storage.
     */
    public function storeExamQuestion(Request $request, Exam $exam)
    {
        // Check if the teacher owns the exam
        if ($exam->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'content' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'points' => 'required|numeric|min:0',
            'options' => 'required_if:question_type,multiple_choice,true_false|array',
            'options.*.content' => 'required_if:question_type,multiple_choice,true_false|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:question_type,essay|nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create the question
            $question = Question::create([
                'exam_id' => $exam->id,
                'content' => $request->content,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'correct_answer' => $request->question_type === 'essay' ? $request->correct_answer : null,
            ]);

            // For multiple choice and true/false, create options
            if (in_array($request->question_type, ['multiple_choice', 'true_false'])) {
                $hasCorrectOption = false;
                
                foreach ($request->options as $optionData) {
                    $option = new Option([
                        'content' => $optionData['content'],
                        'is_correct' => isset($optionData['is_correct']) ? true : false,
                    ]);
                    
                    $question->options()->save($option);
                    
                    if (isset($optionData['is_correct']) && $optionData['is_correct']) {
                        $hasCorrectOption = true;
                    }
                }
                
                // For multiple choice questions, ensure at least one option is marked as correct
                if (!$hasCorrectOption && $request->question_type === 'multiple_choice') {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Setidaknya satu opsi harus dipilih sebagai jawaban yang benar.')
                        ->withInput();
                }
            }

            DB::commit();
            return redirect()->route('guru.exams.questions', $exam)
                ->with('success', 'Pertanyaan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a quiz question.
     */
    public function editQuizQuestion(Quiz $quiz, Question $question)
    {
        // Check if the teacher owns the quiz
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the question belongs to the quiz
        if ($question->quiz_id !== $quiz->id) {
            abort(404, 'Pertanyaan tidak ditemukan.');
        }
        
        $question->load('options');
        $questionTypes = ['multiple_choice' => 'Pilihan Ganda', 'true_false' => 'Benar/Salah', 'essay' => 'Essay'];
        
        return view('guru.questions.edit', compact('quiz', 'question', 'questionTypes'));
    }
    
    /**
     * Show the form for editing an exam question.
     */
    public function editExamQuestion(Exam $exam, Question $question)
    {
        // Check if the teacher owns the exam
        if ($exam->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the question belongs to the exam
        if ($question->exam_id !== $exam->id) {
            abort(404, 'Pertanyaan tidak ditemukan.');
        }
        
        $question->load('options');
        $questionTypes = ['multiple_choice' => 'Pilihan Ganda', 'true_false' => 'Benar/Salah', 'essay' => 'Essay'];
        
        return view('guru.questions.edit', compact('exam', 'question', 'questionTypes'));
    }

    /**
     * Update the specified quiz question in storage.
     */
    public function updateQuizQuestion(Request $request, Quiz $quiz, Question $question)
    {
        // Check if the teacher owns the quiz
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the question belongs to the quiz
        if ($question->quiz_id !== $quiz->id) {
            abort(404, 'Pertanyaan tidak ditemukan.');
        }
        
        $request->validate([
            'content' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'points' => 'required|numeric|min:0',
            'options' => 'required_if:question_type,multiple_choice,true_false|array',
            'options.*.id' => 'nullable|exists:options,id',
            'options.*.content' => 'required_if:question_type,multiple_choice,true_false|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:question_type,essay|nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update the question
            $question->update([
                'content' => $request->content,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'correct_answer' => $request->question_type === 'essay' ? $request->correct_answer : null,
            ]);

            // For multiple choice and true/false, update options
            if (in_array($request->question_type, ['multiple_choice', 'true_false'])) {
                // Get existing option IDs to track which ones should be deleted
                $existingOptionIds = $question->options->pluck('id')->toArray();
                $updatedOptionIds = [];
                $hasCorrectOption = false;
                
                foreach ($request->options as $optionData) {
                    $isCorrect = isset($optionData['is_correct']) ? true : false;
                    
                    if (isset($optionData['id'])) {
                        // Update existing option
                        $option = Option::findOrFail($optionData['id']);
                        $option->update([
                            'content' => $optionData['content'],
                            'is_correct' => $isCorrect,
                        ]);
                        $updatedOptionIds[] = $option->id;
                    } else {
                        // Create new option
                        $option = new Option([
                            'content' => $optionData['content'],
                            'is_correct' => $isCorrect,
                        ]);
                        $question->options()->save($option);
                        $updatedOptionIds[] = $option->id;
                    }
                    
                    if ($isCorrect) {
                        $hasCorrectOption = true;
                    }
                }
                
                // Delete options that weren't included in the update
                $optionsToDelete = array_diff($existingOptionIds, $updatedOptionIds);
                if (!empty($optionsToDelete)) {
                    Option::whereIn('id', $optionsToDelete)->delete();
                }
                
                // For multiple choice questions, ensure at least one option is marked as correct
                if (!$hasCorrectOption && $request->question_type === 'multiple_choice') {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Setidaknya satu opsi harus dipilih sebagai jawaban yang benar.')
                        ->withInput();
                }
            } else {
                // For essay questions, delete all options if they exist
                $question->options()->delete();
            }

            DB::commit();
            return redirect()->route('guru.quizzes.questions', $quiz)
                ->with('success', 'Pertanyaan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update the specified exam question in storage.
     */
    public function updateExamQuestion(Request $request, Exam $exam, Question $question)
    {
        // Check if the teacher owns the exam
        if ($exam->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the question belongs to the exam
        if ($question->exam_id !== $exam->id) {
            abort(404, 'Pertanyaan tidak ditemukan.');
        }
        
        $request->validate([
            'content' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'points' => 'required|numeric|min:0',
            'options' => 'required_if:question_type,multiple_choice,true_false|array',
            'options.*.id' => 'nullable|exists:options,id',
            'options.*.content' => 'required_if:question_type,multiple_choice,true_false|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:question_type,essay|nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update the question
            $question->update([
                'content' => $request->content,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'correct_answer' => $request->question_type === 'essay' ? $request->correct_answer : null,
            ]);

            // For multiple choice and true/false, update options
            if (in_array($request->question_type, ['multiple_choice', 'true_false'])) {
                // Get existing option IDs to track which ones should be deleted
                $existingOptionIds = $question->options->pluck('id')->toArray();
                $updatedOptionIds = [];
                $hasCorrectOption = false;
                
                foreach ($request->options as $optionData) {
                    $isCorrect = isset($optionData['is_correct']) ? true : false;
                    
                    if (isset($optionData['id'])) {
                        // Update existing option
                        $option = Option::findOrFail($optionData['id']);
                        $option->update([
                            'content' => $optionData['content'],
                            'is_correct' => $isCorrect,
                        ]);
                        $updatedOptionIds[] = $option->id;
                    } else {
                        // Create new option
                        $option = new Option([
                            'content' => $optionData['content'],
                            'is_correct' => $isCorrect,
                        ]);
                        $question->options()->save($option);
                        $updatedOptionIds[] = $option->id;
                    }
                    
                    if ($isCorrect) {
                        $hasCorrectOption = true;
                    }
                }
                
                // Delete options that weren't included in the update
                $optionsToDelete = array_diff($existingOptionIds, $updatedOptionIds);
                if (!empty($optionsToDelete)) {
                    Option::whereIn('id', $optionsToDelete)->delete();
                }
                
                // For multiple choice questions, ensure at least one option is marked as correct
                if (!$hasCorrectOption && $request->question_type === 'multiple_choice') {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Setidaknya satu opsi harus dipilih sebagai jawaban yang benar.')
                        ->withInput();
                }
            } else {
                // For essay questions, delete all options if they exist
                $question->options()->delete();
            }

            DB::commit();
            return redirect()->route('guru.exams.questions', $exam)
                ->with('success', 'Pertanyaan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Question $question)
    {
        // Check if the question belongs to a quiz or exam owned by the teacher
        if (
            ($question->quiz_id && $question->quiz->teacher_id !== Auth::id()) ||
            ($question->exam_id && $question->exam->teacher_id !== Auth::id())
        ) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();
        try {
            // Get the parent ID before deleting
            $quizId = $question->quiz_id;
            $examId = $question->exam_id;
            
            // Delete associated options first
            $question->options()->delete();
            
            // Delete the question
            $question->delete();
            
            DB::commit();
            
            if ($quizId) {
                return redirect()->route('guru.quizzes.questions', $quizId)
                    ->with('success', 'Pertanyaan berhasil dihapus.');
            } else {
                return redirect()->route('guru.exams.questions', $examId)
                    ->with('success', 'Pertanyaan berhasil dihapus.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
