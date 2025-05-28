@extends('layouts.dashboard')

@section('title', 'Penilaian Batch Pengumpulan Tugas')

@section('header', 'Penilaian Batch Pengumpulan Tugas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute -right-5 -top-5 opacity-10">
            <i class="fas fa-clipboard-check text-9xl"></i>
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
                    <a href="{{ route('guru.submissions.index', $assignment) }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg flex items-center shadow-sm transition-colors backdrop-blur-sm">
                        <i class="fas fa-list mr-2"></i> Daftar Pengumpulan
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

    <!-- Batch Grading Instructions -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-md">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Penilaian Batch Tugas Siswa</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Untuk menilai banyak tugas sekaligus:</p>
                    <ol class="list-decimal list-inside mt-1 ml-2">
                        <li>Centang kotak di samping nama siswa yang ingin dinilai</li>
                        <li>Klik tombol "Nilai Batch" yang muncul di bawah tabel</li>
                        <li>Isikan nilai default (opsional) dan feedback default (opsional)</li>
                        <li>Sesuaikan nilai individual jika diperlukan</li>
                        <li>Klik "Simpan Semua Nilai" untuk menyimpan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Grading Form -->
    <form id="batch-grade-form" action="{{ route('guru.submissions.update-batch') }}" method="POST" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        @csrf
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <div class="flex items-center">
                <input type="checkbox" id="select-all-submissions" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mr-2">
                <label for="select-all-submissions" class="text-sm font-medium text-gray-700">Pilih Semua</label>
            </div>
            <div class="flex items-center">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">
                    Terpilih: <span id="selected-count">0</span>
                </span>
                <button type="button" id="batch-grade-btn" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded-md hidden transition-colors text-sm">
                    <i class="fas fa-tasks mr-1"></i> Nilai Batch
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pilih
                        </th>
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
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr class="submission-row hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $submission->id }}" class="submission-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                        {{ strtoupper(substr($submission->student->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="ml-3" data-student-name="{{ $submission->student->name ?? 'Unknown' }}">
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
                                        <div class="text-xs text-gray-500">{{ $submission->human_file_size ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" data-submitted-date="{{ $submission->created_at->format('d M Y, H:i') }}">
                                <div class="text-sm text-gray-900">{{ $submission->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->created_at->format('H:i') }}</div>
                                @if($submission->is_late)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Terlambat
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission->score !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                        {{ $submission->score }} / 100
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('guru.submissions.download', $submission) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('guru.submissions.show', ['assignment' => $assignment->id, 'submission' => $submission->id]) }}" class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-2 py-1 rounded" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
    </form>

    <!-- Pagination -->
    @if($submissions->hasPages())
        <div class="px-4">
            {{ $submissions->links() }}
        </div>
    @endif

    <!-- Batch Grade Modal -->
    <div id="batch-grade-modal" class="fixed inset-0 overflow-y-auto hidden z-50" aria-modal="true" role="dialog">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-tasks text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Penilaian Batch Tugas
                            </h3>
                            <div class="mt-4">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="default-score" class="block text-sm font-medium text-gray-700 mb-1">Nilai Default (0-100)</label>
                                        <input type="number" id="default-score" min="0" max="100" 
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    </div>
                                    <div>
                                        <label for="default-feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback Default</label>
                                        <input type="text" id="default-feedback" 
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                               placeholder="Contoh: Bagus, tingkatkan lagi">
                                    </div>
                                </div>

                                <div class="overflow-y-auto max-h-96">
                                    <table id="batch-grade-table" class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengumpulan</th>
                                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feedback</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <!-- Will be filled dynamically by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit-batch-grades">
                        <i class="fas fa-save mr-2"></i> Simpan Semua Nilai
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-modal">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div id="progress-modal" class="fixed inset-0 overflow-y-auto hidden z-50" aria-modal="true" role="dialog">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-spinner fa-spin text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Memproses Penilaian
                            </h3>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div id="progress-bar" class="bg-blue-500 h-2.5 rounded-full" style="width: 0%"></div>
                                </div>
                                <p id="progress-text" class="text-sm text-gray-500 mt-2">Memproses...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/batch-grading.js') }}"></script>
@endpush

@endsection
