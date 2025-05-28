@extends('layouts.dashboard')

@section('title', 'Hasil Ujian')

@section('header', 'Hasil Ujian')

@section('navigation')
    @include('guru.partials.sidebar')
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('guru.exams.show', $exam->id) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Ujian
        </a>
    </div>
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Hasil Ujian: {{ $exam->title }}
        </h2>
    </div>

    <!-- Alert Success -->
    @if (session('success'))
    <div class="mb-4 px-4 py-3 leading-normal text-green-700 bg-green-100 rounded-lg" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <!-- Alert Error -->
    @if (session('error'))
    <div class="mb-4 px-4 py-3 leading-normal text-red-700 bg-red-100 rounded-lg" role="alert">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid gap-6 mb-8">
        <!-- Statistik Ringkasan -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 mr-4">
                        <i class="fas fa-users text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Peserta</p>
                        <p class="text-lg font-semibold text-gray-700">{{ $attempts->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <i class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Nilai Tertinggi</p>
                        <p class="text-lg font-semibold text-gray-700">
                            @if($attempts->total() > 0)
                                {{ number_format($attempts->max('score'), 1) }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 mr-4">
                        <i class="fas fa-times-circle text-red-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Nilai Terendah</p>
                        <p class="text-lg font-semibold text-gray-700">
                            @if($attempts->total() > 0)
                                {{ number_format($attempts->min('score'), 1) }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 mr-4">
                        <i class="fas fa-chart-line text-purple-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rata-rata Nilai</p>
                        <p class="text-lg font-semibold text-gray-700">
                            @if($attempts->total() > 0)
                                {{ number_format($attempts->avg('score'), 1) }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Hasil Ujian Siswa -->
        <div class="w-full overflow-hidden rounded-lg shadow-md">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Waktu Mulai</th>
                            <th class="px-4 py-3">Waktu Selesai</th>
                            <th class="px-4 py-3">Durasi</th>
                            <th class="px-4 py-3">Nilai</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @forelse($attempts as $attempt)
                        <tr class="text-gray-700">
                            <td class="px-4 py-3">
                                {{ $attempt->student->name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $attempt->student->classroom ? $attempt->student->classroom->name : '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $attempt->start_time->format('d M Y, H:i:s') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $attempt->end_time ? $attempt->end_time->format('d M Y, H:i:s') : 'Belum selesai' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($attempt->end_time)
                                    @php
                                        $duration = $attempt->start_time->diffInMinutes($attempt->end_time);
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                    @endphp
                                    {{ $hours > 0 ? $hours . ' jam ' : '' }}{{ $minutes }} menit
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($attempt->is_submitted)
                                    {{ number_format($attempt->score, 1) }}
                                @else
                                    <span class="text-gray-500">Belum dikumpulkan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($attempt->is_submitted)
                                    @if($attempt->score >= $exam->passing_score)
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Lulus</span>
                                    @else
                                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Tidak Lulus</span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">Belum Selesai</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('guru.exams.view-attempt', ['exam' => $exam->id, 'attempt' => $attempt->id]) }}" 
                                   class="px-2 py-1 text-xs font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-center">
                                <p class="text-gray-500">Belum ada siswa yang mengerjakan ujian ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-4 py-3 border-t">
                {{ $attempts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
