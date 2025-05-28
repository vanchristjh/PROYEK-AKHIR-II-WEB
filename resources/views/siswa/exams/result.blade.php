@extends('layouts.siswa')

@section('title', 'Hasil Ujian')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .result-card {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .score-display {
        font-size: 3rem;
        font-weight: bold;
    }
    
    .question-item {
        border-left: 4px solid #E5E7EB;
        transition: border-color 0.3s ease;
    }
    
    .question-item.correct {
        border-left-color: #10B981;
    }
    
    .question-item.incorrect {
        border-left-color: #EF4444;
    }
    
    .question-item.ungraded {
        border-left-color: #F59E0B;
    }
    
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-correct {
        background-color: #D1FAE5;
        color: #047857;
    }
    
    .status-incorrect {
        background-color: #FEE2E2;
        color: #B91C1C;
    }
    
    .status-ungraded {
        background-color: #FEF3C7;
        color: #92400E;
    }
</style>
@endpush

@section('content')
<div class="container px-4 py-5">
    <div class="mb-6">
        <a href="{{ route('siswa.exams.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Ujian
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Score summary -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow result-card">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Hasil Ujian</h2>
                <h3 class="text-lg font-medium text-gray-700 mb-4">{{ $exam->title }}</h3>
                
                <div class="flex flex-col items-center justify-center py-6 border-t border-b">
                    @php
                        $scoreClass = $attempt->score >= $exam->passing_grade ? 'text-green-600' : 'text-red-600';
                        $passStatus = $attempt->score >= $exam->passing_grade ? 'Lulus' : 'Tidak Lulus';
                        $statusClass = $attempt->score >= $exam->passing_grade ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    @endphp
                    
                    <div class="score-display {{ $scoreClass }}">{{ number_format($attempt->score, 1) }}</div>
                    <div class="text-sm text-gray-500 mt-1">dari 100</div>
                    
                    <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full {{ $statusClass }}">
                        <span>{{ $passStatus }}</span>
                    </div>
                    
                    @if(!$attempt->is_graded)
                        <div class="mt-3 text-sm text-yellow-600">
                            <svg class="w-5 h-5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Beberapa soal masih dalam proses penilaian
                        </div>
                    @endif
                </div>
                
                <div class="mt-5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Tanggal Pengerjaan</span>
                        <span class="text-sm text-gray-800">{{ $attempt->start_time->format('d M Y, H:i') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Waktu Selesai</span>
                        <span class="text-sm text-gray-800">{{ $attempt->submit_time->format('d M Y, H:i') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Durasi</span>
                        <span class="text-sm text-gray-800">{{ $attempt->start_time->diffInMinutes($attempt->submit_time) }} menit</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Nilai Minimum Lulus</span>
                        <span class="text-sm text-gray-800">{{ $exam->passing_grade }}</span>
                    </div>
                    
                    @if($attempt->is_graded)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Jumlah Benar</span>
                            <span class="text-sm text-gray-800">{{ $correctAnswersCount }} dari {{ $questionsCount }}</span>
                        </div>
                    @endif
                </div>
                
                <div class="mt-6 pt-4 border-t">
                    <a href="{{ route('siswa.exams.show', $exam->id) }}" class="block text-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-blue-700">
                        Kembali ke Detail Ujian
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Question results -->
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow result-card">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Jawaban</h2>
                
                <div class="space-y-5">
                    @foreach($answers as $answer)
                        @php
                            $questionClass = '';
                            $statusBadge = '';
                            
                            if ($answer->is_graded) {
                                if ($answer->points_earned > 0) {
                                    $questionClass = 'correct';
                                    $statusBadge = '<span class="status-badge status-correct">Benar</span>';
                                } else {
                                    $questionClass = 'incorrect';
                                    $statusBadge = '<span class="status-badge status-incorrect">Salah</span>';
                                }
                            } else {
                                $questionClass = 'ungraded';
                                $statusBadge = '<span class="status-badge status-ungraded">Belum Dinilai</span>';
                            }
                        @endphp
                        
                        <div class="question-item {{ $questionClass }} bg-gray-50 p-4">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-md font-medium text-gray-900">{{ $loop->iteration }}. {{ $answer->question->question_text }}</h3>
                                {!! $statusBadge !!}
                            </div>
                            
                            @if($answer->question->image_path)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $answer->question->image_path) }}" alt="Question image" class="max-w-xs h-auto rounded-lg">
                                </div>
                            @endif
                            
                            @if($answer->question->question_type === 'multiple_choice' || $answer->question->question_type === 'true_false')
                                <div class="mt-3 space-y-2">
                                    @foreach($answer->question->options as $option)
                                        @php
                                            $optionClass = '';
                                            
                                            if ($answer->option_id == $option->id) {
                                                // This is the user's answer
                                                $optionClass = $option->is_correct ? 'bg-green-100 border-green-400' : 'bg-red-100 border-red-400';
                                            } elseif ($option->is_correct) {
                                                // This is the correct answer
                                                $optionClass = 'bg-green-50 border-green-300';
                                            }
                                        @endphp
                                        
                                        <div class="p-2 border rounded {{ $optionClass }}">
                                            <div class="flex items-start">
                                                <span class="mr-2">{{ chr(64 + $loop->iteration) }}.</span>
                                                <span>{{ $option->option_text }}</span>
                                            </div>
                                            
                                            @if($option->image_path)
                                                <div class="mt-2 ml-5">
                                                    <img src="{{ asset('storage/' . $option->image_path) }}" alt="Option image" class="max-w-xs h-auto rounded-lg">
                                                </div>
                                            @endif
                                            
                                            @if($answer->option_id == $option->id)
                                                <div class="mt-1 ml-5 text-sm {{ $option->is_correct ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $option->is_correct ? 'Jawaban Anda benar' : 'Jawaban Anda salah' }}
                                                </div>
                                            @elseif($option->is_correct)
                                                <div class="mt-1 ml-5 text-sm text-green-600">
                                                    Jawaban yang benar
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($answer->question->question_type === 'essay')
                                <div class="mt-3">
                                    <div class="mb-2 font-medium text-sm">Jawaban Anda:</div>
                                    <div class="p-3 bg-white border rounded-md whitespace-pre-wrap">
                                        {{ $answer->text_answer ?: 'Tidak ada jawaban' }}
                                    </div>
                                    
                                    @if($answer->is_graded)
                                        <div class="mt-3 flex justify-between items-center">
                                            <span class="text-sm font-medium">Nilai:</span>
                                            <span class="font-bold {{ $answer->points_earned > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $answer->points_earned }} / {{ $answer->question->points }} poin
                                            </span>
                                        </div>
                                    @else
                                        <div class="mt-3 text-sm text-yellow-600">
                                            <svg class="w-5 h-5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Jawaban sedang dalam proses penilaian
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            @if($answer->question->explanation)
                                <div class="mt-4 pt-3 border-t">
                                    <div class="text-sm font-medium mb-1">Penjelasan:</div>
                                    <div class="text-sm text-gray-700">
                                        {{ $answer->question->explanation }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
