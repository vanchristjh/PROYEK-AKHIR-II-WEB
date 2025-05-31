@extends('layouts.guru')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="py-4 container-fluid">
    <!-- Page Header -->
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 fw-bold text-primary">
                <i class="fas fa-cog me-2"></i>Pengaturan Akun
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('guru.dashboard') }}" class="shadow-sm btn btn-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="shadow-sm alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="shadow-sm alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> Terjadi kesalahan:
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main content area -->
    <div class="row g-4">
        <!-- Left sidebar navigation -->
        <div class="col-lg-3">
            <div class="overflow-hidden border-0 shadow-sm card rounded-3">
                <div class="p-0 card-body">
                    <div class="p-4 text-center bg-gradient-primary">
                        <div class="mb-3 avatar-wrapper">
                            @if($user->avatar)
                                <div class="position-relative d-inline-block">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" 
                                         class="border-white shadow rounded-circle img-thumbnail border-3" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                    <div class="bottom-0 position-absolute end-0">
                                        <button class="shadow-sm btn btn-light btn-sm rounded-circle" 
                                                id="change-avatar" title="Ubah foto profil">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="mx-auto bg-white shadow text-primary rounded-circle d-flex align-items-center justify-content-center position-relative"
                                     style="width: 120px; height: 120px; font-size: 3rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                    <div class="bottom-0 position-absolute end-0">
                                        <button class="shadow-sm btn btn-light btn-sm rounded-circle" 
                                                id="change-avatar" title="Tambah foto profil">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <h5 class="mb-1 text-white">{{ $user->name }}</h5>
                        <p class="mb-2 text-white-50 small">{{ $user->email }}</p>
                        <span class="px-3 py-2 text-white bg-white bg-opacity-25 badge">
                            <i class="fas fa-chalkboard-teacher me-1"></i> Guru
                        </span>
                    </div>
                    
                    <div class="list-group list-group-flush settings-nav">
                        <a href="#account" class="px-4 py-3 list-group-item list-group-item-action d-flex align-items-center active" data-bs-toggle="list">
                            <i class="fas fa-user-circle me-3"></i>
                            <span>Informasi Akun</span>
                        </a>
                        <a href="#password" class="px-4 py-3 list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="list">
                            <i class="fas fa-lock me-3"></i>
                            <span>Ubah Password</span>
                        </a>
                        <a href="#preferences" class="px-4 py-3 list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="list">
                            <i class="fas fa-bell me-3"></i>
                            <span>Notifikasi & Preferensi</span>
                        </a>
                        <a href="#appearance" class="px-4 py-3 list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="list">
                            <i class="fas fa-paint-brush me-3"></i>
                            <span>Tampilan Aplikasi</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings content -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Account Information Tab -->
                <div class="tab-pane fade show active animate__animated animate__fadeIn" id="account">
                    <div class="border-0 shadow-sm card rounded-3">
                        <div class="py-3 bg-white card-header">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                Informasi Akun
                            </h5>
                            <p class="mb-0 text-muted small">Kelola data dan profil akun Anda</p>
                        </div>
                        <div class="p-4 card-body">
                            <form action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="name" class="mb-2 form-label fw-medium">Nama Lengkap</label>
                                            <div class="input-group input-group-seamless">
                                                <span class="border-0 input-group-text bg-light">
                                                    <i class="fas fa-user text-primary"></i>
                                                </span>
                                                <input type="text" 
                                                       class="border-0 form-control ps-2" 
                                                       id="name" 
                                                       name="name" 
                                                       value="{{ old('name', $user->name) }}" 
                                                       required 
                                                       placeholder="Masukkan nama lengkap">
                                            </div>
                                            @error('name')
                                                <div class="mt-1 text-danger small">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email" class="mb-2 form-label fw-medium">Alamat Email</label>
                                            <div class="input-group input-group-seamless">
                                                <span class="border-0 input-group-text bg-light">
                                                    <i class="fas fa-envelope text-primary"></i>
                                                </span>
                                                <input type="email" 
                                                       class="border-0 form-control ps-2" 
                                                       id="email" 
                                                       name="email" 
                                                       value="{{ old('email', $user->email) }}" 
                                                       required 
                                                       placeholder="Masukkan alamat email">
                                            </div>
                                            @error('email')
                                                <div class="mt-1 text-danger small">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-3 mt-4 text-end border-top">
                                    <button type="submit" class="px-4 py-2 shadow-sm btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password Tab -->                <div class="tab-pane fade" id="password">
                    <div class="border-0 shadow-sm card rounded-3">
                        <div class="py-3 bg-white card-header">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-lock text-primary me-2"></i>
                                Ubah Password
                            </h5>
                            <p class="mb-0 text-muted small">Ubah password login akun Anda</p>
                        </div>
                        <div class="p-4 card-body">
                            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                @csrf
                                @method('put')
                                  <div class="mb-4">
                                    <label for="current_password" class="form-label fw-medium">Password Saat Ini</label>
                                    <div class="input-group input-group-seamless">
                                        <span class="border-0 shadow-sm input-group-text bg-light text-primary">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="border-0 shadow-sm form-control password-input" id="current_password" name="current_password" required placeholder="Masukkan password saat ini">
                                        <button class="border-0 shadow-sm btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="mt-1 text-danger small">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                  <div class="mb-4">
                                    <label for="password" class="form-label fw-medium">Password Baru</label>
                                    <div class="input-group input-group-seamless">
                                        <span class="border-0 shadow-sm input-group-text bg-light text-primary">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="border-0 shadow-sm form-control password-input" id="password" name="password" required placeholder="Masukkan password baru">
                                        <button class="border-0 shadow-sm btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="mt-1 text-danger small">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="mt-2 form-text small">
                                        <i class="fas fa-info-circle text-primary me-1"></i>
                                        Password harus minimal 8 karakter dan mengandung huruf dan angka.
                                    </div>
                                </div>
                                  <div class="mb-4">
                                    <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Password Baru</label>
                                    <div class="input-group input-group-seamless">
                                        <span class="border-0 shadow-sm input-group-text bg-light text-primary">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="border-0 shadow-sm form-control password-input" id="password_confirmation" name="password_confirmation" required placeholder="Konfirmasi password baru">
                                        <button class="border-0 shadow-sm btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                  <div class="pt-3 mt-4 text-end border-top">
                                    <button type="submit" class="px-4 py-2 btn btn-primary">
                                        <i class="fas fa-key me-2"></i> Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Notification Preferences Tab -->                <div class="tab-pane fade" id="preferences">
                    <div class="border-0 shadow-sm card rounded-3">
                        <div class="py-3 bg-white card-header">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-bell text-primary me-2"></i>
                                Notifikasi & Preferensi
                            </h5>
                            <p class="mb-0 text-muted small">Sesuaikan preferensi notifikasi dan penggunaan</p>
                        </div>
                        <div class="p-4 card-body">
                            <form action="{{ route('guru.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                  <div class="mb-4">
                                    <h6 class="fw-bold d-flex align-items-center">
                                        <i class="fas fa-envelope-open text-primary me-2"></i>
                                        Pengaturan Email
                                    </h6>
                                    <p class="mb-3 text-muted small">Pilih kapan Anda ingin menerima notifikasi email</p>
                                    
                                    <div class="p-3 mb-3 border-0 shadow-sm card">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                                {{ isset($user->preferences['email_notifications']) && $user->preferences['email_notifications'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="email_notifications">
                                                Notifikasi Email
                                                <small class="mt-1 d-block text-muted">Terima semua notifikasi melalui email</small>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 mb-3 border-0 shadow-sm card ms-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="assignment_reminders" name="assignment_reminders"
                                                {{ isset($user->preferences['assignment_reminders']) && $user->preferences['assignment_reminders'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="assignment_reminders">
                                                Pengingat Tugas
                                                <small class="mt-1 d-block text-muted">Terima pengingat tentang tenggat tugas</small>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 border-0 shadow-sm card ms-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="new_submissions" name="new_submissions"
                                                {{ isset($user->preferences['new_submissions']) && $user->preferences['new_submissions'] ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="new_submissions">
                                                Pengumpulan Baru
                                                <small class="mt-1 d-block text-muted">Terima notifikasi saat siswa mengumpulkan tugas</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                  <div class="mb-4">
                                    <h6 class="fw-bold d-flex align-items-center">
                                        <i class="fas fa-sliders-h text-primary me-2"></i>
                                        Preferensi Lainnya
                                    </h6>
                                    <p class="mb-3 text-muted small">Sesuaikan pengalaman penggunaan aplikasi Anda</p>
                                    
                                    <div class="p-3 mb-3 border-0 shadow-sm card">
                                        <div class="mb-0">
                                            <label class="mb-2 form-label fw-medium">Bahasa Aplikasi</label>
                                            <select class="border-0 shadow-sm form-select" name="language" disabled>
                                                <option value="id" selected>Bahasa Indonesia</option>
                                                <option value="en">English</option>
                                            </select>
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i> Coming Soon
                                                </span>
                                                <small class="text-muted ms-2">Fitur ini akan segera tersedia</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  <div class="pt-3 mt-4 text-end border-top">
                                    <button type="submit" class="px-4 py-2 btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Simpan Preferensi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Appearance Settings Tab -->                <div class="tab-pane fade" id="appearance">
                    <div class="border-0 shadow-sm card rounded-3">
                        <div class="py-3 bg-white card-header">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-paint-brush text-primary me-2"></i>
                                Tampilan Aplikasi
                            </h5>
                            <p class="mb-0 text-muted small">Sesuaikan tampilan dan tema aplikasi</p>
                        </div>
                        <div class="p-4 card-body">
                            <form action="{{ route('guru.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                  <div class="mb-4">
                                    <h6 class="mb-3 fw-bold d-flex align-items-center">
                                        <i class="fas fa-desktop text-primary me-2"></i>
                                        Tema Aplikasi
                                    </h6>
                                    
                                    <div class="flex-wrap gap-3 d-flex">
                                        <div class="p-3 border-0 shadow-sm card">
                                            <div class="mb-0 form-check theme-check">
                                                <input class="form-check-input visually-hidden" type="radio" name="theme" id="theme-light" value="light" 
                                                    {{ (!isset($user->preferences['theme']) || $user->preferences['theme'] == 'light') ? 'checked' : '' }}>
                                                <label class="form-check-label theme-option light" for="theme-light">
                                                    <div class="theme-preview">
                                                        <div class="theme-sidebar"></div>
                                                        <div class="theme-content"></div>
                                                    </div>
                                                    <div class="theme-title">Terang</div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="p-3 border-0 shadow-sm card">
                                            <div class="mb-0 form-check theme-check">
                                                <input class="form-check-input visually-hidden" type="radio" name="theme" id="theme-dark" value="dark"
                                                    {{ (isset($user->preferences['theme']) && $user->preferences['theme'] == 'dark') ? 'checked' : '' }} disabled>
                                                <label class="form-check-label theme-option dark" for="theme-dark">
                                                    <div class="theme-preview">
                                                        <div class="theme-sidebar"></div>
                                                        <div class="theme-content"></div>
                                                    </div>
                                                    <div class="theme-title">
                                                        Gelap 
                                                        <span class="badge bg-warning text-dark rounded-pill ms-1">
                                                            <i class="fas fa-clock me-1"></i> Coming Soon
                                                        </span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                  <div class="mb-4">
                                    <h6 class="mb-3 fw-bold d-flex align-items-center">
                                        <i class="fas fa-palette text-primary me-2"></i>
                                        Warna Aksen
                                    </h6>
                                    
                                    <div class="p-3 mb-2 border-0 shadow-sm card">
                                        <div class="flex-wrap gap-3 mb-2 d-flex">
                                            <div class="form-check color-check">
                                                <input class="form-check-input visually-hidden" type="radio" name="accent_color" id="color-blue" value="blue" checked disabled>
                                                <label class="form-check-label color-option blue" for="color-blue">
                                                    <i class="fas fa-check color-check-icon"></i>
                                                </label>
                                            </div>
                                            
                                            <div class="form-check color-check">
                                                <input class="form-check-input visually-hidden" type="radio" name="accent_color" id="color-green" value="green" disabled>
                                                <label class="form-check-label color-option green" for="color-green">
                                                    <i class="fas fa-check color-check-icon"></i>
                                                </label>
                                            </div>
                                            
                                            <div class="form-check color-check">
                                                <input class="form-check-input visually-hidden" type="radio" name="accent_color" id="color-purple" value="purple" disabled>
                                                <label class="form-check-label color-option purple" for="color-purple">
                                                    <i class="fas fa-check color-check-icon"></i>
                                                </label>
                                            </div>
                                            
                                            <div class="form-check color-check">
                                                <input class="form-check-input visually-hidden" type="radio" name="accent_color" id="color-red" value="red" disabled>
                                                <label class="form-check-label color-option red" for="color-red">
                                                    <i class="fas fa-check color-check-icon"></i>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i> Coming Soon
                                            </span>
                                            <small class="text-muted ms-2">Fitur pemilihan warna akan segera tersedia</small>
                                        </div>
                                    </div>
                                </div>
                                  <div class="pt-3 mt-4 text-end border-top">
                                    <button type="submit" class="px-4 py-2 btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Simpan Tampilan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    :root {
        --primary-rgb: 67, 97, 238;
        --primary-color: rgb(var(--primary-rgb));
        --primary-hover: #3651e3;
    }

    /* Card styles */
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 1rem;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    /* Navigation styles */
    .settings-nav .list-group-item {
        border: none;
        color: #4B5563;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .settings-nav .list-group-item:hover {
        background-color: rgba(var(--primary-rgb), 0.05);
        color: var(--primary-color);
    }

    .settings-nav .list-group-item.active {
        background-color: rgba(var(--primary-rgb), 0.1);
        color: var(--primary-color);
        border-left: 4px solid var(--primary-color);
    }

    .settings-nav .list-group-item i {
        width: 20px;
        text-align: center;
    }

    /* Form styles */
    .input-group-seamless {
        background: #F9FAFB;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .input-group-seamless:focus-within {
        background: #fff;
        box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.25);
    }

    .input-group-seamless .input-group-text {
        border-radius: 0.5rem 0 0 0.5rem;
    }

    .input-group-seamless .form-control {
        border-radius: 0 0.5rem 0.5rem 0;
        background: transparent;
    }

    .form-control:focus {
        box-shadow: none;
    }

    /* Button styles */
    .btn {
        font-weight: 500;
        letter-spacing: 0.3px;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    /* Avatar styles */
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .avatar-wrapper img {
        transition: all 0.2s ease;
    }

    .avatar-wrapper:hover img {
        opacity: 0.9;
    }

    /* Gradient background */
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    }

    /* Custom shadows */
    .shadow-sm {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
    }

    .shadow-hover:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    }

    /* Animations */
    .animate__animated {
        animation-duration: 0.4s;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .col-lg-3 {
            margin-bottom: 2rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            new bootstrap.Tooltip(tooltip);
        });

        // Smooth scroll for navigation
        document.querySelectorAll('.settings-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                target.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Avatar upload handling
        const changeAvatarBtn = document.getElementById('change-avatar');
        if (changeAvatarBtn) {
            changeAvatarBtn.addEventListener('click', function() {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.click();

                input.onchange = function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.querySelector('.avatar-wrapper img');
                            if (preview) {
                                preview.src = e.target.result;
                                preview.classList.add('animate__animated', 'animate__fadeIn');
                            }
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                }
            });
        }

        // Form validation and animation
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Animate invalid fields
                    this.querySelectorAll(':invalid').forEach(input => {
                        input.parentElement.classList.add('animate__animated', 'animate__shakeX');
                        setTimeout(() => {
                            input.parentElement.classList.remove('animate__animated', 'animate__shakeX');
                        }, 1000);
                    });
                }
                this.classList.add('was-validated');
            });
        });

        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });

        // Auto-dismiss alerts
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endpush
