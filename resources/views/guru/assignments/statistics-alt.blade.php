@extends('layouts.statistics')

@section('styles')
<style>
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .chart-container {
        height: 300px;
        position: relative;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h1 class="h4 mb-3">Statistik Tugas: {{ $assignment->title }}</h1>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Informasi Tugas</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Mata Pelajaran</span>
                                <span class="badge bg-primary rounded-pill">{{ $assignment->subject->name }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Kelas</span>
                                <div>
                                    @foreach($assignment->classes as $class)
                                        <span class="badge bg-info rounded-pill me-1">{{ $class->name }}</span>
                                    @endforeach
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Deadline</span>
                                <span>{{ $assignment->deadline->format('d M Y, H:i') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Status</span>
                                @if($assignment->isExpired())
                                    <span class="badge bg-danger">Berakhir</span>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Ringkasan Pengumpulan</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="card stat-card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-primary mb-1">Total Siswa</h6>
                                        <h3 class="mb-0">{{ $studentsCount }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card stat-card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-success mb-1">Pengumpulan</h6>
                                        <h3 class="mb-0">{{ $submissionsCount }}</h3>
                                        <small class="text-muted">{{ $submissionRate }}% dari total siswa</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card stat-card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-warning mb-1">Belum Dinilai</h6>
                                        <h3 class="mb-0">{{ $pendingCount }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card stat-card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="text-info mb-1">Sudah Dinilai</h6>
                                        <h3 class="mb-0">{{ $gradedCount }}</h3>
                                        <small class="text-muted">{{ $gradeRate }}% dari pengumpulan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Distribusi Nilai</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success rounded me-2" style="width:15px; height:15px;"></div>
                    <span>A (80-100): {{ $scoreDistribution['a'] }} siswa</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded me-2" style="width:15px; height:15px;"></div>
                    <span>B (70-79): {{ $scoreDistribution['b'] }} siswa</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning rounded me-2" style="width:15px; height:15px;"></div>
                    <span>C (60-69): {{ $scoreDistribution['c'] }} siswa</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-orange rounded me-2" style="width:15px; height:15px; background-color: #f97316;"></div>
                    <span>D (50-59): {{ $scoreDistribution['d'] }} siswa</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-danger rounded me-2" style="width:15px; height:15px;"></div>
                    <span>E (0-49): {{ $scoreDistribution['e'] }} siswa</span>
                </div>
                
                <div class="text-center p-3 bg-light rounded">
                    <p class="mb-2 text-muted">Nilai Rata-Rata</p>
                    <h2 class="display-5">{{ number_format($averageScore ?? 0, 1) }}</h2>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ min(100, $averageScore ?? 0) }}%" 
                            aria-valuenow="{{ $averageScore ?? 0 }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        @if(count($submissionDates) > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Timeline Pengumpulan</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    Data pengumpulan dari tanggal: {{ implode(', ', array_keys($submissionDates)) }}
                </p>
                
                <div class="chart-container border rounded p-2">
                    <div id="chartLoading" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <canvas id="submissionChart"></canvas>
                </div>
            </div>
        </div>
        @endif
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Tindakan</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('guru.assignments.submissions.index', $assignment) }}" class="btn btn-primary">
                        <i class="fas fa-list-ul me-1"></i>Lihat Semua Pengumpulan
                    </a>
                    <a href="{{ route('guru.assignments.edit', $assignment) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Edit Tugas
                    </a>
                    <a href="{{ route('guru.assignments.statistics.export', $assignment) }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i>Export Data (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM Content loaded for alternative statistics page");
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error("Chart.js is not loaded in alternative page");
        return;
    }
    
    @if(count($submissionDates) > 0)
    try {
        const chartLoading = document.getElementById('chartLoading');
        const chartEl = document.getElementById('submissionChart');
        
        if (!chartEl) {
            console.error("Chart canvas element not found");
            return;
        }
        
        const ctx = chartEl.getContext('2d');
        const dates = @json(array_keys($submissionDates));
        const counts = @json(array_values($submissionDates));
        
        console.log("Alternative chart data:", { dates, counts });
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dates.map(date => {
                    const d = new Date(date);
                    return d.toLocaleDateString('id-ID', { 
                        day: 'numeric', 
                        month: 'short',
                        year: 'numeric'
                    });
                }),
                datasets: [{
                    label: 'Jumlah Pengumpulan',
                    data: counts,
                    backgroundColor: 'rgba(79, 70, 229, 0.6)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
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
                },
                animation: {
                    onComplete: () => {
                        if (chartLoading) chartLoading.style.display = 'none';
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error initializing chart:', error);
    }
    @endif
});
</script>
@endsection
