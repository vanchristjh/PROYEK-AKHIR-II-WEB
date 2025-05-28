@extends('layouts.dashboard')

@section('title', 'Buat Kuis Baru')

@section('header', 'Buat Kuis Baru')

@section('content')
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-yellow-500 to-amber-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-plus-circle text-9xl"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Buat Kuis Baru</h2>
            <p class="text-yellow-100">Buat kuis untuk mengevaluasi pemahaman siswa.</p>
        </div>
    </div>

    <div class="mb-6">
        <a href="{{ route('guru.quizzes.index') }}" class="inline-flex items-center text-yellow-600 hover:text-yellow-800 transition-colors">
            <i class="fas fa-chevron-left mr-2 text-sm"></i>
            <span>Kembali ke Daftar Kuis</span>
        </a>
    </div>
    
    @if($errors->any())
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
            <div class="font-medium">Terdapat beberapa kesalahan:</div>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="{{ route('guru.quizzes.store') }}" method="POST" class="animate-fade-in" enctype="multipart/form-data" id="quizForm">
                @csrf
                @method('POST')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Kuis <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heading text-gray-400"></i>
                            </div>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kuis</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <textarea name="description" id="description" rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300">{{ old('description') }}</textarea>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Berikan instruksi atau informasi tambahan untuk siswa mengenai kuis ini.</p>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>                    
                    <div class="form-group mb-5">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-book text-gray-400"></i>
                            </div>
                            <select name="subject_id" id="subject_id" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($subjects ?? [] as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Pilih mata pelajaran untuk kuis ini.</p>
                        @error('subject_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas (Pilih satu atau lebih) <span class="text-red-500">*</span></label>
                        <div class="mt-1 bg-white rounded-lg border border-gray-300 px-3 py-2 focus-within:ring focus-within:ring-yellow-200 focus-within:ring-opacity-50 focus-within:border-yellow-500">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($classrooms ?? [] as $classroom)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="classroom_{{ $classroom->id }}" name="classroom_id[]" value="{{ $classroom->id }}"
                                            class="rounded border-gray-300 text-yellow-600 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50"
                                            {{ in_array($classroom->id, old('classroom_id', [])) ? 'checked' : '' }}>
                                        <label for="classroom_{{ $classroom->id }}" class="ml-2 text-sm text-gray-700">{{ $classroom->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button type="button" id="select-all-classrooms" 
                                    class="text-xs text-yellow-600 hover:text-yellow-800 font-medium">
                                    Pilih Semua
                                </button>
                            </div>
                        </div>
                        @error('classroom_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Mulai <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        @error('start_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Berakhir <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        @error('end_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit) <span class="text-red-500">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <input type="number" name="duration" id="duration" value="{{ old('duration', 30) }}" min="1" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Waktu maksimal siswa mengerjakan kuis dalam menit.</p>
                        @error('duration')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-1">Percobaan Maksimum</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-redo text-gray-400"></i>
                            </div>
                            <input type="number" name="max_attempts" id="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Jumlah maksimal percobaan siswa mengerjakan kuis ini.</p>
                        @error('max_attempts')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="passing_grade" class="block text-sm font-medium text-gray-700 mb-1">Nilai Kelulusan</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-percentage text-gray-400"></i>
                            </div>
                            <input type="number" name="passing_grade" id="passing_grade" value="{{ old('passing_grade', 70) }}" min="0" max="100" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 transition-shadow duration-300">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Nilai minimum untuk lulus kuis ini (0-100).</p>
                        @error('passing_grade')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5 md:col-span-2">
                        <div class="flex items-center space-x-2 mb-2">
                            <label for="show_answers" class="text-sm font-medium text-gray-700">Pengaturan Tambahan</label>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                Opsional
                            </span>
                        </div>
                        <div class="mt-1 bg-white rounded-lg border border-gray-300 px-4 py-3 space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_random" name="is_random" type="checkbox" value="1" {{ old('is_random') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                </div>
                                <div class="ml-3">
                                    <label for="is_random" class="text-sm font-medium text-gray-700">Acak Urutan Soal</label>
                                    <p class="text-xs text-gray-500">Mengacak urutan soal untuk setiap siswa.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="show_answers" name="show_answers" type="checkbox" value="1" {{ old('show_answers') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                </div>
                                <div class="ml-3">
                                    <label for="show_answers" class="text-sm font-medium text-gray-700">Tampilkan Jawaban Benar</label>
                                    <p class="text-xs text-gray-500">Siswa dapat melihat jawaban benar setelah mengerjakan kuis.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                </div>
                                <div class="ml-3">
                                    <label for="is_active" class="text-sm font-medium text-gray-700">Aktifkan Kuis</label>
                                    <p class="text-xs text-gray-500">Siswa dapat melihat dan mengakses kuis ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-5">
                    <div class="flex justify-end">
                        <a href="{{ route('guru.quizzes.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit" class="ml-3 px-6 py-2 bg-gradient-to-r from-yellow-500 to-amber-600 text-white rounded-lg hover:from-yellow-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Buat Kuis
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .animate-fade-in {
        animation: fade-in 0.6s ease-in-out;
    }
    
    @keyframes fade-in {
        0% {
            opacity: 0;
            transform: translateY(10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-group:focus-within label {
        color: #F59E0B;
    }
    
    .form-group:focus-within i {
        color: #F59E0B;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('quizForm');
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');
        
        // Validate end time is after start time
        function validateTimes() {
            if (startTime.value && endTime.value) {
                const start = new Date(startTime.value);
                const end = new Date(endTime.value);
                
                if (end <= start) {
                    endTime.setCustomValidity('Waktu berakhir harus setelah waktu mulai');
                    return false;
                }
                endTime.setCustomValidity('');
                return true;
            }
            return true;
        }

        startTime.addEventListener('change', validateTimes);
        endTime.addEventListener('change', validateTimes);

        // Validate at least one classroom is selected
        form.addEventListener('submit', function(e) {
            const classroomCheckboxes = document.querySelectorAll('input[name="classroom_id[]"]:checked');
            if (classroomCheckboxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu kelas');
                return false;
            }
            if (!validateTimes()) {
                e.preventDefault();
                return false;
            }
        });

        // Select all classrooms button
        const selectAllBtn = document.getElementById('select-all-classrooms');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('input[name="classroom_id[]"]');
                const someUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
                checkboxes.forEach(cb => cb.checked = someUnchecked);
                this.textContent = someUnchecked ? 'Batal Pilih Semua' : 'Pilih Semua';
            });
        }
    });
</script>
@endpush
