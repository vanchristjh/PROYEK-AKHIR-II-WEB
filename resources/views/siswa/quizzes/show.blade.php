@extends('layouts.dashboard')

@section('title', 'Detail Kuis')

@push('styles')
<style>
    /* Improved responsive styles and enhanced visual elements */
    .quiz-header {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: white;
        border-radius: 20px;
        padding: clamp(1.5rem, 5vw, 2.5rem);
        margin-bottom: 2rem;
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
        position: relative;
        overflow: hidden;
        animation: fadeIn 0.6s ease-out;
        transform-style: preserve-3d;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .quiz-header:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.4);
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
        animation: float 15s ease-in-out infinite alternate;
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
        animation: float 20s ease-in-out infinite alternate-reverse;
    }
    
    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-15px, 15px) scale(1.05); }
        100% { transform: translate(15px, -15px) scale(0.95); }
    }
    
    .quiz-header .text-muted {
        color: rgba(255, 255, 255, 0.9) !important;
        position: relative;
        z-index: 1;
    }
    
    .question-card {
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        margin-bottom: 1.5rem;
        background-color: white;
    }
    
    .question-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.18);
    }
    
    .option-label {
        display: block;
        padding: 1.1rem 1.5rem;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-bottom: 0.85rem;
    }
    
    .option-label:hover {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        transform: translateX(8px);
    }
    
    .option-input:checked + .option-label {
        background-color: #ede9fe;
        border-color: #6366F1;
    }
    
    .quiz-timer {
        position: sticky;
        top: 20px;
        z-index: 100;
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: white;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.25);
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }    .info-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.65rem 1rem;
        border-radius: 50px;
        background-color: rgba(255, 255, 255, 0.15);
        font-size: 0.9rem;
        margin-right: 0.65rem;
        margin-bottom: 0.65rem;
        backdrop-filter: blur(5px);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .info-badge:hover {
        background-color: rgba(255, 255, 255, 0.25);
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .info-badge i {
        margin-right: 0.5rem;
        transition: transform 0.3s ease;
    }
    
    .info-badge:hover i {
        transform: scale(1.2);
    }    .breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 1.75rem;
        font-size: 0.875rem;
        background: rgba(255, 255, 255, 0.9);
        padding: 0.85rem 1.25rem;
        border-radius: 50px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(10px);
        animation: fadeIn 0.4s ease-out;
        border: 1px solid rgba(229, 231, 235, 0.8);
        flex-wrap: wrap;
    }

    .breadcrumb a {
        color: #4F46E5;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }

    .breadcrumb a:hover {
        color: #7C3AED;
    }
    
    .breadcrumb a::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(to right, #4F46E5, #7C3AED);
        transition: width 0.3s ease;
    }
    
    .breadcrumb a:hover::after {
        width: 100%;
    }

    .breadcrumb span {
        margin: 0 0.5rem;
        color: #9CA3AF;
    }

    .content-card {
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06);
        padding: clamp(1.25rem, 5vw, 2rem);
        margin-bottom: 1.75rem;
        border: 1px solid rgba(229, 231, 235, 0.6);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        animation: fadeUp 0.5s ease-out;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    .content-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #4F46E5, #7C3AED);
        z-index: 2;
        transition: height 0.3s ease;
        border-radius: 20px 20px 0 0;
    }
    
    .content-card:hover::before {
        height: 8px;
    }
    
    .content-card:hover {
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.12);
        transform: translateY(-5px);
    }    .btn {
        display: inline-flex;
        align-items: center;
        padding: 0.85rem 1.75rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
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
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%);
        transform: translateY(-5px) scale(1.03);
        box-shadow: 0 12px 30px rgba(79, 70, 229, 0.4);
    }
    
    .btn-primary i {
        transition: transform 0.3s ease;
    }
    
    .btn-primary:hover i {
        transform: scale(1.2);
    }

    .btn-outline {
        background: white;
        color: #4F46E5;
        border: 2px solid #4F46E5;
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.12);
    }

    .btn-outline:hover {
        background: #EEF2FF;
        transform: translateY(-5px) scale(1.03);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
    }
    
    .btn-outline i {
        transition: transform 0.3s ease;
    }
    
    .btn-outline:hover i {
        transform: translateX(-3px);
    }
    
    .btn-disabled {
        background: #E5E7EB;
        color: #6B7280;
        cursor: not-allowed;
        box-shadow: none;
        opacity: 0.8;
    }    .alert {
        padding: 1.25rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        border: none;
        animation: fadeIn 0.5s ease-out;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.2);
    }

    .alert-info {
        background-color: rgba(238, 242, 255, 0.7);
        border-left: 5px solid #6366F1;
        color: #4338CA;
        position: relative;
        overflow: hidden;
    }
    
    .alert-info::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0) 100%);
        z-index: 0;
    }

    .alert-info i {
        color: #6366F1;
        margin-right: 0.85rem;
        font-size: 1.35rem;
        margin-top: 0.1rem;
        position: relative;
        z-index: 1;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .alert-link {
        font-weight: 600;
        color: #4F46E5;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        display: inline-block;
        z-index: 1;
    }

    .alert-link:hover {
        color: #7C3AED;
    }
    
    .alert-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(to right, #4F46E5, #7C3AED);
        transition: width 0.3s ease;
    }
    
    .alert-link:hover::after {
        width: 100%;
    }
    
    .info-box {
        background-color: #f8fafc;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(229, 231, 235, 0.8);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }
    
    .info-box:hover {
        background-color: #f1f5f9;
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
    }
    
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        padding: 0.5rem;
        border-radius: 10px;
    }
    
    .info-item:hover {
        background-color: rgba(99, 102, 241, 0.05);
        transform: translateX(5px);
    }
    
    .info-item:last-child {
        margin-bottom: 0;
    }
    
    .info-item i {
        color: #4F46E5;
        margin-right: 0.75rem;
        width: 24px;
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .info-item:hover i {
        transform: scale(1.2);
    }
      .rule-list {
        position: relative;
        padding-left: 1.25rem;
    }
    
    .rule-list li {
        position: relative;
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        opacity: 0.9;
    }
    
    .rule-list li:hover {
        transform: translateX(8px);
        opacity: 1;
        color: #1F2937;
    }
    
    .rule-list li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0.5rem;
        width: 8px;
        height: 8px;
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        border-radius: 50%;
        transition: all 0.3s ease;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .rule-list li:hover::before {
        transform: scale(1.5);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }
    
    .rule-list li::after {
        content: '';
        position: absolute;
        left: 3px;
        top: 0.75rem;
        width: 2px;
        height: calc(100% + 0.75rem);
        background: linear-gradient(to bottom, #E0E7FF, transparent);
        transition: all 0.3s ease;
    }
    
    .rule-list li:hover::after {
        background: linear-gradient(to bottom, #6366F1, transparent);
    }
    
    .rule-list li:last-child::after {
        display: none;
    }
    
    /* Responsive media queries */
    @media (max-width: 768px) {
        .quiz-header {
            padding: 1.5rem;
        }
        
        .info-badge {
            padding: 0.5rem 0.85rem;
            font-size: 0.8rem;
        }
        
        .breadcrumb {
            padding: 0.75rem 1rem;
            margin-bottom: 1.25rem;
        }
        
        .content-card {
            padding: 1.25rem;
        }
        
        .btn {
            padding: 0.75rem 1.25rem;
        }
    }
    
    @media (max-width: 640px) {
        .grid.grid-cols-1.md\\:grid-cols-2 {
            display: block;
        }
        
        .info-box {
            padding: 1.25rem;
        }
        
        .quiz-header h1 {
            font-size: 1.5rem;
        }
    }
    
    /* Enhanced animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Accessibility improvements */
    @media (prefers-reduced-motion: reduce) {
        .quiz-header, .quiz-header::before, .quiz-header::after,
        .content-card, .btn, .info-badge, .rule-list li,
        .alert {
            animation: none !important;
            transition: opacity 0.1s linear !important;
        }
    }
</style>
@endpush

@section('content')
<div class="breadcrumb mb-4">
    <a href="{{ route('siswa.dashboard') }}" class="hover:text-indigo-800 transition-colors">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="mx-2">/</span>
    <a href="{{ route('siswa.quizzes.index') }}" class="hover:text-indigo-800 transition-colors">Kuis</a>
    <span class="mx-2">/</span>
    <span class="text-gray-600">{{ $quiz->title }}</span>
</div>

<div class="quiz-header">
    <h1 class="text-2xl font-bold mb-3">{{ $quiz->title }}</h1>
    <p class="text-muted mb-4">{{ $quiz->description }}</p>
    <div class="flex flex-wrap gap-2">
        <span class="info-badge">
            <i class="fas fa-question-circle"></i> {{ $quiz->questions_count ?? 'N/A' }} Pertanyaan
        </span>
        <span class="info-badge">
            <i class="fas fa-clock"></i> {{ $quiz->time_limit ?? 'Tidak ada batas' }}
        </span>
        @if($quiz->end_date)
        <span class="info-badge">
            <i class="fas fa-calendar"></i> Berakhir: {{ $quiz->end_date->format('d M Y, H:i') }}
        </span>
        @endif
        @if($quiz->subject)
        <span class="info-badge">
            <i class="fas fa-book"></i> {{ $quiz->subject->name }}
        </span>
        @endif
    </div>
</div>

<div class="content-card">
    @if($quiz->attempts()->where('user_id', auth()->id())->where('submitted_at', '!=', null)->exists())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <p class="mb-1 font-medium">Anda telah menyelesaikan kuis ini.</p>
                <a href="{{ route('siswa.quizzes.results', $quiz->id) }}" class="alert-link">
                    <i class="fas fa-chart-bar mr-1"></i> Lihat hasil Anda
                </a>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-4 border-l-5 border-red-500">
            <div class="font-medium">Terjadi kesalahan:</div>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="prose max-w-none mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Informasi Kuis</h2>
        
        <div class="info-box">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="info-item">
                        <i class="fas fa-book"></i>
                        <div>
                            <span class="font-medium text-gray-700">Mata Pelajaran:</span> 
                            <span class="ml-1">{{ $quiz->subject ? $quiz->subject->name : 'Umum' }}</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <div>
                            <span class="font-medium text-gray-700">Guru:</span> 
                            <span class="ml-1">{{ $quiz->teacher ? $quiz->teacher->name : 'Admin' }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="info-item">
                        <i class="fas fa-question-circle"></i>
                        <div>
                            <span class="font-medium text-gray-700">Jumlah Soal:</span> 
                            <span class="ml-1">{{ $quiz->questions_count ?? 'N/A' }} pertanyaan</span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="font-medium text-gray-700">Durasi:</span> 
                            <span class="ml-1">{{ $quiz->time_limit ?? 'Tidak ada batas waktu' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-gray-800 mb-4">Peraturan Kuis</h2>
        <ul class="rule-list text-gray-600 mb-6">
            <li>Kerjakan secara individu. Jangan bekerjasama atau menyalin jawaban orang lain.</li>
            <li>Setelah memulai kuis, timer akan berjalan dan tidak dapat dihentikan.</li>
            <li>Pastikan koneksi internet Anda stabil selama mengerjakan kuis.</li>
            <li>Kuis akan otomatis terkirim ketika waktu habis.</li>
            <li>Setelah mengirimkan jawaban, Anda tidak dapat mengerjakan ulang kuis.</li>
        </ul>
    </div>

    <div class="flex items-center justify-between pt-5 border-t border-gray-100">
        <a href="{{ route('siswa.quizzes.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
        </a>

        @if(!$quiz->attempts()->where('user_id', auth()->id())->where('submitted_at', '!=', null)->exists() && (!$quiz->end_date || $quiz->end_date > now()))
        <form action="{{ route('siswa.quizzes.attempt', $quiz->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-play-circle mr-2"></i> Mulai Mengerjakan
            </button>
        </form>
        @elseif($quiz->attempts()->where('user_id', auth()->id())->where('submitted_at', '!=', null)->exists())
        <a href="{{ route('siswa.quizzes.results', $quiz->id) }}" class="btn btn-primary">
            <i class="fas fa-chart-bar mr-2"></i> Lihat Hasil Kuis
        </a>
        @else
        <button disabled class="btn btn-disabled opacity-60 cursor-not-allowed">
            <i class="fas fa-lock mr-2"></i> Kuis Sudah Berakhir
        </button>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced interactive effects
        const infoBadges = document.querySelectorAll('.info-badge');
        const ruleItems = document.querySelectorAll('.rule-list li');
        const infoItems = document.querySelectorAll('.info-item');
        const contentCard = document.querySelector('.content-card');
        const quizHeader = document.querySelector('.quiz-header');
        
        // Add staggered animation to rule items
        ruleItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                item.style.transition = 'all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, 100 + (index * 100));
        });
        
        // Add hover effects for info badges with 3D tilt effect
        infoBadges.forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.05)';
                this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.2)';
            });
            
            badge.addEventListener('mouseleave', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
            });
        });
        
        // Add hover effects for info items
        infoItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(99, 102, 241, 0.05)';
                this.style.transform = 'translateX(5px)';
                const icon = this.querySelector('i');
                if (icon) icon.style.transform = 'scale(1.2)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.transform = '';
                const icon = this.querySelector('i');
                if (icon) icon.style.transform = '';
            });
        });
        
        // Add subtle parallax effect to quiz header
        if (quizHeader) {
            document.addEventListener('mousemove', function(e) {
                const moveX = (e.clientX - window.innerWidth/2) * 0.01;
                const moveY = (e.clientY - window.innerHeight/2) * 0.01;
                quizHeader.style.transform = `translateY(-5px) perspective(1000px) rotateX(${moveY}deg) rotateY(${-moveX}deg)`;
            });
            
            document.addEventListener('mouseleave', function() {
                quizHeader.style.transform = 'translateY(-5px)';
            });
        }
        
        // Add accessibility support
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReducedMotion) {
            const allAnimatedElements = document.querySelectorAll('.quiz-header, .content-card, .info-badge, .rule-list li, .alert');
            allAnimatedElements.forEach(el => {
                el.style.transition = 'none';
                el.style.animation = 'none';
                el.style.transform = 'none';
            });
        }
    });
</script>
@endpush
