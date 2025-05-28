@extends('layouts.dashboard')

@section('title', $material->title)

@section('header', 'Detail Materi Pembelajaran')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
    <div class="mb-6">
        <a href="{{ route('guru.materials.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-chevron-left mr-2 text-sm"></i>
            <span>Kembali ke Daftar Materi</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Material Header -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100/50 mb-6">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex items-start">
                    <div class="{{ $material->getFileColorAttribute() }} bg-opacity-20 p-3 rounded-full mr-4">
                        <i class="fas {{ $material->getFileIconAttribute() }} {{ $material->getFileColorAttribute() }} text-xl"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="text-xs font-medium uppercase tracking-wider {{ $material->getFileColorAttribute() }} bg-{{ strtolower(pathinfo($material->file_path, PATHINFO_EXTENSION)) }}-100 bg-opacity-50 rounded-md px-2.5 py-0.5">{{ $material->getFileTypeShort() }}</span>
                            
                            @if($material->isNew())
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-md">Baru</span>
                            @endif
                            
                            @if($material->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-md">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-md">Nonaktif</span>
                            @endif
                        </div>
                        
                        <h1 class="text-2xl font-bold text-gray-900">
                            {{ $material->title }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-book mr-1"></i>
                                <span>{{ $material->subject->name ?? 'Tidak ada mata pelajaran' }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <span>Diterbitkan: {{ $material->publish_date->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="far fa-clock mr-1"></i>
                                <span>Diperbarui: {{ $material->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action buttons -->
                <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
                    <a href="{{ route('guru.materials.edit', $material) }}" 
                       class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-1.5"></i> Edit
                    </a>
                    
                    <a href="{{ route('guru.materials.download', $material) }}" 
                       class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-download mr-1.5"></i> Unduh
                    </a>
                    
                    <form action="{{ route('guru.materials.toggle-active', $material) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="{{ $material->is_active ? 'bg-gray-600' : 'bg-green-600' }} inline-flex items-center px-3 py-2 text-white text-sm font-medium rounded-md shadow-sm hover:{{ $material->is_active ? 'bg-gray-700' : 'bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:{{ $material->is_active ? 'ring-gray-500' : 'ring-green-500' }}">
                            <i class="fas {{ $material->is_active ? 'fa-toggle-off mr-1.5' : 'fa-toggle-on mr-1.5' }}"></i> 
                            {{ $material->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('guru.materials.destroy', $material) }}" method="POST" class="inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash-alt mr-1.5"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Material Content -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left column - Description -->
            <div class="md:col-span-2">
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i> Deskripsi Materi
                    </h2>
                    <div class="text-gray-700 prose max-w-none">
                        {!! nl2br(e($material->description)) !!}
                    </div>
                </div>
            </div>
            
            <!-- Right column - File and Classroom info -->
            <div class="md:col-span-1">
                <!-- File preview card -->
                <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file mr-2 {{ $material->getFileColorAttribute() }}"></i> File Materi
                    </h2>
                    
                    <div class="flex flex-col items-center justify-center p-5 border-2 border-dashed rounded-lg border-gray-300 bg-gray-50">
                        <div class="{{ $material->getFileColorAttribute() }} bg-opacity-20 p-5 rounded-full mb-3">
                            <i class="fas {{ $material->getFileIconAttribute() }} {{ $material->getFileColorAttribute() }} text-3xl"></i>
                        </div>
                        <p class="text-sm text-gray-600 mb-1">{{ basename($material->file_path) }}</p>
                        <p class="text-xs text-gray-500 mb-3">{{ $material->getFileType() }}</p>
                        <a href="{{ route('guru.materials.download', $material) }}" 
                           class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-download mr-1.5"></i> Unduh File
                        </a>
                    </div>
                </div>
                
                <!-- Classroom info card -->
                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-users mr-2 text-indigo-600"></i> Kelas yang Menerima
                    </h2>
                    
                    @if($material->classrooms->isEmpty())
                        <div class="text-gray-500 text-center py-4">
                            Tidak ada kelas yang ditambahkan
                        </div>
                    @else
                        <ul class="space-y-2">
                            @foreach($material->classrooms as $classroom)
                                <li class="flex items-center p-2 rounded-lg hover:bg-gray-50">
                                    <div class="flex-shrink-0 h-8 w-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $classroom->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $classroom->grade_level }} · {{ $classroom->academic_year }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Confirmation dialog for delete
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForm = document.querySelector('.delete-form');
        
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus materi ini? Tindakan ini tidak dapat dibatalkan.')) {
                    this.submit();
                }
            });
        }
    });
</script>
@endpush