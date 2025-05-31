@extends('layouts.dashboard')

@section('title', 'Daftar Pengumpulan Tugas')

@section('header', 'Daftar Pengumpulan Tugas')

@section('content')
<div class="container px-4 py-6 mx-auto">
    <!-- Page Header -->
    <div class="relative p-6 mb-6 overflow-hidden text-white shadow-lg bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl">
        <div class="absolute -right-5 -top-5 opacity-10">
            <i class="fas fa-clipboard-list text-9xl"></i>
        </div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-3 py-1 mr-2 text-xs font-semibold text-blue-800 bg-blue-100 border border-blue-200 rounded-full">
                            <i class="mr-1 fas fa-book"></i> {{ $assignment->subject->name }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold">{{ $assignment->title }}</h2>
                    <p class="mt-1 text-blue-100">
                        <i class="mr-1 fas fa-calendar-alt"></i> 
                        Deadline: {{ $assignment->deadline ? $assignment->deadline->format('d M Y, H:i') : 'Tidak ada' }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('guru.assignments.show', $assignment) }}" class="flex items-center px-4 py-2 transition-colors rounded-lg shadow-sm bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i class="mr-2 fas fa-eye"></i> Detail Tugas
                    </a>
                    <a href="{{ route('guru.assignments.index') }}" class="flex items-center px-4 py-2 transition-colors rounded-lg shadow-sm bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i class="mr-2 fas fa-tasks"></i> Daftar Tugas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Messages -->
    @if(session('success'))
        <div class="p-4 mb-6 border-l-4 border-green-500 rounded-md bg-green-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="text-green-500 fas fa-check-circle"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 border-l-4 border-red-500 rounded-md bg-red-50">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="text-red-500 fas fa-exclamation-circle"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Submissions -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-blue-600 bg-blue-100 rounded-lg">
                    <i class="text-xl fas fa-file-alt"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Pengumpulan</div>
                    <div class="text-xl font-bold text-gray-800">{{ $submissions->total() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Graded Submissions -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-green-600 bg-green-100 rounded-lg">
                    <i class="text-xl fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Sudah Dinilai</div>
                    <div class="text-xl font-bold text-gray-800">{{ $submissions->filter(function($submission) { return !is_null($submission->score); })->count() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Pending Submissions -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-yellow-600 bg-yellow-100 rounded-lg">
                    <i class="text-xl fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Belum Dinilai</div>
                    <div class="text-xl font-bold text-gray-800">{{ $submissions->filter(function($submission) { return is_null($submission->score); })->count() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Average Score -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-purple-600 bg-purple-100 rounded-lg">
                    <i class="text-xl fas fa-star"></i>
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
    <div class="p-4 mb-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">            <form action="{{ route('guru.assignments.submissions.index', $assignment) }}" method="GET" class="flex flex-col items-center flex-1 gap-4 sm:flex-row">
                <div class="flex-1">
                    <div class="relative rounded-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="text-gray-400 fas fa-search"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." 
                               class="block w-full pl-10 border-gray-300 rounded-lg sm:text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <select name="status" class="border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Semua Status</option>
                        <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Sudah Dinilai</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Belum Dinilai</option>
                    </select>
                    <button type="submit" class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="mr-2 fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
            <div class="flex items-center mt-4 sm:mt-0">
                <a href="{{ route('guru.assignments.batch-grade', $assignment) }}" class="flex items-center px-4 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="mr-2 fas fa-tasks"></i> Penilaian Batch
                </a>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="mb-6 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Siswa
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            File
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Waktu Pengumpulan
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Nilai
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 text-gray-500 bg-gray-200 rounded-full">
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
                                        <div class="max-w-xs text-sm font-medium text-gray-900 truncate">{{ $submission->file_name ?? basename($submission->file_path) }}</div>
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
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('guru.assignments.submissions.download', [$assignment->id, $submission->id]) }}" class="px-2 py-1 text-blue-600 rounded hover:text-blue-900 bg-blue-50 hover:bg-blue-100">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button type="button" class="px-2 py-1 text-green-600 rounded hover:text-green-900 bg-green-50 hover:bg-green-100 grade-btn" data-submission="{{ $submission->id }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 mb-4 text-gray-400 bg-gray-100 rounded-full">
                                        <i class="text-2xl fas fa-file-alt"></i>
                                    </div>
                                    <p class="font-medium text-gray-500">Belum ada pengumpulan tugas</p>
                                    <p class="mt-1 text-sm text-gray-400">Siswa belum mengumpulkan tugas ini.</p>
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
    <div id="grading-modal" class="fixed inset-0 hidden overflow-y-auto" aria-modal="true" role="dialog">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" id="modal-backdrop"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-green-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <i class="text-green-600 fas fa-star"></i>
                        </div>
                        <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Beri Nilai Tugas
                            </h3>
                            <div class="mt-4">                                <form id="grading-form" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-4">
                                        <label for="score" class="block mb-1 text-sm font-medium text-gray-700">Nilai (0-100)</label>
                                        <input type="number" name="score" id="score" min="0" max="100" 
                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50" required>
                                    </div>
                                    <div>
                                        <label for="feedback" class="block mb-1 text-sm font-medium text-gray-700">Feedback (Opsional)</label>
                                        <textarea name="feedback" id="feedback" rows="3" 
                                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit-grade">
                        Simpan Nilai
                    </button>
                    <button type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" id="close-modal">
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
            button.addEventListener('click', function() {        const submissionId = this.dataset.submission;
        const assignmentId = '{{ $assignment->id }}';
        gradingForm.action = `{{ route('guru.assignments.submissions.grade', ['assignment' => ':assignment', 'submission' => ':submission']) }}`
          .replace(':assignment', assignmentId)
          .replace(':submission', submissionId);
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
