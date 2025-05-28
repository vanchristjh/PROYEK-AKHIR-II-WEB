<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $assignmentId;
    
    public function __construct($assignmentId = null)
    {
        $this->assignmentId = $assignmentId;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Submission::with(['student', 'assignment']);
        
        if ($this->assignmentId) {
            $query->where('assignment_id', $this->assignmentId);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tugas',
            'Nama Siswa',
            'Kelas',
            'File',
            'Ukuran File',
            'Status',
            'Nilai',
            'Catatan',
            'Umpan Balik',
            'Tanggal Pengumpulan',
            'Tanggal Penilaian',
        ];
    }
    
    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->assignment->title,
            $row->student->name,
            $row->student->student->class->name ?? 'N/A',
            $row->file_name,
            $row->file_size,
            $this->getStatusText($row->status),
            $row->score,
            $row->notes,
            $row->feedback,
            $row->created_at->format('d-m-Y H:i:s'),
            $row->graded_at ? $row->graded_at->format('d-m-Y H:i:s') : 'Belum dinilai',
        ];
    }
    
    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
    
    /**
     * Convert status to readable text
     */
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
