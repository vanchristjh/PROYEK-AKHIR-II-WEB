<?php

namespace App\Exports;

use App\Models\Quiz;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class QuizResultsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function collection()
    {
        return $this->quiz->attempts;
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Siswa',
            'Waktu Mulai',
            'Waktu Selesai',
            'Durasi (menit)',
            'Nilai',
            'Status',
        ];
    }

    public function map($attempt): array
    {
        $duration = $attempt->completed_at 
            ? round((strtotime($attempt->completed_at) - strtotime($attempt->started_at)) / 60)
            : '-';

        return [
            $attempt->student->nis ?? '-',
            $attempt->student->name ?? 'Unknown',
            $attempt->started_at ? date('d/m/Y H:i', strtotime($attempt->started_at)) : '-',
            $attempt->completed_at ? date('d/m/Y H:i', strtotime($attempt->completed_at)) : '-',
            $duration,
            number_format($attempt->score, 1),
            $attempt->score >= $this->quiz->passing_score ? 'Lulus' : 'Tidak Lulus',
        ];
    }
}
