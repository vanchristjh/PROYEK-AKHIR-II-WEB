<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdditionalDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed materials
        $this->seedMaterials();
        
        // Seed assignments
        $this->seedAssignments();
        
        // Seed quizzes
        $this->seedQuizzes();
        
        // Seed announcements if not already seeded
        $this->seedAnnouncements();
        
        // Seed schedules if not already seeded
        $this->seedSchedules();
    }
    
    /**
     * Seed materials data
     */
    private function seedMaterials()
    {
        if (!Schema::hasTable('materials')) {
            echo "Materials table does not exist. Skipping materials seeding.\n";
            return;
        }
        
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get();
        $classes = SchoolClass::all();
        
        if ($subjects->isEmpty() || $teachers->isEmpty() || $classes->isEmpty()) {
            echo "Missing required data for materials. Skipping materials seeding.\n";
            return;
        }
        
        $materialTypes = ['PDF', 'Document', 'Video', 'Presentation', 'Link'];
        $materialTitles = [
            'Pengenalan Konsep Dasar',
            'Materi Latihan Soal',
            'Rangkuman Bab 1',
            'Bahan Ujian Tengah Semester',
            'Materi Pengayaan'
        ];
        
        $materialDescriptions = [
            'Materi ini berisi konsep dasar dari topik yang dipelajari',
            'Kumpulan latihan soal untuk persiapan ujian',
            'Rangkuman materi bab 1 untuk memudahkan siswa belajar',
            'Materi yang akan diujikan pada UTS',
            'Materi tambahan untuk memperdalam pemahaman'
        ];
        
        foreach ($subjects as $subject) {
            $teacher = $teachers->random();
            
            // Assign 2 materials per subject
            for ($i = 0; $i < 2; $i++) {
                $typeIndex = rand(0, count($materialTypes) - 1);
                $titleIndex = rand(0, count($materialTitles) - 1);
                $descIndex = rand(0, count($materialDescriptions) - 1);
                  $materialId = DB::table('materials')->insertGetId([
                    'title' => $materialTitles[$titleIndex] . ' ' . $subject->name,
                    'description' => $materialDescriptions[$descIndex],
                    'attachment_path' => 'documents/materials/sample_' . strtolower(str_replace(' ', '_', $subject->name)) . '_' . ($i + 1) . '.pdf',
                    'teacher_id' => $teacher->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                    'updated_at' => Carbon::now(),
                    'subject_id' => $subject->id,
                    'publish_date' => Carbon::now(),
                    'is_active' => true
                ]);
                  // If classroom_material table exists, assign it to some classrooms
                if (Schema::hasTable('classroom_material')) {
                    // Get classrooms rather than classes
                    $classrooms = DB::table('classrooms')->inRandomOrder()->limit(rand(1, 3))->get();
                    
                    if ($classrooms->count() > 0) {
                        foreach ($classrooms as $classroom) {
                            DB::table('classroom_material')->insert([
                                'material_id' => $materialId,
                                'classroom_id' => $classroom->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                    }
                }
            }
        }
        
        echo "Materials seeded successfully!\n";
    }
    
    /**
     * Seed assignments data
     */
    private function seedAssignments()
    {
        if (!Schema::hasTable('assignments')) {
            echo "Assignments table does not exist. Skipping assignments seeding.\n";
            return;
        }
        
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get();
        $classes = SchoolClass::all();
        
        if ($subjects->isEmpty() || $teachers->isEmpty() || $classes->isEmpty()) {
            echo "Missing required data for assignments. Skipping assignments seeding.\n";
            return;
        }
        
        $assignmentTypes = ['Homework', 'Project', 'Essay', 'Practice'];
        
        $titles = [
            'Tugas Harian',
            'Proyek Kelompok',
            'Latihan Soal',
            'Essay Refleksi',
            'Tugas Praktikum'
        ];
        
        $descriptions = [
            'Kerjakan soal latihan pada halaman 45-50 buku paket.',
            'Buatlah proyek kelompok dengan tema yang sudah ditentukan. Presentasi akan dilaksanakan minggu depan.',
            'Selesaikan latihan soal berikut untuk mempersiapkan ujian.',
            'Tulislah esai reflektif tentang materi yang telah dipelajari.',
            'Lakukan praktikum sesuai dengan petunjuk dan kumpulkan laporannya.'
        ];
        
        foreach ($subjects as $subject) {
            $teacher = $teachers->random();
            
            // Create 2-3 assignments per subject
            for ($i = 0; $i < rand(2, 3); $i++) {
                $typeIndex = rand(0, count($assignmentTypes) - 1);
                $titleIndex = rand(0, count($titles) - 1);
                $descIndex = rand(0, count($descriptions) - 1);
                
                $dueDate = Carbon::now()->addDays(rand(3, 14));
                  $assignmentData = [
                    'title' => $titles[$titleIndex] . ' - ' . $subject->name,
                    'description' => $descriptions[$descIndex],
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 7)),
                    'updated_at' => Carbon::now(),
                    'visibility' => 'visible',
                    'is_draft' => false,
                    'published_at' => Carbon::now()
                ];
                
                // Check for specific column names that might be used for the due date
                if (Schema::hasColumn('assignments', 'due_date')) {
                    $assignmentData['due_date'] = $dueDate;
                } else if (Schema::hasColumn('assignments', 'deadline')) {
                    $assignmentData['deadline'] = $dueDate;
                }
                
                // Check for max score field
                if (Schema::hasColumn('assignments', 'max_score')) {
                    $assignmentData['max_score'] = 100;
                }
                
                // Check for type field
                if (Schema::hasColumn('assignments', 'type')) {
                    $assignmentData['type'] = $assignmentTypes[$typeIndex];
                }
                
                $assignmentId = DB::table('assignments')->insertGetId($assignmentData);
                  // If there's a pivot table for assignment-class relationship
                if (Schema::hasTable('assignment_class')) {
                    // Get school_classes for assignment_class table
                    $targetClasses = $classes->random(min(rand(1, 3), $classes->count()));
                    
                    foreach ($targetClasses as $class) {
                        DB::table('assignment_class')->insert([
                            'assignment_id' => $assignmentId,
                            'class_id' => $class->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                } else if (Schema::hasTable('classroom_assignment')) {
                    // Get classrooms for classroom_assignment table
                    $classrooms = DB::table('classrooms')->inRandomOrder()->limit(rand(1, 3))->get();
                    
                    if ($classrooms->count() > 0) {
                        foreach ($classrooms as $classroom) {
                            DB::table('classroom_assignment')->insert([
                                'assignment_id' => $assignmentId,
                                'classroom_id' => $classroom->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                    }
                }
            }
        }
        
        echo "Assignments seeded successfully!\n";
    }
    
    /**
     * Seed quizzes data
     */
    private function seedQuizzes()
    {
        if (!Schema::hasTable('quizzes')) {
            echo "Quizzes table does not exist. Skipping quizzes seeding.\n";
            return;
        }
        
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get();
        $classes = SchoolClass::all();
        
        if ($subjects->isEmpty() || $teachers->isEmpty() || $classes->isEmpty()) {
            echo "Missing required data for quizzes. Skipping quizzes seeding.\n";
            return;
        }
        
        $quizTypes = ['Multiple Choice', 'Essay', 'Mixed'];
        
        $titles = [
            'Quiz Harian',
            'Ulangan Singkat',
            'Tes Pemahaman Materi',
            'Evaluasi Bab 1',
            'Latihan Interaktif'
        ];
        
        foreach ($subjects as $subject) {
            $teacher = $teachers->random();
            
            // Create 1-2 quizzes per subject
            for ($i = 0; $i < rand(1, 2); $i++) {
                $typeIndex = rand(0, count($quizTypes) - 1);
                $titleIndex = rand(0, count($titles) - 1);
                
                $startDate = Carbon::now()->addDays(rand(1, 7));
                $endDate = (clone $startDate)->addHours(2);
                
                $quizData = [
                    'title' => $titles[$titleIndex] . ' - ' . $subject->name,
                    'description' => 'Quiz untuk mengukur pemahaman materi ' . $subject->name,
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 7)),
                    'updated_at' => Carbon::now(),
                    'is_active' => true,
                    'duration' => 60,
                    'max_attempts' => 1,
                    'randomize_questions' => rand(0, 1),
                    'show_result' => true,
                    'passing_score' => 70
                ];
                
                // Check for specific column names that might be used
                if (Schema::hasColumn('quizzes', 'start_time')) {
                    $quizData['start_time'] = $startDate;
                }
                
                if (Schema::hasColumn('quizzes', 'end_time')) {
                    $quizData['end_time'] = $endDate;
                }
                
                $quizId = DB::table('quizzes')->insertGetId($quizData);
                
                // Add questions for this quiz
                $this->seedQuestionsForQuiz($quizId, $subject->name);
            }
        }
        
        echo "Quizzes seeded successfully!\n";
    }
    
    /**
     * Seed questions for a quiz
     */
    private function seedQuestionsForQuiz($quizId, $subjectName)
    {
        if (!Schema::hasTable('questions')) {
            echo "Questions table does not exist. Skipping questions seeding.\n";
            return;
        }
        
        $questionTypes = ['multiple_choice', 'essay'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        $questionContents = [
            'Matematika' => [
                'Berapakah hasil dari persamaan 2x + 5 = 15?',
                'Tentukan luas lingkaran dengan jari-jari 7 cm.',
                'Tentukan rumus umum untuk barisan aritmatika.',
                'Selesaikan sistem persamaan: 3x + 2y = 12 dan x - y = 1.',
                'Hitunglah integral dari fungsi f(x) = 2x^2 + 3x - 4.'
            ],
            'Bahasa Indonesia' => [
                'Jelaskan pengertian dari majas personifikasi.',
                'Berikut ini yang termasuk kalimat efektif adalah...',
                'Apa perbedaan antara kalimat aktif dan kalimat pasif?',
                'Jelaskan makna dari peribahasa "Air beriak tanda tak dalam".',
                'Tentukan ide pokok dari paragraf berikut.'
            ],
            'Bahasa Inggris' => [
                'Complete the sentence: "If I ____ rich, I would buy a mansion."',
                'What is the passive form of "They are building a house"?',
                'Identify the correct use of Present Perfect Tense.',
                'Choose the correct preposition: "I am afraid ____ spiders."',
                'What is the meaning of the idiomatic expression "break a leg"?'
            ],
            'Fisika' => [
                'Hitunglah gaya yang diperlukan untuk menggerakkan benda bermassa 5 kg dengan percepatan 2 m/s².',
                'Jelaskan prinsip kerja dari transformator.',
                'Berapakah frekuensi gelombang dengan panjang gelombang 2 meter dan kecepatan 4 m/s?',
                'Jelaskan perbedaan antara gerak lurus beraturan dan gerak lurus berubah beraturan.',
                'Hitunglah energi kinetik dari benda bermassa 3 kg yang bergerak dengan kecepatan 4 m/s.'
            ],
            'Biologi' => [
                'Sebutkan bagian-bagian dari sel hewan.',
                'Jelaskan proses fotosintesis pada tumbuhan.',
                'Apa perbedaan antara DNA dan RNA?',
                'Jelaskan fungsi dari sistem endokrin pada manusia.',
                'Sebutkan tahapan-tahapan dari siklus sel.'
            ]
        ];
        
        // Default to generic questions if subject isn't in our list
        $defaultQuestions = [
            'Jelaskan konsep dasar dari materi ini.',
            'Berikut ini adalah contoh dari penerapan teori yang baru dipelajari...',
            'Hitunglah hasil dari permasalahan berikut.',
            'Bandingkan dan bedakan konsep A dan konsep B.',
            'Apa yang dimaksud dengan istilah X dalam konteks materi ini?'
        ];
        
        // Select questions for this subject, or use default
        $questionList = $questionContents[$subjectName] ?? $defaultQuestions;
        
        // Create 5-10 questions for each quiz
        $questionCount = rand(5, 10);
        for ($i = 0; $i < $questionCount; $i++) {
            $questionType = $questionTypes[rand(0, count($questionTypes) - 1)];
            $questionContent = $questionList[rand(0, count($questionList) - 1)];
            $difficultyLevel = $difficultyLevels[rand(0, count($difficultyLevels) - 1)];
            
            $questionData = [
                'quiz_id' => $quizId,
                'content' => $questionContent,
                'question_type' => $questionType,
                'points' => rand(1, 5),
                'difficulty_level' => $difficultyLevel,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            
            if ($questionType == 'essay') {
                // For essay questions, we might provide a sample answer
                $questionData['correct_answer'] = 'Jawaban contoh untuk pertanyaan esai ini.';
                $questionData['explanation'] = 'Penjelasan dari jawaban yang benar.';
                
                $questionId = DB::table('questions')->insertGetId($questionData);
            } else {
                // For multiple choice, we don't fill the correct_answer field because
                // it will be indicated in the options table
                $questionId = DB::table('questions')->insertGetId($questionData);
                
                // Add 4 options for multiple choice questions
                $this->seedOptionsForQuestion($questionId);
            }
        }
    }
    
    /**
     * Seed options for a multiple-choice question
     */
    private function seedOptionsForQuestion($questionId)
    {
        if (!Schema::hasTable('options')) {
            echo "Options table does not exist. Skipping options seeding.\n";
            return;
        }
        
        $options = [
            'Pilihan A',
            'Pilihan B',
            'Pilihan C',
            'Pilihan D'
        ];
        
        // Pick one option as correct answer (randomly)
        $correctAnswerIndex = rand(0, count($options) - 1);
        
        for ($i = 0; $i < count($options); $i++) {
            DB::table('options')->insert([
                'question_id' => $questionId,
                'content' => $options[$i],
                'is_correct' => ($i == $correctAnswerIndex) ? 1 : 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
    
    /**
     * Seed announcements data if not already seeded
     */
    private function seedAnnouncements()
    {
        if (!Schema::hasTable('announcements')) {
            echo "Announcements table does not exist. Skipping announcements seeding.\n";
            return;
        }
        
        // Check if announcements already exist
        $count = DB::table('announcements')->count();
        if ($count > 0) {
            echo "Announcements already seeded. Skipping.\n";
            return;
        }
        
        $teachers = User::where('role_id', 2)->get();
        $classes = SchoolClass::all();
        
        if ($teachers->isEmpty() || $classes->isEmpty()) {
            echo "Missing required data for announcements. Skipping announcements seeding.\n";
            return;
        }
        
        $announcementTitles = [
            'Pengumuman Ujian Tengah Semester',
            'Jadwal Remedial',
            'Info Kegiatan Sekolah',
            'Perubahan Jadwal Pelajaran',
            'Pengumuman Libur Sekolah',
            'Jadwal Konsultasi Guru',
            'Informasi Kegiatan Ekstrakurikuler'
        ];
        
        $announcementContents = [
            'Ujian tengah semester akan dilaksanakan pada tanggal 15-20 Juni 2025. Harap seluruh siswa mempersiapkan diri dengan baik.',
            'Bagi siswa yang belum mencapai nilai KKM, remedial akan dilaksanakan pada hari Sabtu, 27 Juni 2025.',
            'Kegiatan ekstrakurikuler akan diadakan setiap hari Jumat setelah jam sekolah. Harap siswa dapat berpartisipasi.',
            'Terjadi perubahan jadwal pelajaran untuk minggu depan. Silakan cek di papan pengumuman sekolah.',
            'Sekolah akan libur pada tanggal 5 Juni 2025 sehubungan dengan hari raya nasional.',
            'Jadwal konsultasi dengan guru mata pelajaran tersedia setiap hari Selasa dan Kamis setelah jam sekolah.',
            'Pendaftaran untuk kegiatan ekstrakurikuler akan dibuka mulai tanggal 10 Juni 2025.'
        ];
        
        foreach ($teachers as $teacher) {
            // Create 1-2 announcements per teacher
            for ($i = 0; $i < rand(1, 2); $i++) {
                $titleIndex = rand(0, count($announcementTitles) - 1);
                $contentIndex = rand(0, count($announcementContents) - 1);
                
                // Choose random classes to target (1 to 3 classes)
                $targetClassCount = min(rand(1, 3), $classes->count());
                $targetClasses = $classes->random($targetClassCount);
                $targetClassIds = $targetClasses->pluck('id')->toArray();
                
                $announcementId = DB::table('announcements')->insertGetId([
                    'title' => $announcementTitles[$titleIndex],
                    'content' => $announcementContents[$contentIndex],
                    'author_id' => $teacher->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 14)),
                    'updated_at' => Carbon::now()
                ]);
                  // Check if announcement_class table exists
                if (Schema::hasTable('announcement_class')) {
                    // Check if class_id column exists in announcement_class
                    if (Schema::hasColumn('announcement_class', 'class_id')) {
                        // Assign announcement to target classes
                        foreach ($targetClassIds as $classId) {
                            DB::table('announcement_class')->insert([
                                'announcement_id' => $announcementId,
                                'class_id' => $classId,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                    } else if (Schema::hasColumn('announcement_class', 'classroom_id')) {
                        // Get classrooms instead
                        $classrooms = DB::table('classrooms')->inRandomOrder()->limit(rand(1, 3))->get();
                    
                        if ($classrooms->count() > 0) {
                            foreach ($classrooms as $classroom) {
                                DB::table('announcement_class')->insert([
                                    'announcement_id' => $announcementId,
                                    'classroom_id' => $classroom->id,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ]);
                            }
                        }
                    }
                }
            }
        }
        
        echo "Announcements seeded successfully!\n";
    }
    
    /**
     * Seed schedules data if not already seeded
     */    private function seedSchedules()
    {
        if (!Schema::hasTable('schedules')) {
            echo "Schedules table does not exist. Skipping schedules seeding.\n";
            return;
        }
        
        // Check if schedules already exist
        $count = DB::table('schedules')->count();
        if ($count > 0) {
            echo "Schedules already seeded. Skipping.\n";
            return;
        }
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        // First, check if we have classrooms
        $classroomsCount = DB::table('classrooms')->count();
        if ($classroomsCount == 0) {
            // Create some default classrooms
            echo "No classrooms found, creating default classrooms...\n";
            for ($i = 1; $i <= 3; $i++) {
                DB::table('classrooms')->insert([
                    'name' => 'Class ' . $i,
                    'grade_level' => $i,
                    'academic_year' => '2024/2025',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            echo "Created 3 default classrooms.\n";
        }
        
        // Get classroom IDs
        $classrooms = DB::table('classrooms')->get();
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get();
          if ($classrooms->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty()) {
            echo "Missing required data for schedules. Skipping schedules seeding.\n";
            return;
        }
        
        foreach ($classrooms as $classroom) {
            foreach ($days as $day) {
                // Create 4 sessions per day
                for ($session = 1; $session <= 4; $session++) {
                    // Randomize subjects and teachers
                    $subject = $subjects->random();
                    $teacher = $teachers->random();
                    
                    // Start times by session
                    $startTimes = ['07:30:00', '09:30:00', '11:30:00', '13:30:00'];                    // End times by session (90 minutes per session)
                    $endTimes = ['09:00:00', '11:00:00', '13:00:00', '15:00:00'];
                    
                    $scheduleData = [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'school_year' => '2024/2025', // Add the school_year field
                        'day' => $day,
                        'start_time' => $startTimes[$session - 1],
                        'end_time' => $endTimes[$session - 1]
                    ];
                    
                    // Get valid classroom IDs from the classrooms table
                    $validClassrooms = DB::table('classrooms')->pluck('id')->toArray();
                    
                    if (empty($validClassrooms)) {
                        echo "No valid classrooms found. Skipping schedules seeding.\n";
                        return;
                    }
                    
                    // Use a valid classroom ID
                    $classroomId = $validClassrooms[array_rand($validClassrooms)];
                    
                    // Handle different column names
                    if (Schema::hasColumn('schedules', 'class_id')) {
                        $scheduleData['class_id'] = $class->id; // Using school class ID
                    } else if (Schema::hasColumn('schedules', 'classroom_id')) {
                        $scheduleData['classroom_id'] = $classroomId; // Using valid classroom ID
                    }
                    
                    if (Schema::hasColumn('schedules', 'day')) {
                        $scheduleData['day'] = $day;
                    }
                    
                    if (Schema::hasColumn('schedules', 'session')) {
                        $scheduleData['session'] = $session;
                    }
                    
                    if (Schema::hasColumn('schedules', 'start_time')) {
                        $scheduleData['start_time'] = $startTimes[$session - 1];
                    }
                    
                    if (Schema::hasColumn('schedules', 'end_time')) {
                        $scheduleData['end_time'] = $endTimes[$session - 1];
                    }
                    
                    DB::table('schedules')->insert($scheduleData);
                }
            }
        }
        
        echo "Schedules seeded successfully!\n";
    }
}
