<?php

use Illuminate\Support\Facades\Route;

// Student routes for assignments
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {    
    // Assignments
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Siswa\SiswaAssignmentController::class, 'index'])->name('index');
        Route::get('/{assignment}', [App\Http\Controllers\Siswa\SiswaAssignmentController::class, 'show'])->name('show');
        Route::post('/{assignment}/submit', [App\Http\Controllers\Siswa\SiswaAssignmentController::class, 'submit'])->name('submit');
        Route::post('/{assignment}/update', [App\Http\Controllers\Siswa\SiswaAssignmentController::class, 'updateSubmission'])->name('update-submission');
    });
    
    // Submissions
    Route::prefix('submissions')->name('submissions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Siswa\SubmissionController::class, 'index'])->name('index');
        Route::get('/{submission}', [App\Http\Controllers\Siswa\SubmissionController::class, 'show'])->name('show');
        Route::put('/{submission}', [App\Http\Controllers\Siswa\SubmissionController::class, 'update'])->name('update');
        Route::delete('/{submission}', [App\Http\Controllers\Siswa\SubmissionController::class, 'destroy'])->name('destroy');
        Route::get('/{submission}/download', [App\Http\Controllers\Siswa\SubmissionController::class, 'download'])->name('download');
    });
});

// Teacher routes for assignments
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {    // Assignment management
    Route::resource('assignments', App\Http\Controllers\Guru\AssignmentController::class);
    
    // Alternative statistics view
    Route::get('assignments/{assignment}/statistics-alt', [App\Http\Controllers\Guru\AssignmentController::class, 'statisticsAlt'])
        ->name('assignments.statistics.alt');
    
    // Submissions management
    Route::get('submissions/{submission}/download', [App\Http\Controllers\Guru\SubmissionController::class, 'download'])->name('submissions.download')->whereNumber('submission');
    Route::post('/submissions/{submission}/grade', [App\Http\Controllers\Guru\SubmissionController::class, 'grade'])->name('submissions.grade');
    Route::post('/assignments/{assignment}/remind', [App\Http\Controllers\Guru\SubmissionController::class, 'sendReminder'])->name('assignments.remind');
    Route::post('/assignments/{assignment}/remind-all', [App\Http\Controllers\Guru\SubmissionController::class, 'sendBulkReminders'])->name('assignments.remind-all');
    
    // All submissions view
    Route::get('/all-submissions', [App\Http\Controllers\Guru\AllSubmissionsController::class, 'index'])->name('all-submissions.index');
    Route::post('/all-submissions/bulk-grade', [App\Http\Controllers\Guru\AllSubmissionsController::class, 'bulkGrade'])->name('all-submissions.bulk-grade');
    
    // Batch grading
    Route::get('/assignments/{assignment}/batch-grade', [App\Http\Controllers\Guru\AssignmentController::class, 'batchGrade'])->name('assignments.batch-grade');
    
    // Assignment statistics
    Route::get('/assignments/{assignment}/statistics', [App\Http\Controllers\Guru\AssignmentController::class, 'statistics'])->name('assignments.statistics');
    Route::get('/assignments/{assignment}/statistics/export', [App\Http\Controllers\Guru\AssignmentController::class, 'exportStatistics'])->name('assignments.statistics.export');
    Route::get('/assignments/{assignment}/statistics/export-pdf', [App\Http\Controllers\Guru\AssignmentController::class, 'exportStatisticsPdf'])->name('assignments.statistics.export-pdf');
    
    // Export submissions
    Route::get('/assignments/{assignment}/submissions/export', [App\Http\Controllers\Guru\SubmissionController::class, 'exportSubmissions'])->name('assignments.submissions.export');
    Route::get('/assignments/{assignment}/submissions/export-pdf', [App\Http\Controllers\Guru\SubmissionController::class, 'exportSubmissionsPdf'])->name('assignments.submissions.export-pdf');
});

// Help page for assignments system
Route::get('/bantuan/tugas', [App\Http\Controllers\PageController::class, 'assignmentHelp'])->name('help.assignments');
