<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AssignmentStatisticsExportController extends Controller
{
    public function export(Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relations
        $assignment->load(['subject', 'classes', 'submissions.student.class']);
        
        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Statistik Tugas');
        
        // Set header style
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        
        // Set data style
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        
        // Set title
        $sheet->setCellValue('A1', 'STATISTIK TUGAS');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Assignment info
        $sheet->setCellValue('A3', 'Judul Tugas:');
        $sheet->setCellValue('B3', $assignment->title);
        $sheet->mergeCells('B3:G3');
        
        $sheet->setCellValue('A4', 'Mata Pelajaran:');
        $sheet->setCellValue('B4', $assignment->subject->name);
        $sheet->mergeCells('B4:G4');
        
        $sheet->setCellValue('A5', 'Deadline:');
        $sheet->setCellValue('B5', $assignment->deadline ? $assignment->deadline->format('d M Y, H:i') : 'Tidak ada');
        $sheet->mergeCells('B5:G5');
        
        // Get classes
        $classes = $assignment->classes;
        $classNames = $classes->pluck('name')->implode(', ');
        $sheet->setCellValue('A6', 'Kelas:');
        $sheet->setCellValue('B6', $classNames);
        $sheet->mergeCells('B6:G6');
        
        // Style for assignment info
        $sheet->getStyle('A3:A6')->getFont()->setBold(true);
        
        // Get statistics
        $submissions = $assignment->submissions;
        $totalStudents = 0;
        foreach($classes as $class) {
            $totalStudents += $class->students->count();
        }
        
        $submissionsCount = $submissions->count();
        $submissionRate = $totalStudents > 0 ? round(($submissionsCount / $totalStudents) * 100, 1) : 0;
        
        $gradedSubmissions = $submissions->filter(function($submission) {
            return $submission->score !== null;
        });
        $gradedCount = $gradedSubmissions->count();
        $gradeRate = $submissionsCount > 0 ? round(($gradedCount / $submissionsCount) * 100, 1) : 0;
        
        $pendingCount = $submissionsCount - $gradedCount;
        
        // Calculate average score
        $totalScore = 0;
        foreach($gradedSubmissions as $submission) {
            $totalScore += $submission->score;
        }
        $averageScore = $gradedCount > 0 ? round($totalScore / $gradedCount, 1) : 0;
        
        // Calculate score distribution
        $scoreDistribution = [
            'a' => 0, // 80-100
            'b' => 0, // 70-79
            'c' => 0, // 60-69
            'd' => 0, // 50-59
            'e' => 0, // 0-49
        ];
        
        foreach($gradedSubmissions as $submission) {
            $score = $submission->score;
            if($score >= 80) {
                $scoreDistribution['a']++;
            } elseif($score >= 70) {
                $scoreDistribution['b']++;
            } elseif($score >= 60) {
                $scoreDistribution['c']++;
            } elseif($score >= 50) {
                $scoreDistribution['d']++;
            } else {
                $scoreDistribution['e']++;
            }
        }
        
        // Summary section
        $sheet->setCellValue('A8', 'RINGKASAN PENGUMPULAN');
        $sheet->mergeCells('A8:G8');
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A8')->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(['rgb' => 'E7E6E6']);
        
        $sheet->setCellValue('A10', 'Total Siswa:');
        $sheet->setCellValue('B10', $totalStudents);
        
        $sheet->setCellValue('D10', 'Pengumpulan:');
        $sheet->setCellValue('E10', $submissionsCount);
        $sheet->setCellValue('F10', 'Persentase:');
        $sheet->setCellValue('G10', $submissionRate . '%');
        
        $sheet->setCellValue('A11', 'Sudah Dinilai:');
        $sheet->setCellValue('B11', $gradedCount);
        
        $sheet->setCellValue('D11', 'Belum Dinilai:');
        $sheet->setCellValue('E11', $pendingCount);
        
        $sheet->setCellValue('F11', 'Persentase Dinilai:');
        $sheet->setCellValue('G11', $gradeRate . '%');
        
        $sheet->setCellValue('A12', 'Nilai Rata-rata:');
        $sheet->setCellValue('B12', $averageScore);
        
        // Style for summary
        $sheet->getStyle('A10:A12')->getFont()->setBold(true);
        $sheet->getStyle('D10:D11')->getFont()->setBold(true);
        $sheet->getStyle('F10:F11')->getFont()->setBold(true);
        
        // Score Distribution
        $sheet->setCellValue('A14', 'DISTRIBUSI NILAI');
        $sheet->mergeCells('A14:G14');
        $sheet->getStyle('A14')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A14')->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(['rgb' => 'E7E6E6']);
        
        // Headers
        $sheet->setCellValue('A16', 'Kategori');
        $sheet->setCellValue('B16', 'Rentang Nilai');
        $sheet->setCellValue('C16', 'Jumlah Siswa');
        $sheet->setCellValue('D16', 'Persentase');
        
        $sheet->getStyle('A16:D16')->applyFromArray($headerStyle);
        
        // Score distribution data
        $data = [
            ['A', '80-100', $scoreDistribution['a'], $gradedCount > 0 ? round(($scoreDistribution['a'] / $gradedCount) * 100, 1) . '%' : '0%'],
            ['B', '70-79', $scoreDistribution['b'], $gradedCount > 0 ? round(($scoreDistribution['b'] / $gradedCount) * 100, 1) . '%' : '0%'],
            ['C', '60-69', $scoreDistribution['c'], $gradedCount > 0 ? round(($scoreDistribution['c'] / $gradedCount) * 100, 1) . '%' : '0%'],
            ['D', '50-59', $scoreDistribution['d'], $gradedCount > 0 ? round(($scoreDistribution['d'] / $gradedCount) * 100, 1) . '%' : '0%'],
            ['E', '0-49', $scoreDistribution['e'], $gradedCount > 0 ? round(($scoreDistribution['e'] / $gradedCount) * 100, 1) . '%' : '0%'],
        ];
        
        $row = 17;
        foreach($data as $rowData) {
            $sheet->setCellValue('A' . $row, $rowData[0]);
            $sheet->setCellValue('B' . $row, $rowData[1]);
            $sheet->setCellValue('C' . $row, $rowData[2]);
            $sheet->setCellValue('D' . $row, $rowData[3]);
            $row++;
        }
        
        $sheet->getStyle('A17:D21')->applyFromArray($dataStyle);
        
        // Submissions list
        $sheet->setCellValue('A23', 'DAFTAR PENGUMPULAN');
        $sheet->mergeCells('A23:G23');
        $sheet->getStyle('A23')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A23')->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(['rgb' => 'E7E6E6']);
        
        // Headers
        $sheet->setCellValue('A25', 'No');
        $sheet->setCellValue('B25', 'Nama Siswa');
        $sheet->setCellValue('C25', 'Kelas');
        $sheet->setCellValue('D25', 'Waktu Pengumpulan');
        $sheet->setCellValue('E25', 'Status');
        $sheet->setCellValue('F25', 'Nilai');
        $sheet->setCellValue('G25', 'Keterangan');
        
        $sheet->getStyle('A25:G25')->applyFromArray($headerStyle);
        
        // Submissions data
        $row = 26;
        $no = 1;
        foreach($submissions as $submission) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $submission->student->name ?? 'Tidak diketahui');
            $sheet->setCellValue('C' . $row, $submission->student->class->name ?? 'Tidak diketahui');
            $sheet->setCellValue('D' . $row, $submission->created_at->format('d M Y, H:i'));
            $sheet->setCellValue('E' . $row, $submission->score !== null ? 'Sudah dinilai' : 'Belum dinilai');
            $sheet->setCellValue('F' . $row, $submission->score !== null ? $submission->score : '-');
            
            // Add grade category
            if ($submission->score !== null) {
                if ($submission->score >= 80) {
                    $sheet->setCellValue('G' . $row, 'A (Sangat Baik)');
                } elseif ($submission->score >= 70) {
                    $sheet->setCellValue('G' . $row, 'B (Baik)');
                } elseif ($submission->score >= 60) {
                    $sheet->setCellValue('G' . $row, 'C (Cukup)');
                } elseif ($submission->score >= 50) {
                    $sheet->setCellValue('G' . $row, 'D (Kurang)');
                } else {
                    $sheet->setCellValue('G' . $row, 'E (Sangat Kurang)');
                }
            } else {
                $sheet->setCellValue('G' . $row, '-');
            }
            
            $row++;
        }
        
        if($submissions->count() > 0) {
            $sheet->getStyle('A26:G' . ($row - 1))->applyFromArray($dataStyle);
        } else {
            $sheet->setCellValue('A26', 'Belum ada pengumpulan tugas');
            $sheet->mergeCells('A26:G26');
            $sheet->getStyle('A26')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A26:G26')->applyFromArray($dataStyle);
        }
        
        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Statistik_Tugas_' . str_replace(' ', '_', $assignment->title) . '_' . date('Y-m-d') . '.xlsx';
        
        // Create writer and save
        $writer = new Xlsx($spreadsheet);
        
        // Set header to download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
}
