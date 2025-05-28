@extends('layouts.dashboard')

@section('title', 'Hasil Kuis')

@push('styles')
<style>
    .result-header {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: white;
        border-radius: 20px;
        padding: clamp(1.5rem, 5vw, 2.5rem);
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.3);
        position: relative;
        overflow: hidden;
        animation: fadeInDown 0.6s ease-out;
        transform-style: preserve-3d;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .result-header:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.4);
    }
    
    .result-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        z-index: 1;
        animation: float 15s ease-in-out infinite alternate;
    }
    
    .result-header::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: -60px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        z-index: 1;
        animation: float 20s ease-in-out infinite alternate-reverse;
    }
    
    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        50% { transform: translate(-15px, 15px) scale(1.05); }
        100% { transform: translate(15px, -15px) scale(0.95); }
    }
    
    .result-header .text-muted {
        color: rgba(255, 255, 255, 0.85) !important;
    }
    
    .score-card {
        text-align: center;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        background-color: white;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.15);
        transition: all 0.4s ease;
        border: 1px solid rgba(229, 231, 235, 0.5);
        animation: fadeInUp 0.5s ease-out;
        position: relative;
        overflow: hidden;
    }
    
    .score-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #4F46E5, #7C3AED, #4F46E5);
        background-size: 200% 100%;
        animation: gradientSlide 3s linear infinite;
    }
    
    @keyframes gradientSlide {
        0% { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }
    
    .score-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.2);
    }
    
    .score-circle {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 3.5rem;
        font-weight: 700;
        color: white;
        position: relative;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        background-size: 300% 300%;
        animation: scoreReveal 1.5s ease-out, gradientAnimation 8s ease infinite;
    }
    
    .score-circle::before {
        content: '';
        position: absolute;
        inset: -5px;
        border-radius: 50%;
        padding: 5px;
        background: linear-gradient(315deg, #4F46E5, transparent, #7C3AED, transparent);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0.7;
        animation: rotate 4s linear infinite;
    }
    
    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes scoreReveal {
        0% {
            transform: scale(0.7);
            opacity: 0;
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    @keyframes gradientAnimation {
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
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .question-card {
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.1);
        border: none;
        margin-bottom: 1.5rem;
        background-color: white;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid rgba(229, 231, 235, 0.5);
        animation: fadeIn 0.6s ease-out;
        animation-fill-mode: both;
        position: relative;
    }
    
    .question-card::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 4px;
        background: linear-gradient(to bottom, #4F46E5, #7C3AED);
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform 0.3s ease;
    }
    
    .question-card:hover::after {
        transform: scaleY(1);
    }
    
    .question-card:hover {
        transform: translateY(-6px) translateX(3px);
        box-shadow: 0 12px 30px rgba(79, 70, 229, 0.2);
    }
    
    .option-label {
        display: block;
        padding: 0.85rem 1.25rem;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        margin-bottom: 0.75rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }
    
    .option-label:hover {
        transform: translateX(5px);
    }
    
    .option-correct {
        background-color: rgba(16, 185, 129, 0.15);
        border-color: #10B981;
        border-width: 2px;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.15);
    }
    
    .option-correct::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(16, 185, 129, 0.1), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s;
    }
    
    .option-correct:hover::before {
        transform: translateX(100%);
    }
    
    .option-incorrect {
        background-color: rgba(239, 68, 68, 0.15);
        border-color: #EF4444;
        border-width: 2px;
        box-shadow: 0 3px 10px rgba(239, 68, 68, 0.15);
    }
    
    .option-incorrect::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(239, 68, 68, 0.1), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s;
    }
    
    .option-incorrect:hover::before {
        transform: translateX(100%);
    }
    
    .option-selected {
        border-width: 2px;
        transform: scale(1.02);
    }
    
    .breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        background: rgba(255, 255, 255, 0.8);
        padding: 0.85rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(5px);
        animation: fadeIn 0.3s ease-out;
    }

    .breadcrumb a {
        color: #4F46E5;
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 500;
        position: relative;
    }

    .breadcrumb a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, #4F46E5, #7C3AED);
        transition: width 0.3s ease;
    }

    .breadcrumb a:hover::after {
        width: 100%;
    }

    .breadcrumb a:hover {
        color: #7C3AED;
    }

    .breadcrumb span {
        margin: 0 0.5rem;
        color: #9CA3AF;
    }

    .btn-back {
        background: transparent;
        color: #4F46E5;
        border: 2px solid #4F46E5;
        border-radius: 12px;
        padding: 0.7rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .btn-back::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 100%;
        background: linear-gradient(90deg, #4F46E5, #7C3AED);
        transition: all 0.3s ease;
        z-index: -1;
    }

    .btn-back:hover {
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(79, 70, 229, 0.25);
        border-color: transparent;
    }

    .btn-back:hover::before {
        width: 100%;
    }

    .main-content {
        background-color: #F9FAFB;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        padding: clamp(1.25rem, 3vw, 1.75rem);
        animation: fadeIn 0.4s ease-out;
    }

    .summary-card {
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.1);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(229, 231, 235, 0.5);
        animation: fadeIn 0.5s ease-out 0.2s;
        animation-fill-mode: both;
        position: relative;
        overflow: hidden;
    }
    
    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4F46E5, #7C3AED);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }
    
    .summary-card:hover::before {
        transform: scaleX(1);
    }
    
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(79, 70, 229, 0.15);
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.85rem 0;
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .summary-item:hover {
        background-color: #F9FAFB;
        transform: translateX(5px);
        border-radius: 8px;
        padding-left: 8px;
        padding-right: 8px;
    }

    .summary-item:last-child {
        border-bottom: none;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .stat-box {
        padding: 1rem;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .stat-box::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 60%);
        opacity: 0;
        transform: scale(0.5);
        transition: transform 0.4s ease, opacity 0.4s ease;
        z-index: -1;
    }
    
    .stat-box:hover::after {
        opacity: 1;
        transform: scale(1);
    }
    
    .stat-box:hover {
        transform: translateY(-5px) scale(1.03);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .explanation-box {
        border-left: 4px solid #4F46E5;
        background-color: #EEF2FF;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
        margin-top: 1rem;
        font-style: italic;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }
    
    .explanation-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(79, 70, 229, 0.05), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s;
    }
    
    .explanation-box:hover::before {
        transform: translateX(100%);
    }
    
    .explanation-box:hover {
        background-color: #E0E7FF;
        transform: translateX(5px) scale(1.02);
        box-shadow: 3px 3px 10px rgba(79, 70, 229, 0.1);
    }
    
    /* Score card progress animations */
    .progress-ring {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .progress-ring circle {
        transition: stroke-dashoffset 1s ease;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
        stroke-linecap: round;
    }
    
    /* Badge animations */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 50rem;
        transition: all 0.2s ease-in-out;
    }
    
    .badge-correct {
        color: #10B981;
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    
    .badge-incorrect {
        color: #EF4444;
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    
    .badge:hover {
        transform: scale(1.05);
    }
    
    /* Responsive Design Improvements */
    @media (max-width: 768px) {
        .score-circle {
            width: 150px;
            height: 150px;
            font-size: 3rem;
        }
        
        .stat-box {
            padding: 0.75rem;
        }
        
        .question-card {
            padding: 0.75rem;
        }
        
        .result-header {
            padding: 1.25rem;
        }
        
        .option-label {
            padding: 0.75rem 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .score-circle {
            width: 120px;
            height: 120px;
            font-size: 2.5rem;
        }
        
        .breadcrumb {
            font-size: 0.75rem;
            padding: 0.65rem 1rem;
        }
        
        .btn-back {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
    }
    
    /* Accessibility Improvements */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
        
        .score-circle::before,
        .explanation-box::before,
        .option-correct::before,
        .option-incorrect::before {
            display: none !important;
        }
    }
    
    /* High contrast improvements */
    @media (prefers-contrast: more) {
        .result-header {
            background: #4F46E5 !important;
        }
        
        .option-correct {
            background-color: rgba(16, 185, 129, 0.3) !important;
            border-color: #10B981 !important;
            border-width: 3px !important;
        }
        
        .option-incorrect {
            background-color: rgba(239, 68, 68, 0.3) !important;
            border-color: #EF4444 !important;
            border-width: 3px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="breadcrumb mb-4">
    <a href="{{ route('siswa.dashboard') }}" aria-label="Kembali ke Dashboard">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="mx-2">/</span>
    <a href="{{ route('siswa.quizzes.index') }}" aria-label="Daftar Kuis">Kuis</a>
    <span class="mx-2">/</span>
    <span class="text-gray-600">Hasil Kuis</span>
</div>

<div class="main-content">
    <div class="row">
        <div class="col-md-8">
            <div class="result-header">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">{{ $quiz->title }}</h1>
                        <p class="text-muted">{{ $quiz->description }}</p>
                    </div>
                    <a href="{{ route('siswa.quizzes.index') }}" class="btn-back" aria-label="Kembali ke Daftar Kuis">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
            
            <div class="score-card">
                <div class="card-body">
                    @php
                        $score = $attempt->score ?? 0;
                        $scoreClass = 'bg-gradient-to-br from-red-500 to-red-600';
                        $scoreText = 'Perlu Ditingkatkan';
                        $ringColor = '#EF4444';
                        $ringPercent = $score;
                        
                        if($score >= 80) {
                            $scoreClass = 'bg-gradient-to-br from-green-400 to-green-600';
                            $scoreText = 'Sangat Baik';
                            $ringColor = '#10B981';
                        } elseif($score >= 70) {
                            $scoreClass = 'bg-gradient-to-br from-blue-400 to-blue-600';
                            $scoreText = 'Baik';
                            $ringColor = '#3B82F6';
                        } elseif($score >= 60) {
                            $scoreClass = 'bg-gradient-to-br from-indigo-400 to-indigo-600';
                            $scoreText = 'Cukup';
                            $ringColor = '#6366F1';
                        } elseif($score >= 40) {
                            $scoreClass = 'bg-gradient-to-br from-yellow-400 to-yellow-600';
                            $scoreText = 'Kurang';
                            $ringColor = '#F59E0B';
                        }
                    @endphp
                    
                    <div class="score-circle {{ $scoreClass }}" aria-label="Nilai {{ $score }}">
                        <svg class="progress-ring" width="180" height="180" aria-hidden="true">
                            <circle
                                stroke="#ffffff"
                                stroke-opacity="0.2"
                                stroke-width="10"
                                fill="transparent"
                                r="80"
                                cx="90"
                                cy="90"
                            />
                            <circle
                                stroke="{{ $ringColor }}"
                                stroke-width="10"
                                fill="transparent"
                                r="80"
                                cx="90"
                                cy="90"
                                stroke-dasharray="{{ 2 * 3.14 * 80 }}"
                                stroke-dashoffset="{{ 2 * 3.14 * 80 * (1 - $ringPercent / 100) }}"
                            />
                        </svg>
                        {{ $score }}
                    </div>
                    <h3 class="text-xl font-semibold mb-2">{{ $scoreText }}</h3>
                    <p class="text-gray-500 mb-4">Kuis diselesaikan pada {{ $attempt->submitted_at->format('d M Y, H:i') }}</p>
                    
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="stat-box bg-green-50 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">Jawaban Benar</div>
                            <div class="text-xl font-semibold text-green-600">{{ $correctAnswers }} dari {{ count($results) }}</div>
                        </div>
                        <div class="stat-box bg-red-50 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">Jawaban Salah</div>
                            <div class="text-xl font-semibold text-red-600">{{ count($results) - $correctAnswers }} dari {{ count($results) }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="w-100 bg-gray-200 rounded-full h-2.5 mb-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2.5 rounded-full animate-pulse" style="width: {{ ($correctAnswers / count($results)) * 100 }}%" role="progressbar" aria-valuenow="{{ ($correctAnswers / count($results)) * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-sm text-gray-500 text-center">Akurasi: {{ round(($correctAnswers / count($results)) * 100) }}%</div>
                    </div>
                </div>
            </div>
            
            <h3 class="text-xl font-semibold mb-4 text-gray-800 d-flex align-items-center">
                <span class="mr-2">Detail Jawaban</span>
                <span class="badge badge-correct ml-2" title="Jawaban Benar">{{ $correctAnswers }}</span>
                <span class="badge badge-incorrect ml-2" title="Jawaban Salah">{{ count($results) - $correctAnswers }}</span>
            </h3>
            
            @foreach($results as $index => $result)
            <div class="question-card" style="animation-delay: {{ 100 + ($index * 50) }}ms" id="question-{{ $index + 1 }}">
                <div class="card-body p-4">
                    <div class="flex justify-between mb-3">
                        <h5 class="card-title font-semibold">Pertanyaan {{ $index + 1 }}</h5>
                        @if($result['is_correct'])
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Benar
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Salah
                            </span>
                        @endif
                    </div>
                    
                    <p class="mb-4 text-gray-700">{{ $result['question']->text }}</p>
                    
                    @if($result['question']->image)
                    <div class="mb-4 text-center">
                        <img src="{{ asset('storage/' . $result['question']->image) }}" alt="Question Image" class="max-w-full h-auto rounded-lg mx-auto shadow-sm hover:shadow-md transition-all" style="max-height: 300px;">
                    </div>
                    @endif
                    
                    <div class="options-container">
                        @foreach($result['question']->options as $option)
                            @php
                                $classes = '';
                                
                                if($option->id == $result['correct_option_id']) {
                                    $classes .= ' option-correct';
                                }
                                
                                if($option->id == $result['selected_option_id']) {
                                    if($option->id != $result['correct_option_id']) {
                                        $classes .= ' option-incorrect';
                                    }
                                    $classes .= ' option-selected';
                                }
                            @endphp
                            
                            <div class="option-label{{ $classes }}" role="option" aria-selected="{{ $option->id == $result['selected_option_id'] ? 'true' : 'false' }}">
                                {{ $option->text }}
                                
                                @if($option->id == $result['correct_option_id'])
                                    <i class="fas fa-check-circle text-green-500 float-right" aria-hidden="true"></i>
                                @elseif($option->id == $result['selected_option_id'] && $option->id != $result['correct_option_id'])
                                    <i class="fas fa-times-circle text-red-500 float-right" aria-hidden="true"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    @if($result['explanation'])
                    <div class="explanation-box">
                        <div class="text-sm font-medium text-indigo-800 mb-1">Penjelasan:</div>
                        <div class="text-sm text-gray-700">{{ $result['explanation'] }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="col-md-4">
            <div class="summary-card">
                <h4 class="text-lg font-semibold mb-3 text-gray-800">Ringkasan Kuis</h4>
                
                <div class="summary-item">
                    <span class="text-gray-500">Mata Pelajaran</span>
                    <span class="font-medium">{{ $quiz->subject ? $quiz->subject->name : 'Umum' }}</span>
                </div>
                
                <div class="summary-item">
                    <span class="text-gray-500">Guru</span>
                    <span class="font-medium">{{ $quiz->teacher ? $quiz->teacher->name : 'Admin' }}</span>
                </div>
                
                <div class="summary-item">
                    <span class="text-gray-500">Jumlah Soal</span>
                    <span class="font-medium">{{ count($results) }}</span>
                </div>
                
                <div class="summary-item">
                    <span class="text-gray-500">Waktu Mulai</span>
                    <span class="font-medium">{{ $attempt->created_at->format('d M Y, H:i') }}</span>
                </div>
                
                <div class="summary-item">
                    <span class="text-gray-500">Waktu Selesai</span>
                    <span class="font-medium">{{ $attempt->submitted_at->format('d M Y, H:i') }}</span>
                </div>
                
                <div class="summary-item">
                    <span class="text-gray-500">Durasi</span>
                    <span class="font-medium">{{ $attempt->created_at->diffForHumans($attempt->submitted_at, true) }}</span>
                </div>
            </div>

            <div class="summary-card">
                <h4 class="text-lg font-semibold mb-3 text-gray-800">Navigasi Cepat</h4>
                <div class="d-grid gap-1">
                    @foreach($results as $index => $result)
                        <a href="#question-{{ $index + 1 }}" class="btn btn-sm {{ $result['is_correct'] ? 'btn-outline-success' : 'btn-outline-danger' }} mb-1 position-relative" style="animation: fadeIn 0.3s ease-out {{ 0.1 + ($index * 0.05) }}s both;">
                            {{ $index + 1 }}
                            @if($result['is_correct'])
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="font-size: 0.6rem; padding: 0.2em 0.4em;">
                                    <i class="fas fa-check" aria-hidden="true"></i>
                                </span>
                            @else
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.2em 0.4em;">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('siswa.quizzes.index') }}" class="btn-back w-100" aria-label="Kembali ke Daftar Kuis">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Kuis
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate question cards on scroll
        const cards = document.querySelectorAll('.question-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        cards.forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(20px)';
            observer.observe(card);
        });
        
        // Add smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    // Only use smooth scrolling if user hasn't requested reduced motion
                    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    
                    if (!prefersReducedMotion) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    } else {
                        targetElement.scrollIntoView();
                    }
                    
                    // Add highlight effect to the target element
                    targetElement.classList.add('highlight-pulse');
                    setTimeout(() => {
                        targetElement.classList.remove('highlight-pulse');
                    }, 1500);
                }
            });
        });
        
        // Add keyboard navigation for accessibility
        document.addEventListener('keydown', function(e) {
            // Navigate between questions with arrow keys
            if (e.key === 'ArrowDown' || e.key === 'ArrowRight') {
                e.preventDefault();
                navigateQuestions('next');
            } else if (e.key === 'ArrowUp' || e.key === 'ArrowLeft') {
                e.preventDefault();
                navigateQuestions('prev');
            }
        });
        
        function navigateQuestions(direction) {
            const questions = document.querySelectorAll('.question-card');
            const currentIndex = Array.from(questions).findIndex(q => 
                q.getBoundingClientRect().top >= 0 && 
                q.getBoundingClientRect().top <= window.innerHeight / 2
            );
            
            if (currentIndex !== -1) {
                let targetIndex;
                if (direction === 'next' && currentIndex < questions.length - 1) {
                    targetIndex = currentIndex + 1;
                } else if (direction === 'prev' && currentIndex > 0) {
                    targetIndex = currentIndex - 1;
                } else {
                    return;
                }
                
                const targetQuestion = questions[targetIndex];
                const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                
                if (!prefersReducedMotion) {
                    targetQuestion.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                } else {
                    targetQuestion.scrollIntoView();
                }
                
                targetQuestion.classList.add('highlight-pulse');
                setTimeout(() => {
                    targetQuestion.classList.remove('highlight-pulse');
                }, 1500);
            }
        }
    });
</script>
@endpush
