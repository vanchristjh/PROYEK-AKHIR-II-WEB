@extends('layouts.app')

@section('styles')
<link href="{{ asset('css/statistics-fix.css') }}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/girsip-chart.js') }}"></script>
<style>
    /* Additional inline styles to force display */
    body, html {
        display: block !important;
        visibility: visible !important;
    }
    .container {
        display: block !important;
        visibility: visible !important;
    }
    .bg-white {
        background-color: white !important;
    }
    #chartError {
        display: none !important;
    }
    .debug-info {
        margin-top: 10px;
        padding: 10px;
        background-color: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 4px;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
<div class="container px-4 py-6 mx-auto">    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Statistik Tugas: {{ $assignment->title }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('guru.assignments.statistics.export', $assignment) }}" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                <i class="mr-2 fas fa-file-excel"></i>Export Excel
            </a>
            <a href="{{ route('guru.assignments.statistics.export-pdf', $assignment) }}" class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                <i class="mr-2 fas fa-file-pdf"></i>Export PDF
            </a>
            <a href="{{ route('guru.assignments.show', $assignment) }}" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </div>

    <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <h2 class="mb-4 text-lg font-semibold">Informasi Tugas</h2>                <div class="space-y-2">
                    <p><span class="font-medium">Mata Pelajaran:</span> 
                        @if($assignment->subject)
                            {{ $assignment->subject->name }}
                        @else
                            Mata pelajaran tidak tersedia
                        @endif
                    </p>
                    <p><span class="font-medium">Kelas:</span> 
                        @if($assignment->classes && count($assignment->classes) > 0)
                            @foreach($assignment->classes as $class)
                                <span class="inline-block px-2 py-1 mr-1 text-xs text-blue-800 bg-blue-100 rounded">{{ $class->name }}</span>
                            @endforeach
                        @else
                            <span class="inline-block px-2 py-1 mr-1 text-xs text-gray-800 bg-gray-100 rounded">Tidak ada kelas yang dipilih</span>
                        @endif
                    </p>
                    <p><span class="font-medium">Deadline:</span> 
                        @if($assignment->deadline)
                            {{ $assignment->deadline->format('d M Y, H:i') }}
                        @else
                            Tidak ada tenggat waktu
                        @endif
                    </p>
                    <p><span class="font-medium">Status:</span> 
                        @if($assignment->isExpired())
                            <span class="text-red-600">Berakhir</span>
                        @else
                            <span class="text-green-600">Aktif</span>
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <h2 class="mb-4 text-lg font-semibold">Ringkasan Pengumpulan</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 transition-transform duration-200 rounded-lg bg-blue-50 hover:scale-105 cursor-help" title="Total siswa yang terdaftar di kelas">
                        <p class="text-sm font-medium text-blue-600">Total Siswa</p>
                        <div class="mt-2">
                            <p class="text-2xl font-bold text-blue-800 animate-in">{{ $studentsCount }}</p>
                            <div class="w-full bg-blue-200 rounded-full h-1.5 mt-2">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 transition-transform duration-200 rounded-lg bg-green-50 hover:scale-105 cursor-help" title="Jumlah tugas yang sudah dikumpulkan">
                        <p class="text-sm font-medium text-green-600">Pengumpulan</p>
                        <div class="mt-2">
                            <p class="text-2xl font-bold text-green-800">{{ $submissionsCount }}</p>
                            <div class="w-full bg-green-200 rounded-full h-1.5 mt-2">
                                <div class="bg-green-600 h-1.5 rounded-full" style="width: {{ $submissionRate }}%"></div>
                            </div>
                            <p class="mt-1 text-sm text-green-600">{{ $submissionRate }}% dari total siswa</p>
                        </div>
                    </div>
                    <div class="p-4 transition-transform duration-200 rounded-lg bg-yellow-50 hover:scale-105 cursor-help" title="Tugas yang menunggu penilaian">
                        <p class="text-sm font-medium text-yellow-600">Belum Dinilai</p>
                        <div class="mt-2">
                            <p class="text-2xl font-bold text-yellow-800">{{ $pendingCount }}</p>
                            <div class="w-full bg-yellow-200 rounded-full h-1.5 mt-2">
                                <div class="bg-yellow-600 h-1.5 rounded-full" style="width: {{ $submissionsCount ? ($pendingCount / $submissionsCount) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 transition-transform duration-200 rounded-lg bg-purple-50 hover:scale-105 cursor-help" title="Tugas yang sudah dinilai">
                        <p class="text-sm font-medium text-purple-600">Sudah Dinilai</p>
                        <div class="mt-2">
                            <p class="text-2xl font-bold text-purple-800">{{ $gradedCount }}</p>
                            <div class="w-full bg-purple-200 rounded-full h-1.5 mt-2">
                                <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $gradeRate }}%"></div>
                            </div>
                            <p class="mt-1 text-sm text-purple-600">{{ $gradeRate }}% dari pengumpulan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
        <h2 class="mb-4 text-lg font-semibold">Distribusi Nilai</h2>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-4">
                <div class="flex items-center justify-between p-2 transition-colors rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="w-4 h-4 mr-2 bg-green-600 rounded-sm"></div>
                        <span class="font-medium">A (80-100)</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium text-green-600">{{ $scoreDistribution['a'] }}</span>
                        <span class="ml-1 text-gray-500">siswa</span>
                        <div class="w-20 h-2 ml-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-green-600 rounded-full" style="width: {{ ($scoreDistribution['a'] / max(1, array_sum($scoreDistribution))) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-2 transition-colors rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="w-4 h-4 mr-2 bg-blue-600 rounded-sm"></div>
                        <span class="font-medium">B (70-79)</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium text-blue-600">{{ $scoreDistribution['b'] }}</span>
                        <span class="ml-1 text-gray-500">siswa</span>
                        <div class="w-20 h-2 ml-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-blue-600 rounded-full" style="width: {{ ($scoreDistribution['b'] / max(1, array_sum($scoreDistribution))) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-2 transition-colors rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="w-4 h-4 mr-2 bg-yellow-600 rounded-sm"></div>
                        <span class="font-medium">C (60-69)</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium text-yellow-600">{{ $scoreDistribution['c'] }}</span>
                        <span class="ml-1 text-gray-500">siswa</span>
                        <div class="w-20 h-2 ml-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-yellow-600 rounded-full" style="width: {{ ($scoreDistribution['c'] / max(1, array_sum($scoreDistribution))) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-2 transition-colors rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="w-4 h-4 mr-2 bg-orange-600 rounded-sm"></div>
                        <span class="font-medium">D (50-59)</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium text-orange-600">{{ $scoreDistribution['d'] }}</span>
                        <span class="ml-1 text-gray-500">siswa</span>
                        <div class="w-20 h-2 ml-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-orange-600 rounded-full" style="width: {{ ($scoreDistribution['d'] / max(1, array_sum($scoreDistribution))) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-2 transition-colors rounded-lg hover:bg-gray-50">
                    <div class="flex items-center">
                        <div class="w-4 h-4 mr-2 bg-red-600 rounded-sm"></div>
                        <span class="font-medium">E (0-49)</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium text-red-600">{{ $scoreDistribution['e'] }}</span>
                        <span class="ml-1 text-gray-500">siswa</span>
                        <div class="w-20 h-2 ml-2 bg-gray-200 rounded-full">
                            <div class="h-2 bg-red-600 rounded-full" style="width: {{ ($scoreDistribution['e'] / max(1, array_sum($scoreDistribution))) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="p-6 text-center rounded-lg bg-gray-50">
                    <p class="mb-2 text-sm font-medium text-gray-600">Nilai Rata-Rata</p>
                    <p class="mb-4 text-4xl font-bold text-gray-800">{{ number_format($averageScore ?? 0, 1) }}</p>
                    <div class="w-full h-3 bg-gray-200 rounded-full">
                        <div class="h-3 transition-all duration-1000 bg-blue-600 rounded-full" style="width: {{ min(100, $averageScore ?? 0) }}%"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">dari nilai maksimal 100</p>
                </div>
            </div>
        </div>
    </div>    @if(count($submissionDates) > 0)    <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
        <h2 class="mb-4 text-lg font-semibold">Timeline Pengumpulan</h2>
        <div>
            <p class="mb-3 text-sm text-gray-500">
                Data pengumpulan dari tanggal: 
                @if(count($submissionDates) > 0)
                    {{ implode(', ', array_keys($submissionDates)) }}
                @else
                    Belum ada data pengumpulan
                @endif
            </p>
        </div>
        
        <div class="debug-info">
            <p><strong>Debug:</strong> Jumlah data pengumpulan: {{ count($submissionDates) }}</p>
            <p><strong>Keys:</strong> {{ implode(', ', array_keys($submissionDates)) }}</p>
            <p><strong>Values:</strong> {{ implode(', ', array_values($submissionDates)) }}</p>
        </div>
        
        <div class="relative h-64" style="border: 1px solid #eee; border-radius: 5px;">
            <div id="chartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80">
                <div class="w-8 h-8 border-4 border-indigo-600 rounded-full animate-spin border-t-transparent"></div>
                <span class="ml-3">Memuat grafik...</span>
            </div>
            <div id="chartError" class="absolute inset-0 items-center justify-center bg-white bg-opacity-80" style="display: none;">
                <div class="text-center text-red-600">
                    <i class="mb-2 text-2xl fas fa-exclamation-circle"></i>
                    <p>Gagal memuat grafik. Silakan muat ulang halaman.</p>
                    <button onclick="window.location.reload()" class="px-4 py-2 mt-3 text-white bg-blue-500 rounded hover:bg-blue-600">
                        <i class="mr-1 fas fa-sync-alt"></i> Muat Ulang
                    </button>
                </div>
            </div>
            <canvas id="submissionChart"></canvas>
        </div>
    </div>
    @endif    <div class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="mb-4 text-lg font-semibold">Tindakan</h2>
        <div class="flex flex-wrap gap-4">            <a href="{{ route('guru.assignments.submissions.index', $assignment) }}" class="px-4 py-2 text-white bg-indigo-600 rounded hover:bg-indigo-700">
                <i class="mr-2 fas fa-list-ul"></i>Lihat Semua Pengumpulan
            </a>
            <a href="{{ route('guru.assignments.edit', $assignment) }}" class="px-4 py-2 text-white bg-yellow-600 rounded hover:bg-yellow-700">
                <i class="mr-2 fas fa-edit"></i>Edit Tugas
            </a>
            <a href="#" onclick="exportToExcel()" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                <i class="mr-2 fas fa-file-excel"></i>Export Data (Excel)
            </a>
            <a href="{{ route('guru.assignments.statistics.alt', $assignment) }}" class="px-4 py-2 text-white bg-purple-600 rounded hover:bg-purple-700">
                <i class="mr-2 fas fa-window-restore"></i>Tampilan Alternatif
            </a>
        </div>
    </div>
      <!-- Debug Footer -->
    <div class="p-4 mt-8 text-xs text-gray-500 border border-gray-300 border-dashed rounded-lg bg-gray-50">
        <p>Mengalami masalah menampilkan statistik? Coba opsi berikut:</p>
        <ul class="pl-5 mt-2 list-disc">
            <li>Gunakan <a href="{{ route('guru.assignments.statistics.alt', $assignment) }}" class="text-blue-600 hover:underline">tampilan alternatif</a></li>
            <li>Lihat <a href="{{ asset('fallback-statistics.html') }}" target="_blank" class="text-blue-600 hover:underline">halaman darurat</a> (generic fallback)</li>
            <li>Lihat <a href="{{ asset('debug-statistics.html') }}" target="_blank" class="text-blue-600 hover:underline">halaman debug</a> untuk tes</li>
            <li>Perbarui halaman dengan menekan tombol F5</li>
            <li>Coba buka di browser lain (Chrome/Firefox/Edge)</li>
            <li>Hubungi administrator sistem jika masalah berlanjut</li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/statistics-helper.js') }}"></script>
<script>
// Immediate console logging to check if script is loaded
console.log("Statistics page scripts starting to load...");

// Global error handler for debugging
window.addEventListener('error', function(e) {
    console.error('Global error caught:', e.message, e.filename, e.lineno);
    document.getElementById('chartError').style.display = 'flex';
});

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM Content loaded for statistics page");
    
    // Check if our custom chart library is loaded
    if (typeof window.girsipChart === 'undefined') {
        console.error("GirsipChart library not found");
        // Load it dynamically
        const scriptTag = document.createElement('script');
        scriptTag.src = "{{ asset('js/girsip-chart.js') }}";
        document.head.appendChild(scriptTag);
        
        // Wait for it to load
        scriptTag.onload = function() {
            console.log("GirsipChart library loaded dynamically");
            initializeCharts();
        };
    } else {
        console.log("GirsipChart library already loaded");
        initializeCharts();
    }
    
    function initializeCharts() {
        // Timeline Chart
        @if(count($submissionDates) > 0)
        console.log("Initializing chart with submission dates");
        const chartLoading = document.getElementById('chartLoading');
        const chartError = document.getElementById('chartError');
        
        if (chartLoading) chartLoading.style.display = 'flex';
        if (chartError) chartError.style.display = 'none';
          try {
            console.log("Initializing chart with GirsipChart");
            const dates = @json(array_keys($submissionDates));
            const counts = @json(array_values($submissionDates));
            
            console.log("Chart data:", { dates, counts });
            
            // Format dates for display
            const formattedDates = dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', { 
                    day: 'numeric', 
                    month: 'short',
                    year: 'numeric'
                });
            });
            
            // Use our custom chart library
            window.girsipChart.waitForChart(function() {
                window.girsipChart.createBarChart('submissionChart', {
                    labels: formattedDates,
                    datasets: [{
                        label: 'Jumlah Pengumpulan',
                        data: counts,
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        maxBarThickness: 50
                    }]
                }, {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    animation: {
                        onComplete: () => {
                            console.log("Chart rendering complete");
                            if (chartLoading) chartLoading.style.display = 'none';
                        }
                    }
                });
            });
        } catch (error) {
            console.error('Error initializing chart:', error);
            if (chartLoading) chartLoading.style.display = 'none';
            if (chartError) chartError.style.display = 'flex';
        }
        @else
        console.log("No submission dates to display in chart");
        @endif
    }
});

// Function to export data to Excel with loading state
function exportToExcel() {
    console.log("Exporting to Excel");
    const button = event.target.closest('a');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="mr-2 fas fa-spinner fa-spin"></i>Mengekspor...';
    
    try {
        window.location.href = "{{ route('guru.assignments.statistics.export', $assignment) }}";
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    } catch (error) {
        console.error('Error exporting:', error);
        button.innerHTML = originalText;
        alert('Gagal mengekspor data. Silakan coba lagi.');
    }
}
</script>
@endpush
