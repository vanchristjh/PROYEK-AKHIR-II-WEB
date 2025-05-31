@extends('layouts.dashboard')

@section('title', 'Daftar Pengumpulan Tugas')

@section('header', 'Daftar Pengumpulan Tugas')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.assignments.show', $assignment) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke detail tugas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Pengumpulan Tugas: {{ $assignment->title }}</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                    <i class="fas fa-book mr-1"></i> {{ $assignment->subject->name }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                    <i class="fas fa-users mr-1"></i> {{ $assignment->classes->pluck('name')->join(', ') }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $assignment->isExpired() ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                    <i class="fas fa-clock mr-1"></i> Deadline: {{ $assignment->deadline ? $assignment->deadline->format('d M Y, H:i') : 'Tidak ada' }}
                </span>
            </div>
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

            <!-- Search and Filter Controls -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div>
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status-filter" class="w-full md:w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="all">Semua Status</option>
                            <option value="submitted">Sudah Mengumpulkan</option>
                            <option value="not-submitted">Belum Mengumpulkan</option>
                            <option value="graded">Sudah Dinilai</option>
                            <option value="not-graded">Belum Dinilai</option>
                        </select>
                    </div>
                    <div>
                        <label for="class-filter" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <select id="class-filter" class="w-full md:w-40 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="all">Semua Kelas</option>
                            @foreach($assignment->classes as $class)
                                <option value="{{ $class->name }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Siswa</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="search" class="pl-10 w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Nama siswa...">
                    </div>
                </div>
            </div>

            <!-- Submissions Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Siswa
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kelas
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Pengumpulan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nilai
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="submissions-table-body">
                        @foreach($students as $student)
                            <tr class="student-row" data-name="{{ strtolower($student['name']) }}" data-class="{{ $student['class'] }}" data-status="{{ $student['has_submitted'] ? ($student['submission'] && $student['submission']->score !== null ? 'graded' : 'not-graded') : 'not-submitted' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $student['name'] }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ID: {{ $student['id'] }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $student['class'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student['has_submitted'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah Mengumpulkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Belum Mengumpulkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($student['has_submitted'])
                                        {{ $student['submission']->created_at->format('d M Y, H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student['has_submitted'])
                                        @if($student['submission']->score !== null)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ $student['submission']->score }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">Belum dinilai</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($student['has_submitted'])
                                        <a href="{{ route('guru.assignments.submissions.show', ['assignment' => $assignment->id, 'submission' => $student['submission']]) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                        <a href="{{ route('guru.assignments.submissions.grade', ['assignment' => $assignment->id, 'submission' => $student['submission']]) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-star"></i> Nilai
                                        </a>
                                    @else
                                        <span class="text-gray-400">Tidak ada aksi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Stats -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="text-sm text-blue-800 font-medium mb-1">Total Siswa</div>
                    <div class="text-2xl font-bold text-blue-900">{{ count($students) }}</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                    <div class="text-sm text-green-800 font-medium mb-1">Sudah Mengumpulkan</div>
                    <div class="text-2xl font-bold text-green-900">{{ collect($students)->where('has_submitted', true)->count() }}</div>
                    <div class="text-xs text-green-700">{{ count($students) > 0 ? round((collect($students)->where('has_submitted', true)->count() / count($students)) * 100) : 0 }}% dari total siswa</div>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                    <div class="text-sm text-yellow-800 font-medium mb-1">Sudah Dinilai</div>
                    <div class="text-2xl font-bold text-yellow-900">{{ collect($students)->filter(function($student) { return $student['has_submitted'] && $student['submission']->score !== null; })->count() }}</div>
                    <div class="text-xs text-yellow-700">{{ collect($students)->where('has_submitted', true)->count() > 0 ? round((collect($students)->filter(function($student) { return $student['has_submitted'] && $student['submission']->score !== null; })->count() / collect($students)->where('has_submitted', true)->count()) * 100) : 0 }}% dari pengumpulan</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                    <div class="text-sm text-red-800 font-medium mb-1">Belum Mengumpulkan</div>
                    <div class="text-2xl font-bold text-red-900">{{ collect($students)->where('has_submitted', false)->count() }}</div>
                    <div class="text-xs text-red-700">{{ count($students) > 0 ? round((collect($students)->where('has_submitted', false)->count() / count($students)) * 100) : 0 }}% dari total siswa</div>
                </div>
            </div>
            
            <!-- Batch Actions -->
            <div class="mt-6 flex justify-between items-center">
                <div>
                    <a href="{{ route('guru.assignments.statistics', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-chart-bar mr-2"></i> Lihat Statistik
                    </a>
                    <a href="{{ route('guru.assignments.batch-grade', $assignment) }}" class="ml-3 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-tasks mr-2"></i> Penilaian Batch
                    </a>
                </div>
                <a href="{{ route('submissions.export', $assignment) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-file-export mr-2"></i> Export Data
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('status-filter');
        const classFilter = document.getElementById('class-filter');
        const rows = document.querySelectorAll('.student-row');
        
        function filterRows() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const classValue = classFilter.value;
            
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const classStr = row.getAttribute('data-class');
                const status = row.getAttribute('data-status');
                
                const matchesSearch = name.includes(searchTerm);
                const matchesStatus = statusValue === 'all' || 
                    (statusValue === 'submitted' && (status === 'graded' || status === 'not-graded')) ||
                    (statusValue === 'not-submitted' && status === 'not-submitted') ||
                    (statusValue === 'graded' && status === 'graded') ||
                    (statusValue === 'not-graded' && status === 'not-graded');
                const matchesClass = classValue === 'all' || classStr === classValue;
                
                if (matchesSearch && matchesStatus && matchesClass) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            updateNoResultsMessage();
        }
        
        function updateNoResultsMessage() {
            const visibleRows = document.querySelectorAll('.student-row[style=""]').length;
            const tableBody = document.getElementById('submissions-table-body');
            const existingMessage = document.getElementById('no-results-message');
            
            if (visibleRows === 0) {
                if (!existingMessage) {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'no-results-message';
                    noResultsRow.innerHTML = `
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <i class="fas fa-search mr-2"></i> Tidak ada hasil yang sesuai dengan filter.
                        </td>
                    `;
                    tableBody.appendChild(noResultsRow);
                }
            } else if (existingMessage) {
                existingMessage.remove();
            }
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', filterRows);
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', filterRows);
        }
        
        if (classFilter) {
            classFilter.addEventListener('change', filterRows);
        }
    });
</script>
@endpush
@endsection
