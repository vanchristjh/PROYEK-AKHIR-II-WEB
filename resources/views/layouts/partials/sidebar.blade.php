@php
    $user = auth()->user();
    $role = $user->role ? $user->role->name : null;
@endphp


<!-- Admin Navigation -->
@if($role === 'admin')
    <!-- Dashboard Section -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Dashboard</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-home"></i>
            <span>Beranda</span>
        </a>
    </div>

    <!-- School Management Section -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Manajemen Sekolah</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-users"></i>
            <span>Pengguna</span>
        </a>
        <a href="{{ route('admin.subjects.index') }}" class="nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-book"></i>
            <span>Mata Pelajaran</span>
        </a>
        <a href="{{ route('admin.classrooms.index') }}" class="nav-item {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-school"></i>
            <span>Kelas</span>
        </a>
    </div>
<!-- Teacher Navigation -->
@elseif($role === 'guru')
    <!-- Dashboard Section -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Dashboard</div>
        <a href="{{ route('guru.dashboard') }}" class="nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-home"></i>
            <span>Beranda</span>
        </a>
    </div>

    <!-- Teaching Materials -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Pembelajaran</div>
        <a href="{{ route('guru.materials.index') }}" class="nav-item {{ request()->routeIs('guru.materials.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-book-open"></i>
            <span>Materi Pelajaran</span>
        </a>
        <a href="{{ route('guru.assignments.index') }}" class="nav-item {{ request()->routeIs('guru.assignments.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-tasks"></i>
            <span>Tugas</span>
        </a>
    </div>

    <!-- Student Monitoring -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Monitoring Siswa</div>
        <a href="{{ route('guru.grades.index') }}" class="nav-item {{ request()->routeIs('guru.grades.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-star"></i>
            <span>Nilai</span>
        </a>
        <a href="{{ route('guru.attendance.index') }}" class="nav-item {{ request()->routeIs('guru.attendance.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-clipboard-check"></i>
            <span>Absensi</span>
        </a>
    </div>
<!-- Student Navigation -->
@elseif($role === 'siswa')
    <!-- Dashboard Section -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Dashboard</div>
        <a href="{{ route('siswa.dashboard') }}" class="nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-home"></i>
            <span>Beranda</span>
        </a>
    </div>

    <!-- Learning Section -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Pembelajaran</div>
        <a href="{{ route('siswa.schedule.index') }}" class="nav-item {{ request()->routeIs('siswa.schedule.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-calendar-alt"></i>
            <span>Jadwal</span>
        </a>
        <a href="{{ route('siswa.materials.index') }}" class="nav-item {{ request()->routeIs('siswa.materials.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-book-reader"></i>
            <span>Materi</span>
        </a>
        <a href="{{ route('siswa.assignments.index') }}" class="nav-item {{ request()->routeIs('siswa.assignments.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-clipboard-list"></i>
            <span>Tugas</span>
        </a>
    </div>

    <!-- Results Section -->
    <div class="mb-6">
        <div class="px-3 mb-2 text-xs font-medium uppercase text-white/50">Hasil</div>
        <a href="{{ route('siswa.grades.index') }}" class="nav-item {{ request()->routeIs('siswa.grades.*') ? 'active' : '' }}">
            <i class="w-5 mr-3 text-center fas fa-chart-line"></i>
            <span>Nilai</span>
        </a>
    </div>
@endif

<div class="pt-3 mt-3 border-t border-white/10">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center w-full text-sm text-left transition-colors text-white/70 hover:text-white">
            <i class="mr-2 fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </form>
</div>
