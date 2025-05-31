@extends('layouts.dashboard')

@section('title', 'Penilaian Batch Tugas')

@section('header', 'Penilaian Batch Tugas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.assignments.show', $assignment) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke detail tugas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="p-6 bg-gradient-to-r from-green-50 to-teal-50 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Penilaian Batch: {{ $assignment->title }}</h3>
            <p class="mt-1 text-sm text-gray-500">
                Nilai beberapa pengumpulan tugas sekaligus dengan mudah.
            </p>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <form id="batch-grade-form" action="{{ route('guru.assignments.batch-grade.save', $assignment) }}" method="POST">
                @csrf
                
                <!-- Tabs for classes -->                <div class="mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs" x-data="{ activeTab: '{{ $submissionsByClass->keys()->first() }}' }">
                            @foreach($submissionsByClass as $classId => $classData)
                                <button 
                                    type="button"
                                    @click="activeTab = '{{ $classId }}'"
                                    class="tab-button whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm"
                                    :class="activeTab === '{{ $classId }}' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    data-target="tab-{{ $classId }}"
                                >
                                    {{ $classData['class_name'] }}
                                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-blue-100 text-blue-800">
                                        {{ count($classData['submissions']) }}
                                    </span>
                                </button>
                            @endforeach
                        </nav>
                    </div>
                </div>
                
                <!-- Tab contents -->
                @foreach($submissionsByClass as $classId => $classData)
                    <div id="tab-{{ $classId }}" class="tab-content" x-show="activeTab === '{{ $classId }}'">
                        <div class="mb-4 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Kelas: {{ $classData['class_name'] }}</h3>
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <label for="select-all-{{ $classId }}" class="text-sm text-gray-700 mr-2">Pilih Semua</label>
                                    <input type="checkbox" id="select-all-{{ $classId }}" class="select-all-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="bulk-score-{{ $classId }}" class="text-sm text-gray-700 mr-2">Nilai Massal</label>
                                    <input type="number" id="bulk-score-{{ $classId }}" min="0" max="100" class="bulk-score w-20 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <button type="button" class="apply-bulk-score ml-2 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700" data-class-id="{{ $classId }}">
                                        Terapkan
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @if(count($classData['submissions']) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                                <span class="sr-only">Pilih</span>
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nama Siswa
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tanggal Pengumpulan
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                                Nilai
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($classData['submissions'] as $submission)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="checkbox" name="selected[]" value="{{ $submission->id }}" class="submission-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-500"></i>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $submission->student->name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                NIS: {{ $submission->student->nis ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        {{ $submission->created_at->format('d M Y') }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $submission->created_at->format('H:i') }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @if($submission->score !== null)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Sudah dinilai
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            Belum dinilai
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <input type="number" name="scores[{{ $submission->id }}]" min="0" max="100" class="score-input w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm" value="{{ $submission->score }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 p-6 text-center rounded-md border border-gray-200">
                                <i class="fas fa-info-circle text-blue-500 text-xl mb-2"></i>
                                <p class="text-gray-600">Tidak ada pengumpulan tugas untuk kelas ini.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
                
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('guru.assignments.show', $assignment) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Simpan Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkboxes functionality
        const selectAllCheckboxes = document.querySelectorAll('.select-all-checkbox');
        
        selectAllCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const classId = this.id.replace('select-all-', '');
                const tabContent = document.getElementById(`tab-${classId}`);
                const submissionCheckboxes = tabContent.querySelectorAll('.submission-checkbox');
                
                submissionCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
            });
        });
        
        // Apply bulk score functionality
        const applyBulkScoreButtons = document.querySelectorAll('.apply-bulk-score');
        
        applyBulkScoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const classId = this.getAttribute('data-class-id');
                const bulkScoreInput = document.getElementById(`bulk-score-${classId}`);
                const score = bulkScoreInput.value.trim();
                
                if (!score) {
                    alert('Silakan masukkan nilai yang ingin diterapkan.');
                    return;
                }
                
                const scoreValue = parseFloat(score);
                if (isNaN(scoreValue) || scoreValue < 0 || scoreValue > 100) {
                    alert('Nilai harus berupa angka antara 0-100.');
                    return;
                }
                
                const tabContent = document.getElementById(`tab-${classId}`);
                const selectedCheckboxes = tabContent.querySelectorAll('.submission-checkbox:checked');
                
                if (selectedCheckboxes.length === 0) {
                    alert('Pilih minimal satu siswa untuk diterapkan nilai.');
                    return;
                }
                
                selectedCheckboxes.forEach(checkbox => {
                    const submissionId = checkbox.value;
                    const scoreInput = tabContent.querySelector(`input[name="scores[${submissionId}]"]`);
                    scoreInput.value = scoreValue;
                });
                
                alert(`Nilai ${scoreValue} telah diterapkan untuk ${selectedCheckboxes.length} siswa yang dipilih.`);
            });
        });
        
        // Validate form before submit
        const form = document.getElementById('batch-grade-form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const selectedCheckboxes = document.querySelectorAll('.submission-checkbox:checked');
                
                if (selectedCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Pilih minimal satu siswa untuk dinilai.');
                    return false;
                }
                
                let hasError = false;
                
                selectedCheckboxes.forEach(checkbox => {
                    const submissionId = checkbox.value;
                    const scoreInput = document.querySelector(`input[name="scores[${submissionId}]"]`);
                    const score = scoreInput.value.trim();
                    
                    if (!score) {
                        hasError = true;
                        scoreInput.classList.add('border-red-500');
                    } else {
                        const scoreValue = parseFloat(score);
                        if (isNaN(scoreValue) || scoreValue < 0 || scoreValue > 100) {
                            hasError = true;
                            scoreInput.classList.add('border-red-500');
                        } else {
                            scoreInput.classList.remove('border-red-500');
                        }
                    }
                });
                
                if (hasError) {
                    e.preventDefault();
                    alert('Pastikan semua nilai siswa yang dipilih sudah diisi dengan benar (0-100).');
                    return false;
                }
                
                return true;
            });
        }
    });
</script>
@endpush
@endsection
