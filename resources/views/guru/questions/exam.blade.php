@extends('layouts.dashboard')

@section('title', 'Kelola Soal Ujian')

@section('header', 'Kelola Soal Ujian')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.exams.show', $exam->id) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Ujian
        </a>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Kelola Soal: {{ $exam->title }}
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

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('guru.exams.create-question', $exam->id) }}" 
               class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i> Tambah Soal Baru
            </a>
        </div>
        <div class="text-sm">
            Total Soal: <span class="font-medium">{{ $exam->questions->count() }}</span> | 
            Total Poin: <span class="font-medium">{{ $exam->questions->sum('points') }}</span>
        </div>
    </div>

    @if($exam->questions->count() > 0)
        <div class="w-full overflow-hidden rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Soal</th>
                            <th class="px-4 py-3">Tipe Soal</th>
                            <th class="px-4 py-3">Poin</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach($exam->questions as $index => $question)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-800 truncate max-w-xs">
                                                {!! \Illuminate\Support\Str::limit(strip_tags($question->content), 80) !!}
                                            </p>
                                            @if($question->question_type === 'multiple_choice')
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $question->options->count() }} pilihan, {{ $question->options->where('is_correct', true)->count() }} jawaban benar
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($question->question_type === 'multiple_choice')
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-blue-700 bg-blue-100 rounded-full">
                                            Pilihan Ganda
                                        </span>
                                    @elseif($question->question_type === 'true_false')
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                            Benar/Salah
                                        </span>
                                    @elseif($question->question_type === 'essay')
                                        <span class="px-2 py-1 text-xs font-semibold leading-tight text-purple-700 bg-purple-100 rounded-full">
                                            Essay
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $question->points }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('guru.exams.edit-question', ['exam' => $exam->id, 'question' => $question->id]) }}" 
                                           class="px-2 py-1 text-xs font-medium text-white bg-yellow-500 rounded-md hover:bg-yellow-600">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('guru.questions.destroy', $question->id) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="redirect_route" value="guru.exams.questions">
                                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                            <button type="submit" 
                                                    class="px-2 py-1 text-xs font-medium text-white bg-red-500 rounded-md hover:bg-red-600">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="mb-4">
                <i class="fas fa-question-circle text-gray-400 text-5xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-700 mb-2">Belum Ada Soal</h3>
            <p class="text-gray-600 mb-4">
                Ujian ini belum memiliki soal. Tambahkan soal agar siswa dapat mengerjakan ujian ini.
            </p>
            <a href="{{ route('guru.exams.create-question', $exam->id) }}" 
               class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Tambah Soal Sekarang
            </a>
        </div>
    @endif
</div>
@endsection
