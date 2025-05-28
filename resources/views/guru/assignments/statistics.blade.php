@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Statistik Tugas: {{ $assignment->title }}</h1>
        <div>
            <a href="{{ route('guru.assignments.statistics.export', $assignment) }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 mr-2">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </a>
            <a href="{{ route('guru.assignments.show', $assignment) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold mb-4">Informasi Tugas</h2>
                <div class="space-y-2">
                    <p><span class="font-medium">Mata Pelajaran:</span> {{ $assignment->subject->name }}</p>
                    <p><span class="font-medium">Kelas:</span> 
                        @foreach($assignment->classes as $class)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">{{ $class->name }}</span>
                        @endforeach
                    </p>
                    <p><span class="font-medium">Deadline:</span> {{ $assignment->deadline->format('d M Y, H:i') }}</p>
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
                <h2 class="text-lg font-semibold mb-4">Ringkasan Pengumpulan</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-blue-600">Total Siswa</p>
                        <p class="text-2xl font-bold text-blue-800">{{ $studentsCount }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-green-600">Pengumpulan</p>
                        <p class="text-2xl font-bold text-green-800">{{ $submissionsCount }}</p>
                        <p class="text-sm text-green-600">{{ $submissionRate }}% dari total siswa</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <p class="text-sm text-yellow-600">Belum Dinilai</p>
                        <p class="text-2xl font-bold text-yellow-800">{{ $pendingCount }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-sm text-purple-600">Sudah Dinilai</p>
                        <p class="text-2xl font-bold text-purple-800">{{ $gradedCount }}</p>
                        <p class="text-sm text-purple-600">{{ $gradeRate }}% dari pengumpulan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Distribusi Nilai</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-600 rounded-sm mr-2"></div>
                        <span>A (80-100)</span>
                    </div>
                    <span class="font-medium">{{ $scoreDistribution['a'] }} siswa</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-600 rounded-sm mr-2"></div>
                        <span>B (70-79)</span>
                    </div>
                    <span class="font-medium">{{ $scoreDistribution['b'] }} siswa</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-600 rounded-sm mr-2"></div>
                        <span>C (60-69)</span>
                    </div>
                    <span class="font-medium">{{ $scoreDistribution['c'] }} siswa</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-orange-600 rounded-sm mr-2"></div>
                        <span>D (50-59)</span>
                    </div>
                    <span class="font-medium">{{ $scoreDistribution['d'] }} siswa</span>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-600 rounded-sm mr-2"></div>
                        <span>E (0-49)</span>
                    </div>
                    <span class="font-medium">{{ $scoreDistribution['e'] }} siswa</span>
                </div>
            </div>
            <div>
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-sm text-gray-600 mb-1">Nilai Rata-Rata</p>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($averageScore ?? 0, 1) }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min(100, $averageScore ?? 0) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(count($submissionDates) > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Timeline Pengumpulan</h2>
        <div class="h-64">
            <canvas id="submissionChart"></canvas>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">Tindakan</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('guru.submissions.index', $assignment) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                <i class="fas fa-list-ul mr-2"></i>Lihat Semua Pengumpulan
            </a>
            <a href="{{ route('guru.assignments.edit', $assignment) }}" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                <i class="fas fa-edit mr-2"></i>Edit Tugas
            </a>
            <a href="#" onclick="exportToExcel()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fas fa-file-excel mr-2"></i>Export Data (Excel)
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Timeline Chart
    @if(count($submissionDates) > 0)
    const ctx = document.getElementById('submissionChart').getContext('2d');
    
    const dates = @json(array_keys($submissionDates));
    const counts = @json(array_values($submissionDates));
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Jumlah Pengumpulan',
                data: counts,
                backgroundColor: 'rgba(79, 70, 229, 0.6)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
    @endif
});

// Function to export data to Excel
function exportToExcel() {
    window.location.href = "{{ route('guru.assignments.statistics.export', $assignment) }}";
}
</script>
@endpush
