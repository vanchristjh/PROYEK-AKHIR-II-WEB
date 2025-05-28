@extends('layouts.dashboard')

@section('title', 'Detail Ujian')

@section('header', 'Detail Ujian')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.exams.index') }}" 
           class="inline-flex items-center px-4 py-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-all duration-300 shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> 
            <span class="font-medium">Kembali ke daftar ujian</span>
        </a>
    </div>
      <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">
                Detail Ujian
            </h2>
            <p class="text-gray-500">Kelola detail dan pengaturan ujian</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('guru.exams.questions', $exam->id) }}" 
               class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg shadow-blue-200 hover:shadow-blue-300 transform hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-list mr-2"></i> Kelola Soal
            </a>
            <a href="{{ route('guru.exams.results', $exam->id) }}" 
               class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg shadow-green-200 hover:shadow-green-300 transform hover:-translate-y-1 transition-all duration-200">
                <i class="fas fa-chart-bar mr-2"></i> Lihat Hasil
            </a>
        </div>
    </div>    <!-- Alert Success -->
    @if (session('success'))
    <div class="mb-4 px-6 py-4 flex items-center text-green-700 bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm animate-fade-in" role="alert">
        <i class="fas fa-check-circle text-xl mr-3"></i>
        <div>
            <h4 class="font-semibold">Berhasil!</h4>
            <p>{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Alert Error -->
    @if (session('error'))
    <div class="mb-4 px-6 py-4 flex items-center text-red-700 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm animate-fade-in" role="alert">
        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
        <div>
            <h4 class="font-semibold">Error!</h4>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid gap-6 mb-8 md:grid-cols-12">
        <!-- Informasi Ujian -->
        <div class="md:col-span-8">        <div class="min-w-0 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="mb-6 border-b pb-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-3">{{ $exam->title }}</h3>
                @if ($exam->description)
                    <p class="text-gray-600 text-lg leading-relaxed">{{ $exam->description }}</p>
                @endif
            </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Mata Pelajaran:</span> 
                            {{ $exam->subject->name }}
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Tipe Ujian:</span> 
                            @if ($exam->exam_type === 'uts')
                                UTS (Ujian Tengah Semester)
                            @elseif ($exam->exam_type === 'uas')
                                UAS (Ujian Akhir Semester)
                            @elseif ($exam->exam_type === 'daily')
                                Ujian Harian
                            @endif
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Durasi:</span> 
                            {{ $exam->duration }} menit
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Jumlah Percobaan:</span> 
                            {{ $exam->max_attempts }}
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">KKM:</span> 
                            {{ $exam->passing_score ?? '(Tidak diatur)' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Waktu Mulai:</span> 
                            {{ $exam->start_time->format('d M Y, H:i') }}
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Waktu Selesai:</span> 
                            {{ $exam->end_time->format('d M Y, H:i') }}
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Status:</span> 
                            @if($exam->is_active && $exam->start_time <= now() && $exam->end_time >= now())
                                <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Aktif</span>
                            @elseif($exam->is_active && $exam->start_time > now())
                                <span class="px-2 py-1 text-xs font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">Dijadwalkan</span>
                            @elseif($exam->is_active && $exam->end_time < now())
                                <span class="px-2 py-1 text-xs font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full">Selesai</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">Nonaktif</span>
                            @endif
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Acak Urutan Soal:</span> 
                            {{ $exam->randomize_questions ? 'Ya' : 'Tidak' }}
                        </p>
                        <p class="text-sm mb-2">
                            <span class="font-semibold">Tampilkan Hasil:</span> 
                            {{ $exam->show_result ? 'Ya' : 'Tidak' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm font-semibold mb-2">Kelas:</p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($exam->classrooms as $classroom)
                            <span class="px-2 py-1 text-sm font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">
                                {{ $classroom->name }}
                            </span>
                        @empty
                            <p class="text-gray-500 text-sm">Belum ada kelas yang ditugaskan</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>        <!-- Statistik -->
        <div class="md:col-span-4">
            <div class="min-w-0 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
                <h4 class="text-xl font-bold text-gray-800 mb-6">Statistik Ujian</h4>
                  <div class="flex flex-col space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200 transform transition-all duration-300 hover:scale-105">
                        <div>
                            <p class="text-sm font-medium text-blue-600 mb-1">Jumlah Soal</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $exam->questions->count() }}</p>
                        </div>
                        <div class="p-4 rounded-full bg-blue-200 bg-opacity-50">
                            <i class="fas fa-question text-2xl text-blue-600"></i>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Siswa Mengerjakan</p>
                            <p class="text-xl font-semibold text-gray-700">{{ $exam->attempts->count() }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-green-100">
                            <i class="fas fa-users text-green-500"></i>
                        </div>
                    </div>
                    
                    @if($exam->attempts->count() > 0)
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Rata-rata Nilai</p>
                            <p class="text-xl font-semibold text-gray-700">
                                {{ number_format($exam->attempts->avg('score'), 1) }}
                            </p>
                        </div>
                        <div class="p-3 rounded-full bg-purple-100">
                            <i class="fas fa-chart-line text-purple-500"></i>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="mt-6 flex flex-col space-y-2">
                    <a href="{{ route('guru.exams.edit', $exam->id) }}" 
                       class="inline-flex justify-center items-center px-4 py-2 text-sm text-white bg-yellow-500 rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-edit mr-2"></i> Edit Ujian
                    </a>
                    <form action="{{ route('guru.exams.destroy', $exam->id) }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini? Semua data terkait juga akan dihapus.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-600">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Ujian
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>    <!-- Daftar Soal Preview -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Daftar Soal</h4>
                <p class="text-gray-500 mt-1">Preview soal-soal dalam ujian ini</p>
            </div>
            <a href="{{ route('guru.exams.questions', $exam->id) }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                Lihat semua <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        @if($exam->questions->count() > 0)
            <div class="overflow-x-auto">                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-600 uppercase bg-gray-50 rounded-t-lg">
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Soal</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($exam->questions->take(5) as $index => $question)                            <tr class="text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-800">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($question->content), 100) }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    @if($question->question_type === 'multiple_choice')
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full ring-1 ring-blue-200">
                                            <i class="fas fa-list-ul mr-1"></i> Pilihan Ganda
                                        </span>
                                    @elseif($question->question_type === 'true_false')
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full ring-1 ring-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Benar/Salah
                                        </span>
                                    @elseif($question->question_type === 'essay')
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded-full ring-1 ring-purple-200">
                                            <i class="fas fa-pen mr-1"></i> Essay
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-800">{{ $question->points }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($exam->questions->count() > 5)
                <div class="mt-4 text-center text-sm text-gray-500">
                    Menampilkan 5 dari {{ $exam->questions->count() }} soal.
                </div>
            @endif
        @else
            <div class="py-4 text-center text-gray-500">
                <p>Belum ada soal untuk ujian ini.</p>
                <a href="{{ route('guru.exams.questions', $exam->id) }}" class="inline-block mt-2 text-blue-500 hover:underline">
                    <i class="fas fa-plus mr-1"></i> Tambah soal sekarang
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
