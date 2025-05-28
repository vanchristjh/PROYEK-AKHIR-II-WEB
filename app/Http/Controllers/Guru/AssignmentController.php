<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        
        $assignmentsQuery = Assignment::with(['subject', 'classes'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc');
        
        // Filter by subject
        if ($request->has('subject') && $request->subject) {
            $assignmentsQuery->where('subject_id', $request->subject);
        }
        
        // Filter by class
        if ($request->has('class') && $request->class) {
            $assignmentsQuery->whereHas('classes', function ($query) use ($request) {
                $query->where('school_classes.id', $request->class);
            });
        }
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $assignmentsQuery->where('is_active', true)
                    ->where(function ($query) {
                        $query->where('deadline', '>=', now())
                            ->orWhereNull('deadline');
                    });
            } elseif ($request->status === 'expired') {
                $assignmentsQuery->where('deadline', '<', now());
            } elseif ($request->status === 'inactive') {
                $assignmentsQuery->where('is_active', false);
            }
        }
        
        $assignments = $assignmentsQuery->paginate(10)->withQueryString();
        
        // Get list of subjects and classes for filtering
        $subjects = Subject::whereHas('assignments', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        $classes = SchoolClass::whereHas('assignments', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        
        return view('guru.assignments.index', compact('assignments', 'subjects', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teacher = Auth::user();
        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        
        return view('guru.assignments.create', compact('subjects', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) 
    {
        try {
            \Log::info('Starting assignment creation', ['data' => $request->all()]);
            
            // Validate input
            $validated = $this->validateAssignment($request);
            \Log::info('Input validation passed');
            
            // Start transaction
            DB::beginTransaction();
            
            try {
                $filePath = null;
                
                // Handle file upload if provided
                if ($request->hasFile('file')) {
                    \Log::info('Processing file upload');
                    $file = $request->file('file');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('assignments', $fileName, 'public');
                    \Log::info('File uploaded successfully', ['path' => $filePath]);
                }
                
                // Create new assignment
                \Log::info('Creating assignment record');
                $assignment = new Assignment();
                $assignment->title = $validated['title'];
                $assignment->description = $validated['description'];
                $assignment->subject_id = $validated['subject_id'];
                $assignment->teacher_id = Auth::id();
                $assignment->deadline = $validated['deadline'];
                $assignment->file = $filePath;
                $assignment->is_active = true;
                $assignment->max_score = $validated['max_score'] ?? 100;
                $assignment->allow_late_submission = $validated['allow_late_submission'] ?? false;
                $assignment->late_submission_penalty = $validated['late_submission_penalty'] ?? 0;
                
                if (!$assignment->save()) {
                    throw new \Exception('Failed to save assignment');
                }
                \Log::info('Assignment saved successfully', ['id' => $assignment->id]);
                
                // First attach selected classes
                if (!empty($validated['classes'])) {
                    \Log::info('Attaching classes', ['classes' => $validated['classes']]);
                    $assignment->classes()->attach($validated['classes']);
                    
                    // Get classrooms from SchoolClass model
                    $classrooms = SchoolClass::whereIn('id', $validated['classes'])
                        ->pluck('classroom_id')
                        ->filter() // Remove any null values
                        ->unique()
                        ->values()
                        ->all();
                    
                    // If no classrooms found or all are null, log a warning
                    if (empty($classrooms)) {
                        \Log::warning('No classroom_id found in the selected classes. This may cause assignments to appear on dashboard but not in task list.');
                        \Log::info('Using classes IDs as classroom_ids as a fallback to ensure consistency');
                        
                        // As a fallback, use the class IDs as classroom IDs
                        // This ensures assignments appear in both views even if classroom_id is not set in SchoolClass
                        $assignment->classrooms()->attach($validated['classes']);
                    } else {
                        \Log::info('Attaching classrooms', ['classrooms' => $classrooms]);
                        $assignment->classrooms()->attach($classrooms);
                    }
                    
                    // Create notifications for students in these classes
                    $this->createStudentNotifications($assignment, $validated['classes']);
                }
                
                \Log::info('Committing transaction');
                DB::commit();
                
                return redirect()->route('guru.assignments.show', $assignment)
                    ->with('success', 'Tugas berhasil dibuat.');
                    
            } catch (\Exception $e) {
                \Log::error('Error in assignment creation transaction', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                DB::rollBack();
                
                // Delete uploaded file if exists
                if ($filePath && Storage::exists('public/' . $filePath)) {
                    Storage::delete('public/' . $filePath);
                }
                
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error in assignment creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() 
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat tugas: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $assignment->load(['subject', 'classes', 'submissions.student.class']);
        
        // Submission statistics
        $submissionStats = [
            'total' => $assignment->submissions->count(),
            'graded' => $assignment->submissions->filter(function($submission) {
                return $submission->score !== null;
            })->count(),
        ];
        
        return view('guru.assignments.show', compact('assignment', 'submissionStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $subjects = Subject::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();
        $selectedClasses = $assignment->classes->pluck('id')->toArray();
        
        return view('guru.assignments.edit', compact('assignment', 'subjects', 'classes', 'selectedClasses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'classes' => 'required|array',
            'classes.*' => 'exists:school_classes,id',
            'deadline' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png|max:102400',
        ], [
            'title.required' => 'Judul tugas wajib diisi',
            'description.required' => 'Deskripsi tugas wajib diisi',
            'subject_id.required' => 'Mata pelajaran wajib dipilih',
            'classes.required' => 'Pilih minimal satu kelas',
            'deadline.required' => 'Deadline wajib diisi',
            'file.max' => 'Ukuran file maksimal 100MB',
        ]);

        // Add validation for deadline being in the future if changed
        if ($request->deadline != $assignment->deadline) {
            $request->validate([
                'deadline' => 'after:now',
            ], [
                'deadline.after' => 'Deadline baru harus di masa depan',
            ]);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Update assignment details
            $assignment->title = $validated['title'];
            $assignment->description = $validated['description'];
            $assignment->subject_id = $validated['subject_id'];
            $assignment->deadline = $validated['deadline'];

            // Handle file upload if provided
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($assignment->file && Storage::exists('public/' . $assignment->file)) {
                    Storage::delete('public/' . $assignment->file);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('assignments', $fileName, 'public');
                $assignment->file = $filePath;
            }

            // Update is_active if provided
            if ($request->has('is_active')) {
                $assignment->is_active = (bool)$request->is_active;
            }

            $assignment->save();

            // Sync classes
            $assignment->classes()->sync($validated['classes']);

            // Sync associated classrooms
            $classrooms = SchoolClass::whereIn('id', $validated['classes'])
                ->pluck('classroom_id')
                ->filter() // Remove null values
                ->unique()
                ->values()
                ->all();

            $assignment->classrooms()->sync($classrooms);

            DB::commit();

            return redirect()->route('guru.assignments.show', $assignment)
                ->with('success', 'Tugas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if assignment has submissions
        if ($assignment->submissions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus tugas yang sudah memiliki pengumpulan.');
        }
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Delete file if exists
            if ($assignment->file && Storage::exists('public/' . $assignment->file)) {
                Storage::delete('public/' . $assignment->file);
            }
            
            // Detach classes
            $assignment->classes()->detach();
            
            // Delete assignment
            $assignment->delete();
            
            DB::commit();
            
            return redirect()->route('guru.assignments.index')
                ->with('success', 'Tugas berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show all submissions for an assignment.
     */
    public function submissions(Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $assignment->load(['subject', 'classes', 'submissions.student.class']);
        
        // Get all students from assigned classes
        $students = [];
        foreach ($assignment->classes as $class) {
            foreach ($class->students as $student) {
                $students[$student->id] = [
                    'id' => $student->id,
                    'name' => $student->name,
                    'class' => $class->name,
                    'has_submitted' => false,
                    'submission' => null
                ];
            }
        }
        
        // Mark students who have submitted
        foreach ($assignment->submissions as $submission) {
            if (isset($students[$submission->student_id])) {
                $students[$submission->student_id]['has_submitted'] =
            abort(403, 'Unauthorized action.');
        }
        
        $assignment->load(['subject', 'classes', 'submissions.student.class']);
        
        // Count total students
        $studentsCount = 0;
        foreach ($assignment->classes as $class) {
            $studentsCount += $class->students->count();
        }
        
        // Submissions count
        $submissions = $assignment->submissions;
        $submissionsCount = $submissions->count();
        $submissionRate = $studentsCount > 0 ? round(($submissionsCount / $studentsCount) * 100, 1) : 0;
        
        // Graded submissions
        $gradedSubmissions = $submissions->filter(function($submission) {
            return $submission->score !== null;
        });
        $gradedCount = $gradedSubmissions->count();
        $gradeRate = $submissionsCount > 0 ? round(($gradedCount / $submissionsCount) * 100, 1) : 0;
        
        // Pending submissions
        $pendingCount = $submissionsCount - $gradedCount;
        
        // Average score
        $totalScore = 0;
        foreach ($gradedSubmissions as $submission) {
            $totalScore += $submission->score;
        }
        $averageScore = $gradedCount > 0 ? round($totalScore / $gradedCount, 1) : 0;
        
        // Score distribution
        $scoreDistribution = [
            'a' => 0, // 80-100
            'b' => 0, // 70-79
            'c' => 0, // 60-69
            'd' => 0, // 50-59
            'e' => 0, // 0-49
        ];
        
        foreach ($gradedSubmissions as $submission) {
            $score = $submission->score;
            if ($score >= 80) {
                $scoreDistribution['a']++;
            } elseif ($score >= 70) {
                $scoreDistribution['b']++;
            } elseif ($score >= 60) {
                $scoreDistribution['c']++;
            } elseif ($score >= 50) {
                $scoreDistribution['d']++;
            } else {
                $scoreDistribution['e']++;
            }
        }
        
        // Submission timeline (group by date)
        $submissionDates = $submissions->groupBy(function ($submission) {
            return $submission->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->count();
        })->toArray();
        
        return view('guru.assignments.statistics', compact(
            'assignment',
            'studentsCount',
            'submissionsCount',
            'submissionRate',
            'gradedCount',
            'gradeRate',
            'pendingCount',
            'averageScore',
            'scoreDistribution',
            'submissionDates'
        ));
    }
    
    /**
     * Show batch grading interface.
     */
    public function batchGrade(Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $assignment->load(['subject', 'classes', 'submissions.student.class']);
        
        // Get submissions grouped by class
        $classesList = $assignment->classes;
        $submissionsByClass = [];
        
        foreach ($classesList as $class) {
            $submissionsByClass[$class->id] = [
                'class_name' => $class->name,
                'submissions' => $submissions = $assignment->submissions->filter(function($submission) use ($class) {
                    return $submission->student && $submission->student->class_id == $class->id;
                })->sortBy(function($submission) {
                    return $submission->student->name;
                })
            ];
        }
        
        return view('guru.assignments.batch-grade', compact('assignment', 'submissionsByClass'));
    }
    
    /**
     * Export submissions to Excel
     */
    public function exportSubmissions(Assignment $assignment)
    {
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $assignment->load(['subject', 'classes', 'submissions.student.class']);
        
        // Get all students from assigned classes
        $students = [];
        foreach ($assignment->classes as $class) {
            foreach ($class->students as $student) {
                $students[$student->id] = [
                    'id' => $student->id,
                    'name' => $student->name,
                    'class' => $class->name,
                    'has_submitted' => false,
                    'submission' => null
                ];
            }
        }
        
        // Mark students who have submitted
        foreach ($assignment->submissions as $submission) {
            if (isset($students[$submission->student_id])) {
                $students[$submission->student_id]['has_submitted'] = true;
                $students[$submission->student_id]['submission'] = $submission;
            }
        }
        
        // Sort by name
        uasort($students, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        
        // Create a new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pengumpulan Tugas');
        
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
        $sheet->setCellValue('A1', 'DAFTAR PENGUMPULAN TUGAS');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Assignment info
        $sheet->setCellValue('A3', 'Judul Tugas:');
        $sheet->setCellValue('B3', $assignment->title);
        $sheet->mergeCells('B3:F3');
        
        $sheet->setCellValue('A4', 'Mata Pelajaran:');
        $sheet->setCellValue('B4', $assignment->subject->name);
        $sheet->mergeCells('B4:F4');
        
        $sheet->setCellValue('A5', 'Deadline:');
        $sheet->setCellValue('B5', $assignment->deadline ? $assignment->deadline->format('d M Y, H:i') : 'Tidak ada');
        $sheet->mergeCells('B5:F5');
        
        // Get classes
        $classes = $assignment->classes;
        $classNames = $classes->pluck('name')->implode(', ');
        $sheet->setCellValue('A6', 'Kelas:');
        $sheet->setCellValue('B6', $classNames);
        $sheet->mergeCells('B6:F6');
        
        // Style for assignment info
        $sheet->getStyle('A3:A6')->getFont()->setBold(true);
        
        // Headers
        $sheet->setCellValue('A8', 'No');
        $sheet->setCellValue('B8', 'Nama Siswa');
        $sheet->setCellValue('C8', 'Kelas');
        $sheet->setCellValue('D8', 'Status');
        $sheet->setCellValue('E8', 'Tanggal Pengumpulan');
        $sheet->setCellValue('F8', 'Nilai');
        
        $sheet->getStyle('A8:F8')->applyFromArray($headerStyle);
        
        // Data
        $row = 9;
        $no = 1;
        foreach ($students as $student) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $student['name']);
            $sheet->setCellValue('C' . $row, $student['class']);
            $sheet->setCellValue('D' . $row, $student['has_submitted'] ? 'Sudah Mengumpulkan' : 'Belum Mengumpulkan');
            $sheet->setCellValue('E' . $row, $student['has_submitted'] ? $student['submission']->created_at->format('d M Y, H:i') : '-');
            $sheet->setCellValue('F' . $row, $student['has_submitted'] && $student['submission']->score !== null ? $student['submission']->score : '-');
            
            // Color the status cell based on submission status
            if ($student['has_submitted']) {
                $sheet->getStyle('D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(['rgb' => 'D4EDDA']);
            } else {
                $sheet->getStyle('D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(['rgb' => 'F8D7DA']);
            }
            
            $row++;
        }
        
        $sheet->getStyle('A9:F' . ($row - 1))->applyFromArray($dataStyle);
        
        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set filename
        $filename = 'Pengumpulan_Tugas_' . str_replace(' ', '_', $assignment->title) . '_' . date('Y-m-d') . '.xlsx';
        
        // Create writer and save
        $writer = new Xlsx($spreadsheet);
        
        // Set header to download file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
    
    /**
     * Validate an assignment request.
     */
    private function validateAssignment(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'classes' => 'required|array',
            'classes.*' => 'exists:school_classes,id',
            'deadline' => 'required|date|after:now',
            'file' => 'nullable|file|max:20480', // 20MB limit
            'max_score' => 'nullable|integer|min:0|max:100',
            'allow_late_submission' => 'nullable|boolean',
            'late_submission_penalty' => 'nullable|integer|min:0|max:100',
        ]);
    }
    
    /**
     * Create notifications for students when a new assignment is added
     */
    private function createStudentNotifications(Assignment $assignment, array $classIds)
    {
        if (class_exists('App\Models\AssignmentNotification')) {
            $students = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'siswa');
            })->whereHas('classes', function($q) use ($classIds) {
                $q->whereIn('school_classes.id', $classIds);
            })->get();
            
            foreach ($students as $student) {
                $notification = new \App\Models\AssignmentNotification();
                $notification->user_id = $student->id;
                $notification->title = 'Tugas Baru';
                $notification->message = 'Tugas baru "' . $assignment->title . '" telah ditambahkan untuk mata pelajaran ' . $assignment->subject->name;
                $notification->type = 'new_assignment';
                $notification->reference_id = $assignment->id;
                $notification->save();
            }
        }
    }
}
