@extends('layouts.dashboard')

@section('title', 'Edit Ujian')

@section('header', 'Edit Ujian')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <a href="{{ route('guru.exams.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar ujian
        </a>
    </div>

    <!-- Alert Error -->
    @if (session('error'))
    <div class="mb-4 px-4 py-3 leading-normal text-red-700 bg-red-100 rounded-lg" role="alert">
        {{ session('error') }}
    </div>
    @endif
    
    <!-- Validation Error -->
    @if ($errors->any())
    <div class="mb-4 px-4 py-3 leading-normal text-red-700 bg-red-100 rounded-lg">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">
        <form action="{{ route('guru.exams.update', $exam->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid gap-6 mb-4 md:grid-cols-2">
                <!-- Judul Ujian -->
                <div class="col-span-2">
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Judul Ujian <span class="text-red-500">*</span></span>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" required
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                            placeholder="Masukkan judul ujian">
                    </label>
                </div>
                
                <!-- Deskripsi -->
                <div class="col-span-2">
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Deskripsi</span>
                        <textarea name="description" rows="3"
                            class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:focus:shadow-outline-gray"
                            placeholder="Masukkan deskripsi atau petunjuk ujian">{{ old('description', $exam->description) }}</textarea>
                    </label>
                </div>
                
                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Mata Pelajaran <span class="text-red-500">*</span></span>
                        <select name="subject_id" required
                            class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:focus:shadow-outline-gray">
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
                
                <!-- Tipe Ujian -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Tipe Ujian <span class="text-red-500">*</span></span>
                        <select name="exam_type" required
                            class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:focus:shadow-outline-gray">
                            @foreach ($examTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('exam_type', $exam->exam_type) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
                
                <!-- Waktu Mulai -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Waktu Mulai <span class="text-red-500">*</span></span>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time->format('Y-m-d\TH:i')) }}" required
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input">
                    </label>
                </div>
                
                <!-- Waktu Selesai -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Waktu Selesai <span class="text-red-500">*</span></span>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time', $exam->end_time->format('Y-m-d\TH:i')) }}" required
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input">
                    </label>
                </div>
                
                <!-- Durasi (menit) -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Durasi (menit) <span class="text-red-500">*</span></span>
                        <input type="number" name="duration" value="{{ old('duration', $exam->duration) }}" min="1" required
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                            placeholder="Contoh: 60">
                    </label>
                </div>
                
                <!-- Jumlah Percobaan -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Jumlah Percobaan <span class="text-red-500">*</span></span>
                        <input type="number" name="max_attempts" value="{{ old('max_attempts', $exam->max_attempts) }}" min="1" required
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                            placeholder="Contoh: 1">
                    </label>
                </div>
                
                <!-- Nilai Kelulusan -->
                <div>
                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Nilai Kelulusan (KKM)</span>
                        <input type="number" name="passing_score" value="{{ old('passing_score', $exam->passing_score) }}" min="0" max="100"
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                            placeholder="Contoh: 75">
                    </label>
                </div>
                
                <!-- Kelas -->
                <div class="col-span-2">
                    <label class="block text-sm mb-2">
                        <span class="text-gray-700 dark:text-gray-400">Pilih Kelas <span class="text-red-500">*</span></span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                        @php
                            $selectedClassrooms = old('classrooms', $exam->classrooms->pluck('id')->toArray());
                        @endphp
                        
                        @foreach ($classrooms as $classroom)
                            <label class="inline-flex items-center text-sm">
                                <input type="checkbox" name="classrooms[]" value="{{ $classroom->id }}"
                                    {{ in_array($classroom->id, $selectedClassrooms) ? 'checked' : '' }}
                                    class="text-blue-600 form-checkbox focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:focus:shadow-outline-gray">
                                <span class="ml-2">{{ $classroom->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Pengaturan Tambahan -->
            <div class="block text-sm mb-4">
                <span class="text-gray-700 dark:text-gray-400 font-medium">Pengaturan Tambahan</span>
                <div class="mt-2">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="is_active" {{ old('is_active', $exam->is_active) ? 'checked' : '' }}
                            class="text-blue-600 form-checkbox focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:focus:shadow-outline-gray">
                        <span class="ml-2">Aktif</span>
                    </label>
                </div>
                <div class="mt-2">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="randomize_questions" {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}
                            class="text-blue-600 form-checkbox focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:focus:shadow-outline-gray">
                        <span class="ml-2">Acak Urutan Soal</span>
                    </label>
                </div>
                <div class="mt-2">
                    <label class="inline-flex items-center text-sm">
                        <input type="checkbox" name="show_result" {{ old('show_result', $exam->show_result) ? 'checked' : '' }}
                            class="text-blue-600 form-checkbox focus:border-blue-400 focus:outline-none focus:shadow-outline-blue dark:focus:shadow-outline-gray">
                        <span class="ml-2">Tampilkan Hasil Setelah Selesai</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end mt-6 space-x-3">
                <a href="{{ route('guru.exams.index') }}"
                    class="px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition-colors duration-150 border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
