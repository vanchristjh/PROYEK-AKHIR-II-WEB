@extends('layouts.dashboard')

@section('title', 'Daftar Kuis')

@push('styles')
<style>
    .quiz-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(229, 231, 235, 0.5);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.1);
        background: white;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        animation: fadeIn 0.6s ease-out;
        animation-fill-mode: both;
    }
    
    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .quiz-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.2);
    }
    
    .quiz-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.3);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        z-index: -1;
    }
    
    .quiz-card:hover::after {
        opacity: 1;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.85em;
        border-radius: 30px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
    }
    
    .status-badge i {
        margin-right: 0.35rem;
    }
    
    .status-completed {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        color: white;
    }
    
    .status-available {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
    }
    
    .status-expired {
        background: linear-gradient(135deg, #F43F5E 0%, #E11D48 100%);
        color: white;
    }
    
    .countdown {
        font-size: 0.85rem;
        color: #6B7280;
        transition: all 0.2s ease;
    }
    
    .quiz-card:hover .countdown {
        color: #4F46E5;
    }
    
    .card-title {
        color: #1F2937;
        font-weight: 600;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
        transition: all 0.3s ease;
    }
    
    .quiz-card:hover .card-title {
        color: #4F46E5;
    }
    
    .quiz-header {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.25);
        position: relative;
        overflow: hidden;
        animation: fadeInDown 0.6s ease-out;
    }

    .quiz-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .quiz-header::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: -60px;
        width: 250px;
        height: 250px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .quiz-header h1 {
        font-weight: 700;
        position: relative;
        z-index: 1;
    }

    .quiz-header p {
        position: relative;
        z-index: 1;
    }

    .main-content {
        background-color: #F9FAFB;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        padding: 2rem;
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInDown {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 1.75rem;
        font-size: 0.875rem;
        background: rgba(255, 255, 255, 0.8);
        padding: 0.85rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(5px);
        animation: fadeIn 0.4s ease-out;
    }

    .breadcrumb a {
        color: #4F46E5;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .breadcrumb a:hover {
        color: #7C3AED;
    }

    .breadcrumb span {
        margin: 0 0.5rem;
        color: #9CA3AF;
    }

    .card-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 1.5rem;
    }

    .card-footer {
        margin-top: auto;
        border-top: 1px solid #F3F4F6;
        padding: 1.25rem 1.5rem;
    }

    .quiz-description {
        position: relative;
        line-height: 1.6;
        color: #6B7280;
        max-height: 4.8em;
        overflow: hidden;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .quiz-card:hover .quiz-description {
        color: #4B5563;
    }
    
    .quiz-description::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 30%;
        height: 1.6em;
        background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1));
    }

    .empty-state {
        text-align: center;
        padding: 4rem 1.5rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        animation: fadeIn 0.6s ease-out;
    }

    .empty-illustration {
        width: 150px;
        height: 150px;
        margin: 0 auto 2rem;
        color: #6366F1;
        opacity: 0.8;
        animation: floating 6s ease-in-out infinite;
    }
    
    @keyframes floating {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }

    .btn {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .btn::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.1);
        z-index: -1;
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.5s ease-out;
    }
    
    .btn:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.25);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
    }

    .btn-outline {
        background: white;
        color: #4F46E5;
        border: 2px solid #4F46E5;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.12);
    }

    .btn-outline:hover {
        background: #EEF2FF;
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.2);
    }
    
    .btn-disabled {
        background: #E5E7EB;
        color: #6B7280;
        cursor: not-allowed;
        box-shadow: none;
    }

    .subject-badge {
        display: inline-flex;
        align-items: center;
        background: #EEF2FF;
        color: #4F46E5;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.15);
    }
    
    .subject-badge:hover {
        background: #E0E7FF;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
    }
    
    .subject-badge i {
        margin-right: 0.35rem;
    }

    .teacher-badge {
        display: inline-flex;
        align-items: center;
        background: #F3F4F6;
        color: #4B5563;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(75, 85, 99, 0.15);
    }
    
    .teacher-badge:hover {
        background: #E5E7EB;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 10px rgba(75, 85, 99, 0.25);
    }
    
    .teacher-badge i {
        margin-right: 0.35rem;
    }

    .pagination {
        margin-top: 2.5rem;
        display: flex;
        justify-content: center;
    }
    
    .alert {
        padding: 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.75rem;
        display: flex;
        align-items: flex-start;
        border: none;
        animation: fadeIn 0.5s ease-out;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .alert-success {
        background-color: rgba(16, 185, 129, 0.1);
        border-left: 5px solid #10B981;
        color: #065F46;
    }
    
    .alert-danger {
        background-color: rgba(239, 68, 68, 0.1);
        border-left: 5px solid #EF4444;
        color: #B91C1C;
    }
    
    .alert i {
        margin-right: 0.85rem;
        font-size: 1.35rem;
        margin-top: 0.1rem;
    }
</style>
@endpush

@section('content')
<div class="breadcrumb mb-4">
    <a href="{{ route('siswa.dashboard') }}" class="hover:text-indigo-800 transition-colors">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="mx-2">/</span>
    <span class="text-gray-600">Kuis</span>
</div>

<div class="quiz-header mb-6">
    <h1 class="text-3xl font-bold mb-3">Daftar Kuis</h1>
    <p class="text-indigo-100">Semua kuis yang tersedia untuk Anda menurut jadwal dan mata pelajaran</p>
</div>
    
<div class="main-content">
    @if(session('success'))
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle"></i>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-circle"></i>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    @if($quizzes->isEmpty())
    <div class="empty-state">
        <div class="empty-illustration">
            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Saat Ini Belum Ada Kuis</h3>
        <p class="text-gray-500 mb-5 max-w-md mx-auto">Belum ada kuis yang tersedia untuk Anda saat ini. Silahkan periksa kembali nanti.</p>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($quizzes as $index => $quiz)
        <div class="quiz-card" style="animation-delay: {{ 100 + ($index * 100) }}ms">
            <div class="card-content">
                <div class="flex justify-between items-start mb-3">
                    <h5 class="card-title text-lg">{{ $quiz->title }}</h5>
                    @php
                        $attempt = $quiz->attempts()->where('user_id', auth()->id())->first();
                        $isCompleted = $attempt && $attempt->submitted_at;
                        $isExpired = $quiz->end_date && $quiz->end_date < now();
                    @endphp
                    
                    @if($isCompleted)
                        <span class="status-badge status-completed">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                    @elseif($isExpired)
                        <span class="status-badge status-expired">
                            <i class="fas fa-clock"></i> Berakhir
                        </span>
                    @else
                        <span class="status-badge status-available">
                            <i class="fas fa-play-circle"></i> Tersedia
                        </span>
                    @endif
                </div>
                
                <p class="text-gray-600 text-sm mb-3 flex items-center">
                    <i class="fas fa-clock mr-2 text-indigo-400"></i> Durasi: {{ $quiz->time_limit ?? 'Tidak ada batas' }}
                </p>
                <div class="quiz-description">{{ \Illuminate\Support\Str::limit($quiz->description, 120) }}</div>
                
                <div class="flex justify-between items-center mt-4">
                    @if($quiz->end_date)
                    <small class="countdown flex items-center">
                        <i class="fas fa-hourglass-half mr-1 text-amber-500"></i>
                        @if($quiz->end_date > now())
                            <span>Berakhir: {{ $quiz->end_date->format('d M Y, H:i') }}</span>
                        @else
                            <span class="text-red-500">Sudah berakhir</span>
                        @endif
                    </small>
                    @else
                    <small class="countdown flex items-center">
                        <i class="fas fa-infinity mr-1 text-indigo-400"></i> Tanpa batas waktu
                    </small>
                    @endif
                </div>
            </div>
                
            <div class="card-footer">
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($quiz->subject)
                    <span class="subject-badge">
                        <i class="fas fa-book"></i> {{ $quiz->subject->name }}
                    </span>
                    @endif
                    @if($quiz->teacher)
                    <span class="teacher-badge">
                        <i class="fas fa-user"></i> {{ $quiz->teacher->name }}
                    </span>
                    @endif
                </div>
                
                <div class="flex justify-end mt-3">
                    @if($isCompleted)
                        <a href="{{ route('siswa.quizzes.results', $quiz->id) }}" class="btn btn-outline">
                            <i class="fas fa-chart-bar mr-1"></i> Lihat Hasil
                        </a>
                    @elseif(!$isExpired)
                        <a href="{{ route('siswa.quizzes.show', $quiz->id) }}" class="btn btn-primary">
                            <i class="fas fa-play-circle mr-1"></i> Mulai Kuis
                        </a>
                    @else
                        <button class="btn btn-disabled" disabled>
                            <i class="fas fa-lock mr-1"></i> Terkunci
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="pagination">
        {{ $quizzes->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effect for quiz cards
        const quizCards = document.querySelectorAll('.quiz-card');
        
        // Create a staggered animation effect
        quizCards.forEach((card, index) => {
            card.style.animationDelay = `${100 + (index * 100)}ms`;
            
            // Add additional hover interactions
            card.addEventListener('mouseenter', function() {
                this.querySelectorAll('.btn').forEach(btn => {
                    btn.style.transform = 'translateY(-3px)';
                });
            });
            
            card.addEventListener('mouseleave', function() {
                this.querySelectorAll('.btn').forEach(btn => {
                    btn.style.transform = 'translateY(0)';
                });
            });
        });
        
        // Add smooth hover transitions for badges
        document.querySelectorAll('.status-badge, .subject-badge, .teacher-badge').forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.05)';
            });
            
            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Animate alerts to fade out
        const alerts = document.querySelectorAll('.alert');
        if (alerts.length > 0) {
            setTimeout(() => {
                alerts.forEach(alert => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    alert.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                });
            }, 5000);
        }
    });
</script>
@endpush
