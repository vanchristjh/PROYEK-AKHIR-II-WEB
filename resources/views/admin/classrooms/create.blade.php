@extends('layouts.dashboard')

@section('title', 'Tambah Kelas Baru')

@section('header', 'Tambah Kelas Baru')

@section('navigation')
    <li>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-users text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Pengguna</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.subjects.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-book text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Mata Pelajaran</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.classrooms.index') }}" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-school text-lg w-6"></i>
            <span class="ml-3">Kelas</span>
        </a>
    </li>
@endsection

@section('content')
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-school text-9xl"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Tambah Kelas Baru</h2>
            <p class="text-purple-100">Buat kelas baru dan tetapkan guru wali kelas serta mata pelajaran.</p>
        </div>
    </div>
    
    <!-- Display any form validation errors at the top -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="{{ route('admin.classrooms.store') }}" method="POST" class="animate-fade-in">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group mb-5">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-chalkboard text-gray-400"></i>
                            </div>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Contoh: X IPA 1, XI IPS 2, XII Bahasa</p>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="grade_level" class="block text-sm font-medium text-gray-700 mb-1">Tingkat Kelas</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-layer-group text-gray-400"></i>
                            </div>
                            <select name="grade_level" id="grade_level" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="10" {{ old('grade_level') == '10' ? 'selected' : '' }}>Kelas 10</option>
                                <option value="11" {{ old('grade_level') == '11' ? 'selected' : '' }}>Kelas 11</option>
                                <option value="12" {{ old('grade_level') == '12' ? 'selected' : '' }}>Kelas 12</option>
                            </select>
                        </div>
                        @error('grade_level')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                            </div>
                            <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y').'/'.((int)date('Y')+1)) }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: YYYY/YYYY (misal: 2023/2024)</p>
                        @error('academic_year')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="homeroom_teacher_id" class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-chalkboard-teacher text-gray-400"></i>
                            </div>
                            <select name="homeroom_teacher_id" id="homeroom_teacher_id" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Guru Wali Kelas</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }} {{ !empty($teacher->nip) ? '(' . $teacher->nip . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Pilih guru yang akan menjadi wali kelas</p>
                        
                        @if($teachers->isEmpty())
                            <div class="mt-2 text-yellow-600 text-xs flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Tidak ada guru tersedia. <a href="{{ route('admin.users.create') }}" class="ml-1 text-purple-600 hover:underline">Tambah guru baru</a>
                            </div>
                        @endif
                        @error('homeroom_teacher_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Kapasitas</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', 30) }}" min="1" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Jumlah maksimum siswa dalam kelas</p>
                        @error('capacity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Add the room_number field back as it might be required in the database -->
                    <div class="form-group mb-5">
                        <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Ruangan</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-door-open text-gray-400"></i>
                            </div>
                            <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-shadow duration-300">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Opsional: Nomor/nama ruangan fisik</p>
                        @error('room_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group md:col-span-2 mt-4">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-book-reader text-purple-500 mr-2"></i>
                            <h3 class="text-lg font-medium text-gray-800">Mata Pelajaran</h3>
                        </div>
                        
                        <div class="bg-purple-50 p-5 rounded-xl border border-purple-100 max-h-64 overflow-y-auto">
                            @if(isset($subjects) && $subjects->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($subjects as $subject)
                                        <div class="flex items-center p-2 rounded-lg hover:bg-purple-100/50 transition-colors">
                                            <input type="checkbox" id="subject-{{ $subject->id }}" name="subjects[]" value="{{ $subject->id }}" 
                                                class="rounded border-purple-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50"
                                                {{ (is_array(old('subjects')) && in_array($subject->id, old('subjects'))) ? 'checked' : '' }}>
                                            <label for="subject-{{ $subject->id }}" class="ml-2 cursor-pointer">
                                                <div class="text-sm font-medium text-gray-700">{{ $subject->name }}</div>
                                                <div class="text-xs text-gray-500">Kode: {{ $subject->code }}</div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center justify-center py-8 text-center">
                                    <div>
                                        <div class="text-purple-400 text-3xl mb-2">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <p class="text-gray-500">Tidak ada mata pelajaran tersedia. Silakan buat mata pelajaran terlebih dahulu.</p>
                                        <a href="{{ route('admin.subjects.create') }}" class="text-purple-600 hover:text-purple-800 text-sm mt-2 inline-block">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah mata pelajaran baru
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @error('subjects')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-5">
                    <div class="flex justify-end">
                        <a href="{{ route('admin.classrooms.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit" class="ml-3 px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Kelas
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
        color: #8b5cf6;
    }
    
    .form-group:focus-within i {
        color: #8b5cf6;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate form groups when focused
        document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(element => {
            element.addEventListener('focus', function() {
                this.closest('.form-group').classList.add('focused');
            });
            
            element.addEventListener('blur', function() {
                this.closest('.form-group').classList.remove('focused');
            });
        });

        // Teacher select enhancement
        const teacherSelect = document.getElementById('homeroom_teacher_id');
        if (teacherSelect) {
            // Add placeholder text styling
            if (teacherSelect.value === '') {
                teacherSelect.classList.add('text-gray-400');
            }
            
            teacherSelect.addEventListener('change', function() {
                if (this.value === '') {
                    this.classList.add('text-gray-400');
                } else {
                    this.classList.remove('text-gray-400');
                }
            });
        }
    });
</script>
@endpush
