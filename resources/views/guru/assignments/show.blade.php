@extends('layouts.dashboard')

@section('title', 'Detail Tugas')

@section('header', 'Detail Tugas')

@section('navigation')
    <li>
        <a href="{{ route('guru.dashboard') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200">
            <div class="p-1.5 rounded-lg bg-indigo-700/50 group-hover:bg-indigo-700 transition-all duration-200">
                <i class="fas fa-tachometer-alt text-lg w-5 h-5 flex items-center justify-center text-indigo-300 group-hover:text-white"></i>
            </div>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.materials.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200">
            <div class="p-1.5 rounded-lg bg-indigo-700/50 group-hover:bg-green-700/50 transition-all duration-200">
                <i class="fas fa-book text-lg w-5 h-5 flex items-center justify-center text-indigo-300 group-hover:text-white"></i>
            </div>
            <span class="ml-3">Materi Pelajaran</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.assignments.index') }}" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-2.5 group relative text-white">
            <div class="p-1.5 rounded-lg bg-green-800 transition-all duration-200">
                <i class="fas fa-tasks text-lg w-5 h-5 flex items-center justify-center text-white"></i>
            </div>
            <span class="ml-3">Tugas</span>
            <span class="absolute inset-y-0 left-0 w-1 bg-green-400 rounded-tr-md rounded-br-md"></span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.grades.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200">
            <div class="p-1.5 rounded-lg bg-indigo-700/50 group-hover:bg-purple-700/50 transition-all duration-200">
                <i class="fas fa-star text-lg w-5 h-5 flex items-center justify-center text-indigo-300 group-hover:text-white"></i>
            </div>
            <span class="ml-3">Penilaian</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.attendance.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200">
            <div class="p-1.5 rounded-lg bg-indigo-700/50 group-hover:bg-purple-700/50 transition-all duration-200">
                <i class="fas fa-clipboard-check text-lg w-5 h-5 flex items-center justify-center text-indigo-300 group-hover:text-white"></i>
            </div>
            <span class="ml-3">Kehadiran</span>
        </a>
    </li>
    <li>
        <a href="{{ route('guru.announcements.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-2.5 group relative text-indigo-100 hover:text-white transition-all duration-200">
            <div class="p-1.5 rounded-lg bg-indigo-700/50 group-hover:bg-red-700/50 transition-all duration-200">
                <i class="fas fa-bullhorn text-lg w-5 h-5 flex items-center justify-center text-indigo-300 group-hover:text-white"></i>
            </div>
            <span class="ml-3">Pengumuman</span>
        </a>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
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

    <div class="mb-6">
        <a href="{{ route('guru.assignments.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar tugas
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200 mr-2">
                                    <i class="fas fa-book mr-1"></i> {{ $assignment->subject->name }}
                                </span>
                                @if($assignment->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                        <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $assignment->title }}</h1>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <div class="inline-flex items-center">
                                    <i class="fas fa-calendar-alt mr-1 text-gray-400"></i>
                                    <span>Dibuat: {{ $assignment->created_at->format('d M Y') }}</span>
                                </div>
                                @php
                                    $deadlineDate = $assignment->deadline ?? null;
                                    $isExpired = $deadlineDate ? now()->gt($deadlineDate) : false;
                                @endphp
                                <div class="inline-flex items-center">
                                    <i class="fas fa-clock mr-1 {{ $isExpired ? 'text-red-500' : 'text-gray-400' }}"></i>
                                    <span class="{{ $isExpired ? 'text-red-500 font-medium' : '' }}">
                                        Deadline: {{ $deadlineDate ? $deadlineDate->format('d M Y, H:i') : 'Tidak ada' }}
                                    </span>
                                </div>
                            </div>
                        </div>                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">                            <a href="{{ route('guru.assignments.edit', $assignment) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                <i class="fas fa-edit mr-2"></i> Edit                            </a>                            <a href="{{ route('guru.assignments.submissions.index', ['assignment' => $assignment]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors"><i class="fas fa-clipboard-list mr-2"></i> Pengumpulan 
                                <span class="ml-1 bg-white text-blue-700 rounded-full h-5 w-5 flex items-center justify-center text-xs">
                                    {{ $submissionStats['total'] ?? 0 }}
                                </span>
                            </a>
                            <a href="{{ route('guru.assignments.batch-grade', $assignment) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                <i class="fas fa-tasks mr-2"></i> Penilaian Batch
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Deskripsi Tugas</h3>
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            {!! nl2br(e($assignment->description)) !!}
                        </div>
                    </div>
                      @if($assignment->file)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Lampiran Tugas</h3>
                            <div class="flex items-center bg-blue-50 border border-blue-100 p-4 rounded-md">
                                <div class="p-2 bg-blue-100 rounded-md mr-4">
                                    @php
                                        $fileExt = pathinfo($assignment->file, PATHINFO_EXTENSION);
                                        $iconClass = 'fa-file-alt';
                                        $iconColor = 'text-blue-600';
                                        
                                        switch(strtolower($fileExt)) {
                                            case 'pdf':
                                                $iconClass = 'fa-file-pdf';
                                                $iconColor = 'text-red-600';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $iconClass = 'fa-file-word';
                                                $iconColor = 'text-blue-600';
                                                break;
                                            case 'xls':
                                            case 'xlsx':
                                                $iconClass = 'fa-file-excel';
                                                $iconColor = 'text-green-600';
                                                break;
                                            case 'ppt':
                                            case 'pptx':
                                                $iconClass = 'fa-file-powerpoint';
                                                $iconColor = 'text-orange-600';
                                                break;
                                            case 'zip':
                                            case 'rar':
                                                $iconClass = 'fa-file-archive';
                                                $iconColor = 'text-yellow-600';
                                                break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif':
                                                $iconClass = 'fa-file-image';
                                                $iconColor = 'text-purple-600';
                                                break;
                                        }
                                    @endphp
                                    <i class="fas {{ $iconClass }} {{ $iconColor }} text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-700 font-medium mb-1">{{ Str::afterLast($assignment->file, '/') }}</p>
                                    <p class="text-sm text-gray-500">File tugas yang diberikan kepada siswa</p>
                                    @if(Storage::exists($assignment->file) && ($size = Storage::size($assignment->file)))
                                        <p class="text-xs text-gray-500">{{ round($size / 1024 / 1024, 2) }} MB</p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ Storage::url($assignment->file) }}" target="_blank" class="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                    <a href="#" onclick="previewFile('{{ Storage::url($assignment->file) }}', '{{ strtolower($fileExt) }}')" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Preview
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Kelas yang Ditugaskan</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @if($assignment->class_id && $assignment->classes && !is_null($assignment->classes->id))
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 flex items-center">
                                    <div class="h-8 w-8 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <span class="text-gray-700">{{ $assignment->classes->name }}</span>
                                </div>
                            @else
                                <div class="col-span-3 text-gray-500 italic">Tidak ada kelas yang ditugaskan.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Submission Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Statistik Pengumpulan</h3>
                    <div class="flex space-x-2">
                        <a href="{{ route('guru.assignments.submissions.export', $assignment) }}" class="px-3 py-1 text-sm text-white bg-green-500 rounded hover:bg-green-600 flex items-center">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </a>
                        <a href="{{ route('guru.assignments.submissions.export-pdf', $assignment) }}" class="px-3 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600 flex items-center">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                            <div class="text-blue-800 text-sm font-medium mb-1">Total Siswa</div>
                            <div class="text-2xl font-bold text-blue-900">
                                @php
                                    $totalStudents = 0;
                                    foreach($assignment->classes as $class) {
                                        $totalStudents += $class->students_count ?? 0;
                                    }
                                @endphp
                                {{ $totalStudents }}
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                            <div class="text-green-800 text-sm font-medium mb-1">Sudah Mengumpulkan</div>
                            <div class="text-2xl font-bold text-green-900">{{ $submissionStats['total'] }}</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                            <div class="text-yellow-800 text-sm font-medium mb-1">Sudah Dinilai</div>
                            <div class="text-2xl font-bold text-yellow-900">{{ $submissionStats['graded'] }}</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                            <div class="text-red-800 text-sm font-medium mb-1">Belum Mengumpulkan</div>
                            <div class="text-2xl font-bold text-red-900">{{ $totalStudents - $submissionStats['total'] }}</div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <div class="text-sm font-medium mb-2">Progres Pengumpulan</div>
                        @php
                            $submissionPercent = $totalStudents > 0 ? round(($submissionStats['total'] / $totalStudents) * 100) : 0;
                            $gradingPercent = $submissionStats['total'] > 0 ? round(($submissionStats['graded'] / $submissionStats['total']) * 100) : 0;
                        @endphp
                        
                        <!-- Submission Progress Bar -->
                        <div class="h-3 relative max-w-full rounded-full overflow-hidden bg-gray-200 mb-4">
                            <div class="h-full bg-green-500 absolute rounded-full" style="width: {{ $submissionPercent }}%"></div>
                        </div>
                        <div class="text-sm text-gray-500 flex justify-between mb-4">
                            <div>Pengumpulan: {{ $submissionPercent }}%</div>
                            <div>{{ $submissionStats['total'] }}/{{ $totalStudents }}</div>
                        </div>
                        
                        <!-- Grading Progress Bar -->
                        <div class="h-3 relative max-w-full rounded-full overflow-hidden bg-gray-200 mb-4">
                            <div class="h-full bg-yellow-500 absolute rounded-full" style="width: {{ $gradingPercent }}%"></div>
                        </div>
                        <div class="text-sm text-gray-500 flex justify-between">
                            <div>Penilaian: {{ $gradingPercent }}%</div>
                            <div>{{ $submissionStats['graded'] }}/{{ $submissionStats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recently Submitted Work Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Pengumpulan Terbaru</h3>
                </div>
                <div class="p-6">
                    @if($assignment->submissions && $assignment->submissions->count() > 0)
                        <div class="space-y-3">
                            @foreach($assignment->submissions->sortByDesc('created_at')->take(3) as $submission)
                                <div class="flex items-center p-3 bg-gray-50 rounded-md border border-gray-200">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-800">{{ $submission->student->name ?? 'Siswa' }}</p>
                                        <p class="text-xs text-gray-500">{{ $submission->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if($submission->score)
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            Nilai: {{ $submission->score }}
                                        </span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                            Belum dinilai
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($assignment->submissions->count() > 3)
                            <div class="mt-3 text-center">
                                <a href="{{ route('guru.assignments.submissions.index', $assignment) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    Lihat semua pengumpulan ({{ $assignment->submissions->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3 bg-gray-50 rounded-md border border-gray-200">
                            <p class="text-sm text-gray-500">Belum ada pengumpulan tugas</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Tindakan Cepat</h3>
                </div>                <div class="p-4 space-y-3">                    <a href="{{ route('guru.assignments.submissions.index', $assignment) }}" 
                       class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg text-blue-700 transition-colors">
                        <i class="fas fa-clipboard-list mr-3"></i>
                        <span>Lihat Semua Pengumpulan</span>
                    </a>
                    <a href="{{ route('guru.assignments.statistics', $assignment) }}" 
                       class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg text-purple-700 transition-colors">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Lihat Statistik Tugas</span>
                    </a>
                    <a href="{{ route('guru.assignments.edit', $assignment) }}" 
                       class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg text-yellow-700 transition-colors">
                        <i class="fas fa-edit mr-3"></i>
                        <span>Edit Tugas</span>
                    </a>
                    
                    <form action="{{ route('guru.assignments.destroy', $assignment) }}" method="POST" 
                          id="delete-assignment-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" id="delete-assignment-btn" class="w-full flex items-center p-3 bg-red-50 hover:bg-red-100 rounded-lg text-red-700 transition-colors {{ $submissionStats['total'] > 0 ? 'opacity-50 cursor-not-allowed' : '' }}" 
                                {{ $submissionStats['total'] > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash-alt mr-3"></i>
                            <span>Hapus Tugas</span>
                        </button>
                        @if($submissionStats['total'] > 0)
                            <p class="text-xs text-red-500 mt-1">Tugas dengan pengumpulan tidak dapat dihapus.</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Preview Overlay -->
<div id="file-preview-overlay" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden" style="display: none; align-items: center; justify-content: center;">
    <div class="w-11/12 h-5/6 bg-white rounded-lg overflow-hidden relative max-w-5xl flex flex-col">
        <div class="bg-gray-100 p-4 flex items-center justify-between border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i id="preview-file-icon" class="fas fa-file mr-2"></i>
                <span id="preview-filename">Preview File</span>
            </h3>
            <button type="button" onclick="closeFilePreview()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="flex-1 overflow-auto p-4">
            <div id="file-content-container" class="h-full flex items-center justify-center">
                <div id="pdf-preview" class="hidden h-full w-full"></div>
                <div id="image-preview" class="hidden h-full flex items-center justify-center">
                    <img id="preview-image" src="" alt="File Preview" class="max-h-full max-w-full object-contain">
                </div>
                <div id="other-file-preview" class="hidden text-center p-8">
                    <div class="w-24 h-24 rounded-full bg-gray-100 mx-auto flex items-center justify-center mb-4">
                        <i id="other-preview-icon" class="fas fa-file-alt text-4xl text-blue-500"></i>
                    </div>
                    <h4 class="text-xl font-medium text-gray-800 mb-2">File tidak dapat ditampilkan</h4>
                    <p class="text-gray-600 mb-4">Tipe file ini tidak dapat ditampilkan secara langsung. Silakan download file untuk melihatnya.</p>
                    <a id="download-file-link" href="" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i> Download File
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
    // Set up PDF.js worker
    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        const deleteBtn = document.getElementById('delete-assignment-btn');
        const deleteForm = document.getElementById('delete-assignment-form');
        
        if(deleteBtn && deleteForm) {
            deleteBtn.addEventListener('click', function() {
                if(this.disabled) return;
                
                if(confirm('Apakah Anda yakin ingin menghapus tugas ini? Tindakan ini tidak dapat dibatalkan.')) {
                    deleteForm.submit();
                }
            });
        }
    });
    
    function previewFile(fileUrl, fileType) {
        const overlay = document.getElementById('file-preview-overlay');
        const pdfPreview = document.getElementById('pdf-preview');
        const imagePreview = document.getElementById('image-preview');
        const otherFilePreview = document.getElementById('other-file-preview');
        const previewImage = document.getElementById('preview-image');
        const previewFilename = document.getElementById('preview-filename');
        const downloadLink = document.getElementById('download-file-link');
        const previewFileIcon = document.getElementById('preview-file-icon');
        const otherPreviewIcon = document.getElementById('other-preview-icon');
        
        // Set download link
        downloadLink.href = fileUrl;
        
        // Set filename
        previewFilename.textContent = fileUrl.split('/').pop();
        
        // Reset all preview containers
        pdfPreview.classList.add('hidden');
        imagePreview.classList.add('hidden');
        otherFilePreview.classList.add('hidden');
        
        // Set appropriate icon
        let iconClass = 'fa-file-alt text-blue-500';
        
        switch(fileType) {
            case 'pdf':
                iconClass = 'fa-file-pdf text-red-500';
                break;
            case 'doc':
            case 'docx':
                iconClass = 'fa-file-word text-blue-500';
                break;
            case 'xls':
            case 'xlsx':
                iconClass = 'fa-file-excel text-green-500';
                break;
            case 'ppt':
            case 'pptx':
                iconClass = 'fa-file-powerpoint text-orange-500';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                iconClass = 'fa-file-image text-purple-500';
                break;
            case 'zip':
            case 'rar':
                iconClass = 'fa-file-archive text-yellow-500';
                break;
        }
        
        previewFileIcon.className = `fas ${iconClass} mr-2`;
        otherPreviewIcon.className = `fas ${iconClass} text-4xl`;
        
        // Show appropriate preview based on file type
        if (fileType === 'pdf' && window.pdfjsLib) {
            pdfPreview.classList.remove('hidden');
            pdfPreview.innerHTML = '';
            
            // Load PDF with PDF.js
            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                // Create container for all pages
                const container = document.createElement('div');
                container.className = 'pdf-container';
                pdfPreview.appendChild(container);
                
                // Load just the first few pages for performance
                const maxPagesToDisplay = 3;
                const numPages = Math.min(pdf.numPages, maxPagesToDisplay);
                
                for (let pageNum = 1; pageNum <= numPages; pageNum++) {
                    pdf.getPage(pageNum).then(function(page) {
                        const scale = 1.5;
                        const viewport = page.getViewport({ scale });
                        
                        // Create canvas for this page
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.className = 'mb-4 shadow-md mx-auto';
                        
                        // Add page number label
                        const pageLabel = document.createElement('div');
                        pageLabel.className = 'text-center text-sm text-gray-500 mb-6';
                        pageLabel.textContent = `Halaman ${pageNum} dari ${pdf.numPages}`;
                        
                        // Create wrapper for canvas and label
                        const pageWrapper = document.createElement('div');
                        pageWrapper.className = 'flex flex-col items-center mb-8';
                        pageWrapper.appendChild(canvas);
                        pageWrapper.appendChild(pageLabel);
                        container.appendChild(pageWrapper);
                        
                        // Render PDF page into canvas context
                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        
                        page.render(renderContext);
                    });
                }
                
                // Add a message if there are more pages
                if (pdf.numPages > maxPagesToDisplay) {
                    const morePages = document.createElement('div');
                    morePages.className = 'text-center p-4 bg-gray-100 rounded-md mb-4';
                    morePages.innerHTML = `<i class="fas fa-info-circle text-blue-500 mr-2"></i> PDF ini memiliki ${pdf.numPages} halaman. Download file untuk melihat semua halaman.`;
                    container.appendChild(morePages);
                }
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                pdfPreview.innerHTML = `<div class="text-center text-red-500">
                    <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                    <p>Gagal memuat dokumen PDF. Silahkan download file untuk melihatnya.</p>
                </div>`;
            });
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
            // For images, use img tag
            imagePreview.classList.remove('hidden');
            previewImage.src = fileUrl;
        } else {
            // For other file types, show download prompt
            otherFilePreview.classList.remove('hidden');
        }
          // Show the overlay
        overlay.classList.remove('hidden');
        overlay.style.display = 'flex';
    }
      function closeFilePreview() {
        const overlay = document.getElementById('file-preview-overlay');
        overlay.classList.add('hidden');
        overlay.style.display = 'none';
    }
</script>
@endpush
@endsection