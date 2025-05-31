<?php

use Illuminate\Support\Facades\Route;

// Import controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSubjectController;
use App\Http\Controllers\Admin\AdminClassroomController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\DatabaseFixController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Guru\EventController as GuruEventController;
use App\Http\Controllers\Siswa\EventController as SiswaEventController;

// Update these imports to match your actual controller names
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\MaterialController as GuruMaterialController;
use App\Http\Controllers\Guru\AssignmentController as GuruAssignmentController;
use App\Http\Controllers\Guru\GradeController as GuruGradeController;
use App\Http\Controllers\Guru\AttendanceController as GuruAttendanceController;
use App\Http\Controllers\Guru\AnnouncementController as GuruAnnouncementController;

use App\Http\Controllers\Siswa\SiswaDashboardController;
use App\Http\Controllers\Siswa\SiswaScheduleController;
use App\Http\Controllers\Siswa\SiswaAssignmentController;
use App\Http\Controllers\Siswa\SiswaMaterialController;
use App\Http\Controllers\Siswa\SiswaGradeController;
use App\Http\Controllers\Siswa\SiswaAnnouncementController;
use App\Http\Controllers\Siswa\SubmissionController;
use App\Http\Controllers\Siswa\AttendanceController as SiswaAttendanceController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;

// Controllers for Profile and Settings
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Guru\ProfileController as GuruProfileController;
use App\Http\Controllers\Guru\SettingsController as GuruSettingsController;

use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Guru\ScheduleController as GuruScheduleController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    // If user is logged in, they'll be redirected by the guest middleware
    return redirect()->route('login');
});

// Authentication Routes - use only one controller for consistency
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Emergency login route for bypassing potential redirect loops
    Route::get('/emergency-login', [App\Http\Controllers\Auth\EmergencyLoginController::class, 'showLoginForm'])->name('emergency.login');
    Route::post('/emergency-login', [App\Http\Controllers\Auth\EmergencyLoginController::class, 'login'])->name('emergency.login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/unauthorized', [AuthController::class, 'unauthorized'])->name('unauthorized');

// Clear route cache if there's a redirect loop (helpful for debugging)
Route::get('/clear-cache', function() {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return redirect('/')->with('message', 'Cache cleared successfully');
});

// Debug route for authentication status - remove in production
Route::get('/auth-debug', function() {
    return view('auth.debug');
});

// Database pivot table check routes
Route::get('/check-pivot-tables', [App\Http\Controllers\DatabaseCheckController::class, 'checkPivotTables']);
Route::get('/create-test-data', [App\Http\Controllers\DatabaseCheckController::class, 'createTestData']);

// New debug routes
Route::get('/debug/auth', [App\Http\Controllers\DebugController::class, 'checkAuth'])->name('debug.auth');
Route::get('/debug/guru-route', [App\Http\Controllers\DebugController::class, 'tryGuruRoute'])->name('debug.guru-route');

// New comprehensive debugging tool
Route::get('/debug-auth', [App\Http\Controllers\AuthDebugController::class, 'debugAuth'])->name('debug.auth.debug');

// Add password update route for authentication
Route::middleware('auth')->group(function () {
    Route::put('/password-update', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
});

// Admin Routes
// Adding a special fallback route for admin login issues
Route::get('/admin/simple-dashboard', function() {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    if (!$user->role || $user->role->slug !== 'admin') {
        return redirect()->route('unauthorized');
    }
    return view('admin.dashboard.debug', ['user' => $user]);
})->name('admin.simple-dashboard');

// Special route to break redirect loops for admin dashboard
Route::get('/admin/dashboard/fallback', function() {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    // Clear any session values that might be causing loops
    session()->forget('redirect_count');
    session()->forget('url.intended');
    
    // Always show admin debug dashboard if we get here
    return view('admin.dashboard.debug', [
        'user' => $user,
        'debug_info' => ['message' => 'Using fallback dashboard to break redirect loop']
    ]);
})->name('admin.dashboard.fallback');

// Regular admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard routes - use a simplified approach first to avoid redirect loops
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [App\Http\Controllers\Admin\DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/activities', [App\Http\Controllers\Admin\DashboardController::class, 'getActivities'])->name('dashboard.activities');
    
    // User management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    
    // Classroom management - remove duplicate routes
    Route::get('classrooms/export-all', [App\Http\Controllers\Admin\ClassroomController::class, 'exportAll'])
        ->name('classrooms.export-all');
    Route::delete('classrooms/{classroom}/students/{student}', [App\Http\Controllers\Admin\ClassroomController::class, 'removeStudent'])
        ->name('classrooms.removeStudent');
    Route::get('classrooms/{classroom}/export', [App\Http\Controllers\Admin\ClassroomController::class, 'export'])
        ->name('classrooms.export');
    Route::resource('classrooms', App\Http\Controllers\Admin\ClassroomController::class);
    
    // Subject management
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
    Route::get('subjects/{subject}/download/{format?}', [App\Http\Controllers\Admin\SubjectController::class, 'download'])
        ->name('subjects.download');
    Route::get('subjects/{subject}/teachers', [App\Http\Controllers\Admin\SubjectController::class, 'getTeachers'])->name('subjects.teachers');
    
    // Schedule management
    Route::get('schedule/calendar', [App\Http\Controllers\Admin\ScheduleController::class, 'calendar'])->name('schedule.calendar');
    Route::get('schedule/export', [App\Http\Controllers\Admin\ScheduleController::class, 'export'])->name('schedule.export');
    Route::get('schedule/bulk-create', [App\Http\Controllers\Admin\ScheduleController::class, 'bulkCreate'])->name('schedule.bulk-create');
    Route::post('schedule/bulk-store', [App\Http\Controllers\Admin\ScheduleController::class, 'bulkStore'])->name('schedule.bulk-store');
    Route::post('schedule/check-conflicts', [App\Http\Controllers\Admin\ScheduleController::class, 'checkConflicts'])->name('schedule.check-conflicts');
    Route::get('/admin/schedule/check-conflicts', 'App\Http\Controllers\Admin\ScheduleController@checkConflicts')->name('admin.schedule.check-conflicts.index');
    Route::resource('schedule', App\Http\Controllers\Admin\ScheduleController::class);
    
    // Schedule repair routes
    Route::get('/schedule/repair', [App\Http\Controllers\Admin\ScheduleRepairController::class, 'index'])->name('schedule.repair.index');
    Route::post('/schedule/repair', [App\Http\Controllers\Admin\ScheduleRepairController::class, 'repair'])->name('schedule.repair');
    Route::get('/schedule/clean-relations', [App\Http\Controllers\Admin\ScheduleRepairController::class, 'cleanNullRelations'])->name('schedule.clean-relations');
    
    // Announcement management
    Route::resource('announcements', App\Http\Controllers\Admin\AnnouncementController::class);
    Route::get('announcements/{announcement}/download', [AppHttp\Controllers\Admin\AnnouncementController::class, 'download'])->name('announcements.download');
    Route::get('announcements/{announcement}/export/pdf', [AppHttp\Controllers\Admin\AnnouncementController::class, 'exportPdf'])->name('announcements.export.pdf');
    Route::get('announcements/{announcement}/export/excel', [AppHttp\Controllers\Admin\AnnouncementController::class, 'exportExcel'])->name('announcements.export.excel');
    
    // Events management
    Route::resource('events', AdminEventController::class);

    // Profile routes
    Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    
    // Debug routes
    Route::get('/debug/schema', [App\Http\Controllers\Admin\DebugController::class, 'schema'])->name('debug.schema');
    
    // Schedule routes
    Route::get('/schedule/check-conflicts', [AdminScheduleController::class, 'checkConflictsApi'])->name('schedule.check-conflicts.api');
    Route::get('/subjects/{subject}/teachers', [App\Http\Controllers\Admin\ScheduleController::class, 'getTeachersBySubject']);
    Route::resource('schedule', App\Http\Controllers\Admin\ScheduleController::class);

    // Add this route in the admin group
    Route::get('/admin/get-teachers', 'App\Http\Controllers\Admin\TeacherController@getTeachers')->name('admin.get-teachers');
    
    // Add the missing class routes
    Route::get('/class', [App\Http\Controllers\Admin\ClassController::class, 'index'])->name('class');
    Route::get('/class/create', [App\Http\Controllers\Admin\ClassController::class, 'create'])->name('class.create');
    Route::post('/class', [App\Http\Controllers\Admin\ClassController::class, 'store'])->name('class.store');
    Route::get('/class/{class}/edit', [App\Http\Controllers\Admin\ClassController::class, 'edit'])->name('class.edit');
    Route::put('/class/{class}', [App\Http\Controllers\Admin\ClassController::class, 'update'])->name('class.update');
    Route::delete('/class/{class}', [App\Http\Controllers\Admin\ClassController::class, 'destroy'])->name('class.destroy');

    Route::resource('schedules', AdminScheduleController::class);
});

// Admin routes group
Route::group(['middleware' => ['auth', 'role:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    // Subject routes
    Route::resource('subjects', App\Http\Controllers\Admin\SubjectController::class);
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    
    // Settings routes
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Schedule Routes
    Route::resource('schedule', AdminScheduleController::class);
    Route::get('/schedule/calendar', [AdminScheduleController::class, 'calendar'])->name('schedule.calendar');
    Route::get('/schedule/export', [AdminScheduleController::class, 'export'])->name('schedule.export');
    Route::get('/schedule/bulk-create', [AdminScheduleController::class, 'bulkCreate'])->name('schedule.bulk-create');
    Route::post('/schedule/bulk-store', [AdminScheduleController::class, 'bulkStore'])->name('schedule.bulk-store');
    Route::get('/schedule/check-conflicts', [AdminScheduleController::class, 'checkConflictsApi'])->name('schedule.check-conflicts.api');
    
    // API endpoint to get teachers for dropdown
    Route::get('/get-teachers', [App\Http\Controllers\TeacherApiController::class, 'getTeachers'])->name('get-teachers');
    Route::get('/schedule/repair', [AdminScheduleController::class, 'repair'])->name('schedule.repair.admin');
});

// Guru Routes
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role:guru'])->group(function() {
    // Dashboard routes
    Route::get('/dashboard', [App\Http\Controllers\Guru\GuruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [App\Http\Controllers\Guru\GuruDashboardController::class, 'refresh'])->name('dashboard.refresh');
    
    // Schedule routes for teachers with full CRUD
    Route::resource('schedule', GuruScheduleController::class);
    Route::get('/schedule', 'App\Http\Controllers\Guru\ScheduleController@index')->name('schedule.index');
    Route::get('/schedule/export/pdf', 'App\Http\Controllers\Guru\ScheduleController@exportPDF')->name('schedule.export.pdf');
    Route::get('/schedule/export/excel', 'App\Http\Controllers\Guru\ScheduleController@exportExcel')->name('schedule.export.excel');
    Route::get('/schedule/create', 'App\Http\Controllers\Guru\ScheduleController@create')->name('schedule.create');
    Route::post('/schedule', 'App\Http\Controllers\Guru\ScheduleController@store')->name('schedule.store');
    Route::get('/schedule/{id}/edit', 'App\Http\Controllers\Guru\ScheduleController@edit')->name('schedule.edit.guru');
    Route::put('/schedule/{id}', 'App\Http\Controllers\Guru\ScheduleController@update')->name('schedule.update.guru');
    Route::delete('/schedule/{id}', 'App\Http\Controllers\Guru\ScheduleController@destroy')->name('schedule.destroy.guru');
    
    // Materials management
    Route::resource('materials', App\Http\Controllers\Guru\MaterialController::class);
    Route::get('materials/{material}/download', [App\Http\Controllers\Guru\MaterialController::class, 'download'])->name('materials.download');
    Route::patch('materials/{material}/toggle-active', [App\Http\Controllers\Guru\MaterialController::class, 'toggleActive'])->name('materials.toggle-active');
    
    // Assignment and submission routes
    Route::prefix('assignments')->name('assignments.')->group(function() {
        // Regular assignment routes
        Route::get('/', [App\Http\Controllers\Guru\AssignmentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Guru\AssignmentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Guru\AssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}', [App\Http\Controllers\Guru\AssignmentController::class, 'show'])->name('show');
        Route::get('/{assignment}/edit', [App\Http\Controllers\Guru\AssignmentController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [App\Http\Controllers\Guru\AssignmentController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [App\Http\Controllers\Guru\AssignmentController::class, 'destroy'])->name('destroy');
        Route::get('/{assignment}/statistics', [App\Http\Controllers\Guru\AssignmentController::class, 'statistics'])->name('statistics');
        Route::get('/{assignment}/statistics/export', [App\Http\Controllers\Guru\AssignmentController::class, 'exportStatistics'])->name('statistics.export');
        
        // Submissions nested under assignments
        Route::prefix('{assignment}/submissions')->name('submissions.')->group(function() {
            Route::get('/', [App\Http\Controllers\Guru\SubmissionController::class, 'index'])->name('index');
            Route::get('/{submission}', [App\Http\Controllers\Guru\SubmissionController::class, 'show'])->name('show')->whereNumber(['assignment', 'submission']);
            Route::get('/{submission}/download', [App\Http\Controllers\Guru\SubmissionController::class, 'download'])->name('download')->whereNumber(['assignment', 'submission']);
            Route::get('/{submission}/preview', [App\Http\Controllers\Guru\SubmissionController::class, 'preview'])->name('preview')->whereNumber(['assignment', 'submission']);
            Route::put('/{submission}/grade', [App\Http\Controllers\Guru\SubmissionController::class, 'grade'])
        ->name('grade')
        ->whereNumber('submission');
            Route::post('/update-batch', [App\Http\Controllers\Guru\SubmissionController::class, 'updateBatch'])->name('update-batch');
        });

        // Batch grade route
        Route::get('/{assignment}/batch-grade', [App\Http\Controllers\Guru\AssignmentController::class, 'batchGrade'])->name('batch-grade');
        Route::post('/{assignment}/batch-grade/save', [App\Http\Controllers\Guru\AssignmentController::class, 'saveBatchGrade'])->name('batch-grade.save');
    });
    
    // Attendance management
    Route::resource('attendance', App\Http\Controllers\Guru\AttendanceController::class);
    
    // AJAX routes for dependent dropdowns redirected to the controller
    
    // Grades management
    Route::resource('grades', App\Http\Controllers\Guru\GradeController::class);
    
    // AJAX routes for dependent dropdowns
    Route::get('subjects/{subject}/classrooms', [App\Http\Controllers\Guru\GradeController::class, 'getTeacherClassrooms'])
        ->name('grades.getClassrooms');
    Route::get('classrooms/{classroom}/students', [App\Http\Controllers\Guru\GradeController::class, 'getClassroomStudents'])
        ->name('grades.getStudents');
    
    // Announcements management
    Route::resource('announcements', App\Http\Controllers\Guru\AnnouncementController::class);
    Route::get('announcements/{announcement}/download', [AppHttp\Controllers\Guru\AnnouncementController::class, 'download'])
        ->name('announcements.download');
    
    // Events management (read-only)
    Route::get('events', [GuruEventController::class, 'index'])->name('events.index');
    Route::get('events/{event}', [GuruEventController::class, 'show'])->name('events.show');
    
    // Profile routes
    Route::get('profile', [App\Http\Controllers\Guru\ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [App\Http\Controllers\Guru\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [App\Http\Controllers\Guru\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [App\Http\Controllers\Guru\ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Settings routes
    Route::get('settings', [App\Http\Controllers\Guru\SettingsController::class, 'index'])->name('settings.index');
    Route::get('settings/privacy', [App\Http\Controllers\Guru\SettingsController::class, 'privacy'])->name('settings.privacy');
    
    // Help routes
    Route::get('help', [App\Http\Controllers\Guru\HelpController::class, 'index'])->name('help');
    Route::get('help/tutorial', [App\Http\Controllers\Guru\HelpController::class, 'tutorial'])->name('help.tutorial');
    Route::get('/help/assignments', [App\Http\Controllers\HelpController::class, 'assignments'])->name('help.assignments');
    Route::post('settings', [GuruSettingsController::class, 'update'])->name('settings.update');

    // AJAX routes for dependent dropdowns are defined above with the controller

    // Assignments routes
    Route::resource('assignments', GuruAssignmentController::class);
    Route::get('subjects/{id}/classrooms', [GuruAssignmentController::class, 'getClassrooms']);

    // Submissions routes
    Route::post('submissions/{id}/grade', [App\Http\Controllers\Guru\SubmissionController::class, 'grade'])->name('submissions.grade.teacher.id');
    Route::get('submissions/{id}/download', [App\Http\Controllers\Guru\SubmissionController::class, 'download'])->name('submissions.download.teacher.id');
    Route::put('submissions/{submission}', [App\Http\Controllers\Guru\SubmissionController::class, 'update'])->name('submissions.update.teacher');
        Route::resource('schedules', GuruScheduleController::class);
    Route::post('assignments/batch-update', [GuruAssignmentController::class, 'batchUpdate'])->name('assignments.batch-update');
    Route::get('assignments/{assignment}/clone', [GuruAssignmentController::class, 'clone'])->name('assignments.clone');
    Route::get('assignments/{assignment}/data', [GuruAssignmentController::class, 'getAssignment'])->name('assignments.data');
    
    // Export Excel route
    Route::get('export-excel', [App\Http\Controllers\Guru\GuruExportController::class, 'export'])->name('export-excel');
    
    // Submissions handling routes
    Route::post('assignments/{assignment}/send-reminder', [App\Http\Controllers\Guru\SubmissionController::class, 'sendReminder'])->name('assignments.send-reminder');
    Route::post('assignments/{assignment}/send-bulk-reminders', [App\Http\Controllers\Guru\SubmissionController::class, 'sendBulkReminders'])->name('assignments.send-bulk-reminders');
    Route::get('assignments/{assignment}/export-submissions', [App\Http\Controllers\Guru\SubmissionController::class, 'exportSubmissions'])->name('assignments.export-submissions');
});

// Siswa Routes
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [App\Http\Controllers\Siswa\SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/refresh', [App\Http\Controllers\Siswa\SiswaDashboardController::class, 'refresh'])->name('dashboard.refresh');
    
    // Materials routes
    Route::get('/materials', [App\Http\Controllers\Siswa\SiswaMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [App\Http\Controllers\Siswa\SiswaMaterialController::class, 'show'])->name('materials.show');
    
    // Schedule for students (view only)
    Route::get('/schedule', [App\Http\Controllers\Siswa\SiswaScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/day/{day}', [AppHttp\Controllers\Siswa\SiswaScheduleController::class, 'showDay'])->name('schedule.day');
    Route::get('/schedule/export/ical', [App\Http\Controllers\Siswa\SiswaScheduleController::class, 'exportIcal'])->name('schedule.export-ical');
    Route::get('/schedule/{id}', [App\Http\Controllers\Siswa\SiswaScheduleController::class, 'show'])->name('schedule.show');
    
    // Grades for students
    Route::get('/grades', [App\Http\Controllers\Siswa\SiswaGradeController::class, 'index'])->name('grades.index');
    
    // Announcements for students
    Route::get('/announcements', [App\Http\Controllers\Siswa\SiswaAnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}', [AppHttp\Controllers\Siswa\SiswaAnnouncementController::class, 'show'])->name('announcements.show');
    Route::get('/announcements/{announcement}/download', [App\Http\Controllers\Siswa\SiswaAnnouncementController::class, 'download'])
        ->name('announcements.download');
        
    // Events management (read-only)
    Route::get('events', [SiswaEventController::class, 'index'])->name('events.index');
    Route::get('events/{event}', [SiswaEventController::class, 'show'])->name('events.show');
    
    // Student Attendance Routes (read-only)
    Route::get('attendance', [App\Http\Controllers\Siswa\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/{month?}/{year?}', [App\Http\Controllers\Siswa\AttendanceController::class, 'month'])->name('attendance.month');
    Route::get('attendance/subject/{subject}', [App\Http\Controllers\Siswa\AttendanceController::class, 'bySubject'])->name('attendance.by-subject');
    
    // Profile
    Route::get('/profile', [App\Http\Controllers\Siswa\SiswaProfileController::class, 'show'])->name('profile.show');
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Siswa\SiswaSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Siswa\SiswaSettingsController::class, 'update'])->name('settings.update');

    // Material routes
    Route::resource('material', SiswaMaterialController::class);
});

// Temporary route to fix database schema issues (remove after using)
Route::get('/fix-database', function() {
    if (!Schema::hasTable('role_user')) {
        Schema::create('role_user', function ($table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'user_id']);
        });
        
        // Populate the table with existing user role data
        DB::statement('
            INSERT INTO role_user (role_id, user_id, created_at, updated_at)
            SELECT role_id, id, NOW(), NOW()
            FROM users
            WHERE role_id IS NOT NULL
        ');
        
        return redirect('/')->with('message', 'Database schema fixed successfully!');
    }
    
    return redirect('/')->with('message', 'Database schema already correct!');
})->middleware(['auth']);

// Database fix route - Remove or comment out after use
Route::get('/fix-database', [App\Http\Controllers\DatabaseFixController::class, 'checkAndFixSchedules']);

// Manual fix route - access this once to fix the database
// Make sure to remove or comment out this route after using it
Route::get('/fix-schedules-table', [App\Http\Controllers\ManualFixController::class, 'fixSchedulesTable']);

// Quick fix route - access this route once to add the missing column
// Remove or comment this route after using it
Route::get('/fix-schedules', [App\Http\Controllers\DatabaseFixController::class, 'fixSchedulesTable']);

// Temporary diagnostic route - remove after debugging
Route::get('/diagnostic/check-teacher/{id}', function ($id) {
    $teacher = DB::table('teachers')->where('id', $id)->first();
    $subjects = DB::table('subjects')->get();
    $subjectTeacher = DB::table('subject_teacher')->where('teacher_id', $id)->get();
    
    return [
        'teacher_exists' => !is_null($teacher),
        'teacher_data' => $teacher,
        'subject_count' => $subjects->count(),
        'existing_assignments' => $subjectTeacher
    ];
});

// Database fix routes
Route::get('/admin/fix-assignments-teacher-id', [DatabaseFixController::class, 'fixAssignmentsTeacherId'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.fix-assignments-teacher-id');

// Add this diagnostic route - REMOVE AFTER FIXING THE ISSUE!
Route::get('/diagnostic/teachers', function() {
    $teachers = \App\Models\User::where('role_id', 2)->get();
    $allUsers = \App\Models\User::all();
    
    return response()->json([
        'teacher_count' => $teachers->count(),
        'all_users_count' => $allUsers->count(),
        'teacher_role_exists' => \App\Models\Role::where('id', 2)->exists(),
        'roles' => \App\Models\Role::all()->pluck('name', 'id'),
        'sample_teachers' => $teachers->take(5)->map(function($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'role_id' => $teacher->role_id
            ];
        })
    ]);
});

// API routes for schedule functionality
Route::prefix('api')->name('api.')->group(function () {
    Route::get('check-schedule-conflict', 'App\Http\Controllers\API\ScheduleController@checkConflict');
    Route::post('check-bulk-conflicts', 'App\Http\Controllers\API\ScheduleController@checkBulkConflicts');
    Route::get('subjects/{subject}/teachers', 'App\Http\Controllers\API\ScheduleController@getTeachersBySubject');
    Route::get('schedules/{schedule}', 'App\Http\Controllers\API\ScheduleController@getSchedule');
    
    // New improved schedule conflict check API
    Route::get('schedule/check-conflicts', 'App\Http\Controllers\Admin\ScheduleController@checkConflicts')->name('schedule.check-conflicts');
});

// Admin schedule repair route
Route::post('admin/schedule/{schedule}/refresh-relations', 'App\Http\Controllers\Admin\ScheduleController@refreshRelations')
    ->name('admin.schedule.refresh-relations');

// Test diagnostic route
Route::get('/test-assignments', [App\Http\Controllers\TestController::class, 'testAssignmentRelations']);

// Error routes
Route::fallback(function () {
    return view('errors.404');
});

// At the bottom of your web.php file, add:
require __DIR__.'/debug.php';
require __DIR__.'/materials.php';
