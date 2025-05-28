@extends('layouts.siswa')

@section('title', 'Pengumpulan Tugas')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .task-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .task-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
        border-radius: 4px;
        font-weight: 600;
    }
    
    .status-submitted {
        background-color: #10B981;
        color: white;
    }
    
    .status-pending {
        background-color: #F59E0B;
        color: white;
    }
    
    .status-late {
        background-color: #EF4444;
        color: white;
    }
    
    .status-graded {
        background-color: #3B82F6;
        color: white;
    }
    
    .deadline-indicator {
        font-size: 0.85rem;
    }
    
    .deadline-near {
        color: #EF4444;
        font-weight: 500;
    }
    
    .deadline-safe {
        color: #10B981;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">    <!-- Page Header -->
    <div class="bg-white p-5 rounded-lg shadow-sm mb-6 border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pengumpulan Tugas</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('siswa.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li class="inline-flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="text-sm text-gray-500">Pengumpulan Tugas</span>
                </li>
            </ol>
        </nav>
    </div>
    
    <!-- Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-5 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-0.5">TOTAL TUGAS</p>
                <div class="text-2xl font-semibold text-gray-800">{{ $totalAssignments ?? '10' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-0.5">SUDAH DIKUMPULKAN</p>
                <div class="text-2xl font-semibold text-gray-800">{{ $submittedCount ?? '7' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-0.5">BELUM DIKUMPULKAN</p>
                <div class="text-2xl font-semibold text-gray-800">{{ $pendingCount ?? '3' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5 flex items-center">
            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-0.5">NILAI RATA-RATA</p>
                <div class="text-2xl font-semibold text-gray-800">{{ $averageScore ?? '85' }}</div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1.5">
                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $averageScore ?? '85' }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4 border border-gray-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="subjectFilter" class="block text-xs text-gray-500 mb-1">Mata Pelajaran</label>
                <select id="subjectFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    <option value="">Semua Mata Pelajaran</option>
                    <option value="1">Matematika</option>
                    <option value="2">Bahasa Indonesia</option>
                    <option value="3">Bahasa Inggris</option>
                    <option value="4">Fisika</option>
                </select>
            </div>
            
            <div>
                <label for="statusFilter" class="block text-xs text-gray-500 mb-1">Status</label>
                <select id="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    <option value="">Semua Status</option>
                    <option value="submitted">Sudah Dikumpulkan</option>
                    <option value="pending">Belum Dikumpulkan</option>
                    <option value="late">Terlambat</option>
                    <option value="graded">Sudah Dinilai</option>
                </select>
            </div>
            
            <div>
                <label for="deadlineFilter" class="block text-xs text-gray-500 mb-1">Tenggat</label>
                <select id="deadlineFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                    <option value="">Semua Tenggat</option>
                    <option value="today">Hari Ini</option>
                    <option value="tomorrow">Besok</option>
                    <option value="week">Minggu Ini</option>
                </select>
            </div>
            
            <div>
                <label for="searchTugas" class="block text-xs text-gray-500 mb-1">Cari</label>
                <div class="relative">
                    <input type="text" id="searchTugas" placeholder="Cari tugas..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm pr-10">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Tugas</h2>
            <div id="activeFilterBadge" class="hidden px-3 py-1 bg-gray-100 rounded-full text-sm text-gray-700">
                <i class="fas fa-filter mr-1"></i> Filter Aktif
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Tugas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenggat Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                                    <i class="fas fa-file-word"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Analisis Novel "Laskar Pelangi"</div>
                                    <div class="text-sm text-gray-500">Diunggah: 12 Mei 2023</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Bahasa Indonesia</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-green-600">
                                <i class="far fa-clock mr-1"></i> 20 Mei 2023
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-md bg-green-100 text-green-800">
                                Sudah Dikumpulkan
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">85</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button class="text-gray-600 hover:text-gray-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Edit Pengumpulan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Row 2 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-red-500 rounded-lg flex items-center justify-center text-white">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Soal Latihan Matematika BAB 3</div>
                                    <div class="text-sm text-gray-500">Diunggah: 15 Mei 2023</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Matematika</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-red-600 font-medium">
                                <i class="far fa-clock mr-1"></i> Besok, 23:59
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-md bg-yellow-100 text-yellow-800">
                                Belum Dikumpulkan
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                Kumpulkan
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Row 3 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-500 rounded-lg flex items-center justify-center text-white">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Laporan Praktikum Fisika</div>
                                    <div class="text-sm text-gray-500">Diunggah: 10 Mei 2023</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Fisika</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-green-600">
                                <i class="far fa-clock mr-1"></i> 25 Mei 2023
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-md bg-blue-100 text-blue-800">
                                Sudah Dinilai
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">92</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button class="text-gray-600 hover:text-gray-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Lihat Komentar">
                                    <i class="fas fa-comment"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Row 4 -->
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-yellow-500 rounded-lg flex items-center justify-center text-white">
                                    <i class="fas fa-file-powerpoint"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Presentasi Sejarah Indonesia</div>
                                    <div class="text-sm text-gray-500">Diunggah: 5 Mei 2023</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Sejarah</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i> Terlewat 2 hari
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-md bg-red-100 text-red-800">
                                Terlambat
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">75</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button class="text-gray-600 hover:text-gray-900" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Edit Pengumpulan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Menampilkan 1-4 dari 10 tugas
            </div>
            <nav class="inline-flex rounded-md shadow-sm">
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-400 hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-indigo-50 text-sm font-medium text-indigo-600 hover:bg-indigo-100">
                    1
                </a>
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    2
                </a>
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                    3
                </a>
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </nav>
        </div>
    </div>
    
    <!-- Upcoming Deadlines Section -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Tenggat Waktu Mendatang</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Deadline Item 1 -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Soal Latihan Matematika BAB 3</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Mendesak
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fas fa-book mr-2"></i> Mata Pelajaran: Matematika
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div class="bg-red-600 h-2.5 rounded-full" style="width: 85%"></div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-red-600 font-medium"><i class="far fa-clock mr-1"></i> Besok, 23:59</span>
                            <button class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                Kumpulkan
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Deadline Item 2 -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                    <div class="p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Laporan Praktikum Fisika</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Minggu Ini
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fas fa-flask mr-2"></i> Mata Pelajaran: Fisika
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 45%"></div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600"><i class="far fa-clock mr-1"></i> 25 Mei 2023</span>
                            <button class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-b border-gray-200 px-4 py-3">
                <h5 class="modal-title text-lg font-medium text-gray-900" id="uploadModalLabel">Kumpulkan Tugas</h5>
                <button type="button" class="btn-close flex items-center justify-center w-6 h-6 text-gray-400 hover:text-gray-500" data-bs-dismiss="modal" aria-label="Close">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body p-6">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="assignment_title" class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md px-3 py-2 w-full" id="assignment_title" value="Soal Latihan Matematika BAB 3" readonly>
                    </div>
                    
                    <div class="mb-4">
                        <label for="assignment_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Tugas</label>
                        <textarea class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md px-3 py-2 w-full" id="assignment_description" rows="3" readonly>Kerjakan latihan soal matematika BAB 3 halaman 45-50.</textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="submission_file" class="block text-sm font-medium text-gray-700 mb-1">Unggah File <span class="text-red-500">*</span></label>
                        <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-md py-2 px-3 file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100" type="file" id="submission_file" required>
                        <p class="mt-1 text-sm text-gray-500">Format yang diperbolehkan: PDF, DOC, DOCX. Ukuran maksimal: 10MB</p>
                    </div>
                    
                    <div class="mb-4">
                        <label for="submission_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea class="border border-gray-300 text-gray-900 text-sm rounded-md px-3 py-2 w-full" id="submission_notes" rows="3" placeholder="Tambahkan catatan untuk guru..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-gray-50 px-6 py-3 flex justify-end">
                <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Kumpulkan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips with Tippy.js or fallback to Bootstrap tooltips
        function initTooltips() {
            if (typeof tippy !== 'undefined') {
                // If Tippy.js is available
                tippy('[title]', {
                    content(reference) {
                        const title = reference.getAttribute('title');
                        reference.removeAttribute('title');
                        return title;
                    },
                    delay: [100, 0],
                    arrow: true,
                    animation: 'scale'
                });
            } else if (typeof bootstrap !== 'undefined') {
                // Fallback to Bootstrap tooltips
                const tooltipTriggerList = document.querySelectorAll('[title], [data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].forEach(el => {
                    const tooltip = new bootstrap.Tooltip(el);
                });
            } else {
                // Basic native title attribute
                console.log('No tooltip library found, using native tooltips');
            }
        }

        // Try to initialize tooltips
        try {
            initTooltips();
        } catch (e) {
            console.warn('Could not initialize tooltips:', e);
        }
        
        // Add filter functionality
        const filterInputs = document.querySelectorAll('#subjectFilter, #statusFilter, #deadlineFilter, #searchTugas');
        const activeFilterBadge = document.getElementById('activeFilterBadge');
        
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value) {
                    activeFilterBadge.classList.remove('hidden');
                } else {
                    // Check if any filter is active
                    const anyActive = Array.from(filterInputs).some(filter => filter.value);
                    if (anyActive) {
                        activeFilterBadge.classList.remove('hidden');
                    } else {
                        activeFilterBadge.classList.add('hidden');
                    }
                }
                
                // Here you would normally fetch filtered data from server
                console.log('Filter changed:', this.id, this.value);
            });
            
            if (input.type === 'text') {
                input.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter' && this.value) {
                        activeFilterBadge.classList.remove('hidden');
                        // Here you would search based on the input
                        console.log('Searching for:', this.value);
                    }
                });
            }
        });
        
        // Handle modal data passing for "Kumpulkan" buttons
        const submitButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
        submitButtons.forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                if (row) {
                    const title = row.querySelector('.text-sm.font-medium')?.textContent || 
                                 row.querySelector('.font-medium')?.textContent || 
                                 'Tugas';
                    document.getElementById('assignment_title').value = title;
                }
            });
        });

        // Mobile responsive menu handler (if needed)
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
@endpush
