@extends('layouts.dashboard')

@section('title', 'Tambah Jadwal Mengajar')

@section('header', 'Tambah Jadwal Mengajar')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-calendar-plus text-9xl"></i>
        </div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-2">Tambah Jadwal Mengajar</h2>
            <p class="text-blue-100">Tambahkan jadwal mengajar baru untuk mata pelajaran dan kelas.</p>
            <div class="mt-3 bg-white/20 p-2 rounded-lg inline-block text-sm">
                <i class="fas fa-info-circle mr-1"></i> 
                Catatan: Jadwal yang ditambahkan akan terlihat oleh siswa di kelas yang dipilih.
            </div>
        </div>
    </div>

    <div class="mb-6">
        <a href="{{ route('guru.schedule.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-chevron-left mr-2 text-sm"></i>
            <span>Kembali ke Jadwal Mengajar</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 transform transition hover:shadow-md">
        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('guru.schedule.store') }}" method="POST" class="animate-fade-in">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group mb-5">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-book text-gray-400"></i>
                            </div>
                            <select name="subject_id" id="subject_id" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300" required>
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
                    
                    <div class="form-group mb-5">
                        <label for="classroom_id" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <select name="classroom_id" id="classroom_id" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                        {{ $classroom->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('classroom_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="day" class="block text-sm font-medium text-gray-700 mb-1">Hari</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-day text-gray-400"></i>
                            </div>
                            <select name="day" id="day" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                                <option value="">Pilih Hari</option>
                                <option value="1" {{ old('day') == 1 ? 'selected' : '' }}>Senin</option>
                                <option value="2" {{ old('day') == 2 ? 'selected' : '' }}>Selasa</option>
                                <option value="3" {{ old('day') == 3 ? 'selected' : '' }}>Rabu</option>
                                <option value="4" {{ old('day') == 4 ? 'selected' : '' }}>Kamis</option>
                                <option value="5" {{ old('day') == 5 ? 'selected' : '' }}>Jumat</option>
                                <option value="6" {{ old('day') == 6 ? 'selected' : '' }}>Sabtu</option>
                                <option value="7" {{ old('day') == 7 ? 'selected' : '' }}>Minggu</option>
                            </select>
                        </div>
                        @error('day')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="room" class="block text-sm font-medium text-gray-700 mb-1">Ruangan (Opsional)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-door-open text-gray-400"></i>
                            </div>
                            <input type="text" name="room" id="room" value="{{ old('room') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300" 
                                placeholder="Contoh: R. 101, Lab Komputer">
                        </div>
                        @error('room')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        @error('start_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group mb-5">
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" 
                                class="pl-10 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-shadow duration-300" required>
                        </div>
                        @error('end_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-t border-gray-200 mt-8 pt-5">
                    <div class="flex justify-end">
                        <a href="{{ route('guru.schedule.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-300">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                        <button type="submit" class="ml-3 px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i> Simpan Jadwal
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
        color: #3B82F6;
    }
    
    .form-group:focus-within i {
        color: #3B82F6;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for time conflicts when selecting start/end times
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const form = document.querySelector('form');
        const daySelect = document.getElementById('day');
        const classroomSelect = document.getElementById('classroom_id');
        const subjectSelect = document.getElementById('subject_id');
        
        // Function to validate times
        function validateTimes() {
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            
            if (startTime && endTime && startTime >= endTime) {
                endTimeInput.setCustomValidity('Waktu selesai harus setelah waktu mulai');
                return false;
            } else {
                endTimeInput.setCustomValidity('');
                return true;
            }
        }
        
        // Function to check schedule conflicts
        async function checkScheduleConflict() {
            const day = daySelect.value;
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;
            const classroomId = classroomSelect.value;
            
            if (!day || !startTime || !endTime || !classroomId) {
                return { conflict: false };
            }
            
            try {
                const response = await fetch(`/api/check-schedule-conflict?day=${day}&start_time=${startTime}&end_time=${endTime}&classroom_id=${classroomId}`);
                return await response.json();
            } catch (error) {
                console.error('Error checking schedule conflict:', error);
                return { conflict: false };
            }
        }
        
        // Event listeners for time inputs
        startTimeInput.addEventListener('change', validateTimes);
        endTimeInput.addEventListener('change', validateTimes);
        
        // Enable teacher selection based on subject
        subjectSelect.addEventListener('change', async function() {
            const subjectId = this.value;
            if (subjectId) {
                try {
                    const response = await fetch(`/api/subjects/${subjectId}/teachers`);
                    const teachers = await response.json();
                    
                    // Update teacher field or display info
                    // This would be implemented if teacher selection is required
                } catch (error) {
                    console.error('Error fetching teachers for subject:', error);
                }
            }
        });
        
        // Validate form before submission
        form.addEventListener('submit', async function(e) {
            // Reset validity
            form.querySelectorAll('input, select').forEach(field => {
                field.classList.remove('border-red-300');
                field.setCustomValidity('');
            });
            
            // Check required fields
            let hasEmptyRequired = false;
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.setCustomValidity('Bidang ini wajib diisi');
                    field.classList.add('border-red-300');
                    hasEmptyRequired = true;
                }
            });
            
            if (hasEmptyRequired) {
                e.preventDefault();
                alert('Silakan lengkapi semua bidang yang wajib diisi');
                return;
            }
            
            // Check time validity
            if (!validateTimes()) {
                e.preventDefault();
                alert('Waktu selesai harus setelah waktu mulai');
                endTimeInput.classList.add('border-red-300');
                return;
            }
            
            // Check for schedule conflicts
            e.preventDefault(); // Temporarily prevent submission
            
            // Show checking indicator
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memeriksa jadwal...';
            submitBtn.disabled = true;
            
            const conflictResult = await checkScheduleConflict();
            
            if (conflictResult.conflict) {
                // Show conflict warning
                alert(`Konflik jadwal terdeteksi:\n${conflictResult.message}\n\nSilakan pilih waktu lain.`);
                startTimeInput.classList.add('border-red-300');
                endTimeInput.classList.add('border-red-300');
                
                // Restore button
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                return;
            }
            
            // If no conflicts, show final loading state and submit
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Submit the form
            form.submit();
        });
        
        // Run validation on page load
        validateTimes();
    });
</script>
@endpush
