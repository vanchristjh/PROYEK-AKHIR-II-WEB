<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\User;
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
        $assignmentsQuery = Assignment::with(['subject', 'schoolClasses'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc');

        // Filter by subject
        if ($request->has('subject') && $request->subject) {
            $assignmentsQuery->where('subject_id', $request->subject);
        }

        // Filter by class
        if ($request->has('class') && $request->class) {
            $assignmentsQuery->whereHas('schoolClasses', function($query) use ($request) {
                $query->where('school_classes.id', $request->class);
            });
        }

        $assignments = $assignmentsQuery->paginate(10);        // Get subjects for filter dropdown
        $subjects = Subject::whereHas('teachers', function($query) use ($teacher) {
            $query->where('users.id', $teacher->id);
        })->get();

        // Get classes for filter dropdown
        $classes = SchoolClass::whereHas('subjects.teachers', function($query) use ($teacher) {
            $query->where('users.id', $teacher->id);
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
            
            // Manual validation for deadline
            $deadline = Carbon::parse($validated['deadline']);
            $now = Carbon::now();
            \Log::info('Deadline validation', [
                'deadline' => $deadline->toDateTimeString(),
                'now' => $now->toDateTimeString(),
                'difference_minutes' => $deadline->diffInMinutes($now, false)
            ]);
            
            // Allow up to 2 minutes in the past to account for form filling delay
            if ($deadline->lt($now) && $deadline->diffInMinutes($now) > 2) {
                \Log::warning('Deadline is in the past', [
                    'deadline' => $deadline->toDateTimeString(),
                    'now' => $now->toDateTimeString()
                ]);
                // Don't throw error, just adjust to 1 hour from now
                $validated['deadline'] = $now->addHour()->toDateTimeString();
                \Log::info('Adjusted deadline to 1 hour from now', [
                    'new_deadline' => $validated['deadline']
                ]);
            }
            
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
                
                // Set assignment attributes
                $assignmentData = [
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'subject_id' => $validated['subject_id'],
                    'teacher_id' => Auth::id(),
                    'deadline' => $validated['deadline'],
                    'file' => $filePath,
                    'is_active' => isset($validated['is_active']) ? (bool)$validated['is_active'] : true,
                    'max_score' => isset($validated['max_score']) ? (int)$validated['max_score'] : 100,
                    'allow_late_submission' => isset($validated['allow_late_submission']) ? (bool)$validated['allow_late_submission'] : false,
                    'late_submission_penalty' => isset($validated['late_submission_penalty']) ? (int)$validated['late_submission_penalty'] : 0,
                ];
                
                \Log::info('Assignment data prepared', $assignmentData);
                
                // Fill and save assignment
                $assignment->fill($assignmentData);
                
                try {
                    if (!$assignment->save()) {
                        throw new \Exception('Failed to save assignment');
                    }
                    \Log::info('Assignment saved successfully', [
                        'id' => $assignment->id,
                        'attributes' => $assignment->getAttributes()
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error saving assignment', [
                        'error' => $e->getMessage(),
                        'data' => $assignmentData
                    ]);
                    throw $e;
                }
                  // First attach selected classes                
                if (!empty($validated['classes'])) {
                    \Log::info('Attaching school classes', ['classes' => $validated['classes']]);
                    
                    try {
                        // Attach school classes
                        $assignment->schoolClasses()->sync($validated['classes']);
                        \Log::info('School classes attached successfully');
                        
                        // Get classrooms associated with these school classes
                        $classroomIds = SchoolClass::whereIn('id', $validated['classes'])
                            ->whereNotNull('classroom_id')
                            ->pluck('classroom_id')
                            ->unique()
                            ->values()
                            ->all();
                        
                        if (!empty($classroomIds)) {
                            $assignment->classrooms()->sync($classroomIds);
                            \Log::info('Classrooms attached successfully', ['classroom_ids' => $classroomIds]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error attaching classes/classrooms', [
                            'error' => $e->getMessage(),
                            'classes' => $validated['classes']
                        ]);
                        throw $e;
                    }
                } else {
                    \Log::warning('No classes selected for assignment');
                    throw new \Exception('Pilih minimal satu kelas untuk tugas ini');
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
        // Load the subject and the submissions
        $assignment->load(['subject', 'submissions']);

        // Calculate submission statistics
        $submissionStats = [
            'total' => $assignment->submissions->count(),
            'graded' => $assignment->submissions->whereNotNull('score')->count(),
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

            // Sync with classrooms if provided
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
            
            // Detach all relationships
            $assignment->schoolClasses()->detach();
            $assignment->classrooms()->detach();

            // Delete the assignment
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
                $students[$submission->student_id]['has_submitted'] = true;
                $students[$submission->student_id]['submission'] = $submission;
            }
        }
        
        // Authorization check
        if ($assignment->teacher_id !== Auth::id()) {
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
        $teacher = Auth::user();
        
        // Check if the assignment exists and is active
        if (!$assignment || !$assignment->is_active) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan atau tidak aktif.');
        }
        
        // Check if the teacher has access to this assignment through the subject
        $hasAccess = $teacher->teacherSubjects()->where('subjects.id', $assignment->subject_id)->exists();
        if (!$hasAccess) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menilai tugas ini.');
        }

        $assignment->load(['subject', 'classrooms', 'submissions.student.classroom']);
        
        // Get submissions with students and classrooms
        $submissions = $assignment->submissions()->with('student.classroom')->get();
        
        // Group submissions by student's classroom
        $submissionsByClass = $submissions->groupBy(function($submission) {
            return optional($submission->student)->classroom_id ?? 'unassigned';
        })->mapWithKeys(function($classSubmissions, $classroomId) {
            $className = $classroomId == 'unassigned' ? 'Belum Ada Kelas' : 
                optional($classSubmissions->first()->student->classroom)->name ?? 'Kelas Tidak Ditemukan';
            
            return [$classroomId => [
                'class_name' => $className,
                'submissions' => $classSubmissions->sortBy('student.name')
            ]];
        });
        
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
     */    private function validateAssignment(Request $request)
    {
        \Log::info('Validating assignment request', ['request' => $request->all()]);
        
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'classes' => 'required|array',
            'classes.*' => 'exists:school_classes,id',
            'deadline' => 'required|date',
            'file' => 'nullable|file|max:102400', // 100MB limit            'max_score' => 'nullable|integer|min:0|max:100',
            'allow_late_submission' => 'required|in:0,1',
            'late_submission_penalty' => 'nullable|integer|min:0|max:100',
            'is_active' => 'required|in:0,1',
        ];
        
        $messages = [
            'title.required' => 'Judul tugas wajib diisi',
            'description.required' => 'Deskripsi tugas wajib diisi',
            'subject_id.required' => 'Mata pelajaran wajib dipilih',
            'subject_id.exists' => 'Mata pelajaran tidak valid',
            'classes.required' => 'Pilih minimal satu kelas',
            'deadline.required' => 'Deadline wajib diisi',
            'deadline.date' => 'Format deadline tidak valid',
            'deadline.after' => 'Deadline harus di masa depan',
            'file.max' => 'Ukuran file maksimal 100MB',
        ];
        
        try {
            $validated = $request->validate($rules, $messages);
            \Log::info('Validation successful');
            return $validated;
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        }
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
    
    /**
     * Save batch grades for multiple submissions.
     */
    public function saveBatchGrade(Request $request, Assignment $assignment)
    {
        try {
            $teacher = Auth::user();
            
            // Check if the assignment exists and is active
            if (!$assignment || !$assignment->is_active) {
                return redirect()->back()->with('error', 'Tugas tidak ditemukan atau tidak aktif.');
            }
            
            // Check if the teacher has access to this assignment through the subject
            $hasAccess = $teacher->teacherSubjects()->where('subjects.id', $assignment->subject_id)->exists();
            if (!$hasAccess) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menilai tugas ini.');
            }

            // Validate that at least one submission was selected
            if (!$request->has('selected') || empty($request->selected)) {
                return redirect()->back()->with('error', 'Pilih minimal satu siswa untuk dinilai.');
            }

            // Validate the existence of the submissions
            $selectedSubmissions = Submission::whereIn('id', $request->selected)
                ->where('assignment_id', $assignment->id)
                ->get();

            if ($selectedSubmissions->count() !== count($request->selected)) {
                return redirect()->back()->with('error', 'Beberapa tugas yang dipilih tidak valid.');
            }

            // Validate the scores
            $request->validate([
                'scores' => 'required|array',
                'scores.*' => 'required|numeric|min:0|max:100'
            ], [
                'scores.required' => 'Nilai siswa wajib diisi.',
                'scores.*.required' => 'Nilai untuk setiap siswa yang dipilih wajib diisi.',
                'scores.*.numeric' => 'Nilai harus berupa angka.',
                'scores.*.min' => 'Nilai tidak boleh kurang dari 0.',
                'scores.*.max' => 'Nilai tidak boleh lebih dari 100.'
            ]);

            // Start transaction
            DB::beginTransaction();

            try {
                foreach ($selectedSubmissions as $submission) {
                    $score = $request->input("scores.{$submission->id}");
                    
                    $submission->update([
                        'score' => $score,
                        'graded_at' => now(),
                        'graded_by' => $teacher->id
                    ]);
                }

                DB::commit();
                return redirect()->back()->with('success', 'Nilai berhasil disimpan untuk ' . $selectedSubmissions->count() . ' siswa.');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan saat menyimpan nilai: ' . $e->getMessage())
                    ->withInput();
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show assignment statistics.
     */    public function statistics(Assignment $assignment)
    {
        // Check teacher access
        $teacher = auth()->user();
        $hasAccess = $teacher->teacherSubjects()->where('subjects.id', $assignment->subject_id)->exists();
        if (!$hasAccess) {
            abort(403, 'Unauthorized');
        }

        // Load necessary relationships
        $assignment->load(['subject', 'classes', 'submissions']);
        
        // Get all active students from the assigned classes
        $studentsCount = User::whereHas('student', function($query) use ($assignment) {
            $query->whereIn('class_id', $assignment->classes->pluck('id'));
        })->count();
        
        // Log statistics data for debugging
        \Log::debug('Assignment statistics data', [
            'assignment_id' => $assignment->id,
            'title' => $assignment->title,
            'students_count' => $studentsCount,
            'submission_dates_count' => count($assignment->submissions->groupBy(function ($submission) {
                return $submission->created_at->format('Y-m-d');
            }))
        ]);
        
        // Calculate submission statistics
        $submissions = $assignment->submissions;
        $submissionsCount = $submissions->count();
        $lateSubmissions = $submissions->where('is_late', true)->count();
        $onTimeSubmissions = $submissionsCount - $lateSubmissions;
        $notSubmittedCount = $studentsCount - $submissionsCount;
        
        // Calculate submission rates
        $submissionRate = $studentsCount > 0 ? round(($submissionsCount / $studentsCount) * 100, 1) : 0;
        $lateSubmissionRate = $submissionsCount > 0 ? round(($lateSubmissions / $submissionsCount) * 100, 1) : 0;
        
        // Calculate grading statistics
        $gradedSubmissions = $submissions->whereNotNull('score');
        $gradedCount = $gradedSubmissions->count();
        $pendingCount = $submissionsCount - $gradedCount;
        $gradeRate = $submissionsCount > 0 ? round(($gradedCount / $submissionsCount) * 100, 1) : 0;
        
        // Calculate average score
        $averageScore = round($gradedSubmissions->avg('score') ?? 0, 1);
        
        // Calculate score distribution
        $scoreDistribution = [
            'a' => 0, // 80-100
            'b' => 0, // 70-79  
            'c' => 0, // 60-69
            'd' => 0, // 50-59
            'e' => 0  // 0-49
        ];
        
        foreach ($gradedSubmissions as $submission) {
            $score = $submission->score;
            if ($score >= 80) $scoreDistribution['a']++;
            elseif ($score >= 70) $scoreDistribution['b']++;
            elseif ($score >= 60) $scoreDistribution['c']++;
            elseif ($score >= 50) $scoreDistribution['d']++;
            else $scoreDistribution['e']++;
        }
        
        // Submission timeline (group by date)
        $submissionDates = $submissions->groupBy(function ($submission) {
            return $submission->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->count();
        })->toArray();        return view('guru.assignments.statistics', compact(
            'assignment',
            'studentsCount',
            'submissionsCount',
            'submissionRate',
            'lateSubmissions', 
            'lateSubmissionRate',
            'onTimeSubmissions',
            'notSubmittedCount',
            'gradedCount',
            'gradeRate',
            'pendingCount',
            'averageScore',
            'scoreDistribution',
            'submissionDates'
        ));
    }
    
    /**
     * Show alternative assignment statistics view.
     */
    public function statisticsAlt(Assignment $assignment)
    {
        // Reuse the same logic as the regular statistics method
        $teacher = auth()->user();
        $hasAccess = $teacher->teacherSubjects()->where('subjects.id', $assignment->subject_id)->exists();
        if (!$hasAccess) {
            abort(403, 'Unauthorized');
        }

        // Load relationships
        $assignment->load(['subject', 'classes', 'submissions']);
        
        // Get students count
        $studentsCount = User::whereHas('student', function($query) use ($assignment) {
            $query->whereIn('class_id', $assignment->classes->pluck('id'));
        })->count();
        
        // Calculate submission statistics
        $submissions = $assignment->submissions;
        $submissionsCount = $submissions->count();
        $lateSubmissions = $submissions->where('is_late', true)->count();
        $onTimeSubmissions = $submissionsCount - $lateSubmissions;
        $notSubmittedCount = $studentsCount - $submissionsCount;
        
        // Calculate submission rates
        $submissionRate = $studentsCount > 0 ? round(($submissionsCount / $studentsCount) * 100, 1) : 0;
        $lateSubmissionRate = $submissionsCount > 0 ? round(($lateSubmissions / $submissionsCount) * 100, 1) : 0;
        
        // Calculate grading statistics
        $gradedSubmissions = $submissions->whereNotNull('score');
        $gradedCount = $gradedSubmissions->count();
        $pendingCount = $submissionsCount - $gradedCount;
        $gradeRate = $submissionsCount > 0 ? round(($gradedCount / $submissionsCount) * 100, 1) : 0;
        
        // Calculate average score
        $averageScore = round($gradedSubmissions->avg('score') ?? 0, 1);
        
        // Calculate score distribution
        $scoreDistribution = [
            'a' => 0, // 80-100
            'b' => 0, // 70-79  
            'c' => 0, // 60-69
            'd' => 0, // 50-59
            'e' => 0  // 0-49
        ];
        
        foreach ($gradedSubmissions as $submission) {
            $score = $submission->score;
            if ($score >= 80) $scoreDistribution['a']++;
            elseif ($score >= 70) $scoreDistribution['b']++;
            elseif ($score >= 60) $scoreDistribution['c']++;
            elseif ($score >= 50) $scoreDistribution['d']++;
            else $scoreDistribution['e']++;
        }
        
        // Submission timeline (group by date)
        $submissionDates = $submissions->groupBy(function ($submission) {
            return $submission->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->count();
        })->toArray();

        // Render the alternative view
        return view('guru.assignments.statistics-alt', compact(
            'assignment',
            'studentsCount',
            'submissionsCount',
            'submissionRate',
            'lateSubmissions', 
            'lateSubmissionRate',
            'onTimeSubmissions',
            'notSubmittedCount',
            'gradedCount',
            'gradeRate',
            'pendingCount',
            'averageScore',
            'scoreDistribution',
            'submissionDates'
        ));
    }
    
    /**
     * Export assignment statistics to Excel
     */
    public function exportStatistics(Assignment $assignment)
    {
        // Security check
        if ($assignment->teacher_id !== Auth::id()) {
            return redirect()->route('guru.assignments.index')->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }

        // Calculate statistics (reuse from statistics method)
        $studentsCount = User::whereHas('student', function($query) use ($assignment) {
            $query->whereIn('class_id', $assignment->classes->pluck('id'));
        })->count();
        
        $submissions = $assignment->submissions;
        $submissionsCount = $submissions->count();
        $lateSubmissions = $submissions->where('is_late', true)->count();
        $onTimeSubmissions = $submissionsCount - $lateSubmissions;
        $notSubmittedCount = $studentsCount - $submissionsCount;
        
        $gradedSubmissions = $submissions->whereNotNull('score');
        $gradedCount = $gradedSubmissions->count();
        $pendingCount = $submissionsCount - $gradedCount;
        $averageScore = round($gradedSubmissions->avg('score') ?? 0, 1);

        $stats = [
            'studentsCount' => $studentsCount,
            'submissionsCount' => $submissionsCount,
            'lateSubmissions' => $lateSubmissions,
            'onTimeSubmissions' => $onTimeSubmissions,
            'notSubmittedCount' => $notSubmittedCount,
            'gradedCount' => $gradedCount,
            'pendingCount' => $pendingCount,
            'averageScore' => $averageScore
        ];

        return Excel::download(
            new \App\Exports\AssignmentStatisticsExport($assignment, $stats),
            'statistik-tugas-' . $assignment->id . '.xlsx'
        );
    }

    /**
     * Export assignment statistics to PDF
     */
    public function exportStatisticsPdf(Assignment $assignment)
    {
        // Security check  
        if ($assignment->teacher_id !== Auth::id()) {
            return redirect()->route('guru.assignments.index')->with('error', 'Anda tidak memiliki akses ke tugas ini.');
        }

        // Calculate statistics (reuse from statistics method)
        $assignment->load(['subject', 'classes', 'submissions']);
        
        $studentsCount = User::whereHas('student', function($query) use ($assignment) {
            $query->whereIn('class_id', $assignment->classes->pluck('id'));
        })->count();
        
        $submissions = $assignment->submissions;
        $submissionsCount = $submissions->count();
        $lateSubmissions = $submissions->where('is_late', true)->count();
        $onTimeSubmissions = $submissionsCount - $lateSubmissions;
        $notSubmittedCount = $studentsCount - $submissionsCount;
        
        $gradedSubmissions = $submissions->whereNotNull('score');
        $gradedCount = $gradedSubmissions->count();
        $pendingCount = $submissionsCount - $gradedCount;
        $averageScore = round($gradedSubmissions->avg('score') ?? 0, 1);

        $pdf = PDF::loadView('guru.assignments.statistics-pdf', [
            'assignment' => $assignment,
            'studentsCount' => $studentsCount,
            'submissionsCount' => $submissionsCount, 
            'lateSubmissions' => $lateSubmissions,
            'onTimeSubmissions' => $onTimeSubmissions,
            'notSubmittedCount' => $notSubmittedCount,
            'gradedCount' => $gradedCount,
            'pendingCount' => $pendingCount,
            'averageScore' => $averageScore
        ]);

        return $pdf->download('statistik-tugas-' . $assignment->id . '.pdf');
    }
}
