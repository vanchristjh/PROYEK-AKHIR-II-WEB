

<?php $__env->startSection('title', 'Dashboard Guru'); ?>

<?php $__env->startSection('header', 'Dashboard Guru'); ?>

<?php $__env->startSection('content'); ?>    <!-- Welcome Banner with enhanced gradient and floating shapes -->
    <div class="relative p-6 mb-6 overflow-hidden text-white shadow-xl bg-gradient-to-r from-green-500 via-teal-500 to-emerald-500 animate-gradient-x rounded-xl">
        <div class="absolute inset-0 pointer-events-none particles-container"></div>
        <div class="absolute top-0 right-0 transition-transform duration-700 transform opacity-10 hover:scale-110">
            <i class="-mt-4 -mr-4 fas fa-chalkboard-teacher text-9xl"></i>
        </div>
        <div class="absolute w-64 h-64 rounded-full -left-20 -bottom-20 bg-white/10 blur-2xl"></div>
        <div class="absolute rounded-full right-1/3 -top-12 w-36 h-36 bg-green-300/20 blur-3xl"></div>
        <div class="relative z-10 animate-fade-in">
            <div class="flex items-center justify-between">
                <h2 class="mb-2 text-2xl font-bold">Selamat datang, <?php echo e(auth()->user()->name); ?>!</h2>
                <div class="hidden text-sm text-white md:block">
                    <span class="font-medium"><?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM Y')); ?></span>
                </div>
            </div>
            <p class="text-green-100">Anda dapat mengelola kelas, tugas, dan evaluasi siswa dari dashboard ini.</p>
            <div class="flex flex-wrap gap-3 mt-4">
                <a href="#quick-actions" class="flex items-center px-4 py-2 text-sm font-medium transition-all duration-300 rounded-lg btn-glass hover:-translate-y-1 hover:shadow-lg">
                    <i class="mr-2 fas fa-bolt"></i> Aksi Cepat
                </a>
                <a href="<?php echo e(route('guru.assignments.index')); ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white transition-all duration-300 rounded-lg shadow-md bg-green-700/80 hover:bg-green-800 shadow-green-900/30 backdrop-blur-sm hover:shadow-xl hover:-translate-y-1">
                    <i class="mr-2 fas fa-tasks"></i> Lihat Tugas
                </a>
            </div>
        </div>
    </div>
    
    <!-- Announcements/Reminders Banner -->
    <?php if(isset($pendingGradingCount) && $pendingGradingCount > 0): ?>
    <div class="relative p-4 mb-6 overflow-hidden border bg-gradient-to-r from-amber-50 to-amber-100 border-amber-200 rounded-xl animate-fade-in">
        <div class="flex items-start space-x-4">
            <div class="flex items-center justify-center flex-shrink-0 w-10 h-10 rounded-full bg-amber-400/20 text-amber-600">
                <i class="fas fa-bell"></i>
            </div>
            <div class="flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-medium text-amber-800">Pengingat Penilaian</h3>
                    <button class="text-xs text-amber-500 hover:text-amber-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="mt-1 text-sm text-amber-700">
                    <i class="mr-1 fas fa-exclamation-circle"></i>
                    Anda memiliki <span class="font-semibold"><?php echo e($pendingGradingCount); ?></span> tugas siswa yang menunggu untuk dinilai.
                    <a href="<?php echo e(route('guru.grades.index')); ?>" class="font-medium underline transition-colors hover:text-amber-800">Nilai sekarang</a>
                </p>
            </div>
        </div>
        <div class="absolute w-24 h-24 transform rotate-45 rounded-full -right-6 -bottom-8 bg-amber-300/10 blur-2xl"></div>
    </div>
    <?php endif; ?>
      <!-- Stats Cards with enhanced styling -->
    <h3 class="flex items-center mb-4 text-lg font-medium text-gray-800">
        <div class="p-2 mr-3 bg-green-100 rounded-lg">
            <i class="text-green-600 fas fa-chart-pie"></i>
        </div>
        <span>Statistik Pembelajaran</span>
        <div class="flex items-center px-3 py-1 ml-auto text-sm text-gray-500 bg-white rounded-lg shadow-sm">
            <i class="mr-1 transition-transform cursor-pointer fas fa-sync-alt hover:rotate-180" id="refresh-data-btn" title="Refresh data"></i>
            <span>Terakhir diperbarui: <?php echo e(now()->format('H:i')); ?></span>
        </div>
    </h3>
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 xl:grid-cols-5">
        <!-- Classes Card -->
        <div class="relative p-6 overflow-hidden transition-all transform bg-white shadow-sm dashboard-card rounded-xl hover:scale-105">
            <div class="absolute bottom-0 right-0 w-20 h-20 -mb-10 -mr-10 rounded-full bg-green-50"></div>
            <div class="relative flex items-start">
                <div class="p-3 text-green-600 bg-green-100 rounded-lg shadow-inner">
                    <i class="text-xl fas fa-chalkboard"></i>
                </div>
                <div class="flex-1 ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Mata Pelajaran</h3>
                    <p class="card-number floating-element" data-type="subjects"><?php echo e($subjects); ?></p>
                    <div class="mt-2">
                        <a href="<?php echo e(route('guru.materials.index')); ?>" class="inline-flex items-center text-sm text-green-600 hover:text-green-800 group">
                            <span>Lihat materi</span>
                            <i class="ml-1 text-xs transition-transform fas fa-arrow-right group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Students Card -->
        <div class="relative p-6 overflow-hidden transition-all transform bg-white shadow-sm dashboard-card rounded-xl hover:scale-105">
            <div class="absolute bottom-0 right-0 w-20 h-20 -mb-10 -mr-10 rounded-full bg-blue-50"></div>
            <div class="relative flex items-start">
                <div class="p-3 text-blue-600 bg-blue-100 rounded-lg shadow-inner">
                    <i class="text-xl fas fa-user-graduate"></i>
                </div>                <div class="flex-1 ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Siswa</h3>
                    <p class="card-number floating-element" data-type="students"><?php echo e($studentsCount); ?></p>
                    <div class="mt-2">
                        <a href="<?php echo e(route('guru.attendance.index')); ?>" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 group">
                            <span>Kelola kehadiran</span>
                            <i class="ml-1 text-xs transition-transform fas fa-arrow-right group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assignments Card -->
        <div class="relative p-6 overflow-hidden transition-all transform bg-white shadow-sm dashboard-card rounded-xl hover:scale-105">
            <div class="absolute bottom-0 right-0 w-20 h-20 -mb-10 -mr-10 rounded-full bg-purple-50"></div>
            <div class="relative flex items-start">
                <div class="p-3 text-purple-600 bg-purple-100 rounded-lg shadow-inner">
                    <i class="text-xl fas fa-tasks"></i>
                </div>
                <div class="flex-1 ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Tugas</h3>
                    <p class="card-number floating-element" data-type="assignments"><?php echo e($assignments->count()); ?></p>
                    <div class="mt-2">
                        <a href="<?php echo e(route('guru.assignments.index')); ?>" class="inline-flex items-center text-sm text-purple-600 hover:text-purple-800 group">
                            <span>Kelola tugas</span>
                            <i class="ml-1 text-xs transition-transform fas fa-arrow-right group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Assignments Card -->
        <div class="relative p-6 overflow-hidden transition-all transform bg-white shadow-sm dashboard-card rounded-xl hover:scale-105">
            <div class="absolute bottom-0 right-0 w-20 h-20 -mb-10 -mr-10 rounded-full bg-amber-50"></div>
            <div class="relative flex items-start">
                <div class="p-3 rounded-lg shadow-inner bg-amber-100 text-amber-600">
                    <i class="text-xl fas fa-calendar"></i>
                </div>
                <div class="flex-1 ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Tugas Mendatang</h3>
                    <p class="card-number floating-element" data-type="upcoming"><?php echo e($upcomingAssignments); ?></p>
                    <div class="mt-2">
                        <a href="<?php echo e(route('guru.schedule.index')); ?>" class="inline-flex items-center text-sm text-amber-600 hover:text-amber-800 group">
                            <span>Lihat jadwal</span>
                            <i class="ml-1 text-xs transition-transform fas fa-arrow-right group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending Grading Card -->
        <div class="relative p-6 overflow-hidden transition-all transform bg-white shadow-sm dashboard-card rounded-xl hover:scale-105">
            <div class="absolute bottom-0 right-0 w-20 h-20 -mb-10 -mr-10 rounded-full bg-red-50"></div>
            <div class="relative flex items-start">
                <div class="p-3 text-red-600 bg-red-100 rounded-lg shadow-inner">
                    <i class="text-xl fas fa-clipboard-check"></i>
                </div>
                <div class="flex-1 ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Menunggu Penilaian</h3>
                    <p class="card-number floating-element <?php echo e(isset($pendingGradingCount) && $pendingGradingCount > 0 ? 'text-red-600' : ''); ?>" data-type="pendingGrading"><?php echo e($pendingGradingCount ?? 0); ?></p>
                    <div class="mt-2">
                        <a href="<?php echo e(route('guru.grades.index')); ?>" class="inline-flex items-center text-sm text-red-600 hover:text-red-800 group">
                            <span>Nilai sekarang</span>
                            <i class="ml-1 text-xs transition-transform fas fa-arrow-right group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php if(isset($pendingGradingCount) && $pendingGradingCount > 0): ?>
            <span class="absolute flex items-center justify-center w-5 h-5 top-2 right-2">
                <span class="absolute w-4 h-4 bg-red-400 rounded-full opacity-75 animate-ping"></span>
                <span class="relative w-3 h-3 bg-red-500 rounded-full"></span>
            </span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Main Content Area with 2 columns on larger screens -->
    <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">        <!-- Recent Submissions (Left Column) -->
        <div class="overflow-hidden transition transform bg-white border shadow-sm lg:col-span-2 rounded-xl hover:shadow-lg border-gray-100/60">
            <div class="flex items-center justify-between p-6 border-b border-gray-100 card-header">
                <div class="flex items-center">
                    <div class="p-2 mr-3 bg-green-100 rounded-lg">
                        <i class="text-green-500 fas fa-file-alt"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Pengumpulan Tugas Terbaru</h3>
                </div>
                <a href="<?php echo e(route('guru.assignments.index')); ?>" class="flex items-center text-sm font-medium text-green-600 hover:text-green-800 hover:underline group">
                    <span>Lihat semua</span>
                    <i class="ml-1 text-xs transition-transform fas fa-chevron-right group-hover:translate-x-1"></i>
                </a>
            </div>
            <div class="p-6">
                <div class="space-y-4" id="recent-submissions-list">
                    <?php if(isset($recentSubmissions) && count($recentSubmissions) > 0): ?>
                        <?php $__currentLoopData = $recentSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors duration-300 
                                <?php echo e(!$submission->score && $submission->submitted_at ? 'bg-amber-50 border-amber-200' : ''); ?>">
                                <div class="flex items-center justify-center w-12 h-12 mr-4 overflow-hidden text-blue-600 bg-blue-100 border-2 border-blue-200 rounded-full">
                                    <?php if(isset($submission->student->profile_photo)): ?>
                                        <img src="<?php echo e($submission->student->profile_photo); ?>" alt="<?php echo e($submission->student->name); ?>" class="object-cover w-full h-full">
                                    <?php else: ?>
                                        <i class="text-xl fas fa-user-graduate"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:justify-between">
                                        <h5 class="text-sm font-medium text-gray-800"><?php echo e($submission->student->name); ?></h5>
                                        <span class="mt-1 text-xs text-gray-500 sm:mt-0"><?php echo e($submission->submitted_at ? $submission->submitted_at->diffForHumans() : 'Belum dikumpulkan'); ?></span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Mengumpulkan: <span class="font-medium text-blue-600"><?php echo e($submission->assignment->title); ?></span></p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="text-xs py-1 px-2 rounded-full <?php echo e($submission->score ? 'bg-green-100 text-green-700' : ($submission->submitted_at ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700')); ?>">
                                            <?php if($submission->score): ?>
                                                <i class="mr-1 fas fa-check-circle"></i> Dinilai: <?php echo e($submission->score); ?>/100
                                            <?php elseif($submission->submitted_at): ?>
                                                <i class="mr-1 fas fa-hourglass-half"></i> Menunggu penilaian
                                            <?php else: ?>
                                                <i class="mr-1 fas fa-clock"></i> Belum dikumpulkan
                                            <?php endif; ?>
                                        </span>                                        <div>
                                            <a href="<?php echo e(route('guru.assignments.submissions.show', [$submission->assignment_id, $submission->id])); ?>" 
                                               class="px-3 py-1 text-xs text-blue-600 transition-all rounded-full bg-blue-50 hover:bg-blue-100 hover:shadow-sm">
                                                <i class="mr-1 fas fa-eye"></i> Lihat
                                            </a>
                                            <?php if($submission->submitted_at && !$submission->score): ?>
                                            <a href="<?php echo e(route('guru.grades.edit', [$submission->id])); ?>" 
                                               class="px-3 py-1 ml-1 text-xs text-green-600 transition-all rounded-full bg-green-50 hover:bg-green-100 hover:shadow-sm">
                                                <i class="mr-1 fas fa-star"></i> Nilai
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="py-8 text-center">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full">
                                <i class="text-xl text-gray-400 fas fa-inbox"></i>
                            </div>
                            <h5 class="font-medium text-gray-500">Belum Ada Pengumpulan</h5>
                            <p class="mt-1 text-sm text-gray-400">Siswa belum mengumpulkan tugas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions (Right Column) -->
        <div>
            <h3 class="flex items-center mb-4 text-lg font-medium text-gray-800" id="quick-actions">
                <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                    <i class="text-indigo-600 fas fa-bolt"></i>
                </div>
                <span>Aksi Cepat</span>
            </h3>
            <div class="grid grid-cols-1 gap-4">
                <!-- Create Assignment -->
                <a href="<?php echo e(route('guru.assignments.create')); ?>" class="block p-4 transition-all duration-300 border border-gray-200 quick-action bg-gradient-to-r from-gray-50 to-gray-100 hover:from-green-50 hover:to-green-50 rounded-xl hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center flex-shrink-0 text-white transition-all duration-300 shadow-md h-14 w-14 rounded-xl bg-gradient-to-br from-green-400 to-green-600 group-hover:shadow-green-200 group-hover:scale-110">
                            <i class="text-lg fas fa-clipboard-list"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 transition-colors group-hover:text-green-600">Buat Tugas Baru</h4>
                            <p class="mt-1 text-xs text-gray-500">Tambahkan tugas untuk siswa</p>
                        </div>
                    </div>
                </a>
                
                <!-- Upload Materials -->
                <a href="<?php echo e(route('guru.materials.create')); ?>" class="block p-4 transition-all duration-300 border border-gray-200 quick-action bg-gradient-to-r from-gray-50 to-gray-100 hover:from-blue-50 hover:to-blue-50 rounded-xl hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center flex-shrink-0 text-white transition-all duration-300 shadow-md h-14 w-14 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 group-hover:shadow-blue-200 group-hover:scale-110">
                            <i class="text-lg fas fa-file-upload"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 transition-colors group-hover:text-blue-600">Unggah Materi</h4>
                            <p class="mt-1 text-xs text-gray-500">Tambahkan materi pembelajaran</p>
                        </div>
                    </div>
                </a>
                
                <!-- Record Attendance -->
                <a href="<?php echo e(route('guru.attendance.create')); ?>" class="block p-4 transition-all duration-300 border border-gray-200 quick-action bg-gradient-to-r from-gray-50 to-gray-100 hover:from-purple-50 hover:to-purple-50 rounded-xl hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center flex-shrink-0 text-white transition-all duration-300 shadow-md h-14 w-14 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 group-hover:shadow-purple-200 group-hover:scale-110">
                            <i class="text-lg fas fa-clipboard-check"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 transition-colors group-hover:text-purple-600">Rekam Kehadiran</h4>
                            <p class="mt-1 text-xs text-gray-500">Catat kehadiran siswa</p>
                        </div>
                    </div>
                </a>
                
                <!-- Grade Assignments -->
                <a href="<?php echo e(route('guru.grades.index')); ?>" class="block p-4 transition-all duration-300 border border-gray-200 quick-action bg-gradient-to-r from-gray-50 to-gray-100 hover:from-orange-50 hover:to-orange-50 rounded-xl hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center flex-shrink-0 text-white transition-all duration-300 shadow-md h-14 w-14 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 group-hover:shadow-orange-200 group-hover:scale-110">
                            <i class="text-lg fas fa-star"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 transition-colors group-hover:text-orange-600">Nilai Tugas</h4>
                            <p class="mt-1 text-xs text-gray-500">Berikan nilai pada tugas siswa</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Today's Schedule -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="flex items-center text-lg font-medium text-gray-800">
                <div class="p-2 mr-3 bg-blue-100 rounded-lg shadow-inner">
                    <i class="text-blue-600 fas fa-calendar-alt"></i>
                </div>
                <span>Jadwal Mengajar Hari Ini</span>
            </h3>
            <a href="<?php echo e(route('guru.schedule.index')); ?>" class="flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline group">
                <span>Lihat jadwal lengkap</span>
                <i class="ml-1 text-xs transition-transform fas fa-chevron-right group-hover:translate-x-1"></i>
            </a>
        </div>
        
        <div class="overflow-hidden bg-white border shadow-sm rounded-xl border-gray-100/60">
            <div class="p-6">
                <div class="space-y-4" id="today-schedule">                    <?php
                        try {
                            $todayIndonesian = now()->locale('id')->dayName;
                        } catch (\Exception $e) {
                            // Fallback to using the English day name
                            $todayIndonesian = now()->format('l');
                            
                            // Map English day names to Indonesian
                            $dayMap = [
                                'Monday' => 'Senin',
                                'Tuesday' => 'Selasa',
                                'Wednesday' => 'Rabu',
                                'Thursday' => 'Kamis',
                                'Friday' => 'Jumat',
                                'Saturday' => 'Sabtu',
                                'Sunday' => 'Minggu'
                            ];
                            
                            if (isset($dayMap[$todayIndonesian])) {
                                $todayIndonesian = $dayMap[$todayIndonesian];
                            }
                        }
                        
                        $schedules = App\Models\Schedule::with(['subject', 'classroom'])
                            ->where('teacher_id', auth()->id())
                            ->where('day', $todayIndonesian)
                            ->orderBy('start_time')
                            ->get();
                          $now = now();
                        foreach ($schedules as $schedule) {
                            try {
                                // Check if the time fields exist and have proper format
                                if (!empty($schedule->start_time) && !empty($schedule->end_time)) {
                                    $start = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time);
                                    $end = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time);
                                    $currentTime = \Carbon\Carbon::createFromFormat('H:i:s', $now->format('H:i:s'));
                                    
                                    $schedule->isOngoing = $currentTime->between($start, $end);
                                    $schedule->isUpcoming = $currentTime->lt($start);
                                    $schedule->isPast = $currentTime->gt($end);
                                } else {
                                    // Set default values if time fields are empty
                                    $schedule->isOngoing = false;
                                    $schedule->isUpcoming = false;
                                    $schedule->isPast = false;
                                }
                            } catch (\Exception $e) {
                                // Handle any Carbon formatting exceptions                                $schedule->isOngoing = false;
                                $schedule->isUpcoming = false;
                                $schedule->isPast = false;
                            }
                        }
                    ?>
                    
                    <?php if(count($schedules) > 0): ?>
                        <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors duration-300
                                <?php echo e($schedule->isOngoing ? 'bg-green-50 border-green-200' : ($schedule->isUpcoming ? '' : 'bg-gray-50')); ?>">
                                <div class="p-3 rounded-lg 
                                    <?php echo e($schedule->isOngoing ? 'bg-green-100 text-green-600' : ($schedule->isUpcoming ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600')); ?> 
                                    mr-4">
                                    <i class="fas <?php echo e($schedule->isOngoing ? 'fa-chalkboard-teacher' : ($schedule->isUpcoming ? 'fa-hourglass-half' : 'fa-check')); ?>"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <h5 class="text-sm font-medium text-gray-800"><?php echo e($schedule->subject->name); ?></h5>
                                        <div class="text-xs px-2 py-1 rounded-full 
                                            <?php echo e($schedule->isOngoing ? 'bg-green-100 text-green-800' : ($schedule->isUpcoming ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')); ?>">
                                            <?php echo e($schedule->isOngoing ? 'Sedang Berlangsung' : ($schedule->isUpcoming ? 'Akan Datang' : 'Selesai')); ?>

                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">Kelas: <span class="font-medium"><?php echo e($schedule->classroom->name); ?></span></p>
                                    <div class="flex items-center justify-between mt-1">                                        <span class="text-xs text-gray-600">
                                            <i class="mr-1 fas fa-clock"></i> 
                                            <?php
                                                $startTime = !empty($schedule->start_time) ? substr($schedule->start_time, 0, 5) : '--:--';
                                                $endTime = !empty($schedule->end_time) ? substr($schedule->end_time, 0, 5) : '--:--';
                                            ?>
                                            <?php echo e($startTime); ?> - <?php echo e($endTime); ?>

                                        </span>
                                        <span class="text-xs text-gray-600">
                                            <i class="mr-1 fas fa-map-marker-alt"></i> 
                                            <?php echo e($schedule->room ?: 'Ruang Kelas'); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="py-8 text-center">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full">
                                <i class="text-xl text-gray-400 fas fa-calendar-times"></i>
                            </div>
                            <h5 class="font-medium text-gray-500">Tidak Ada Jadwal Hari Ini</h5>
                            <p class="mt-1 text-sm text-gray-400">Anda tidak memiliki jadwal mengajar untuk hari ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .animate-gradient-x {
        background-size: 400% 400%;
        animation: gradient-x 15s ease infinite;
    }
    
    @keyframes gradient-x {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
    
    .btn-glass {
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .btn-glass:hover {
        background-color: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .dashboard-card:hover .floating-element {
        animation: float 1s ease-in-out infinite;
    }
    
    .card-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #374151;
        line-height: 1.2;
        margin: 0.5rem 0;
    }
    
    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-5px);
        }
        100% {
            transform: translateY(0px);
        }
    }
    
    /* New enhanced styles */
    .dashboard-card {
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1);
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    
    .dashboard-card:hover {
        box-shadow: 0 10px 20px rgba(0,0,0,0.1), 0 6px 6px rgba(0,0,0,0.05);
    }
    
    /* Light shimmer effect on cards */
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            to right,
            rgba(255,255,255,0) 0%,
            rgba(255,255,255,0.15) 50%,
            rgba(255,255,255,0) 100%
        );
        transform: rotate(30deg);
        opacity: 0;
        transition: opacity 0.5s;
    }
    
    .dashboard-card:hover::before {
        animation: shimmer 1.5s ease-in-out;
    }
    
    @keyframes shimmer {
        0% {
            opacity: 0;
            transform: rotate(30deg) translateX(-100%);
        }
        20% {
            opacity: 0.2;
        }
        100% {
            opacity: 0;
            transform: rotate(30deg) translateX(100%);
        }
    }
    
    /* Pulse animation for notification dots */
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.5;
        }
        70% {
            transform: scale(1);
            opacity: 0.8;
        }
        100% {
            transform: scale(0.95);
            opacity: 0.5;
        }
    }
    
    .pulse {
        animation: pulse 2s infinite;
    }
    
    /* Quick action hover effects */
    .quick-action {
        transition: all 0.3s ease;
    }
    
    .quick-action:hover {
        transform: translateY(-5px);
    }
    
    .quick-action:hover .flex-shrink-0 {
        transform: scale(1.1);
    }
    
    /* Improve shadows */
    .custom-shadow {
        box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.12);
    }
    
    .custom-shadow-md {
        box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.08);
    }
    
    .custom-shadow-lg {
        box-shadow: 0 10px 15px rgba(0,0,0,0.03), 0 3px 6px rgba(0,0,0,0.05);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize animation for stat counters
        initCounterAnimation();
        
        // Set up refresh button
        document.getElementById('refresh-data-btn').addEventListener('click', function() {
            this.classList.add('animate-spin');
            fetchUpdatedData().finally(() => {
                this.classList.remove('animate-spin');
                document.querySelector('.ml-auto.text-sm.text-gray-500 span').textContent = 'Terakhir diperbarui: ' + new Date().getHours() + ':' + String(new Date().getMinutes()).padStart(2, '0');
            });
        });
    });
    
    // Function to animate counters
    function initCounterAnimation() {
        document.querySelectorAll('.card-number').forEach(counter => {
            const value = parseInt(counter.textContent);
            counter.textContent = '0';
            
            setTimeout(() => {
                const duration = 1000;
                const steps = 20;
                const stepValue = value / steps;
                const stepTime = duration / steps;
                let currentStep = 0;
                
                const interval = setInterval(() => {
                    currentStep++;
                    counter.textContent = Math.ceil(Math.min(stepValue * currentStep, value)).toString();
                    
                    if (currentStep >= steps) {
                        clearInterval(interval);
                    }
                }, stepTime);
            }, 300);
        });
    }
    
    // Function to fetch updated data
    function fetchUpdatedData() {
        return fetch('<?php echo e(route("guru.dashboard.refresh")); ?>')
            .then(response => response.json())
            .then(data => {
                // Update statistics counters
                if (data.stats) {
                    updateStatCounters(data.stats);
                }
                
                // Update recent submissions
                if (data.recentSubmissions) {
                    updateRecentSubmissions(data.recentSubmissions);
                }
                
                // Update today's schedule
                if (data.todaySchedule) {
                    updateTodaySchedule(data.todaySchedule);
                }
                
                // Show success notification
                showNotification('Data berhasil diperbarui', 'success');
            })
            .catch(error => {
                console.error('Error fetching updated data:', error);
                showNotification('Gagal memperbarui data', 'error');
            });
    }
      // Update stat counters with new values
    function updateStatCounters(stats) {
        if (stats.subjects) document.querySelector('[data-type="subjects"]').textContent = stats.subjects;
        if (stats.students) document.querySelector('[data-type="students"]').textContent = stats.students;
        if (stats.assignments) document.querySelector('[data-type="assignments"]').textContent = stats.assignments;
        if (stats.upcoming) document.querySelector('[data-type="upcoming"]').textContent = stats.upcoming;
        
        // Update pending grading with visual indicators
        if (stats.pendingGrading !== undefined) {
            const pendingGradingEl = document.querySelector('[data-type="pendingGrading"]');
            pendingGradingEl.textContent = stats.pendingGrading;
            
            if (stats.pendingGrading > 0) {
                pendingGradingEl.classList.add('text-red-600');
                
                // Check if notification dot exists, if not add it
                const parentCard = pendingGradingEl.closest('.dashboard-card');
                if (!parentCard.querySelector('.animate-ping')) {
                    const notificationDot = document.createElement('span');
                    notificationDot.className = 'absolute top-2 right-2 flex h-5 w-5 items-center justify-center';
                    notificationDot.innerHTML = `
                        <span class="absolute w-4 h-4 bg-red-400 rounded-full opacity-75 animate-ping"></span>
                        <span class="relative w-3 h-3 bg-red-500 rounded-full"></span>
                    `;
                    parentCard.appendChild(notificationDot);
                }
            } else {
                pendingGradingEl.classList.remove('text-red-600');
                
                // Remove notification dot if it exists
                const parentCard = pendingGradingEl.closest('.dashboard-card');
                const notificationDot = parentCard.querySelector('.animate-ping')?.parentElement;
                if (notificationDot) {
                    notificationDot.remove();
                }
            }
        }
    }
      // Update recent submissions list
    function updateRecentSubmissions(submissions) {
        const container = document.getElementById('recent-submissions-list');
        if (!container) return;
        
        if (submissions.length === 0) {
            container.innerHTML = `
                <div class="py-8 text-center">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full">
                        <i class="text-xl text-gray-400 fas fa-inbox"></i>
                    </div>
                    <h5 class="font-medium text-gray-500">Belum Ada Pengumpulan</h5>
                    <p class="mt-1 text-sm text-gray-400">Siswa belum mengumpulkan tugas</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = '';
        submissions.forEach(submission => {
            const submissionEl = document.createElement('div');
            
            const bgClass = !submission.score && submission.submitted_at ? 'bg-amber-50 border-amber-200' : '';
            submissionEl.className = `flex items-center p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors duration-300 ${bgClass}`;
            
            let statusDisplay, statusClass;
            if (submission.score) {
                statusDisplay = `<i class="mr-1 fas fa-check-circle"></i> Dinilai: ${submission.score}/100`;
                statusClass = 'bg-green-100 text-green-700';
            } else if (submission.submitted_at) {
                statusDisplay = '<i class="mr-1 fas fa-hourglass-half"></i> Menunggu penilaian';
                statusClass = 'bg-amber-100 text-amber-700';
            } else {
                statusDisplay = '<i class="mr-1 fas fa-clock"></i> Belum dikumpulkan';
                statusClass = 'bg-gray-100 text-gray-700';
            }
            
            const actionButtons = submission.submitted_at && !submission.score
                ? `<a href="${submission.detail_url}" class="px-3 py-1 text-xs text-blue-600 transition-all rounded-full bg-blue-50 hover:bg-blue-100 hover:shadow-sm">
                     <i class="mr-1 fas fa-eye"></i> Lihat
                   </a>
                   <a href="/guru/grades/edit/${submission.id}" class="px-3 py-1 ml-1 text-xs text-green-600 transition-all rounded-full bg-green-50 hover:bg-green-100 hover:shadow-sm">
                     <i class="mr-1 fas fa-star"></i> Nilai
                   </a>`
                : `<a href="${submission.detail_url}" class="px-3 py-1 text-xs text-blue-600 transition-all rounded-full bg-blue-50 hover:bg-blue-100 hover:shadow-sm">
                     <i class="mr-1 fas fa-eye"></i> Lihat
                   </a>`;
            
            submissionEl.innerHTML = `
                <div class="flex items-center justify-center w-12 h-12 mr-4 overflow-hidden text-blue-600 bg-blue-100 border-2 border-blue-200 rounded-full">
                    <i class="text-xl fas fa-user-graduate"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:justify-between">
                        <h5 class="text-sm font-medium text-gray-800">${submission.student.name}</h5>
                        <span class="mt-1 text-xs text-gray-500 sm:mt-0">${submission.time_ago}</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Mengumpulkan: <span class="font-medium text-blue-600">${submission.assignment.title}</span></p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-xs py-1 px-2 rounded-full ${statusClass}">
                            ${statusDisplay}
                        </span>
                        <div>
                            ${actionButtons}
                        </div>
                    </div>
                </div>
            `;
            
            container.appendChild(submissionEl);
        });
    }
    
    // Update today's schedule
    function updateTodaySchedule(schedules) {
        const container = document.getElementById('today-schedule');
        if (!container) return;
        
        if (schedules.length === 0) {
            container.innerHTML = `
                <div class="py-8 text-center">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full">
                        <i class="text-xl text-gray-400 fas fa-calendar-times"></i>
                    </div>
                    <h5 class="font-medium text-gray-500">Tidak Ada Jadwal Hari Ini</h5>
                    <p class="mt-1 text-sm text-gray-400">Anda tidak memiliki jadwal mengajar untuk hari ini</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = '';
        schedules.forEach(schedule => {
            const scheduleEl = document.createElement('div');
            
            const statusClass = schedule.isOngoing 
                ? 'bg-green-50 border-green-200' 
                : (schedule.isUpcoming ? '' : 'bg-gray-50');
                
            scheduleEl.className = `flex items-center p-3 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors duration-300 ${statusClass}`;
            
            const iconClass = schedule.isOngoing 
                ? 'bg-green-100 text-green-600' 
                : (schedule.isUpcoming ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600');
                
            const icon = schedule.isOngoing 
                ? 'fa-chalkboard-teacher' 
                : (schedule.isUpcoming ? 'fa-hourglass-half' : 'fa-check');
                
            const statusBadge = schedule.isOngoing 
                ? 'bg-green-100 text-green-800' 
                : (schedule.isUpcoming ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800');
                
            const statusText = schedule.isOngoing 
                ? 'Sedang Berlangsung' 
                : (schedule.isUpcoming ? 'Akan Datang' : 'Selesai');
            
            scheduleEl.innerHTML = `
                <div class="p-3 rounded-lg ${iconClass} mr-4">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between">
                        <h5 class="text-sm font-medium text-gray-800">${schedule.subject.name}</h5>
                        <div class="text-xs px-2 py-1 rounded-full ${statusBadge}">
                            ${statusText}
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Kelas: <span class="font-medium">${schedule.classroom.name}</span></p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-gray-600">
                            <i class="mr-1 fas fa-clock"></i> 
                            ${schedule.start_time_formatted} - ${schedule.end_time_formatted}
                        </span>
                        <span class="text-xs text-gray-600">
                            <i class="mr-1 fas fa-map-marker-alt"></i> 
                            ${schedule.room || 'Ruang Kelas'}
                        </span>
                    </div>
                </div>
            `;
            
            container.appendChild(scheduleEl);
        });
    }
    
    // Show notification
    function showNotification(message, type = 'success') {
        // You can implement your own notification system here
        if (type === 'success') {
            console.log('✅ ' + message);
        } else {
            console.error('❌ ' + message);
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SEMUA TENTANG KULIAH\SEMESTER 4\PA2\NEW\PROYEK-AKHIR-II-WEB\resources\views/guru/dashboard.blade.php ENDPATH**/ ?>