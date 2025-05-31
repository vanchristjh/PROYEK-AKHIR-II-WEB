<?php

namespace App\Exports;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class AssignmentStatisticsExport implements WithMultipleSheets
{
    protected $assignment;
    
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }
    
    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        
        $sheets[] = new AssignmentInfoSheet($this->assignment);
        $sheets[] = new AssignmentSubmissionsSheet($this->assignment);
        $sheets[] = new AssignmentScoreDistributionSheet($this->assignment);
        
        return $sheets;
    }
}

class AssignmentInfoSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $assignment;
    
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }
    
    public function collection()
    {
        $this->assignment->load(['classes', 'subject', 'teacher']);
        
        // Get all students in the assigned classes
        $classIds = $this->assignment->classes->pluck('id')->toArray();
        $studentsCount = User::whereHas('student', function($query) use ($classIds) {
            $query->whereIn('class_id', $classIds);
        })->count();
        
        // Get submission statistics
        $submissionsCount = $this->assignment->submissions()->count();
        $submissionRate = $studentsCount > 0 ? round(($submissionsCount / $studentsCount) * 100, 1) : 0;
        
        // Get grading statistics
        $gradedCount = $this->assignment->submissions()->whereNotNull('score')->count();
        $pendingCount = $submissionsCount - $gradedCount;
        $gradeRate = $submissionsCount > 0 ? round(($gradedCount / $submissionsCount) * 100, 1) : 0;
        
        // Calculate average score for graded submissions
        $averageScore = $this->assignment->submissions()->whereNotNull('score')->avg('score');
        
        return new Collection([
            [
                'title' => 'Informasi Tugas',
                'value' => '',
            ],
            [
                'title' => 'Judul Tugas',
                'value' => $this->assignment->title,
            ],
            [
                'title' => 'Mata Pelajaran',
                'value' => $this->assignment->subject->name,
            ],
            [
                'title' => 'Guru',
                'value' => $this->assignment->teacher->name,
            ],
            [
                'title' => 'Kelas',
                'value' => $this->assignment->classes->pluck('name')->implode(', '),
            ],
            [
                'title' => 'Tenggat Waktu',
                'value' => $this->assignment->deadline->format('d M Y, H:i'),
            ],
            [
                'title' => 'Status',
                'value' => $this->assignment->isExpired() ? 'Berakhir' : 'Aktif',
            ],
            [
                'title' => '',
                'value' => '',
            ],
            [
                'title' => 'Statistik Pengumpulan',
                'value' => '',
            ],
            [
                'title' => 'Total Siswa',
                'value' => $studentsCount,
            ],
            [
                'title' => 'Total Pengumpulan',
                'value' => $submissionsCount,
            ],
            [
                'title' => 'Persentase Pengumpulan',
                'value' => $submissionRate . '%',
            ],
            [
                'title' => 'Sudah Dinilai',
                'value' => $gradedCount,
            ],
            [
                'title' => 'Belum Dinilai',
                'value' => $pendingCount,
            ],
            [
                'title' => 'Persentase Dinilai',
                'value' => $gradeRate . '%',
            ],
            [
                'title' => 'Nilai Rata-rata',
                'value' => round($averageScore, 2),
            ],
        ]);
    }
    
    public function title(): string
    {
        return 'Informasi Tugas';
    }
    
    public function headings(): array
    {
        return [
            'Kategori',
            'Detail',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Style first row as header
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'],
            ],
        ]);
        
        // Style section headers
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('A9')->applyFromArray(['font' => ['bold' => true]]);
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class AssignmentSubmissionsSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $assignment;
    
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }
    
    public function collection()
    {
        return $this->assignment->submissions()
            ->with(['student', 'student.student.class'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function title(): string
    {
        return 'Pengumpulan Tugas';
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Nama Siswa',
            'Kelas',
            'Tanggal Pengumpulan',
            'Status',
            'Nilai',
            'Catatan Siswa',
            'Umpan Balik Guru',
            'Tanggal Penilaian',
        ];
    }
    
    public function map($row): array
    {
        return [
            $row->id,
            $row->student->name,
            $row->student->student->class->name ?? 'N/A',
            $row->created_at->format('d-m-Y H:i:s'),
            $this->getStatusText($row->status),
            $row->score,
            $row->notes,
            $row->feedback,
            $row->graded_at ? $row->graded_at->format('d-m-Y H:i:s') : 'Belum dinilai',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
    
    private function getStatusText($status)
    {
        $statusMap = [
            'submitted' => 'Dikumpulkan',
            'updated' => 'Diperbarui',
            'graded' => 'Dinilai',
            'late' => 'Terlambat',
        ];
        
        return $statusMap[$status] ?? $status;
    }
}

class AssignmentScoreDistributionSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $assignment;
    
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }
    
    public function collection()
    {
        $scoreDistribution = [
            ['Kategori' => 'A (80-100)', 'Jumlah Siswa' => $this->assignment->submissions()->whereNotNull('score')->where('score', '>=', 80)->count()],
            ['Kategori' => 'B (70-79)', 'Jumlah Siswa' => $this->assignment->submissions()->whereNotNull('score')->whereBetween('score', [70, 79.99])->count()],
            ['Kategori' => 'C (60-69)', 'Jumlah Siswa' => $this->assignment->submissions()->whereNotNull('score')->whereBetween('score', [60, 69.99])->count()],
            ['Kategori' => 'D (50-59)', 'Jumlah Siswa' => $this->assignment->submissions()->whereNotNull('score')->whereBetween('score', [50, 59.99])->count()],
            ['Kategori' => 'E (0-49)', 'Jumlah Siswa' => $this->assignment->submissions()->whereNotNull('score')->where('score', '<', 50)->count()],
        ];
        
        return new Collection($scoreDistribution);
    }
    
    public function title(): string
    {
        return 'Distribusi Nilai';
    }
    
    public function headings(): array
    {
        return [
            'Kategori',
            'Jumlah Siswa',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class AssignmentStatisticsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $assignment;
    protected $statistics;

    public function __construct($assignment, $statistics)
    {
        $this->assignment = $assignment;
        $this->statistics = $statistics;
    }

    public function collection()
    {
        return collect([
            [
                'metric' => 'Total Siswa',
                'value' => $this->statistics['studentsCount']
            ],
            [
                'metric' => 'Total Pengumpulan',
                'value' => $this->statistics['submissionsCount']
            ],
            [
                'metric' => 'Rata-rata Nilai',
                'value' => $this->statistics['averageScore']
            ],
            [
                'metric' => 'Pengumpulan Tepat Waktu',
                'value' => $this->statistics['onTimeSubmissions']
            ],
            [
                'metric' => 'Pengumpulan Terlambat',
                'value' => $this->statistics['lateSubmissions']
            ],
            [
                'metric' => 'Belum Mengumpulkan',
                'value' => $this->statistics['notSubmittedCount']
            ],
            [
                'metric' => 'Sudah Dinilai',
                'value' => $this->statistics['gradedCount']
            ],
            [
                'metric' => 'Belum Dinilai',
                'value' => $this->statistics['pendingCount']
            ],
        ]);
    }

    public function headings(): array 
    {
        return [
            'Metrik',
            'Nilai'
        ];
    }

    public function map($row): array
    {
        return [
            $row['metric'],
            $row['value']
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A' => ['font' => ['bold' => true]],
        ];
    }
}
