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
        padding: 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .action-button:hover {
        transform: translateY(-2px);
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
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
<div class="container mx-auto px-4 py-6">
    <!-- Page Header dengan gradient yang lebih menarik -->
    <div class="gradient-header rounded-2xl shadow-lg p-8 mb-8 text-white relative overflow-hidden">
        <div class="absolute -right-10 -top-10 opacity-10 animate-float">
            <i class="fas fa-file-alt text-[200px]"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold mb-3">Kelola Ujian</h2>
                    <p class="text-blue-100 text-lg">Buat dan kelola ujian untuk semua kelas dan mata pelajaran</p>
                </div>
                <a href="{{ route('guru.exams.create') }}" 
                   class="px-6 py-3 bg-white text-indigo-700 rounded-xl shadow-md hover:bg-indigo-50 transition-all duration-300 flex items-center font-semibold">
                    <i class="fas fa-plus mr-2"></i> Buat Ujian Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Success with enhanced styling -->
    @if (session('success'))
    <div class="mb-4 px-4 py-3 leading-normal text-green-700 bg-green-100 rounded-lg border-l-4 border-green-500 shadow-sm animate-fade-in flex items-center" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Alert Error with enhanced styling -->
    @if (session('error'))
    <div class="mb-4 px-4 py-3 leading-normal text-red-700 bg-red-100 rounded-lg border-l-4 border-red-500 shadow-sm animate-fade-in flex items-center" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif    <!-- Statistics Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <!-- Total Ujian -->
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Ujian</div>
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
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center text-green-600 mr-3">
                    <i class="fas fa-play-circle text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Ujian Aktif</div>
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
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 mr-3">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Ujian Mendatang</div>
                    <div class="text-xl font-bold text-gray-800">{{ $upcomingExams }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                <i class="fas fa-list-alt text-indigo-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-800">Daftar Semua Ujian</h3>
        </div>
    </div>    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Ujian</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Berakhir</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">                    @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $exam->subject->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($exam->classrooms->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                @foreach ($exam->classrooms as $classroom)
                                    <span class="px-2.5 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                        {{ $classroom->name }}
                                    </span>
                                @endforeach
                                </div>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                    Belum Ada Kelas
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($exam->exam_type === 'uts')
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">UTS</span>
                            @elseif ($exam->exam_type === 'uas')
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full">UAS</span>
                            @elseif ($exam->exam_type === 'daily')
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Harian</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <i class="far fa-calendar-alt mr-1 text-blue-500"></i>
                                {{ $exam->start_time->format('d M Y, H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <i class="far fa-calendar-times mr-1 text-red-500"></i>
                                {{ $exam->end_time->format('d M Y, H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($exam->is_active && $exam->start_time <= now() && $exam->end_time >= now())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                    <span class="inline-block w-2 h-2 rounded-full bg-green-600 mr-1 animate-pulse"></span>
                                    Aktif
                                </span>
                            @elseif($exam->is_active && $exam->start_time > now())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Dijadwalkan
                                </span>
                            @elseif($exam->is_active && $exam->end_time < now())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-flag-checkered mr-1"></i>
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-ban mr-1"></i>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($exam->classrooms->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                @foreach ($exam->classrooms as $classroom)
                                    <span class="px-2.5 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                        {{ $classroom->name }}
                                    </span>
                                @endforeach
                                </div>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                    Belum Ada Kelas
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($exam->exam_type === 'uts')
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">UTS</span>
                            @elseif ($exam->exam_type === 'uas')
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full">UAS</span>
                            @elseif ($exam->exam_type === 'daily')
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Harian</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <i class="far fa-calendar-alt mr-1 text-blue-500"></i>
                                {{ $exam->start_time->format('d M Y, H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <i class="far fa-calendar-times mr-1 text-red-500"></i>
                                {{ $exam->end_time->format('d M Y, H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($exam->is_active && $exam->start_time <= now() && $exam->end_time >= now())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                    <span class="inline-block w-2 h-2 rounded-full bg-green-600 mr-1 animate-pulse"></span>
                                    Aktif
                                </span>
                            @elseif($exam->is_active && $exam->start_time > now())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Dijadwalkan
                                </span>
                            @elseif($exam->is_active && $exam->end_time < now())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-flag-checkered mr-1"></i>
                                    Selesai
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-ban mr-1"></i>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('guru.exams.questions', $exam->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 p-1.5 rounded-md transition-colors" title="Kelola Soal">
                                    <i class="fas fa-list"></i>
                                </a>
                                <a href="{{ route('guru.exams.results', $exam->id) }}" class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 p-1.5 rounded-md transition-colors" title="Lihat Hasil">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <a href="{{ route('guru.exams.edit', $exam->id) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 p-1.5 rounded-md transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('guru.exams.destroy', $exam->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ujian ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 p-1.5 rounded-md transition-colors" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <i class="fas fa-file-alt text-gray-400 text-xl"></i>
                                </div>
                                <h5 class="text-gray-500 font-medium">Belum Ada Ujian</h5>
                                <p class="text-gray-400 text-sm mt-1">Buat ujian baru dengan klik tombol "Buat Ujian Baru"</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">
            {{ $exams->links() }}        </div>
    </div>
</div>
@endsection
```
</copilot-edited-file>
