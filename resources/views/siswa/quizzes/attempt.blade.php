@extends('layouts.dashboard')

@section('title', 'Mengerjakan Kuis')

@push('styles')
<style>
    .quiz-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .question-card {
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.15);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        margin-bottom: 2rem;
        background-color: white;
        overflow: hidden;
        border: 1px solid rgba(229, 231, 235, 0.7);
        animation: fadeInUp 0.5s ease-out;
        position: relative;
    }
    
    .question-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(79, 70, 229, 0.25);
    }
    
    .question-card .card-body {
        position: relative;
        padding: 2rem;
    }
    
    .question-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: linear-gradient(90deg, #4F46E5, #7C3AED);
        z-index: 1;
        transition: height 0.3s ease;
    }
    
    .question-card:hover::before {
        height: 8px;
    }
    
    .option-label {
        display: block;
        padding: 1.15rem 1.5rem;
        border-radius: 16px;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        background-color: white;
    }
      .option-label:hover {
        background-color: #f9fafb;
        transform: translateX(12px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.15);
        border-color: #d1d5db;
    }
    
    .option-label::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(79, 70, 229, 0.05), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .option-label:hover::after {
        opacity: 1;
    }
    
    .option-input:checked + .option-label {
        background-color: #ede9fe;
        border-color: #6366F1;
        border-width: 2px;
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.25);
        transform: translateX(12px) scale(1.02);
    }
    
    .option-input:checked + .option-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 6px;
        background: linear-gradient(to bottom, #4F46E5, #7C3AED);
        animation: slideIn 0.3s forwards;
    }
    
    @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
    }
    
    .quiz-timer {
        position: sticky;
        top: 20px;
        z-index: 100;
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.35);
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        animation: pulseTimer 2s infinite alternate;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
      @keyframes pulseTimer {
        0% {
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.35);
        }
        100% {
            box-shadow: 0 20px 45px rgba(79, 70, 229, 0.55);
        }
    }
    
    .time-warning {
        color: #FBBF24;
        font-weight: bold;
        animation: pulse 1s infinite;
    }
    
    .time-danger {
        color: #FECACA;
        animation: pulse 0.5s infinite;
        font-weight: bold;
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
    
    .quiz-timer-divider {
        width: 2px;
        height: 30px;
        background-color: rgba(255, 255, 255, 0.2);
        margin: 0 15px;
    }
    
    .timer-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .timer-label {
        font-size: 0.75rem;
        opacity: 0.8;
        margin-bottom: 0.35rem;
    }
    
    .timer-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .navigation-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
        color: #4F46E5;
        border: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .navigation-btn::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
        opacity: 0;
        transition: opacity 0.3s ease;
        transform: scale(0.5);
    }
    
    .navigation-btn:hover {
        background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
        color: white;
        transform: scale(1.15) rotate(5deg);
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.35);
    }
    
    .navigation-btn:hover::after {
        opacity: 1;
        transform: scale(1);
    }
      .question-number-btn {
        width: 48px;
        height: 48px;
        margin: 6px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        background-color: #F9FAFB;
        border: 2px solid #E5E7EB;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }
    
    .question-number-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(79, 70, 229, 0.2) 0%, rgba(79, 70, 229, 0) 70%);
        transform: scale(0);
        opacity: 0;
        transition: all 0.4s ease;
    }
    
    .question-number-btn:hover {
        background-color: #EEF2FF;
        border-color: #6366F1;
        transform: scale(1.15) translateY(-5px);
        box-shadow: 0 12px 25px rgba(99, 102, 241, 0.2);
        z-index: 2;
    }
    
    .question-number-btn:hover::before {
        transform: scale(1.5);
        opacity: 1;
    }
    
    .question-answered {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
        border-color: #4F46E5;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    }
    
    .question-current {
        border: 3px solid #6366F1;
        color: #4F46E5;
        font-weight: bold;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        animation: pulseBorder 2s infinite alternate;
    }
    
    @keyframes pulseBorder {
        0% { box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2); }
        100% { box-shadow: 0 0 0 8px rgba(99, 102, 241, 0.1); }
    }
    
    .question-navigator {
        position: sticky;
        top: 120px;
        background-color: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.15);
        padding: 1.75rem;
        border: 1px solid rgba(229, 231, 235, 0.7);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        animation: fadeInRight 0.5s ease-out;
    }
    
    .question-navigator:hover {
        box-shadow: 0 20px 45px rgba(79, 70, 229, 0.2);
        transform: translateY(-5px);
    }
    
    .progress-container {
        width: 100%;
        height: 8px;
        background-color: #E0E7FF;
        border-radius: 10px;
        margin: 1.5rem 0;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #4F46E5, #7C3AED);
        border-radius: 10px;
        transition: width 0.5s ease;
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, 
            rgba(255, 255, 255, 0.1) 0%, 
            rgba(255, 255, 255, 0.2) 20%, 
            rgba(255, 255, 255, 0.1) 40%
        );
        width: 200%;
        animation: shimmer 2s infinite linear;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(50%); }
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        background-color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        animation: fadeIn 0.4s ease-out;
    }

    .breadcrumb a {
        color: #6366F1;
        text-decoration: none;
        transition: all 0.2s ease;
        font-weight: 500;
    }
    
    .breadcrumb a:hover {
        color: #4F46E5;
        text-decoration: underline;
    }

    .breadcrumb span {
        margin: 0 0.5rem;
        color: #9CA3AF;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 0.85rem 1.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 5px 15px rgba(79, 70, 229, 0.25);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #4F46E5 0%, #3730A3 100%);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #9CA3AF 0%, #6B7280 100%);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 0.85rem 1.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 5px 15px rgba(107, 114, 128, 0.25);
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(107, 114, 128, 0.35);
    }

    .btn-danger {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 0.85rem 1.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 5px 15px rgba(220, 38, 38, 0.25);
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(220, 38, 38, 0.35);
    }

    .main-content {
        background-color: #F9FAFB;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        padding: 1.75rem;
        animation: fadeIn 0.5s ease-out;
    }
    
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        color: white;
        border-bottom: none;
        padding: 1.5rem;
    }
    
    .modal-title {
        font-weight: 600;
    }
    
    .btn-close {
        color: white;
        box-shadow: none;
        opacity: 0.8;
    }
    
    .modal-body {
        padding: 1.75rem;
    }
    
    .modal-footer {
        border-top: none;
        padding: 1.25rem 1.75rem 1.75rem;
    }
    
    .alert {
        border-radius: 12px;
        border: none;
        padding: 1.25rem;
    }
    
    .alert-warning {
        background-color: #FEF3C7;
        color: #92400E;
        border-left: 4px solid #F59E0B;
    }
    
    .progress {
        height: 12px;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .progress-bar {
        background: linear-gradient(90deg, #4F46E5, #7C3AED);
        transition: width 0.5s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    
    .legend-item:hover {
        transform: translateX(5px);
    }
    
    .legend-circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 10px;
    }

    /* Quiz Progress Section */
    .quiz-progress {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        animation: fadeInUp 0.6s ease-out;
    }
    
    .progress-stats {
        font-size: 0.9rem;
        color: #6B7280;
        margin-left: 1rem;
        display: flex;
        align-items: center;
    }
    
    .progress-stats-completed {
        color: #4F46E5;
        font-weight: 600;
        margin: 0 0.25rem;
    }
    
    .progress-stats-total {
        color: #4B5563;
        margin-left: 0.25rem;
    }
    
    /* Question Text Enhancement */
    .question-text {
        font-size: 1.25rem;
        color: #1F2937;
        margin-bottom: 1.5rem;
        line-height: 1.6;
        font-weight: 500;
    }
    
    /* Enhanced Submit Button */
    .submit-btn {
        padding: 1rem 2rem;
        border-radius: 50px;
        border: none;
        background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
        color: white;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 15px 35px rgba(79, 70, 229, 0.25);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }
    
    .submit-btn:hover {
        transform: translateY(-5px) scale(1.03);
        box-shadow: 0 20px 45px rgba(79, 70, 229, 0.35);
    }
    
    .submit-btn::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: linear-gradient(90deg, 
            rgba(255, 255, 255, 0) 0%, 
            rgba(255, 255, 255, 0.2) 50%, 
            rgba(255, 255, 255, 0) 100%);
        transform: translateX(-100%);
    }
    
    .submit-btn:hover::after {
        animation: shine 1.5s infinite;
    }
    
    @keyframes shine {
        from { transform: translateX(-100%); }
        to { transform: translateX(100%); }
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .question-navigator {
            position: relative;
            top: 0;
            margin-bottom: 2rem;
        }
        
        .quiz-timer {
            position: relative;
            top: 0;
            margin-bottom: 1.5rem;
        }
    }
    
    @media (max-width: 768px) {
        .question-number-btn {
            width: 40px;
            height: 40px;
            margin: 4px;
            font-size: 14px;
        }
        
        .question-navigator-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .navigation-btn {
            width: 48px;
            height: 48px;
        }
        
        .quiz-timer {
            padding: 1rem;
        }
        
        .timer-section {
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        
        .timer-label {
            margin-right: 0.5rem;
            margin-bottom: 0;
        }
    }
    
    @media (max-width: 640px) {
        .question-card {
            margin-bottom: 1.5rem;
        }
        
        .question-card .card-body {
            padding: 1.5rem;
        }
        
        .option-label {
            padding: 1rem;
        }
        
        .btn-primary, .btn-secondary, .btn-danger {
            width: 100%;
            padding: 0.85rem 1.5rem;
            margin-bottom: 0.5rem;
        }
    }
    
    /* Accessibility Improvements */
    @media (prefers-reduced-motion: reduce) {
        .question-card, .quiz-timer, .question-navigator,
        .option-label, .navigation-btn, .question-number-btn,
        .progress-bar, .submit-btn, .btn-primary, .btn-secondary, .btn-danger {
            animation: none !important;
            transition: opacity 0.1s linear !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
        }
    }
</style>
@endpush

@section('content')
<div class="breadcrumb mb-4">
    <a href="{{ route('siswa.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <span class="mx-2">/</span>
    <a href="{{ route('siswa.quizzes.index') }}" class="text-indigo-600 hover:text-indigo-800">Kuis</a>
    <span class="mx-2">/</span>
    <span class="text-gray-600">Mengerjakan Kuis</span>
</div>

<div class="main-content">
    <form id="quiz-form" action="{{ route('siswa.quizzes.submit', $quiz->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="quiz-container">
                    <div class="quiz-timer mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-clock me-2"></i> Waktu Tersisa:
                            </div>
                            <div id="timer" data-end-time="{{ $endTime }}" class="font-bold">
                                {{ $timeLimit ?? 'Tidak ada batas' }}
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 8px;">
                            <div id="timer-progress" class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="question-card mb-4">
                        <div class="card-body p-4">
                            <h1 class="text-xl font-bold mb-3 text-gray-800">{{ $quiz->title }}</h1>
                            <p class="text-gray-500 mb-2">Harap jawab semua pertanyaan dengan teliti.</p>
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info text-white me-2 p-2 rounded"><i class="fas fa-info-circle me-1"></i> Info</span>
                                    <span class="text-sm text-gray-600">Klik nomor soal untuk berpindah antar soal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    <div id="questions-container">
                        @foreach($questions as $index => $question)
                        <div class="question-card" id="question-{{ $index + 1 }}" style="{{ $index > 0 ? 'display:none' : '' }}">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="text-lg font-semibold text-gray-800">Pertanyaan {{ $index + 1 }}</h5>
                                    <span class="badge bg-indigo-600 text-white px-3 py-2 rounded-full">{{ $index + 1 }} dari {{ count($questions) }}</span>
                                </div>
                                
                                <p class="question-text">{{ $question->text }}</p>
                                
                                @if($question->image)
                                <div class="mb-4 text-center">
                                    <img src="{{ asset('storage/' . $question->image) }}" alt="Gambar Pertanyaan" class="img-fluid rounded-xl mx-auto shadow-sm hover:shadow-md transition-all" style="max-height: 300px;">
                                </div>
                                @endif
                                
                                <div class="options-container">
                                    @foreach($question->options as $option)
                                    <div class="mb-3">
                                        <input type="radio" name="answers[{{ $question->id }}]" id="option-{{ $question->id }}-{{ $option->id }}" value="{{ $option->id }}" class="option-input d-none question-option" data-question="{{ $index + 1 }}">
                                        <label for="option-{{ $question->id }}-{{ $option->id }}" class="option-label">
                                            {{ $option->text }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-between mt-5">
                                    @if($index > 0)
                                    <button type="button" class="navigation-btn prev-btn" data-target="{{ $index }}">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    @else
                                    <div></div>
                                    @endif
                                    
                                    @if($index < count($questions) - 1)
                                    <button type="button" class="navigation-btn next-btn" data-target="{{ $index + 2 }}">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                    @else
                                    <button type="button" class="submit-btn" id="show-confirm-submit">
                                        <i class="fas fa-check me-1"></i> Selesai
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="question-navigator">
                    <h5 class="text-lg font-semibold mb-4 text-gray-800">Navigasi Soal</h5>
                    <div class="d-flex flex-wrap justify-content-center" id="question-numbers">
                        @foreach($questions as $index => $question)
                        <div class="question-number-btn {{ $index === 0 ? 'question-current' : '' }}" 
                            data-target="{{ $index + 1 }}">
                            {{ $index + 1 }}
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <div class="legend-item">
                            <div class="legend-circle bg-gray-200"></div>
                            <span class="text-sm text-gray-600">Belum dijawab</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-circle bg-indigo-600"></div>
                            <span class="text-sm text-gray-600">Sudah dijawab</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-circle border-2 border-indigo-600 bg-white"></div>
                            <span class="text-sm text-gray-600">Soal aktif</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="progress mb-3" style="height: 12px; border-radius: 6px;">
                            <div id="progress-bar" class="progress-bar bg-indigo-600" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-sm text-gray-500 mb-4 text-center">Total soal terjawab: <span id="answered-count">0</span> dari {{ count($questions) }}</div>
                        <button type="button" class="btn-danger w-100" id="show-confirm-submit">
                            <i class="fas fa-paper-plane me-1"></i> Kirim Jawaban
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Pengiriman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Apakah Anda yakin ingin mengirimkan jawaban Anda?</p>
                        <div id="unanswered-warning" class="alert alert-warning mt-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i> Anda masih memiliki <span id="unanswered-count">0</span> soal yang belum dijawab.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary" id="final-submit">Ya, Kirim Jawaban</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate progress
    function updateProgress() {
        const totalQuestions = {{ count($questions) }};
        const answeredQuestions = document.querySelectorAll('.question-answered').length;
        const percentage = (answeredQuestions / totalQuestions) * 100;
        
        document.getElementById('progress-bar').style.width = percentage + '%';
        document.getElementById('progress-bar').setAttribute('aria-valuenow', percentage);
        document.getElementById('answered-count').textContent = answeredQuestions;
        
        // Check if all questions are answered
        if (answeredQuestions === totalQuestions) {
            document.getElementById('progress-bar').style.backgroundColor = '#10B981';
        }
    }
    
    // Add event listener for radio buttons
    const radioButtons = document.querySelectorAll('.question-option');
    radioButtons.forEach(function(radio) {
        radio.addEventListener('change', function() {
            const questionNumber = this.getAttribute('data-question');
            document.querySelector(`.question-number-btn[data-target="${questionNumber}"]`).classList.add('question-answered');
            updateProgress();
        });
    });
    
    // Question navigation
    const questionBtns = document.querySelectorAll('.question-number-btn');
    const questionCards = document.querySelectorAll('[id^="question-"]');
    
    questionBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetQuestion = this.getAttribute('data-target');
            
            // Hide all question cards
            questionCards.forEach(function(card) {
                card.style.display = 'none';
            });
            
            // Show target question card
            document.getElementById(`question-${targetQuestion}`).style.display = 'block';
            
            // Update active question button
            questionBtns.forEach(function(btn) {
                btn.classList.remove('question-current');
            });
            
            this.classList.add('question-current');
        });
    });
    
    // Next and previous buttons
    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    
    nextBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetQuestion = this.getAttribute('data-target');
            
            // Hide all question cards
            questionCards.forEach(function(card) {
                card.style.display = 'none';
            });
            
            // Show target question card
            document.getElementById(`question-${targetQuestion}`).style.display = 'block';
            
            // Update active question button
            questionBtns.forEach(function(btn) {
                btn.classList.remove('question-current');
            });
            
            document.querySelector(`.question-number-btn[data-target="${targetQuestion}"]`).classList.add('question-current');
        });
    });
    
    prevBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetQuestion = this.getAttribute('data-target');
            
            // Hide all question cards
            questionCards.forEach(function(card) {
                card.style.display = 'none';
            });
            
            // Show target question card
            document.getElementById(`question-${targetQuestion}`).style.display = 'block';
            
            // Update active question button
            questionBtns.forEach(function(btn) {
                btn.classList.remove('question-current');
            });
            
            document.querySelector(`.question-number-btn[data-target="${targetQuestion}"]`).classList.add('question-current');
        });
    });
    
    // Show confirmation modal
    const showConfirmBtns = document.querySelectorAll('#show-confirm-submit');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
    
    showConfirmBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const totalQuestions = {{ count($questions) }};
            const answeredQuestions = document.querySelectorAll('.question-answered').length;
            const unansweredQuestions = totalQuestions - answeredQuestions;
            
            // Show warning if there are unanswered questions
            if (unansweredQuestions > 0) {
                document.getElementById('unanswered-warning').style.display = 'block';
                document.getElementById('unanswered-count').textContent = unansweredQuestions;
            } else {
                document.getElementById('unanswered-warning').style.display = 'none';
            }
            
            confirmModal.show();
        });
    });
    
    // Timer functionality
    const timerElement = document.getElementById('timer');
    const timerProgressBar = document.getElementById('timer-progress');
    
    if (timerElement) {
        const endTime = new Date(timerElement.getAttribute('data-end-time')).getTime();
        const totalTime = {{ isset($quiz->time_limit_minutes) ? $quiz->time_limit_minutes * 60 * 1000 : 0 }};
        
        // Function to update timer
        function updateTimer() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            if (distance <= 0) {
                clearInterval(timerInterval);
                document.getElementById('final-submit').click(); // Auto-submit when time is up
                return;
            }
            
            // Calculate hours, minutes, and seconds
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Update timer display
            timerElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // Update progress bar
            if (totalTime > 0) {
                const percentLeft = (distance / totalTime) * 100;
                timerProgressBar.style.width = `${percentLeft}%`;
                
                // Change color based on time left
                if (percentLeft < 30) {
                    timerProgressBar.style.backgroundColor = '#EF4444';
                    timerElement.classList.add('time-danger');
                } else if (percentLeft < 60) {
                    timerProgressBar.style.backgroundColor = '#F59E0B';
                    timerElement.classList.add('time-warning');
                }
            }
        }
        
        // Update timer every second
        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);
    }
    
    // Initialize progress
    updateProgress();
});
</script>
@endpush
