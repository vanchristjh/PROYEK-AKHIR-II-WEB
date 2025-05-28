@extends('layouts.dashboard')

@section('title', 'Detail Pengerjaan Kuis')

@section('header', 'Detail Pengerjaan Kuis')

@section('content')
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-user-graduate text-9xl"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mr-2">
                            <i class="fas fa-book mr-1"></i> {{ $attempt->quiz->subject->name ?? 'Tidak ada mata pelajaran' }}
                        </span>
                        @if($attempt->score >= ($attempt->quiz->passing_grade ?? 70))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                <i class="fas fa-check-circle mr-1"></i> Lulus
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                <i class="fas fa-times-circle mr-1"></i> Tidak Lulus
                            </span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold">Hasil Kuis: {{ $attempt->quiz->title }}</h2>
                    <p class="text-blue-100 mt-1">
                        <i class="fas fa-user mr-1"></i> 
                        {{ $attempt->student->name ?? 'Unknown' }} - {{ $attempt->student->classroom->name ?? 'Tidak ada kelas' }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('guru.quizzes.results', $attempt->quiz_id) }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors backdrop-blur-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Hasil
                    </a>
                    <a href="{{ route('guru.quizzes.show', $attempt->quiz_id) }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors backdrop-blur-sm">
                        <i class="fas fa-eye mr-2"></i> Detail Kuis
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Summary Card -->
    <div class="bg-white rounded-xl shadow-sm mb-6 overflow-hidden border border-gray-100/60">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-medium text-gray-800 flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                    <i class="fas fa-chart-pie text-indigo-600"></i>
                </div>
                <span>Ringkasan Nilai</span>
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Score -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 text-center relative">
                    <div class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-{{ $attempt->score >= ($attempt->quiz->passing_grade ?? 70) ? 'green' : 'red' }}-100 text-{{ $attempt->score >= ($attempt->quiz->passing_grade ?? 70) ? 'green' : 'red' }}-800 text-xs font-medium px-2 py-0.5 rounded-full border border-{{ $attempt->score >= ($attempt->quiz->passing_grade ?? 70) ? 'green' : 'red' }}-200">
                        {{ $attempt->score >= ($attempt->quiz->passing_grade ?? 70) ? 'Lulus' : 'Tidak Lulus' }}
                    </div>
                    <div class="mt-3">
                        <div class="text-sm text-gray-500 mb-1">Nilai Akhir</div>
                        <div class="text-3xl font-bold text-{{ $attempt->score >= ($attempt->quiz->passing_grade ?? 70) ? 'green' : 'red' }}-600">{{ $attempt->score }}</div>
                        <div class="text-xs text-gray-500">Dari 100</div>
                    </div>
                </div>
                
                <!-- Duration -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Waktu Pengerjaan</div>
                            <div class="text-lg font-medium">{{ $attempt->duration_minutes ?? '-' }} menit</div>
                            <div class="text-xs text-gray-500">Dari {{ $attempt->quiz->duration ?? 0 }} menit</div>
                        </div>
                    </div>
                </div>
                
                <!-- Submitted at -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Waktu Selesai</div>
                            <div class="text-lg font-medium">{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}</div>
                            @if($attempt->quiz && $attempt->quiz->end_time && $attempt->submitted_at && $attempt->submitted_at->gt(\Carbon\Carbon::parse($attempt->quiz->end_time)))
                                <div class="text-xs text-red-500">Terlambat</div>
                            @else
                                <div class="text-xs text-green-500">Tepat waktu</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Correct Answers -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Jawaban Benar</div>
                            <div class="text-lg font-medium">{{ $correctAnswers ?? 0 }} / {{ $totalQuestions ?? 0 }}</div>
                            <div class="text-xs text-gray-500">{{ $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0 }}% akurasi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Answer Details -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/60 mb-6">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-medium text-gray-800 flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="fas fa-list-ol text-green-600"></i>
                </div>
                <span>Detail Jawaban</span>
            </h3>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($answers as $index => $answer)
                <div class="p-6 hover:bg-gray-50 transition-colors" id="question-{{ $index + 1 }}">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <div class="px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-800 text-xs font-medium">
                                    Soal #{{ $index + 1 }}
                                </div>
                                <div class="px-2.5 py-1 rounded-md bg-{{ $answer->question->type == 'multiple_choice' ? 'blue' : ($answer->question->type == 'true_false' ? 'green' : 'amber') }}-100 text-{{ $answer->question->type == 'multiple_choice' ? 'blue' : ($answer->question->type == 'true_false' ? 'green' : 'amber') }}-800 text-xs font-medium">
                                    {{ $answer->question->type == 'multiple_choice' ? 'Pilihan Ganda' : ($answer->question->type == 'true_false' ? 'Benar/Salah' : 'Essay') }}
                                </div>
                                <div class="px-2.5 py-1 rounded-md text-xs font-medium {{ $answer->is_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $answer->is_correct ? 'Benar' : 'Salah' }}
                                </div>
                                <div class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 text-xs font-medium">
                                    {{ $answer->question->score ?? 1 }} Poin
                                </div>
                            </div>

                            <div class="prose max-w-none mb-4">
                                <h4 class="text-base font-medium text-gray-800">{{ $answer->question->content }}</h4>
                            </div>

                            @if($answer->question->type == 'multiple_choice')
                                <div class="mt-3 pl-4 space-y-2">
                                    <p class="text-sm font-medium text-gray-600">Pilihan Jawaban:</p>
                                    @foreach($answer->question->options as $i => $option)
                                        <div class="flex items-center {{ $option['is_correct'] ? 'text-green-700' : ($answer->answer == $i && !$option['is_correct'] ? 'text-red-700' : 'text-gray-700') }}">
                                            <div class="w-5 h-5 rounded-full 
                                                {{ $option['is_correct'] ? 'bg-green-500 text-white' : 
                                                  ($answer->answer == $i && !$option['is_correct'] ? 'bg-red-500 text-white' : 'bg-gray-200') }} 
                                                flex items-center justify-center text-xs mr-2">
                                                {{ $option['is_correct'] ? '✓' : ($answer->answer == $i && !$option['is_correct'] ? '✗' : '') }}
                                            </div>
                                            <span class="text-sm {{ ($answer->answer == $i || $option['is_correct']) ? 'font-medium' : '' }}">
                                                {{ $option['text'] }}
                                                @if($answer->answer == $i)
                                                    <span class="text-xs ml-2">(Jawaban Siswa)</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($answer->question->type == 'true_false')
                                <div class="mt-3 pl-4">
                                    <p class="text-sm font-medium text-gray-600">Pilihan Jawaban:</p>
                                    <div class="flex space-x-6 mt-2">
                                        <div class="flex items-center {{ $answer->question->answer == 'true' ? 'text-green-700' : ($answer->answer == 'true' && $answer->question->answer != 'true' ? 'text-red-700' : 'text-gray-700') }}">
                                            <div class="w-5 h-5 rounded-full 
                                                {{ $answer->question->answer == 'true' ? 'bg-green-500 text-white' : 
                                                  ($answer->answer == 'true' && $answer->question->answer != 'true' ? 'bg-red-500 text-white' : 'bg-gray-200') }} 
                                                flex items-center justify-center text-xs mr-2">
                                                {{ $answer->question->answer == 'true' ? '✓' : ($answer->answer == 'true' && $answer->question->answer != 'true' ? '✗' : '') }}
                                            </div>
                                            <span class="text-sm {{ ($answer->answer == 'true' || $answer->question->answer == 'true') ? 'font-medium' : '' }}">
                                                Benar
                                                @if($answer->answer == 'true')
                                                    <span class="text-xs ml-2">(Jawaban Siswa)</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex items-center {{ $answer->question->answer == 'false' ? 'text-green-700' : ($answer->answer == 'false' && $answer->question->answer != 'false' ? 'text-red-700' : 'text-gray-700') }}">
                                            <div class="w-5 h-5 rounded-full 
                                                {{ $answer->question->answer == 'false' ? 'bg-green-500 text-white' : 
                                                  ($answer->answer == 'false' && $answer->question->answer != 'false' ? 'bg-red-500 text-white' : 'bg-gray-200') }} 
                                                flex items-center justify-center text-xs mr-2">
                                                {{ $answer->question->answer == 'false' ? '✓' : ($answer->answer == 'false' && $answer->question->answer != 'false' ? '✗' : '') }}
                                            </div>
                                            <span class="text-sm {{ ($answer->answer == 'false' || $answer->question->answer == 'false') ? 'font-medium' : '' }}">
                                                Salah
                                                @if($answer->answer == 'false')
                                                    <span class="text-xs ml-2">(Jawaban Siswa)</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @elseif($answer->question->type == 'essay')
                                <div class="mt-3 pl-4">
                                    <p class="text-sm font-medium text-gray-600">Jawaban Siswa:</p>
                                    <div class="mt-2 bg-gray-50 p-3 rounded border border-gray-200">
                                        <p class="text-sm text-gray-700">{{ $answer->answer_text }}</p>
                                    </div>
                                    
                                    @if($answer->question->answer_key)
                                        <p class="text-sm font-medium text-gray-600 mt-3">Kunci Jawaban / Kata Kunci:</p>
                                        <div class="mt-1 bg-green-50 p-3 rounded border border-green-200">
                                            <p class="text-sm text-green-700">{{ $answer->question->answer_key }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-4">
                                        <form action="{{ route('guru.quiz-answers.grade', $answer->id) }}" method="POST" class="flex items-end gap-4">
                                            @csrf
                                            <div>
                                                <label for="score_{{ $answer->id }}" class="block text-xs font-medium text-gray-700 mb-1">Nilai (0-{{ $answer->question->score }})</label>
                                                <input type="number" name="score" id="score_{{ $answer->id }}" min="0" max="{{ $answer->question->score }}" value="{{ $answer->score }}" 
                                                    class="w-24 rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" required>
                                            </div>
                                            <button type="submit" class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                                                <i class="fas fa-save mr-1"></i> Simpan Nilai
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-question-circle text-gray-400 text-2xl"></i>
                    </div>
                    <h5 class="text-gray-500 font-medium">Tidak ada jawaban</h5>
                    <p class="text-gray-400 text-sm mt-1">Siswa belum menjawab pertanyaan apapun.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end mb-8 space-x-4">
        @if($attempt->score === null || $attempt->score === '')
            <form action="{{ route('guru.quiz-attempts.autograde', $attempt->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-magic mr-2"></i> Nilai Otomatis
                </button>
            </form>
        @endif
        
        <a href="{{ route('guru.quizzes.results', $attempt->quiz_id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Hasil
        </a>
    </div>
@endsection

@push('styles')
<style>
    .prose h4 {
        margin-top: 0;
        margin-bottom: 0.5rem;
    }
    
    .prose p {
        margin-top: 0;
        margin-bottom: 1rem;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Highlight question if anchored
        if (window.location.hash) {
            const questionElement = document.querySelector(window.location.hash);
            if (questionElement) {
                questionElement.classList.add('bg-yellow-50');
                setTimeout(() => {
                    questionElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }, 300);
            }
        }
        
        // Validate score inputs for essay questions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const scoreInput = this.querySelector('input[name="score"]');
                if (scoreInput) {
                    const max = parseInt(scoreInput.getAttribute('max'));
                    const value = parseInt(scoreInput.value);
                    
                    if (isNaN(value) || value < 0 || value > max) {
                        e.preventDefault();
                        alert(`Nilai harus berada di antara 0 dan ${max}`);
                    }
                }
            });
        });
    });
</script>
@endpush
