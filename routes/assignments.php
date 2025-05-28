<?php

use Illuminate\Support\Facades\Route;

// Student routes for assignments
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {    // Assignments
    Route::get('/assignments', [App\Http\Controllers\Siswa\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [App\Http\Controllers\Siswa\AssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [App\Http\Controllers\Siswa\AssignmentController::class, 'submit'])->name('assignments.submit');
    Route::post('/assignments/{assignment}/update', [App\Http\Controllers\Siswa\AssignmentController::class, 'updateSubmission'])->name('assignments.update-submission');
    
    // Submissions
    Route::put('/submissions/{submission}', [App\Http\Controllers\Siswa\SubmissionController::class, 'update'])->name('submissions.update');
    Route::delete('/submissions/{submission}', [App\Http\Controllers\Siswa\SubmissionController::class, 'destroy'])->name('submissions.destroy');
    Route::get('/submissions/{submission}/download', [App\Http\Controllers\Siswa\SubmissionController::class, 'download'])->name('submissions.download');
    
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Siswa\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [App\Http\Controllers\Siswa\NotificationController::class, 'show'])->name('notifications.show');
    Route::put('/notifications/{notification}/read', [App\Http\Controllers\Siswa\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::put('/notifications/read-all', [App\Http\Controllers\Siswa\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\Siswa\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Teacher routes for assignments
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {    // Assignment management
    Route::resource('assignments', App\Http\Controllers\Guru\AssignmentController::class);
    
    // Submissions management    Route::get('submissions/{submission}/download', [App\Http\Controllers\Guru\SubmissionController::class, 'download'])->name('submissions.download');
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
    
    // Export submissions
    Route::get('/assignments/{assignment}/submissions/export', [App\Http\Controllers\Guru\SubmissionController::class, 'exportSubmissions'])->name('assignments.submissions.export');
    Route::get('/all-submissions/export', [App\Http\Controllers\Guru\AllSubmissionsController::class, 'export'])->name('all-submissions.export');
});

// Help page for assignments system
Route::get('/bantuan/tugas', [App\Http\Controllers\PageController::class, 'assignmentHelp'])->name('help.assignments');
