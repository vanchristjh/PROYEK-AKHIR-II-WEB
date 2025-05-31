<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hasil Kuis: {{ $quiz->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .quiz-info {
            margin-bottom: 20px;
        }
        .quiz-info table {
            width: auto;
            margin-bottom: 10px;
        }
        .quiz-info td {
            padding: 4px;
            border: none;
        }
        .status-passed {
            color: #16a34a;
        }
        .status-failed {
            color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Hasil Kuis: {{ $quiz->title }}</h2>
    </div>

    <div class="quiz-info">
        <table>
            <tr>
                <td><strong>Mata Pelajaran:</strong></td>
                <td>{{ $quiz->subject->name ?? 'Tidak ada mata pelajaran' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal:</strong></td>
                <td>{{ $quiz->start_time ? \Carbon\Carbon::parse($quiz->start_time)->format('d M Y, H:i') : 'Tidak diatur' }} - 
                    {{ $quiz->end_time ? \Carbon\Carbon::parse($quiz->end_time)->format('d M Y, H:i') : 'Tidak diatur' }}</td>
            </tr>
            <tr>
                <td><strong>Durasi:</strong></td>
                <td>{{ $quiz->duration }} menit</td>
            </tr>
            <tr>
                <td><strong>Nilai Kelulusan:</strong></td>
                <td>{{ $quiz->passing_score ?? 70 }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NIS</th>
                <th style="width: 25%;">Nama Siswa</th>
                <th style="width: 15%;">Waktu Mulai</th>
                <th style="width: 15%;">Waktu Selesai</th>
                <th style="width: 10%;">Durasi (menit)</th>
                <th style="width: 7%;">Nilai</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($quiz->attempts as $attempt)
                @php
                    $duration = $attempt->completed_at 
                        ? round((strtotime($attempt->completed_at) - strtotime($attempt->started_at)) / 60)
                        : '-';
                    $isPassed = $attempt->score >= ($quiz->passing_score ?? 70);
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $attempt->student->nis ?? '-' }}</td>
                    <td>{{ $attempt->student->name ?? 'Unknown' }}</td>
                    <td>{{ $attempt->started_at ? date('d/m/Y H:i', strtotime($attempt->started_at)) : '-' }}</td>
                    <td>{{ $attempt->completed_at ? date('d/m/Y H:i', strtotime($attempt->completed_at)) : '-' }}</td>
                    <td>{{ $duration }}</td>
                    <td>{{ number_format($attempt->score, 1) }}</td>
                    <td class="{{ $isPassed ? 'status-passed' : 'status-failed' }}">
                        {{ $isPassed ? 'Lulus' : 'Tidak' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Belum ada yang mengerjakan kuis</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <p><strong>Statistik:</strong></p>
        @php
            $totalStudents = $quiz->attempts->count();
            $passedCount = $quiz->attempts->where('score', '>=', $quiz->passing_score ?? 70)->count();
            $averageScore = $quiz->attempts->avg('score');
            $passRate = $totalStudents > 0 ? ($passedCount / $totalStudents * 100) : 0;
        @endphp
        <table>
            <tr>
                <td style="width: 25%;">Total Siswa:</td>
                <td>{{ $totalStudents }} siswa</td>
            </tr>
            <tr>
                <td>Siswa Lulus:</td>
                <td>{{ $passedCount }} siswa ({{ number_format($passRate, 1) }}%)</td>
            </tr>
            <tr>
                <td>Nilai Rata-rata:</td>
                <td>{{ number_format($averageScore, 1) }}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px; font-size: 10px; text-align: right;">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
