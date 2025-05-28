@extends('layouts.dashboard')

@section('title', 'Dashboard Guru')

@section('header', 'Dashboard Guru')

@section('content')    <!-- Welcome Banner with enhanced gradient and floating shapes -->
    <div class="bg-gradient-to-r from-green-500 via-teal-500 to-emerald-500 animate-gradient-x rounded-xl shadow-xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="particles-container absolute inset-0 pointer-events-none"></div>
        <div class="absolute right-0 top-0 opacity-10 transform hover:scale-110 transition-transform duration-700">
            <i class="fas fa-chalkboard-teacher text-9xl -mt-4 -mr-4"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-1/3 -top-12 w-36 h-36 bg-green-300/20 rounded-full blur-3xl"></div>
        <div class="relative animate-fade-in z-10">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold mb-2">Selamat datang, {{ auth()->user()->name }}!</h2>
                <div class="hidden md:block text-white text-sm">
                    <span class="font-medium">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                </div>
            </div>
            <p class="text-green-100">Anda dapat mengelola kelas, tugas, dan evaluasi siswa dari dashboard ini.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="#quick-actions" class="btn-glass flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <i class="fas fa-bolt mr-2"></i> Aksi Cepat
                </a>
                <a href="{{ route('guru.assignments.index') }}" class="bg-green-700/80 text-white hover:bg-green-800 px-4 py-2 rounded-lg inline-flex items-center text-sm font-medium transition-all duration-300 shadow-md shadow-green-900/30 backdrop-blur-sm hover:shadow-xl hover:-translate-y-1">
                    <i class="fas fa-tasks mr-2"></i> Lihat Tugas
                </a>
            </div>
        </div>
    </div>
    
    <!-- Announcements/Reminders Banner -->
    @if(isset($pendingGradingCount) && $pendingGradingCount > 0)
    <div class="mb-6 bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-4 relative overflow-hidden animate-fade-in">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-amber-400/20 flex items-center justify-center text-amber-600">
                <i class="fas fa-bell"></i>
            </div>
            <div class="flex-1">
                <div class="flex justify-between">
                    <h3 class="text-sm font-medium text-amber-800">Pengingat Penilaian</h3>
                    <button class="text-amber-500 hover:text-amber-700 text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-amber-700 mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Anda memiliki <span class="font-semibold">{{ $pendingGradingCount }}</span> tugas siswa yang menunggu untuk dinilai.
                    <a href="{{ route('guru.grades.index') }}" class="font-medium underline hover:text-amber-800 transition-colors">Nilai sekarang</a>
                </p>
            </div>
        </div>
        <div class="absolute -right-6 -bottom-8 w-24 h-24 bg-amber-300/10 rounded-full transform rotate-45 blur-2xl"></div>
    </div>
    @endif
      <!-- Stats Cards with enhanced styling -->
    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
        <div class="p-2 bg-green-100 rounded-lg mr-3">
            <i class="fas fa-chart-pie text-green-600"></i>
        </div>
        <span>Statistik Pembelajaran</span>
        <div class="ml-auto text-sm text-gray-500 flex items-center bg-white py-1 px-3 rounded-lg shadow-sm">
            <i class="fas fa-sync-alt mr-1 hover:rotate-180 transition-transform cursor-pointer" id="refresh-data-btn" title="Refresh data"></i>
            <span>Terakhir diperbarui: {{ now()->format('H:i') }}</span>
        </div>
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-8">
        <!-- Classes Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-green-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 shadow-inner">
                    <i class="fas fa-chalkboard text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Mata Pelajaran</h3>
                    <p class="card-number floating-element" data-type="subjects">{{ $subjects }}</p>
                    <div class="mt-2">
                        <a href="{{ route('guru.materials.index') }}" class="text-sm text-green-600 hover:text-green-800 inline-flex items-center group">
                            <span>Lihat materi</span>
                            <i class="fas fa-arrow-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Students Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-blue-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 shadow-inner">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Siswa</h3>
                    <p class="card-number floating-element" data-type="students">{{ $studentsCount }}</p>
                    <div class="mt-2">
                        <a href="{{ route('guru.attendance.index') }}" class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center group">
                            <span>Kelola kehadiran</span>
                            <i class="fas fa-arrow-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Assignments Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-purple-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 shadow-inner">
                    <i class="fas fa-tasks text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Total Tugas</h3>
                    <p class="card-number floating-element" data-type="assignments">{{ $assignments->count() }}</p>
                    <div class="mt-2">
                        <a href="{{ route('guru.assignments.index') }}" class="text-sm text-purple-600 hover:text-purple-800 inline-flex items-center group">
                            <span>Kelola tugas</span>
                            <i class="fas fa-arrow-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Assignments Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-amber-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-amber-100 text-amber-600 shadow-inner">
                    <i class="fas fa-calendar text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Tugas Mendatang</h3>
                    <p class="card-number floating-element" data-type="upcoming">{{ $upcomingAssignments }}</p>
                    <div class="mt-2">
                        <a href="{{ route('guru.schedule.index') }}" class="text-sm text-amber-600 hover:text-amber-800 inline-flex items-center group">
                            <span>Lihat jadwal</span>
                            <i class="fas fa-arrow-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pending Grading Card -->
        <div class="dashboard-card bg-white rounded-xl shadow-sm p-6 transform transition-all hover:scale-105 relative overflow-hidden">
            <div class="absolute right-0 bottom-0 w-20 h-20 bg-red-50 rounded-full -mr-10 -mb-10"></div>
            <div class="flex items-start relative">
                <div class="p-3 rounded-lg bg-red-100 text-red-600 shadow-inner">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Menunggu Penilaian</h3>
                    <p class="card-number floating-element {{ isset($pendingGradingCount) && $pendingGradingCount > 0 ? 'text-red-600' : '' }}" data-type="pendingGrading">{{ $pendingGradingCount ?? 0 }}</p>
                    <div class="mt-2">
                        <a href="{{ route('guru.grades.index') }}" class="text-sm text-red-600 hover:text-red-800 inline-flex items-center group">
                            <span>Nilai sekarang</span>
                            <i class="fas fa-arrow-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @if(isset($pendingGradingCount) && $pendingGradingCount > 0)
            <span class="absolute top-2 right-2 flex h-5 w-5 items-center justify-center">
                <span class="animate-ping absolute h-4 w-4 rounded-full bg-red-400 opacity-75"></span>
                <span class="relative rounded-full h-3 w-3 bg-red-500"></span>
            </span>
            @endif
        </div>
    </div>
    
    <!-- Main Content Area with 2 columns on larger screens -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">        <!-- Recent Submissions (Left Column) -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden transform transition hover:shadow-lg border border-gray-100/60">
            <div class="card-header flex items-center justify-between p-6 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <i class="fas fa-file-alt text-green-500"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Pengumpulan Tugas Terbaru</h3>
                </div>
                <a href="{{ route('guru.assignments.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium hover:underline flex items-center group">
                    <span>Lihat semua</span>
                    <i class="fas fa-chevron-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
            <div class="p-6">
                <div class="space-y-4" id="recent-submissions-list">
                    @if(isset($recentSubmissions) && count($recentSubmissions) > 0)
                        @foreach($recentSubmissions as $submission)
                            <div class="flex items-center p-4 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors duration-300 
                                {{ !$submission->score && $submission->submitted_at ? 'bg-amber-50 border-amber-200' : '' }}">
                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-4 overflow-hidden border-2 border-blue-200">
                                    @if(isset($submission->student->profile_photo))
                                        <img src="{{ $submission->student->profile_photo }}" alt="{{ $submission->student->name }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fas fa-user-graduate text-xl"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:justify-between">
                                        <h5 class="text-sm font-medium text-gray-800">{{ $submission->student->name }}</h5>
                                        <span class="text-xs text-gray-500 mt-1 sm:mt-0">{{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : 'Belum dikumpulkan' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Mengumpulkan: <span class="font-medium text-blue-600">{{ $submission->assignment->title }}</span></p>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="text-xs py-1 px-2 rounded-full {{ $submission->score ? 'bg-green-100 text-green-700' : ($submission->submitted_at ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                            @if($submission->score)
                                                <i class="fas fa-check-circle mr-1"></i> Dinilai: {{ $submission->score }}/100
                                            @elseif($submission->submitted_at)
                                                <i class="fas fa-hourglass-half mr-1"></i> Menunggu penilaian
                                            @else
                                                <i class="fas fa-clock mr-1"></i> Belum dikumpulkan
                                            @endif
                                        </span>
                                        <div>
                                            <a href="{{ route('guru.submissions.show', [$submission->assignment_id, $submission->id]) }}" 
                                               class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 py-1 px-3 rounded-full hover:shadow-sm transition-all">
                                                <i class="fas fa-eye mr-1"></i> Lihat
                                            </a>
                                            @if($submission->submitted_at && !$submission->score)
                                            <a href="{{ route('guru.grades.edit', [$submission->id]) }}" 
                                               class="text-xs bg-green-50 text-green-600 hover:bg-green-100 py-1 px-3 rounded-full hover:shadow-sm transition-all ml-1">
                                                <i class="fas fa-star mr-1"></i> Nilai
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-inbox text-gray-400 text-xl"></i>
                            </div>
                            <h5 class="text-gray-500 font-medium">Belum Ada Pengumpulan</h5>
                            <p class="text-gray-400 text-sm mt-1">Siswa belum mengumpulkan tugas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Actions (Right Column) -->
        <div>
            <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center" id="quick-actions">
                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                    <i class="fas fa-bolt text-indigo-600"></i>
                </div>
                <span>Aksi Cepat</span>
            </h3>
            <div class="grid grid-cols-1 gap-4">
                <!-- Create Assignment -->
                <a href="{{ route('guru.assignments.create') }}" class="quick-action block bg-gradient-to-r from-gray-50 to-gray-100 hover:from-green-50 hover:to-green-50 border border-gray-200 p-4 rounded-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-14 w-14 rounded-xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white shadow-md group-hover:shadow-green-200 transition-all duration-300 group-hover:scale-110">
                            <i class="fas fa-clipboard-list text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Buat Tugas Baru</h4>
                            <p class="text-xs text-gray-500 mt-1">Tambahkan tugas untuk siswa</p>
                        </div>
                    </div>
                </a>
                
                <!-- Upload Materials -->
                <a href="{{ route('guru.materials.create') }}" class="quick-action block bg-gradient-to-r from-gray-50 to-gray-100 hover:from-blue-50 hover:to-blue-50 border border-gray-200 p-4 rounded-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-14 w-14 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white shadow-md group-hover:shadow-blue-200 transition-all duration-300 group-hover:scale-110">
                            <i class="fas fa-file-upload text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">Unggah Materi</h4>
                            <p class="text-xs text-gray-500 mt-1">Tambahkan materi pembelajaran</p>
                        </div>
                    </div>
                </a>
                
                <!-- Record Attendance -->
                <a href="{{ route('guru.attendance.create') }}" class="quick-action block bg-gradient-to-r from-gray-50 to-gray-100 hover:from-purple-50 hover:to-purple-50 border border-gray-200 p-4 rounded-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-14 w-14 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white shadow-md group-hover:shadow-purple-200 transition-all duration-300 group-hover:scale-110">
                            <i class="fas fa-clipboard-check text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-purple-600 transition-colors">Rekam Kehadiran</h4>
                            <p class="text-xs text-gray-500 mt-1">Catat kehadiran siswa</p>
                        </div>
                    </div>
                </a>
                
                <!-- Grade Assignments -->
                <a href="{{ route('guru.grades.index') }}" class="quick-action block bg-gradient-to-r from-gray-50 to-gray-100 hover:from-orange-50 hover:to-orange-50 border border-gray-200 p-4 rounded-xl transition-all duration-300 hover:-translate-y-2 hover:shadow-md group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-14 w-14 rounded-xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white shadow-md group-hover:shadow-orange-200 transition-all duration-300 group-hover:scale-110">
                            <i class="fas fa-star text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-orange-600 transition-colors">Nilai Tugas</h4>
                            <p class="text-xs text-gray-500 mt-1">Berikan nilai pada tugas siswa</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Today's Schedule -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-800 flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3 shadow-inner">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                </div>
                <span>Jadwal Mengajar Hari Ini</span>
            </h3>
            <a href="{{ route('guru.schedule.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium hover:underline flex items-center group">
                <span>Lihat jadwal lengkap</span>
                <i class="fas fa-chevron-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/60">
            <div class="p-6">
                <div class="space-y-4" id="today-schedule">                    @php
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
                    @endphp
                    
                    @if(count($schedules) > 0)
                        @foreach($schedules as $schedule)
                            <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-blue-50 transition-colors duration-300
                                {{ $schedule->isOngoing ? 'bg-green-50 border-green-200' : ($schedule->isUpcoming ? '' : 'bg-gray-50') }}">
                                <div class="p-3 rounded-lg 
                                    {{ $schedule->isOngoing ? 'bg-green-100 text-green-600' : ($schedule->isUpcoming ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600') }} 
                                    mr-4">
                                    <i class="fas {{ $schedule->isOngoing ? 'fa-chalkboard-teacher' : ($schedule->isUpcoming ? 'fa-hourglass-half' : 'fa-check') }}"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <h5 class="text-sm font-medium text-gray-800">{{ $schedule->subject->name }}</h5>
                                        <div class="text-xs px-2 py-1 rounded-full 
                                            {{ $schedule->isOngoing ? 'bg-green-100 text-green-800' : ($schedule->isUpcoming ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $schedule->isOngoing ? 'Sedang Berlangsung' : ($schedule->isUpcoming ? 'Akan Datang' : 'Selesai') }}
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">Kelas: <span class="font-medium">{{ $schedule->classroom->name }}</span></p>
                                    <div class="mt-1 flex justify-between items-center">                                        <span class="text-xs text-gray-600">
                                            <i class="fas fa-clock mr-1"></i> 
                                            @php
                                                $startTime = !empty($schedule->start_time) ? substr($schedule->start_time, 0, 5) : '--:--';
                                                $endTime = !empty($schedule->end_time) ? substr($schedule->end_time, 0, 5) : '--:--';
                                            @endphp
                                            {{ $startTime }} - {{ $endTime }}
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            <i class="fas fa-map-marker-alt mr-1"></i> 
                                            {{ $schedule->room ?: 'Ruang Kelas' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-calendar-times text-gray-400 text-xl"></i>
                            </div>
                            <h5 class="text-gray-500 font-medium">Tidak Ada Jadwal Hari Ini</h5>
                            <p class="text-gray-400 text-sm mt-1">Anda tidak memiliki jadwal mengajar untuk hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
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
@endpush

@push('scripts')
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
        return fetch('{{ route("guru.dashboard.refresh") }}')
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
                        <span class="animate-ping absolute h-4 w-4 rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative rounded-full h-3 w-3 bg-red-500"></span>
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
                <div class="text-center py-8">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-inbox text-gray-400 text-xl"></i>
                    </div>
                    <h5 class="text-gray-500 font-medium">Belum Ada Pengumpulan</h5>
                    <p class="text-gray-400 text-sm mt-1">Siswa belum mengumpulkan tugas</p>
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
                statusDisplay = `<i class="fas fa-check-circle mr-1"></i> Dinilai: ${submission.score}/100`;
                statusClass = 'bg-green-100 text-green-700';
            } else if (submission.submitted_at) {
                statusDisplay = '<i class="fas fa-hourglass-half mr-1"></i> Menunggu penilaian';
                statusClass = 'bg-amber-100 text-amber-700';
            } else {
                statusDisplay = '<i class="fas fa-clock mr-1"></i> Belum dikumpulkan';
                statusClass = 'bg-gray-100 text-gray-700';
            }
            
            const actionButtons = submission.submitted_at && !submission.score
                ? `<a href="${submission.detail_url}" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 py-1 px-3 rounded-full hover:shadow-sm transition-all">
                     <i class="fas fa-eye mr-1"></i> Lihat
                   </a>
                   <a href="/guru/grades/edit/${submission.id}" class="text-xs bg-green-50 text-green-600 hover:bg-green-100 py-1 px-3 rounded-full hover:shadow-sm transition-all ml-1">
                     <i class="fas fa-star mr-1"></i> Nilai
                   </a>`
                : `<a href="${submission.detail_url}" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 py-1 px-3 rounded-full hover:shadow-sm transition-all">
                     <i class="fas fa-eye mr-1"></i> Lihat
                   </a>`;
            
            submissionEl.innerHTML = `
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-4 overflow-hidden border-2 border-blue-200">
                    <i class="fas fa-user-graduate text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:justify-between">
                        <h5 class="text-sm font-medium text-gray-800">${submission.student.name}</h5>
                        <span class="text-xs text-gray-500 mt-1 sm:mt-0">${submission.time_ago}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Mengumpulkan: <span class="font-medium text-blue-600">${submission.assignment.title}</span></p>
                    <div class="mt-2 flex justify-between items-center">
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
                <div class="text-center py-8">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fas fa-calendar-times text-gray-400 text-xl"></i>
                    </div>
                    <h5 class="text-gray-500 font-medium">Tidak Ada Jadwal Hari Ini</h5>
                    <p class="text-gray-400 text-sm mt-1">Anda tidak memiliki jadwal mengajar untuk hari ini</p>
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
                    <div class="mt-1 flex justify-between items-center">
                        <span class="text-xs text-gray-600">
                            <i class="fas fa-clock mr-1"></i> 
                            ${schedule.start_time_formatted} - ${schedule.end_time_formatted}
                        </span>
                        <span class="text-xs text-gray-600">
                            <i class="fas fa-map-marker-alt mr-1"></i> 
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
@endpush