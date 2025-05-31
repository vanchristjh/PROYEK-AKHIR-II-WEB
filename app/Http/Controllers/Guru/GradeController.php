<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * Display grades for assignments and direct assessments
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $teacherId = $teacher->id;
        
        // Get teacher's subjects
        $subjects = $teacher->teacherSubjects;
        
        // Get classrooms the teacher has access to
        $classrooms = $teacher->teachingClassrooms;
        
        // Base query for direct grades
        $directGradesQuery = Grade::with(['student', 'subject', 'classroom'])
                                ->where('teacher_id', $teacherId);
        
        // Get submission-based grades
        $submissionsQuery = Submission::with(['student', 'assignment.subject'])
                                ->whereHas('assignment', function($query) use ($teacherId) {
                                    $query->where('teacher_id', $teacherId);
                                });
        
        // Apply filters if provided
        if ($request->filled('subject')) {
            $subjectId = $request->subject;
            $directGradesQuery->where('subject_id', $subjectId);
            $submissionsQuery->whereHas('assignment', function($query) use ($subjectId) {
                $query->where('subject_id', $subjectId);
            });
        }
        
        if ($request->filled('classroom')) {
            $classroomId = $request->classroom;
            $directGradesQuery->where('classroom_id', $classroomId);
            $submissionsQuery->whereHas('student', function($query) use ($classroomId) {
                $query->where('classroom_id', $classroomId);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'graded') {
                $submissionsQuery->whereNotNull('score');
            } elseif ($request->status === 'ungraded') {
                $submissionsQuery->whereNull('score');
            }
        }
        
        // Get paginated results
        $directGrades = $directGradesQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'direct_page');
        $submissions = $submissionsQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'submissions_page');
        
        return view('guru.grades.index', compact('subjects', 'classrooms', 'directGrades', 'submissions'));
    }

    /**
     * Show the form for creating a new grade
     */
    public function create()
    {
        $teacher = Auth::user();
        $subjects = $teacher->teacherSubjects;
        $classrooms = $teacher->teachingClassrooms;
        
        return view('guru.grades.create', compact('subjects', 'classrooms'));
    }

    /**
     * Store a newly created grade
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'student_id' => 'required|exists:users,id',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'type' => 'required|in:tugas,ulangan,ujian,keterampilan,sikap',
            'feedback' => 'nullable|string|max:500',
            'semester' => 'required|in:1,2',
            'academic_year' => 'required|string'
        ]);

        // Verify teacher has access to subject and classroom
        $teacher = Auth::user();
        $hasAccess = $teacher->teacherSubjects()
            ->where('subjects.id', $request->subject_id)
            ->whereHas('classrooms', function($query) use ($request) {
                $query->where('classrooms.id', $request->classroom_id);
            })
            ->exists();

        if (!$hasAccess) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menilai mata pelajaran ini di kelas yang dipilih.');
        }

        // Verify student is in the selected classroom
        $student = User::findOrFail($request->student_id);
        if ($student->classroom_id != $request->classroom_id) {
            return back()->with('error', 'Siswa tidak terdaftar di kelas yang dipilih.');
        }

        try {
            DB::beginTransaction();

            $grade = Grade::create([
                'student_id' => $request->student_id,
                'teacher_id' => $teacher->id,
                'subject_id' => $request->subject_id,
                'classroom_id' => $request->classroom_id,
                'score' => $request->score,
                'max_score' => $request->max_score,
                'type' => $request->type,
                'feedback' => $request->feedback,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year
            ]);

            DB::commit();

            return redirect()->route('guru.grades.index')
                ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan penilaian.');
        }
    }

    /**
     * Show the form for editing a grade
     */
    public function edit(Grade $grade)
    {
        // Verify ownership
        if ($grade->teacher_id !== Auth::id()) {
            return redirect()->route('guru.grades.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengubah penilaian ini.');
        }

        return view('guru.grades.edit', compact('grade'));
    }

    /**
     * Update the specified grade
     */
    public function update(Request $request, Grade $grade)
    {
        // Verify ownership
        if ($grade->teacher_id !== Auth::id()) {
            return redirect()->route('guru.grades.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengubah penilaian ini.');
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . $grade->max_score,
            'feedback' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $grade->update([
                'score' => $request->score,
                'feedback' => $request->feedback
            ]);

            DB::commit();

            return redirect()->route('guru.grades.index')
                ->with('success', 'Penilaian berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui penilaian.');
        }
    }

    /**
     * Remove the specified grade
     */
    public function destroy(Grade $grade)
    {
        // Verify ownership
        if ($grade->teacher_id !== Auth::id()) {
            return redirect()->route('guru.grades.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus penilaian ini.');
        }

        try {
            DB::beginTransaction();
            $grade->delete();
            DB::commit();

            return redirect()->route('guru.grades.index')
                ->with('success', 'Penilaian berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus penilaian.');
        }
    }

    /**
     * Get students for a specific classroom.
     * Used by AJAX in the create form.
     */    public function getClassroomStudents(Classroom $classroom)
    {
        try {
            // Log debug information
            \Log::debug('[getClassroomStudents] Classroom:', ['id' => $classroom->id, 'name' => $classroom->name]);
            
            // Check if teacher has access to this classroom
            $teacher = Auth::user();
            $hasAccess = $teacher->teachingClassrooms()
                ->where('classrooms.id', $classroom->id)
                ->exists();
                
            if (!$hasAccess) {
                \Log::warning('[getClassroomStudents] Teacher does not have access to classroom', [
                    'teacher_id' => $teacher->id,
                    'classroom_id' => $classroom->id
                ]);
                return response()->json(['error' => 'Unauthorized access to classroom'], 403);
            }
            
            // Get all students in the classroom
            $students = $classroom->students()
                ->select('users.id', 'users.name', 'users.nis')
                ->orderBy('name')
                ->get();
            
            \Log::debug('[getClassroomStudents] Found students:', ['count' => $students->count(), 'students' => $students]);
                
            return response()->json($students);
        } catch (\Exception $e) {
            \Log::error('[getClassroomStudents] Error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error loading students: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get teacher's classrooms for a specific subject.
     * Used by AJAX in the create form.
     */    public function getTeacherClassrooms(Subject $subject)
    {
        try {
            $teacher = Auth::user();
            
            // Log debug information
            \Log::debug('[getTeacherClassrooms] Subject:', ['id' => $subject->id, 'name' => $subject->name]);
            \Log::debug('[getTeacherClassrooms] Teacher:', ['id' => $teacher->id, 'name' => $teacher->name]);
            
            // Get all teacher's teaching classrooms
            $classrooms = $teacher->teachingClassrooms()
                ->whereHas('subjects', function($query) use ($subject) {
                    $query->where('subjects.id', $subject->id);
                })
                ->select('classrooms.id', 'classrooms.name')
                ->orderBy('name')
                ->get();
            
            \Log::debug('[getTeacherClassrooms] Found classrooms:', ['count' => $classrooms->count(), 'classrooms' => $classrooms]);
                
            return response()->json($classrooms);
        } catch (\Exception $e) {
            \Log::error('[getTeacherClassrooms] Error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error loading classrooms: ' . $e->getMessage()], 500);
        }
    }
}
