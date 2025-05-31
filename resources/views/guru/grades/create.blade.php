@extends('layouts.dashboard')

@section('title', 'Buat Penilaian Baru')

@section('header', 'Buat Penilaian Baru')

@section('navigation')
    <li>
        <a href="{{ route('guru.dashboard') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.materials.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-book text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Materi Pelajaran</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.assignments.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tasks text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Tugas</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.grades.index') }}" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-star text-lg w-6"></i>
            <span class="ml-3">Penilaian</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.attendance.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-clipboard-check text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Kehadiran</span>
        </a>
    </li>
@endsection

@section('content')
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-star text-9xl"></i>
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Buat Penilaian Baru</h2>
            <p class="text-purple-100">Buat penilaian langsung untuk siswa di luar tugas.</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            <form action="{{ route('guru.grades.store') }}" method="POST" class="animate-fade-in" id="gradeForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Subject Selection -->
                    <div class="form-group mb-5">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <select id="subject_id" name="subject_id" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('subject_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Classroom Selection -->
                    <div class="form-group mb-5">
                        <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <select id="classroom_id" name="classroom_id" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" disabled>
                                <option value="">Pilih Kelas</option>
                            </select>
                        </div>
                        @error('classroom_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Student Selection -->
                    <div class="form-group mb-5">
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Siswa</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <select id="student_id" name="student_id" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50" disabled>
                                <option value="">Pilih Siswa</option>
                            </select>
                            <div id="student-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </div>
                        </div>
                        @error('student_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Assessment Type -->
                    <div class="form-group mb-5">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Penilaian</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <select id="type" name="type" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                <option value="">Pilih Tipe</option>
                                <option value="tugas" {{ old('type') == 'tugas' ? 'selected' : '' }}>Tugas</option>
                                <option value="ulangan" {{ old('type') == 'ulangan' ? 'selected' : '' }}>Ulangan</option>
                                <option value="ujian" {{ old('type') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                                <option value="keterampilan" {{ old('type') == 'keterampilan' ? 'selected' : '' }}>Keterampilan</option>
                                <option value="sikap" {{ old('type') == 'sikap' ? 'selected' : '' }}>Sikap</option>
                            </select>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Score and Max Score -->
                    <div class="form-group mb-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Nilai</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="score" id="score" required min="0" step="0.01" value="{{ old('score') }}" 
                                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            </div>
                            @error('score')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-gray-700 mb-1">Nilai Maksimal</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="max_score" id="max_score" required min="1" step="0.01" value="{{ old('max_score', 100) }}" 
                                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            </div>
                            @error('max_score')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Academic Info -->
                    <div class="form-group mb-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <select id="semester" name="semester" required class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                    <option value="">Pilih Semester</option>
                                    <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                                    <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                                </select>
                            </div>
                            @error('semester')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="text" name="academic_year" id="academic_year" required placeholder="2024/2025" value="{{ old('academic_year') }}" 
                                       class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            </div>
                            @error('academic_year')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Feedback -->
                    <div class="form-group mb-5 md:col-span-2">
                        <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Catatan/Feedback</label>
                        <div class="mt-1 rounded-md shadow-sm">
                            <textarea name="feedback" id="feedback" rows="4" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50">{{ old('feedback') }}</textarea>
                        </div>
                        @error('feedback')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-5">
                    <div class="flex justify-end">
                        <a href="{{ route('guru.grades.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit" class="ml-3 px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Penilaian
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
        color: #8B5CF6;
    }
    
    .form-group:focus-within i {
        color: #8B5CF6;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        const classroomSelect = document.getElementById('classroom_id');
        const studentSelect = document.getElementById('student_id');
        const studentLoading = document.getElementById('student-loading');
        
        // Function to load students when both subject and classroom are selected
        function loadStudents() {
            const classroomId = classroomSelect.value;
            
            if (!classroomId) {
                studentSelect.disabled = true;
                studentSelect.innerHTML = '<option value="">Pilih Siswa</option>';
                return;
            }
            
            // Show loading indicator
            studentLoading.classList.remove('hidden');
            studentSelect.disabled = true;
            
            // Fetch students in this classroom
            fetch(`/guru/classrooms/${classroomId}/students`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(students => {
                    // Hide loading indicator
                    studentLoading.classList.add('hidden');
                    
                    // Populate student select
                    studentSelect.innerHTML = '<option value="">Pilih Siswa</option>';
                    
                    if (students.length > 0) {
                        students.forEach(student => {
                            const option = document.createElement('option');
                            option.value = student.id;
                            option.textContent = `${student.name} (${student.nis || 'NIS tidak tersedia'})`;
                            studentSelect.appendChild(option);
                        });
                        studentSelect.disabled = false;
                    } else {
                        studentSelect.innerHTML = '<option value="">Tidak ada siswa di kelas ini</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    studentLoading.classList.add('hidden');
                    studentSelect.innerHTML = '<option value="">Error loading students</option>';
                });
        }
        
        // Load classrooms when subject changes
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            if (!subjectId) {
                classroomSelect.disabled = true;
                classroomSelect.innerHTML = '<option value="">Pilih Kelas</option>';
                return;
            }
            
            // Show loading for classrooms
            classroomSelect.disabled = true;
            classroomSelect.innerHTML = '<option value="">Loading...</option>';
            
            // Reset student select
            studentSelect.disabled = true;
            studentSelect.innerHTML = '<option value="">Pilih Siswa</option>';
            
            // Fetch classrooms for this subject
            fetch(`/guru/subjects/${subjectId}/classrooms`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(classrooms => {
                    // Populate classroom select
                    classroomSelect.innerHTML = '<option value="">Pilih Kelas</option>';
                    
                    if (classrooms.length > 0) {
                        classrooms.forEach(classroom => {
                            const option = document.createElement('option');
                            option.value = classroom.id;
                            option.textContent = classroom.name;
                            classroomSelect.appendChild(option);
                        });
                        classroomSelect.disabled = false;
                    } else {
                        classroomSelect.innerHTML = '<option value="">Tidak ada kelas untuk mata pelajaran ini</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading classrooms:', error);
                    classroomSelect.innerHTML = '<option value="">Error loading classrooms</option>';
                });
        });
        
        // Load students when classroom changes
        classroomSelect.addEventListener('change', loadStudents);
        
        // Form validation
        const gradeForm = document.getElementById('gradeForm');
        
        gradeForm.addEventListener('submit', function(e) {
            let hasError = false;
            
            // Check if score is less than or equal to max score
            const score = parseFloat(document.getElementById('score').value);
            const maxScore = parseFloat(document.getElementById('max_score').value);
            const scoreInput = document.getElementById('score');
            
            if (score > maxScore) {
                e.preventDefault();
                scoreInput.classList.add('border-red-500');
                const errorMsgContainer = document.querySelector('#score-error') || document.createElement('p');
                errorMsgContainer.id = 'score-error';
                errorMsgContainer.className = 'text-red-500 text-xs mt-1';
                errorMsgContainer.textContent = 'Nilai tidak boleh lebih besar dari nilai maksimal.';
                
                if (!document.querySelector('#score-error')) {
                    scoreInput.parentNode.parentNode.appendChild(errorMsgContainer);
                }
                
                hasError = true;
            } else {
                scoreInput.classList.remove('border-red-500');
                const errorMsg = document.querySelector('#score-error');
                if (errorMsg) errorMsg.remove();
            }
            
            // Check if all required fields are filled
            const requiredFields = document.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                const fieldId = field.id;
                const fieldLabel = document.querySelector(`label[for="${fieldId}"]`)?.textContent || 'Field';
                
                if (!field.value) {
                    e.preventDefault();
                    field.classList.add('border-red-500');
                    
                    const errorMsgContainer = document.querySelector(`#${fieldId}-error`) || document.createElement('p');
                    errorMsgContainer.id = `${fieldId}-error`;
                    errorMsgContainer.className = 'text-red-500 text-xs mt-1';
                    errorMsgContainer.textContent = `${fieldLabel.replace('*', '').trim()} tidak boleh kosong.`;
                    
                    if (!document.querySelector(`#${fieldId}-error`)) {
                        field.parentNode.parentNode.appendChild(errorMsgContainer);
                    }
                    
                    hasError = true;
                } else {
                    field.classList.remove('border-red-500');
                    const errorMsg = document.querySelector(`#${fieldId}-error`);
                    if (errorMsg) errorMsg.remove();
                }
            });
            
            if (hasError) {
                e.preventDefault();
                
                // Show error notification at the top
                const errorNotification = document.createElement('div');
                errorNotification.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md animate-fade-in';
                errorNotification.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">Ada kesalahan pada form. Silakan periksa kembali.</p>
                        </div>
                    </div>
                `;
                
                // Insert at the top of the form
                const form = document.getElementById('gradeForm');
                form.insertBefore(errorNotification, form.firstChild);
                
                // Scroll to top
                window.scrollTo(0, 0);
            }
        });
    });
</script>
@endpush
