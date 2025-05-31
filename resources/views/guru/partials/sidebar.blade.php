@php
use Illuminate\Support\Facades\Request;
@endphp

<!-- Dashboard -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-indigo-400 rounded-full"></span>
        Dashboard
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="{{ route('guru.dashboard') }}" class="sidebar-item {{ Request::routeIs('guru.dashboard') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white' }} flex items-center rounded-lg px-4 py-3 transition-all duration-200">
                <i class="fas fa-tachometer-alt text-lg w-6 {{ !Request::routeIs('guru.dashboard') ? 'text-indigo-300' : '' }}"></i>
                <span class="ml-3">Dashboard</span>
            </a>
        </li>
    </ul>
</div>

<!-- Pembelajaran -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
        Pembelajaran
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="{{ route('guru.materials.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 {{ request()->routeIs('guru.materials.*') ? 'sidebar-active' : '' }}">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.materials.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-green-700/50' }} transition-all duration-200">
                    <i class="fas fa-book text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.materials.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Materi Pelajaran</span>
                @if(request()->routeIs('guru.materials.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-green-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('guru.assignments.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 {{ request()->routeIs('guru.assignments.*') ? 'sidebar-active' : '' }}">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.assignments.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-green-700/70' }} transition-all duration-200">
                    <i class="fas fa-tasks text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.assignments.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Tugas</span>
                @if(request()->routeIs('guru.assignments.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-green-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('guru.quizzes.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 {{ request()->routeIs('guru.quizzes.*') ? 'sidebar-active' : '' }}">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.quizzes.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-yellow-700/50' }} transition-all duration-200">
                    <i class="fas fa-question-circle text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.quizzes.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Kuis</span>
                @if(request()->routeIs('guru.quizzes.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-yellow-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('guru.exams.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 {{ request()->routeIs('guru.exams.*') ? 'sidebar-active' : '' }}">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.exams.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-red-700/50' }} transition-all duration-200">
                    <i class="fas fa-file-alt text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.exams.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Ujian</span>
                @if(request()->routeIs('guru.exams.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-red-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('guru.grades.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 {{ request()->routeIs('guru.grades.*') ? 'sidebar-active' : '' }}">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.grades.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-orange-700/50' }} transition-all duration-200">
                    <i class="fas fa-star text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.grades.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Penilaian</span>
                @if(request()->routeIs('guru.grades.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-orange-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('guru.attendance.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 {{ request()->routeIs('guru.attendance.*') ? 'sidebar-active' : '' }}">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.attendance.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-purple-700/50' }} transition-all duration-200">
                    <i class="fas fa-clipboard-check text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.attendance.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Kehadiran</span>
                @if(request()->routeIs('guru.attendance.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
    </ul>
</div>

<!-- Jadwal -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-blue-400 rounded-full"></span>
        Jadwal
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="{{ route('guru.schedule.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200 {{ request()->routeIs('guru.schedule.*') ? 'sidebar-active' : '' }}">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.schedule.*') ? 'bg-green-800' : 'bg-indigo-700/50 group-hover:bg-blue-700/50' }} transition-all duration-200">
                    <i class="fas fa-calendar-alt text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.schedule.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Jadwal Mengajar</span>
                @if(request()->routeIs('guru.schedule.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-blue-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
    </ul>
</div>

<!-- Informasi -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-red-400 rounded-full"></span>
        Informasi
    </div>
    <ul class="px-3 space-y-1 sidebar-items">
        <li>
            <a href="{{ route('guru.announcements.index') }}" class="sidebar-item {{ Request::routeIs('guru.announcements.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white' }} flex items-center rounded-lg px-4 py-3 transition-all duration-200">
                <i class="fas fa-bullhorn text-lg w-6 {{ !Request::routeIs('guru.announcements.*') ? 'text-indigo-300' : '' }}"></i>
                <span class="ml-3">Pengumuman</span>
                @if(isset($unreadImportantAnnouncements) && $unreadImportantAnnouncements > 0)
                    <span class="inline-flex items-center justify-center px-2 py-1 ml-3 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">{{ $unreadImportantAnnouncements }}</span>
                @endif
            </a>
        </li>
    </ul>
</div>

<!-- Akun -->
<div class="mb-2 sidebar-section">
    <div class="flex items-center px-4 py-2 text-xs font-semibold tracking-wider text-indigo-200 uppercase sidebar-section-header">
        <span class="inline-block w-2 h-2 mr-2 bg-purple-400 rounded-full"></span>
        Akun
    </div>
    <ul class="px-3 space-y-1 sidebar-items">        <li>
            <a href="{{ route('guru.profile.show') }}" class="sidebar-item {{ request()->routeIs('guru.profile.*') ? 'sidebar-active text-white' : 'text-indigo-100 hover:text-white transition-all duration-200' }} flex items-center rounded-lg px-4 py-2.5 group">
                <div class="p-1.5 rounded-lg {{ request()->routeIs('guru.profile.*') ? 'bg-purple-800' : 'bg-indigo-700/50 group-hover:bg-purple-700/50' }} transition-all duration-200">
                    <i class="fas fa-user-circle text-lg w-5 h-5 flex items-center justify-center {{ request()->routeIs('guru.profile.*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                </div>
                <span class="ml-3">Profil Saya</span>
                @if(request()->routeIs('guru.profile.*'))
                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-400 rounded-tr-md rounded-br-md"></span>
                @endif
            </a>
        </li>
    </ul>
</div>