@extends('layouts.dashboard')

@section('title', 'Daftar Pengumpulan Tugas')

@section('header', 'Daftar Pengumpulan Tugas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute -right-5 -top-5 opacity-10">
            <i class="fas fa-clipboard-list text-9xl"></i>
        </div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mr-2">
                            <i class="fas fa-book mr-1"></i> {{ $assignment->subject->name }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold">{{ $assignment->title }}</h2>
                    <p class="text-blue-100 mt-1">
                        <i class="fas fa-calendar-alt mr-1"></i> 
                        Deadline: {{ $assignment->deadline ? $assignment->deadline->format('d M Y, H:i') : 'Tidak ada' }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('guru.assignments.show', $assignment) }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors backdrop-blur-sm">
                        <i class="fas fa-eye mr-2"></i> Detail Tugas
                    </a>
                    <a href="{{ route('guru.assignments.index') }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors backdrop-blur-sm">
                        <i class="fas fa-tasks mr-2"></i> Daftar Tugas
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Submissions -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Pengumpulan</div>
                    <div class="text-xl font-bold text-gray-800">{{ $submissions->total() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Graded Submissions -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center text-green-600 mr-3">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Sudah Dinilai</div>
                    <div class="text-xl font-bold text-gray-800">{{ $submissions->filter(function($submission) { return !is_null($submission->score); })->count() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Pending Submissions -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-yellow-100 flex items-center justify-center text-yellow-600 mr-3">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Belum Dinilai</div>
                    <div class="text-xl font-bold text-gray-800">{{ $submissions->filter(function($submission) { return is_null($submission->score); })->count() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Average Score -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 mr-3">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Rata-rata Nilai</div>
                    <div class="text-xl font-bold text-gray-800">
                        @php
                            $scoredSubmissions = $submissions->filter(function($submission) { 
                                return !is_null($submission->score); 
                            });
                            $avgScore = $scoredSubmissions->count() > 0 
                                ? number_format($scoredSubmissions->sum('score') / $scoredSubmissions->count(), 1) 
                                : '-';
                        @endphp
                        {{ $avgScore }}
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- Search and Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">            <form action="{{ route('guru.assignments.submissions.index', $assignment) }}" method="GET" class="flex flex-1 flex-col sm:flex-row items-center gap-4">
                <div class="flex-1">
                    <div class="relative rounded-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." 
                               class="block w-full pl-10 sm:text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <select name="status" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Semua Status</option>
                        <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Dinilai</option>
                    </select>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                </div>
            </form>
            <div class="flex items-center mt-4 sm:mt-0">
                <a href="{{ route('guru.assignments.batch-grade', $assignment) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-tasks mr-2"></i> Penilaian Batch
                </a>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Siswa
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            File
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Waktu Pengumpulan
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nilai
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        {{ strtoupper(substr($submission->student->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $submission->student->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $submission->student->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-md {{ $submission->file_color ?? 'bg-gray-500' }} flex items-center justify-center text-white">
                                        <i class="fas fa-{{ $submission->file_icon ?? 'file' }}"></i>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ $submission->file_name ?? basename($submission->file_path) }}</div>
                                        <div class="text-xs text-gray-500">{{ $submission->file_size ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $submission->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->created_at->format('H:i') }}</div>
                                @if($submission->created_at->gt($assignment->deadline))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Terlambat
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission->score !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission->score !== null)
                                    <div class="text-sm font-medium text-gray-900">{{ $submission->score }}</div>
                                @else
                                    <div class="text-sm text-gray-500">-</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('guru.submissions.download', $submission) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-2 py-1 rounded grade-btn" data-submission="{{ $submission->id }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex-shrink-0 h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-4">
                                        <i class="fas fa-file-alt text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Belum ada pengumpulan tugas</p>
                                    <p class="text-gray-400 text-sm mt-1">Siswa belum mengumpulkan tugas ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($submissions->hasPages())
        <div class="px-4">
            {{ $submissions->links() }}
        </div>
    @endif

    <!-- Grading Modal -->
    <div id="grading-modal" class="fixed inset-0 overflow-y-auto hidden" aria-modal="true" role="dialog">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="modal-backdrop"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-star text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Beri Nilai Tugas
                            </h3>
                            <div class="mt-4">
                                <form id="grading-form" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Nilai (0-100)</label>
                                        <input type="number" name="score" id="score" min="0" max="100" 
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50" required>
                                    </div>
                                    <div>
                                        <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback (Opsional)</label>
                                        <textarea name="feedback" id="feedback" rows="3" 
                                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit-grade">
                        Simpan Nilai
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="close-modal">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gradingModal = document.getElementById('grading-modal');
        const modalBackdrop = document.getElementById('modal-backdrop');
        const closeModalButton = document.getElementById('close-modal');
        const gradeButtons = document.querySelectorAll('.grade-btn');
        const gradingForm = document.getElementById('grading-form');
        const submitGradeButton = document.getElementById('submit-grade');
        
        // Open modal with the correct submission ID
        gradeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const submissionId = this.dataset.submission;
                gradingForm.action = `{{ url('guru/submissions') }}/${submissionId}/grade`;
                gradingModal.classList.remove('hidden');
            });
        });
        
        // Close modal when backdrop or close button is clicked
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', function() {
                gradingModal.classList.add('hidden');
            });
        }
        
        if (closeModalButton) {
            closeModalButton.addEventListener('click', function() {
                gradingModal.classList.add('hidden');
            });
        }
        
        // Submit the form when the submit button is clicked
        if (submitGradeButton) {
            submitGradeButton.addEventListener('click', function() {
                const scoreInput = document.getElementById('score');
                if (scoreInput.validity.valid) {
                    gradingForm.submit();
                } else {
                    alert('Nilai harus antara 0 dan 100.');
                }
            });
        }
    });
</script>
@endpush
@endsection
