<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistik Tugas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .stats-container {
            margin-bottom: 30px;
        }
        .stat-item {
            margin-bottom: 10px;
        }
        .stat-label {
            font-weight: bold;
            margin-right: 10px;
        }
        .header-info {
            margin-bottom: 30px;
        }
        .header-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Statistik Tugas</h1>
    
    <div class="header-info">
        <p><strong>Judul:</strong> {{ $assignment->title }}</p>
        <p><strong>Mata Pelajaran:</strong> {{ $assignment->subject->name }}</p>
        <p><strong>Kelas:</strong> 
            @foreach($assignment->classes as $class)
                {{ $class->name }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </p>
        <p><strong>Tenggat:</strong> {{ $assignment->deadline->format('d M Y, H:i') }}</p>
    </div>

    <div class="stats-container">
        <div class="stat-item">
            <span class="stat-label">Total Siswa:</span>
            <span>{{ $studentsCount }}</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-label">Total Pengumpulan:</span>
            <span>{{ $submissionsCount }}</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-label">Pengumpulan Tepat Waktu:</span>
            <span>{{ $onTimeSubmissions }}</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-label">Pengumpulan Terlambat:</span>
            <span>{{ $lateSubmissions }}</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-label">Belum Mengumpulkan:</span>
            <span>{{ $notSubmittedCount }}</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-label">Sudah Dinilai:</span>
            <span>{{ $gradedCount }}</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-label">Belum Dinilai:</span>
            <span>{{ $pendingCount }}</span>
        </div>
        
        <div class="stat-item">
            <span class="stat-label">Rata-rata Nilai:</span>
            <span>{{ $averageScore }}</span>
        </div>
    </div>

    <p style="font-size: 0.8em; color: #666; margin-top: 30px;">
        Laporan ini digenerate pada {{ now()->format('d M Y, H:i:s') }}
    </p>
</body>
</html>
