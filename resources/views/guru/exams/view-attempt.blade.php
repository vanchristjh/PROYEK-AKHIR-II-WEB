@extends('layouts.dashboard')

@section('title', 'Detail Jawaban Siswa')

@section('header', 'Detail Jawaban Siswa')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.exams.results', $exam->id) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Hasil Ujian
        </a>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Detail Jawaban Siswa
        </h2>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
    <div class="mb-4 px-4 py-3 leading-normal text-green-700 bg-green-100 rounded-lg" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <!-- Alert Error -->
    @if (session('error'))
    <div class="mb-4 px-4 py-3 leading-normal text-red-700 bg-red-100 rounded-lg" role="alert">
        {{ session('error') }}
    </div>
    @endif
    
    <!-- Informasi Siswa -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Informasi Siswa</h4>
                <p class="text-sm mb-1">
                    <span class="font-medium">Nama:</span> {{ $attempt->student->name }}
                </p>
                <p class="text-sm mb-1">
                    <span class="font-medium">NIS:</span> {{ $attempt->student->nis }}
                </p>
                <p class="text-sm mb-1">
                    <span class="font-medium">Kelas:</span> 
                    {{ $attempt->student->classroom ? $attempt->student->classroom->name : '-' }}
                </p>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Informasi Ujian</h4>
                <p class="text-sm mb-1">
                    <span class="font-medium">Judul Ujian:</span> {{ $exam->title }}
                </p>
                <p class="text-sm mb-1">
                    <span class="font-medium">Mata Pelajaran:</span> {{ $exam->subject->name }}
                </p>
                <p class="text-sm mb-1">
                    <span class="font-medium">Tipe Ujian:</span> 
                    @if($exam->exam_type === 'uts')
                        UTS (Ujian Tengah Semester)
                    @elseif($exam->exam_type === 'uas')
                        UAS (Ujian Akhir Semester)
                    @elseif($exam->exam_type === 'daily')
                        Ujian Harian
                    @endif
                </p>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Hasil</h4>
                <p class="text-sm mb-1">
                    <span class="font-medium">Waktu Mulai:</span> 
                    {{ $attempt->start_time->format('d M Y, H:i:s') }}
                </p>
                <p class="text-sm mb-1">
                    <span class="font-medium">Waktu Selesai:</span> 
                    {{ $attempt->end_time ? $attempt->end_time->format('d M Y, H:i:s') : 'Belum selesai' }}
                </p>
                <p class="text-sm mb-2">
                    <span class="font-medium">Durasi:</span>
                    @if($attempt->end_time)
                        @php
                            $duration = $attempt->start_time->diffInMinutes($attempt->end_time);
                            $hours = floor($duration / 60);
                            $minutes = $duration % 60;
                        @endphp
                        {{ $hours > 0 ? $hours . ' jam ' : '' }}{{ $minutes }} menit
                    @else
                        -
                    @endif
                </p>
                <div class="flex items-center">
                    <span class="font-medium mr-2">Nilai:</span>
                    <span class="px-3 py-1 text-sm font-semibold leading-tight {{ $attempt->score >= $exam->passing_score ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }} rounded-full">
                        {{ number_format($attempt->score, 1) }}
                    </span>
                    <span class="ml-2 text-sm {{ $attempt->score >= $exam->passing_score ? 'text-green-600' : 'text-red-600' }}">
                        ({{ $attempt->score >= $exam->passing_score ? 'Lulus' : 'Tidak Lulus' }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Jawaban -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Ringkasan Jawaban</h3>
        
        <div class="mb-4 pb-4 border-b">
            <div class="flex justify-between mb-2">
                <p class="font-medium">Total Poin:</p>
                <p>{{ $attempt->answers->sum('points_earned') }} / {{ $exam->questions->sum('points') }}</p>
            </div>
            <div class="flex justify-between mb-2">
                <p class="font-medium">Jawaban Benar:</p>
                <p>{{ $attempt->answers->where('is_correct', true)->count() }} dari {{ $attempt->answers->count() }}</p>
            </div>
            <div class="flex justify-between">
                <p class="font-medium">Status Penilaian:</p>
                <p>
                    @if($attempt->is_graded)
                        <span class="text-green-600">Sudah Dinilai</span>
                    @else
                        <span class="text-yellow-600">Belum Dinilai Sepenuhnya</span>
                    @endif
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-600">Total Soal</p>
                <p class="text-xl font-bold text-gray-700">{{ $exam->questions->count() }}</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
                <p class="text-sm text-gray-600">Jawaban Benar</p>
                <p class="text-xl font-bold text-green-700">{{ $attempt->answers->where('is_correct', true)->count() }}</p>
            </div>
            <div class="p-3 bg-red-50 rounded-lg">
                <p class="text-sm text-gray-600">Jawaban Salah</p>
                <p class="text-xl font-bold text-red-700">{{ $attempt->answers->where('is_correct', false)->count() }}</p>
            </div>
        </div>
    </div>
    
    <!-- Detail Jawaban Per Soal -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Detail Jawaban Per Soal</h3>
        
        @foreach($exam->questions as $index => $question)
            @php
                $answer = $attempt->answers->where('question_id', $question->id)->first();
            @endphp
            
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <div class="flex justify-between">
                    <h4 class="font-medium text-gray-700">Soal {{ $index + 1 }} 
                        <span class="text-sm text-gray-500">({{ $question->points }} poin)</span>
                    </h4>
                    <div>
                        @if($answer && $answer->is_correct)
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Benar</span>
                        @elseif($answer)
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Salah</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">Tidak Dijawab</span>
                        @endif
                    </div>
                </div>
                
                <div class="mt-2">
                    <p class="text-gray-700">{!! $question->content !!}</p>
                </div>
                
                @if($question->question_type === 'multiple_choice')
                    <div class="mt-4 border-t pt-2">
                        <p class="text-sm font-medium text-gray-600 mb-2">Pilihan:</p>
                        <ul class="space-y-2">
                            @foreach($question->options as $option)
                                <li class="flex items-start">
                                    <span class="flex-shrink-0 mt-1 mr-2">
                                        @if($option->is_correct)
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        @else
                                            <i class="fas fa-circle text-gray-400"></i>
                                        @endif
                                    </span>
                                    <span class="{{ $option->is_correct ? 'text-green-700 font-medium' : 'text-gray-700' }}">
                                        {{ $option->content }}
                                        @if($answer && $answer->selected_option_id === $option->id)
                                            <span class="ml-2 text-blue-500 font-medium">(Dipilih)</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @elseif($question->question_type === 'true_false')
                    <div class="mt-4 border-t pt-2">
                        <p class="text-sm font-medium text-gray-600 mb-2">Jawaban:</p>
                        <ul class="space-y-2">
                            @foreach($question->options as $option)
                                <li class="flex items-start">
                                    <span class="flex-shrink-0 mt-1 mr-2">
                                        @if($option->is_correct)
                                            <i class="fas fa-check-circle text-green-500"></i>
                                        @else
                                            <i class="fas fa-circle text-gray-400"></i>
                                        @endif
                                    </span>
                                    <span class="{{ $option->is_correct ? 'text-green-700 font-medium' : 'text-gray-700' }}">
                                        {{ $option->content }}
                                        @if($answer && $answer->selected_option_id === $option->id)
                                            <span class="ml-2 text-blue-500 font-medium">(Dipilih)</span>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @elseif($question->question_type === 'essay')
                    <div class="mt-4 border-t pt-2">
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-600 mb-1">Kunci Jawaban:</p>
                            <p class="text-gray-700 bg-gray-50 p-2 rounded">{{ $question->correct_answer }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <p class="text-sm font-medium text-gray-600 mb-1">Jawaban Siswa:</p>
                            <p class="text-gray-700 bg-blue-50 p-2 rounded">{{ $answer ? $answer->essay_answer : 'Tidak dijawab' }}</p>
                        </div>
                        
                        @if($answer)
                            <div class="bg-gray-50 p-3 rounded mb-4">
                                <h4 class="text-sm font-medium text-gray-600 mb-2">Penilaian Essay</h4>
                                
                                @if($answer->is_graded)
                                    <div class="flex justify-between mb-2">
                                        <p class="text-sm text-gray-600">Nilai yang diberikan:</p>
                                        <p class="text-sm font-medium">{{ $answer->points_earned }} / {{ $question->points }}</p>
                                    </div>
                                    
                                    @if($answer->teacher_comment)
                                        <div class="mb-2">
                                            <p class="text-sm text-gray-600">Komentar Guru:</p>
                                            <p class="text-sm text-gray-700 italic">{{ $answer->teacher_comment }}</p>
                                        </div>
                                    @endif
                                @else
                                    <form action="{{ route('guru.exams.grade-essay', ['exam' => $exam->id, 'attempt' => $attempt->id, 'answer' => $answer->id]) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm">
                                                <span class="text-gray-700">Nilai (dari {{ $question->points }} poin)</span>
                                                <input type="number" name="points_earned" min="0" max="{{ $question->points }}" step="0.1" required
                                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    placeholder="Masukkan nilai">
                                            </label>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="block text-sm">
                                                <span class="text-gray-700">Komentar (opsional)</span>
                                                <textarea name="teacher_comment" rows="2"
                                                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                    placeholder="Masukkan komentar (opsional)"></textarea>
                                            </label>
                                        </div>
                                        
                                        <div class="flex justify-end">
                                            <button type="submit" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Berikan Nilai
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
