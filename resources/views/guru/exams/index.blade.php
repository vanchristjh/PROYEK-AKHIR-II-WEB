@extends('layouts.dashboard')

@section('title', 'Kelola Ujian')
@section('header', 'Kelola Ujian')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@push('styles')
<style>
    .gradient-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.1);
    }
    
    .stat-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .icon-gradient {
        background: linear-gradient(120deg, #4f46e5, #7c3aed);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .table-container {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .table-header {
        background: linear-gradient(to right, #f8fafc, #f1f5f9);
    }
    
    .action-button {
        @apply text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 p-2 rounded-lg transition-all duration-200;
    }
    
    .status-badge {
        @apply px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center;
    }
    
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
</style>
@endpush

@section('content')
<div class="container px-4 py-6 mx-auto">
    <!-- Page Header -->
    <div class="relative p-6 mb-6 overflow-hidden text-white shadow-lg gradient-header rounded-xl">
        <div class="absolute top-0 right-0 opacity-10 animate-float">
            <i class="fas fa-file-alt text-[180px]"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="mb-2 text-2xl font-bold">Kelola Ujian</h2>
                    <p class="text-blue-100">Buat dan kelola ujian untuk semua kelas dan mata pelajaran</p>
                </div>
                <a href="{{ route('guru.exams.create') }}" 
                   class="flex items-center px-4 py-2 text-sm font-medium text-indigo-700 transition-all duration-300 bg-white rounded-lg shadow-md hover:bg-indigo-50">
                    <i class="mr-2 fas fa-plus"></i> Buat Ujian Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
    <div class="flex items-center px-4 py-3 mb-4 text-sm leading-normal text-green-700 bg-green-100 rounded-lg" role="alert">
        <i class="mr-2 fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center px-4 py-3 mb-4 text-sm leading-normal text-red-700 bg-red-100 rounded-lg" role="alert">
        <i class="mr-2 fas fa-exclamation-circle"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Total Ujian -->
        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-indigo-600 bg-indigo-100 rounded-lg">
                    <i class="text-xl fas fa-file-alt"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Ujian</div>
                    <div class="text-xl font-bold text-gray-800">{{ $exams->total() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Ujian Aktif -->
        @php
            $activeExams = $exams->where('is_active', true)->filter(function($exam) {
                return $exam->start_time <= now() && $exam->end_time >= now();
            })->count();
        @endphp
        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-green-600 bg-green-100 rounded-lg">
                    <i class="text-xl fas fa-play-circle"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Ujian Aktif</div>
                    <div class="text-xl font-bold text-gray-800">{{ $activeExams }}</div>
                </div>
            </div>
        </div>
        
        <!-- Ujian Mendatang -->
        @php
            $upcomingExams = $exams->where('is_active', true)->filter(function($exam) {
                return $exam->start_time > now();
            })->count();
        @endphp
        <div class="stat-card">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 rounded-lg bg-amber-100 text-amber-600">
                    <i class="text-xl fas fa-clock"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Ujian Mendatang</div>
                    <div class="text-xl font-bold text-gray-800">{{ $upcomingExams }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam List -->
    <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center">
                <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                    <i class="text-indigo-600 fas fa-list-alt"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-800">Daftar Semua Ujian</h3>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Judul</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Mata Pelajaran</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Tipe</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Mulai</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Selesai</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase">Status</th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium text-center text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($exams as $exam)
                    <tr class="transition-colors hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $exam->subject->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($exam->classrooms as $classroom)
                                    <span class="px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                        {{ $classroom->name }}
                                    </span>
                                @empty
                                    <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                        Belum Ada Kelas
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($exam->exam_type === 'uts')
                                <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">UTS</span>
                            @elseif ($exam->exam_type === 'uas')
                                <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full">UAS</span>
                            @elseif ($exam->exam_type === 'daily')
                                <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Harian</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <i class="mr-1 text-blue-500 far fa-calendar-alt"></i>
                                {{ $exam->start_time->format('d M Y, H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <i class="mr-1 text-red-500 far fa-calendar-times"></i>
                                {{ $exam->end_time->format('d M Y, H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($exam->is_active && $exam->start_time <= now() && $exam->end_time >= now())
                                <span class="text-green-800 bg-green-100 status-badge">
                                    <span class="w-2 h-2 mr-1 bg-green-600 rounded-full animate-pulse"></span>
                                    Aktif
                                </span>
                            @elseif($exam->is_active && $exam->start_time > now())
                                <span class="text-blue-800 bg-blue-100 status-badge">
                                    <i class="mr-1 fas fa-clock"></i>
                                    Dijadwalkan
                                </span>
                            @elseif($exam->is_active && $exam->end_time < now())
                                <span class="text-orange-800 bg-orange-100 status-badge">
                                    <i class="mr-1 fas fa-flag-checkered"></i>
                                    Selesai
                                </span>
                            @else
                                <span class="text-gray-800 bg-gray-100 status-badge">
                                    <i class="mr-1 fas fa-ban"></i>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-1">
                                <a href="{{ route('guru.exams.questions', $exam->id) }}" 
                                   class="text-blue-600 action-button hover:text-blue-900" 
                                   title="Kelola Soal">
                                    <i class="fas fa-list"></i>
                                </a>
                                <a href="{{ route('guru.exams.results', $exam->id) }}" 
                                   class="text-green-600 action-button hover:text-green-900" 
                                   title="Lihat Hasil">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <a href="{{ route('guru.exams.edit', $exam->id) }}" 
                                   class="text-yellow-600 action-button hover:text-yellow-900" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('guru.exams.destroy', $exam->id) }}" 
                                      method="POST" 
                                      class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 action-button hover:text-red-900" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full">
                                    <i class="text-xl text-gray-400 fas fa-file-alt"></i>
                                </div>
                                <h5 class="font-medium text-gray-500">Belum Ada Ujian</h5>
                                <p class="mt-1 text-sm text-gray-400">Buat ujian baru dengan klik tombol "Buat Ujian Baru"</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($exams->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $exams->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
