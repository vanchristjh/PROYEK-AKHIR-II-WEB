<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Guru\QuizController as GuruQuizController;
use App\Http\Controllers\Guru\ExamController as GuruExamController;
use App\Http\Controllers\Guru\QuestionController as GuruQuestionController;
use App\Http\Controllers\Siswa\QuizController as SiswaQuizController;
use App\Http\Controllers\Siswa\ExamController as SiswaExamController;

/*
|--------------------------------------------------------------------------
| Quiz and Exam Routes
|--------------------------------------------------------------------------
|
| Routes related to the Quiz and Exam feature of the SMAN1 Girsip application.
|
*/

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Quiz Routes
    Route::resource('quizzes', AdminQuizController::class);
    Route::get('quizzes/{quiz}/questions', [AdminQuestionController::class, 'quizQuestions'])->name('quizzes.questions');
    Route::get('quizzes/{quiz}/questions/create', [AdminQuestionController::class, 'createForQuiz'])->name('quizzes.questions.create');
    Route::post('quizzes/{quiz}/questions', [AdminQuestionController::class, 'storeForQuiz'])->name('quizzes.questions.store');
    Route::get('quizzes/{quiz}/questions/{question}/edit', [AdminQuestionController::class, 'editQuizQuestion'])->name('quizzes.questions.edit');
    Route::put('quizzes/{quiz}/questions/{question}', [AdminQuestionController::class, 'updateQuizQuestion'])->name('quizzes.questions.update');
    
    // Exam Routes
    Route::resource('exams', AdminExamController::class);
    Route::get('exams/{exam}/questions', [AdminQuestionController::class, 'examQuestions'])->name('exams.questions');
    Route::get('exams/{exam}/questions/create', [AdminQuestionController::class, 'createForExam'])->name('exams.questions.create');
    Route::post('exams/{exam}/questions', [AdminQuestionController::class, 'storeForExam'])->name('exams.questions.store');
    Route::get('exams/{exam}/questions/{question}/edit', [AdminQuestionController::class, 'editExamQuestion'])->name('exams.questions.edit');
    Route::put('exams/{exam}/questions/{question}', [AdminQuestionController::class, 'updateExamQuestion'])->name('exams.questions.update');
    
    // Common Question Routes
    Route::delete('questions/{question}', [AdminQuestionController::class, 'destroy'])->name('questions.destroy');
});

// Guru Routes
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Quiz Management
    Route::resource('quizzes', GuruQuizController::class);
    Route::post('quizzes/{quiz}/activate', [GuruQuizController::class, 'activate'])->name('quizzes.activate');
    Route::post('quizzes/{quiz}/deactivate', [GuruQuizController::class, 'deactivate'])->name('quizzes.deactivate');
    Route::get('quizzes/{quiz}/questions', [GuruQuizController::class, 'questions'])->name('quizzes.questions');
    Route::get('quizzes/{quiz}/questions/create', [GuruQuizController::class, 'createQuestion'])->name('quizzes.questions.create');
    Route::post('quizzes/{quiz}/questions', [GuruQuestionController::class, 'storeQuizQuestion'])->name('quizzes.questions.store');
    Route::get('quizzes/{quiz}/questions/{question}/edit', [GuruQuestionController::class, 'editQuizQuestion'])->name('quizzes.questions.edit');
    Route::put('quizzes/{quiz}/questions/{question}', [GuruQuestionController::class, 'updateQuizQuestion'])->name('quizzes.questions.update');
    Route::delete('quizzes/questions/{question}', [GuruQuestionController::class, 'destroy'])->name('quizzes.questions.destroy');
    
    // Quiz Results
    Route::get('quizzes/{quiz}/results', [GuruQuizController::class, 'results'])->name('quizzes.results');
    Route::get('quizzes/{quiz}/export', [GuruQuizController::class, 'export'])->name('quizzes.export');
    Route::get('quizzes/{quiz}/attempts/{attempt}', [GuruQuizController::class, 'viewAttempt'])->name('quizzes.attempts.view');
    Route::post('quizzes/{quiz}/attempts/{attempt}/answers/{answer}/grade', [GuruQuizController::class, 'gradeEssay'])->name('quizzes.attempts.grade-essay');
    
    // Exam Routes
    Route::resource('exams', GuruExamController::class);
    Route::get('exams/{exam}/questions', [GuruExamController::class, 'questions'])->name('exams.questions');
    Route::get('exams/{exam}/questions/create', [GuruExamController::class, 'createQuestion'])->name('exams.questions.create');
    Route::post('exams/{exam}/questions', [GuruQuestionController::class, 'storeExamQuestion'])->name('exams.questions.store');
    Route::get('exams/{exam}/questions/{question}/edit', [GuruQuestionController::class, 'editExamQuestion'])->name('exams.questions.edit');
    Route::put('exams/{exam}/questions/{question}', [GuruQuestionController::class, 'updateExamQuestion'])->name('exams.questions.update');
    Route::delete('exams/questions/{question}', [GuruQuestionController::class, 'destroy'])->name('questions.destroy');
    
    // Exam Results and Grading
    Route::get('exams/{exam}/results', [GuruExamController::class, 'results'])->name('exams.results');
    Route::get('exams/{exam}/attempts/{attempt}', [GuruExamController::class, 'viewAttempt'])->name('exams.attempts.view');
    Route::post('exams/{exam}/attempts/{attempt}/answers/{answer}/grade', [GuruExamController::class, 'gradeEssay'])->name('exams.attempts.grade-essay');
});

// Siswa Routes
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // Quiz Routes
    Route::get('quizzes', [SiswaQuizController::class, 'index'])->name('quizzes.index');
    Route::get('quizzes/{quiz}', [SiswaQuizController::class, 'show'])->name('quizzes.show');
    Route::post('quizzes/{quiz}/start', [SiswaQuizController::class, 'start'])->name('quizzes.start');
    Route::post('quizzes/{quiz}/attempts/{attempt}/save-answer', [SiswaQuizController::class, 'saveAnswer'])->name('quizzes.save-answer');
    Route::post('quizzes/{quiz}/attempts/{attempt}/submit', [SiswaQuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('quizzes/{quiz}/attempts/{attempt}/result', [SiswaQuizController::class, 'result'])->name('quizzes.result');
    
    // Exam Routes
    Route::get('exams', [SiswaExamController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}', [SiswaExamController::class, 'show'])->name('exams.show');
    Route::get('exams/{exam}/start', [SiswaExamController::class, 'start'])->name('exams.start');
    Route::post('exams/{exam}/attempts/{attempt}/save-answer', [SiswaExamController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('exams/{exam}/attempts/{attempt}/submit', [SiswaExamController::class, 'submit'])->name('exams.submit');
    Route::get('exams/{exam}/attempts/{attempt}/result', [SiswaExamController::class, 'result'])->name('exams.result');
});
