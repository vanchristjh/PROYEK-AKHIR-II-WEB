@extends('layouts.dashboard')

@section('title', 'Detail Kuis')

@section('header', 'Detail Kuis')

@section('content')
    <!-- Quiz Header Banner -->
    <div class="bg-gradient-to-r from-yellow-500 to-amber-600 rounded-xl shadow-xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-question-circle text-9xl"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200 mr-2">
                            <i class="fas fa-book mr-1"></i> {{ $quiz->subject->name ?? 'Tidak ada mata pelajaran' }}
                        </span>
                        @if($quiz->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></div>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                <i class="fas fa-draft mr-1"></i> Draft
                            </span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold">{{ $quiz->title }}</h2>
                    <p class="text-yellow-100 mt-1">
                        <i class="fas fa-calendar-alt mr-1"></i> 
                        {{ $quiz->start_time ? \Carbon\Carbon::parse($quiz->start_time)->format('d M Y, H:i') : 'Tidak diatur' }} - 
                        {{ $quiz->end_time ? \Carbon\Carbon::parse($quiz->end_time)->format('d M Y, H:i') : 'Tidak diatur' }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('guru.quizzes.edit', $quiz->id) }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors backdrop-blur-sm">
                        <i class="fas fa-edit mr-2"></i> Edit Kuis
                    </a>
                    <a href="{{ route('guru.quizzes.index') }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors backdrop-blur-sm">
                        <i class="fas fa-list mr-2"></i> Daftar Kuis
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Quiz Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Left Column: Quiz Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                            <i class="fas fa-info-circle text-yellow-600"></i>
                        </div>
                        <span>Informasi Kuis</span>
                    </h3>

                    <div class="prose max-w-none mb-6">
                        <p class="text-gray-700">{{ $quiz->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Pengaturan Waktu</h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Durasi:</span>
                                    <span class="font-medium">{{ $quiz->duration ?? 0 }} menit</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Waktu Mulai:</span>
                                    <span class="font-medium">{{ $quiz->start_time ? \Carbon\Carbon::parse($quiz->start_time)->format('d M Y, H:i') : 'Tidak diatur' }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Waktu Berakhir:</span>
                                    <span class="font-medium">{{ $quiz->end_time ? \Carbon\Carbon::parse($quiz->end_time)->format('d M Y, H:i') : 'Tidak diatur' }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium">
                                        @if($quiz->end_time && \Carbon\Carbon::parse($quiz->end_time)->isPast())
                                            <span class="text-red-600">Selesai</span>
                                        @elseif($quiz->start_time && \Carbon\Carbon::parse($quiz->start_time)->isFuture())
                                            <span class="text-blue-600">Terjadwal</span>
                                        @elseif($quiz->is_active)
                                            <span class="text-green-600">Aktif</span>
                                        @else
                                            <span class="text-gray-600">Draft</span>
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Pengaturan Kuis</h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Nilai Minimum:</span>
                                    <span class="font-medium">{{ $quiz->passing_grade ?? '70' }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Percobaan Maksimum:</span>
                                    <span class="font-medium">{{ $quiz->max_attempts ?? '1' }} kali</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Acak Soal:</span>
                                    <span class="font-medium">{{ $quiz->is_random ? 'Ya' : 'Tidak' }}</span>
                                </li>
                                <li class="flex justify-between">
                                    <span class="text-gray-600">Tampilkan Jawaban:</span>
                                    <span class="font-medium">{{ $quiz->show_answers ? 'Ya' : 'Tidak' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Kelas yang Dapat Mengakses</h4>
                        <div class="flex flex-wrap gap-2">
                            @forelse($quiz->classrooms as $classroom)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    <i class="fas fa-users mr-1"></i> {{ $classroom->name }}
                                </span>
                            @empty
                                <span class="text-gray-500 text-sm">Tidak ada kelas yang dipilih</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions Section -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-lg font-medium text-gray-800 flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-question-circle text-purple-600"></i>
                        </div>
                        <span>Daftar Soal</span>
                    </h3>
                    <a href="{{ route('guru.questions.create', ['quiz_id' => $quiz->id]) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i> Tambah Soal
                    </a>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($quiz->questions ?? [] as $index => $question)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <div class="px-2.5 py-1 rounded-md bg-indigo-100 text-indigo-800 text-xs font-medium mr-2">
                                            Soal #{{ $index + 1 }}
                                        </div>
                                        <div class="px-2.5 py-1 rounded-md bg-{{ $question->type == 'multiple_choice' ? 'blue' : ($question->type == 'true_false' ? 'green' : 'amber') }}-100 text-{{ $question->type == 'multiple_choice' ? 'blue' : ($question->type == 'true_false' ? 'green' : 'amber') }}-800 text-xs font-medium">
                                            {{ $question->type == 'multiple_choice' ? 'Pilihan Ganda' : ($question->type == 'true_false' ? 'Benar/Salah' : 'Essay') }}
                                        </div>
                                    </div>

                                    <div class="prose max-w-none mb-4">
                                        <h4 class="text-base font-medium text-gray-800">{{ $question->content }}</h4>
                                        
                                        @if($question->type == 'multiple_choice')
                                            <div class="mt-2 pl-4 space-y-1.5">
                                                @foreach($question->options ?? [] as $option)
                                                    <div class="flex items-center">
                                                        <div class="w-5 h-5 rounded-full {{ $option['is_correct'] ? 'bg-green-500' : 'bg-gray-200' }} text-white flex items-center justify-center text-xs mr-2">
                                                            {{ $option['is_correct'] ? '✓' : '' }}
                                                        </div>
                                                        <span class="text-sm {{ $option['is_correct'] ? 'font-medium text-green-700' : 'text-gray-700' }}">
                                                            {{ $option['text'] }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($question->type == 'true_false')
                                            <div class="mt-2 pl-4 flex space-x-4">
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded-full {{ $question->answer == 'true' ? 'bg-green-500' : 'bg-gray-200' }} text-white flex items-center justify-center text-xs mr-2">
                                                        {{ $question->answer == 'true' ? '✓' : '' }}
                                                    </div>
                                                    <span class="text-sm {{ $question->answer == 'true' ? 'font-medium text-green-700' : 'text-gray-700' }}">
                                                        Benar
                                                    </span>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="w-5 h-5 rounded-full {{ $question->answer == 'false' ? 'bg-green-500' : 'bg-gray-200' }} text-white flex items-center justify-center text-xs mr-2">
                                                        {{ $question->answer == 'false' ? '✓' : '' }}
                                                    </div>
                                                    <span class="text-sm {{ $question->answer == 'false' ? 'font-medium text-green-700' : 'text-gray-700' }}">
                                                        Salah
                                                    </span>
                                                </div>
                                            </div>
                                        @elseif($question->type == 'essay')
                                            <div class="mt-2 pl-4">
                                                <p class="text-sm text-gray-500 italic">Jawaban akan dinilai secara manual</p>
                                                @if($question->answer_key)
                                                    <div class="mt-2">
                                                        <span class="text-xs font-medium text-gray-600">Kunci Jawaban / Kata Kunci:</span>
                                                        <p class="text-sm text-green-700">{{ $question->answer_key }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex items-center text-xs text-gray-500">
                                        <span class="flex items-center">
                                            <i class="fas fa-star mr-1 text-amber-500"></i>
                                            Poin: {{ $question->score ?? 1 }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex space-x-2 ml-4">
                                    <a href="{{ route('guru.questions.edit', $question->id) }}" class="p-2 text-gray-500 hover:text-blue-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('guru.questions.destroy', $question->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="mx-auto w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-question text-purple-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-800 mb-2">Belum Ada Soal</h3>
                            <p class="text-gray-500 mb-6">Anda belum menambahkan soal untuk kuis ini.</p>
                            <a href="{{ route('guru.questions.create', ['quiz_id' => $quiz->id]) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i> Tambah Soal Sekarang
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Stats and Actions -->
        <div>
            <!-- Stats Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-chart-bar text-blue-600"></i>
                        </div>
                        <span>Statistik Kuis</span>
                    </h3>

                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-700">Partisipasi</h4>
                                <span class="text-blue-600 font-medium">{{ $quiz->attempts_count ?? 0 }} / {{ $quiz->total_students ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $quiz->total_students ? ($quiz->attempts_count / $quiz->total_students) * 100 : 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ $quiz->total_students ? round(($quiz->attempts_count / $quiz->total_students) * 100) : 0 }}% siswa telah mengerjakan kuis
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-sm font-medium text-gray-700">Rata-rata Nilai</h4>
                                <span class="text-{{ ($quiz->average_score ?? 0) >= ($quiz->passing_grade ?? 70) ? 'green' : 'red' }}-600 font-medium">
                                    {{ number_format($quiz->average_score ?? 0, 1) }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-{{ ($quiz->average_score ?? 0) >= ($quiz->passing_grade ?? 70) ? 'green' : 'red' }}-600 h-2 rounded-full" style="width: {{ min(($quiz->average_score ?? 0), 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">0</span>
                                <span class="text-gray-600 font-medium">Nilai kelulusan: {{ $quiz->passing_grade ?? 70 }}</span>
                                <span class="text-gray-500">100</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-green-50 rounded-lg p-4 border border-green-100 text-center">
                                <div class="text-green-600 mb-1">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </div>
                                <div class="text-xl font-bold text-green-700">{{ $quiz->passed_count ?? 0 }}</div>
                                <div class="text-xs text-green-600">Lulus</div>
                            </div>

                            <div class="bg-red-50 rounded-lg p-4 border border-red-100 text-center">
                                <div class="text-red-600 mb-1">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </div>
                                <div class="text-xl font-bold text-red-700">{{ $quiz->failed_count ?? 0 }}</div>
                                <div class="text-xs text-red-600">Tidak Lulus</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                        <div class="p-2 bg-amber-100 rounded-lg mr-3">
                            <i class="fas fa-bolt text-amber-600"></i>
                        </div>
                        <span>Aksi Cepat</span>
                    </h3>

                    <div class="space-y-3">
                        <a href="{{ route('guru.quizzes.results', $quiz->id) }}" class="block w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 flex items-center justify-center">
                            <i class="fas fa-chart-bar mr-2"></i>
                            <span>Lihat Hasil Kuis</span>
                        </a>

                        @if(!$quiz->is_active)
                            <form action="{{ route('guru.quizzes.activate', $quiz->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full py-3 px-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 flex items-center justify-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    <span>Aktifkan Kuis</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('guru.quizzes.deactivate', $quiz->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full py-3 px-4 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 flex items-center justify-center">
                                    <i class="fas fa-pause-circle mr-2"></i>
                                    <span>Nonaktifkan Kuis</span>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('guru.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kuis ini? Semua data terkait kuis ini akan dihapus secara permanen.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full py-3 px-4 bg-white border border-red-500 text-red-600 rounded-lg hover:bg-red-50 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                <span>Hapus Kuis</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    .prose h1, .prose h2, .prose h3, .prose h4 {
        color: #374151;
        margin-top: 0;
        margin-bottom: 0.75em;
    }
    
    .prose p {
        margin-top: 0;
        margin-bottom: 1em;
    }
    
    .prose ul {
        margin-top: 0;
        margin-bottom: 1em;
    }
</style>
@endpush
