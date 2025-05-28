@extends('layouts.dashboard')

@section('title', 'Detail Mata Pelajaran')

@section('header', 'Detail Mata Pelajaran')

@section('navigation')
    <li>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-tachometer-alt text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-users text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Pengguna</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.subjects.index') }}" class="sidebar-item sidebar-active flex items-center rounded-lg px-4 py-3 text-white">
            <i class="fas fa-book text-lg w-6"></i>
            <span class="ml-3">Mata Pelajaran</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.classrooms.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-school text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Kelas</span>
        </a>
    </li>
    <li>
        <a href="{{ route('admin.announcements.index') }}" class="sidebar-item flex items-center rounded-lg px-4 py-3 text-indigo-100 hover:text-white transition-all duration-200">
            <i class="fas fa-bullhorn text-lg w-6 text-indigo-300"></i>
            <span class="ml-3">Pengumuman</span>
        </a>
    </li>
@endsection

@section('content')
    <!-- Header with animation -->
    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 mb-6 text-white relative overflow-hidden animate-fade-in">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-book text-9xl"></i>
        </div>
        <div class="relative">
            <h2 class="text-2xl font-bold mb-2">{{ $subject->code }} - {{ $subject->name }}</h2>
            <p class="text-amber-100 mb-4">Detail lengkap mata pelajaran</p>
            <div class="flex items-center">
                <a href="{{ route('admin.subjects.index') }}" class="flex items-center text-sm text-white hover:text-amber-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Kembali ke Daftar Mata Pelajaran</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Subject Info -->
        <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-amber-500 mr-2"></i>
                    Informasi Mata Pelajaran
                </h3>
                
                <div class="border-t border-gray-200 pt-4">
                    <dl class="divide-y divide-gray-200">
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500">Kode Mata Pelajaran</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $subject->code }}</dd>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500">Nama Mata Pelajaran</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $subject->name }}</dd>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                            <dd class="text-sm text-gray-900 col-span-2">
                                {{ $subject->description ?: 'Tidak ada deskripsi' }}
                            </dd>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500">Dibuat pada</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $subject->created_at->format('d F Y, H:i') }}</dd>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 py-3">
                            <dt class="text-sm font-medium text-gray-500">Terakhir diperbarui</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $subject->updated_at->format('d F Y, H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Teachers -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chalkboard-teacher text-amber-500 mr-2"></i>
                Guru Pengajar
            </h3>
            
            @if($subject->teachers->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($subject->teachers as $teacher)
                        <li class="py-3 flex items-center space-x-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $teacher->name }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $teacher->email }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-4">
                    <span class="text-gray-500 italic">Belum ada guru yang ditugaskan</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Action buttons -->
    <div class="mt-6 flex justify-end space-x-3">
        <a href="{{ route('admin.subjects.edit', $subject) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
            <i class="fas fa-edit mr-2"></i>
            Edit Mata Pelajaran
        </a>
        <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                <i class="fas fa-trash-alt mr-2"></i>
                Hapus Mata Pelajaran
            </button>
        </form>
    </div>
@endsection