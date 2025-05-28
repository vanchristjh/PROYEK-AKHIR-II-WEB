@extends('layouts.dashboard')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Materi Pelajaran')

@section('header', 'Materi Pelajaran')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-book-open text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Materi Pelajaran</h2>
                <p class="text-green-100">Kelola semua materi pembelajaran Anda di sini</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('guru.materials.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-green-600 rounded-lg font-medium shadow-sm hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Materi
                </a>
            </div>
        </div>
    </div>

    <!-- Filter and search section -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 mb-6 p-4">
        <form action="{{ route('guru.materials.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow">
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $searchTerm ?? '' }}" placeholder="Cari materi..." class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                </div>
            </div>
            <div class="w-full md:w-1/4">
                <select name="subject" id="subject" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @if(isset($selectedSubject) && $selectedSubject == $subject->id) selected @endif>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                @if(request()->has('search') || request()->has('subject'))
                    <a href="{{ route('guru.materials.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-2"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Materials grid display -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100/50 mb-6">
        <div class="p-6">            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('info') }}</p>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('warning') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            @if($materials->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="bg-gray-100 rounded-full p-6 mb-4">
                        <i class="fas fa-book-open text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-2xl font-medium text-gray-600 mb-2">Belum ada materi</h3>
                    <p class="text-gray-500 mb-6">Anda belum membuat materi pelajaran apapun</p>
                    <a href="{{ route('guru.materials.create') }}" class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-lg font-medium shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i> Buat Materi
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($materials as $material)
                        <div class="border rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col bg-white {{ $material->is_active ? '' : 'opacity-60' }}">
                            <div class="bg-gray-50 px-4 py-3 flex justify-between items-center border-b">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-md {{ $material->getFileColorAttribute() }} bg-opacity-10 mr-3">
                                        <i class="fas {{ $material->getFileIconAttribute() }} {{ $material->getFileColorAttribute() }}"></i>
                                    </span>
                                    <span class="text-xs font-medium uppercase tracking-wider {{ $material->getFileColorAttribute() }} rounded-md px-2.5 py-0.5">{{ $material->getFileTypeShort() }}</span>
                                </div>
                                <div>
                                    @if($material->isNew())
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-md">Baru</span>
                                    @endif
                                    @if(!$material->is_active)
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-md">Nonaktif</span>
                                    @endif
                                </div>
                            </div>
                            <div class="p-4 flex-1">
                                <h3 class="font-bold text-lg mb-2 text-gray-800">{{ $material->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($material->description, 100) }}</p>
                                <div class="flex items-center text-xs text-gray-500 mt-auto">
                                    <div class="flex items-center">
                                        <i class="fas fa-book mr-1"></i>
                                        <span>{{ $material->subject->name ?? 'Tidak ada mata pelajaran' }}</span>
                                    </div>
                                    <span class="mx-2">•</span>
                                    <div class="flex items-center">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        <span>{{ $material->publish_date->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 border-t">
                                <div class="flex justify-between items-center">
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-users mr-1"></i>
                                        <span>{{ $material->classrooms->count() }} Kelas</span>
                                    </div>
                                    <div class="flex space-x-1">
                                        <a href="{{ route('guru.materials.show', $material) }}" class="text-blue-600 hover:text-blue-800 p-2 rounded-md hover:bg-blue-50" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('guru.materials.edit', $material) }}" class="text-indigo-600 hover:text-indigo-800 p-2 rounded-md hover:bg-indigo-50" title="Edit Materi">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('guru.materials.download', $material) }}" class="text-green-600 hover:text-green-800 p-2 rounded-md hover:bg-green-50" title="Unduh Materi">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <!-- Toggle active status button -->
                                        <form action="{{ route('guru.materials.toggle-active', $material) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="{{ $material->is_active ? 'text-gray-500 hover:text-gray-700' : 'text-green-600 hover:text-green-800' }} p-2 rounded-md hover:bg-gray-50" 
                                                    title="{{ $material->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Materi">
                                                <i class="fas {{ $material->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        <!-- Delete button -->
                                        <form action="{{ route('guru.materials.destroy', $material) }}" method="POST" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-md hover:bg-red-50 delete-btn" title="Hapus Materi">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $materials->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Confirmation dialog for delete
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Apakah Anda yakin ingin menghapus materi ini? Tindakan ini tidak dapat dibatalkan.')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
