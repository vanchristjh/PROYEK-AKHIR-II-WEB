@extends('layouts.dashboard')

@section('title', 'Hasil Kuis')

@section('header', 'Hasil Kuis')

@section('content')
    <!-- Header Banner -->
    <div class="relative p-6 mb-6 overflow-hidden text-white shadow-xl bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl">
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fas fa-chart-bar text-9xl"></i>
        </div>
        <div class="absolute w-64 h-64 rounded-full -left-20 -bottom-20 bg-white/10 blur-2xl"></div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-3 py-1 mr-2 text-xs font-semibold text-blue-800 bg-blue-100 border border-blue-200 rounded-full">
                            <i class="mr-1 fas fa-book"></i> {{ $quiz->subject->name ?? 'Tidak ada mata pelajaran' }}
                        </span>
                        @if($quiz->is_active)
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 border border-green-200 rounded-full">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></div>
                                Aktif
                            </span>
                        @endif
                    </div>
                    <h2 class="text-2xl font-bold">Hasil Kuis: {{ $quiz->title }}</h2>
                    <p class="mt-1 text-blue-100">
                        <i class="mr-1 fas fa-calendar-alt"></i> 
                        {{ $quiz->start_time ? \Carbon\Carbon::parse($quiz->start_time)->format('d M Y, H:i') : 'Tidak diatur' }} - 
                        {{ $quiz->end_time ? \Carbon\Carbon::parse($quiz->end_time)->format('d M Y, H:i') : 'Tidak diatur' }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('guru.quizzes.show', $quiz->id) }}" class="flex items-center px-4 py-2 transition-colors rounded-lg shadow-sm bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i class="mr-2 fas fa-eye"></i> Detail Kuis
                    </a>
                    <a href="{{ route('guru.quizzes.index') }}" class="flex items-center px-4 py-2 transition-colors rounded-lg shadow-sm bg-white/10 hover:bg-white/20 backdrop-blur-sm">
                        <i class="mr-2 fas fa-list"></i> Daftar Kuis
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Attempts -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-blue-600 bg-blue-100 rounded-lg">
                    <i class="text-xl fas fa-users"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Mengerjakan</div>
                    <div class="text-xl font-bold text-gray-800">{{ $attempts->count() }}</div>
                    <div class="text-xs text-gray-500">dari {{ $totalStudents ?? 0 }} siswa</div>
                </div>
            </div>
        </div>
        
        <!-- Average Score -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-green-600 bg-green-100 rounded-lg">
                    <i class="text-xl fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Nilai Rata-rata</div>
                    <div class="text-xl font-bold text-gray-800">{{ number_format($averageScore ?? 0, 1) }}</div>
                    <div class="text-xs text-gray-500">Nilai kelulusan: {{ $quiz->passing_grade ?? 70 }}</div>
                </div>
            </div>
        </div>
        
        <!-- Pass Rate -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 text-green-600 bg-green-100 rounded-lg">
                    <i class="text-xl fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Tingkat Kelulusan</div>
                    <div class="text-xl font-bold text-gray-800">{{ $passRate ?? 0 }}%</div>
                    <div class="text-xs text-gray-500">{{ $passedCount ?? 0 }} siswa lulus</div>
                </div>
            </div>
        </div>
        
        <!-- Highest Score -->
        <div class="p-4 bg-white border border-gray-100 shadow-sm rounded-xl">
            <div class="flex items-center">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mr-3 rounded-lg bg-amber-100 text-amber-600">
                    <i class="text-xl fas fa-trophy"></i>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Nilai Tertinggi</div>
                    <div class="text-xl font-bold text-gray-800">{{ $highestScore ?? 0 }}</div>
                    <div class="text-xs text-gray-500">{{ $highestScoreStudent ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Distribution -->
    <div class="mb-6 overflow-hidden bg-white border shadow-sm rounded-xl border-gray-100/60">
        <div class="p-6 border-b border-gray-100">
            <h3 class="flex items-center text-lg font-medium text-gray-800">
                <div class="p-2 mr-3 bg-indigo-100 rounded-lg">
                    <i class="text-indigo-600 fas fa-chart-bar"></i>
                </div>
                <span>Distribusi Nilai</span>
            </h3>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <div class="p-6 rounded-lg bg-gray-50">
                    <canvas id="scoreDistributionChart" height="300"></canvas>
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-4 mb-6 text-center md:grid-cols-5">
                <div class="p-3 border border-red-100 rounded-lg bg-red-50">
                    <div class="mb-1 text-xs text-gray-500">0-20</div>
                    <div class="text-lg font-bold text-red-600">{{ $scoreDistribution[0] ?? 0 }}</div>
                    <div class="text-xs text-red-600">Sangat Kurang</div>
                </div>
                <div class="p-3 border border-orange-100 rounded-lg bg-orange-50">
                    <div class="mb-1 text-xs text-gray-500">21-40</div>
                    <div class="text-lg font-bold text-orange-600">{{ $scoreDistribution[1] ?? 0 }}</div>
                    <div class="text-xs text-orange-600">Kurang</div>
                </div>
                <div class="p-3 border border-yellow-100 rounded-lg bg-yellow-50">
                    <div class="mb-1 text-xs text-gray-500">41-60</div>
                    <div class="text-lg font-bold text-yellow-600">{{ $scoreDistribution[2] ?? 0 }}</div>
                    <div class="text-xs text-yellow-600">Cukup</div>
                </div>
                <div class="p-3 border border-blue-100 rounded-lg bg-blue-50">
                    <div class="mb-1 text-xs text-gray-500">61-80</div>
                    <div class="text-lg font-bold text-blue-600">{{ $scoreDistribution[3] ?? 0 }}</div>
                    <div class="text-xs text-blue-600">Baik</div>
                </div>
                <div class="p-3 border border-green-100 rounded-lg bg-green-50">
                    <div class="mb-1 text-xs text-gray-500">81-100</div>
                    <div class="text-lg font-bold text-green-600">{{ $scoreDistribution[4] ?? 0 }}</div>
                    <div class="text-xs text-green-600">Sangat Baik</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="p-4 mb-6 bg-white border border-gray-100 shadow-sm rounded-xl">
        <form action="{{ route('guru.quizzes.results', $quiz->id) }}" method="GET" class="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <div class="flex-1">
                <div class="relative rounded-md">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="text-gray-400 fas fa-search"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." 
                           class="block w-full pl-10 border-gray-300 rounded-lg sm:text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <select name="status" class="border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Semua Status</option>
                    <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Lulus</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Tidak Lulus</option>
                </select>
                <button type="submit" class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="mr-2 fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="mb-6 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Siswa
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Kelas
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Nilai
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Waktu Pengerjaan
                        </th>
                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attempts as $attempt)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 text-gray-500 bg-gray-200 rounded-full">
                                        {{ strtoupper(substr($attempt->student->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $attempt->student->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $attempt->student->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $attempt->student->classroom->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium {{ $attempt->score >= ($quiz->passing_grade ?? 70) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $attempt->score }}
                                </div>
                                <div class="text-xs text-gray-500">dari 100</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attempt->score >= ($quiz->passing_grade ?? 70))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Lulus
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tidak Lulus
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $attempt->created_at ? $attempt->created_at->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $attempt->created_at ? $attempt->created_at->format('H:i') : '-' }}</div>
                                <div class="text-xs text-gray-500">Durasi: {{ $attempt->duration_minutes ?? '-' }} menit</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                <a href="{{ route('guru.quiz-attempts.show', $attempt->id) }}" class="px-2 py-1 text-blue-600 rounded hover:text-blue-900 bg-blue-50 hover:bg-blue-100">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex items-center justify-center flex-shrink-0 w-16 h-16 mb-4 text-gray-400 bg-gray-100 rounded-full">
                                        <i class="text-2xl fas fa-users-slash"></i>
                                    </div>
                                    <p class="font-medium text-gray-500">Belum ada yang mengerjakan kuis</p>
                                    <p class="mt-1 text-sm text-gray-400">Siswa belum mengerjakan kuis ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Buttons -->    <div class="flex justify-end mb-8">
        <a href="{{ route('guru.quizzes.export', ['quiz' => $quiz->id, 'format' => 'pdf']) }}" class="inline-flex items-center px-4 py-2 ml-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
            <i class="mr-2 fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('guru.quizzes.export', ['quiz' => $quiz->id, 'format' => 'excel']) }}" class="inline-flex items-center px-4 py-2 ml-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
            <i class="mr-2 fas fa-file-excel"></i> Export Excel
        </a>
    </div>

    <!-- Pagination -->
    @if(isset($attempts) && $attempts->hasPages())
        <div class="px-4">
            {{ $attempts->links() }}
        </div>
    @endif

@endsection

@push('styles')
<style>
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Score Distribution Chart
        const ctx = document.getElementById('scoreDistributionChart').getContext('2d');
        
        const labels = ['0-20', '21-40', '41-60', '61-80', '81-100'];
        const data = [
            {{ $scoreDistribution[0] ?? 0 }},
            {{ $scoreDistribution[1] ?? 0 }},
            {{ $scoreDistribution[2] ?? 0 }},
            {{ $scoreDistribution[3] ?? 0 }},
            {{ $scoreDistribution[4] ?? 0 }}
        ];
        
        const backgroundColors = [
            'rgba(239, 68, 68, 0.6)',
            'rgba(249, 115, 22, 0.6)',
            'rgba(245, 158, 11, 0.6)',
            'rgba(59, 130, 246, 0.6)',
            'rgba(16, 185, 129, 0.6)'
        ];
        
        const borderColors = [
            'rgb(239, 68, 68)',
            'rgb(249, 115, 22)',
            'rgb(245, 158, 11)',
            'rgb(59, 130, 246)',
            'rgb(16, 185, 129)'
        ];
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' siswa';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
