<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles
        $this->seedRoles();
        
        // Users (admins, teachers, students)
        $this->seedUsers();
        
        // School Classes
        $this->seedSchoolClasses();
        
        // Subjects
        $this->seedSubjects();
        
        // SubjectTeacher relationships
        $this->seedSubjectTeacher();
        
        // Schedules
        $this->seedSchedules();
        
        // Assignments
        $this->seedAssignments();
        
        // Quizzes
        $this->seedQuizzes();
        
        // Attendance Records
        $this->seedAttendance();
        
        // Announcements
        $this->seedAnnouncements();
    }
    
    /**
     * Seed roles data
     */
    private function seedRoles()
    {
        $roles = [
            ['name' => 'admin', 'slug' => 'admin', 'description' => 'Administrator'],
            ['name' => 'teacher', 'slug' => 'guru', 'description' => 'Teacher'],
            ['name' => 'student', 'slug' => 'siswa', 'description' => 'Student'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                [
                    'slug' => $role['slug'],
                    'description' => $role['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        echo "Roles seeded successfully!\n";
    }
    
    /**
     * Seed users data
     */    private function seedUsers()
    {
        // Get role IDs
        $adminRole = Role::where('slug', 'admin')->first();
        $teacherRole = Role::where('slug', 'guru')->first();
        $studentRole = Role::where('slug', 'siswa')->first();
        
        // Create admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Sekolah',
                'email' => 'admin@sekolah.ac.id',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'id_number' => 'ADMIN001'
            ]
        );
        
        // Create teacher users
        $teachers = [
            [
                'username' => 'budi_teacher',
                'name' => 'Budi Santoso',
                'email' => 'budi@sekolah.ac.id',
                'nip' => '19850101001',
                'gender' => 'L',
                'phone' => '081234567891',
                'address' => 'Jl. Guru No. 1, Jakarta',
                'birth_date' => '1985-05-15',
                'birth_place' => 'Bandung',
                'subjects' => ['Matematika', 'Fisika']
            ],
            [
                'username' => 'siti_teacher',
                'name' => 'Siti Rahayu',
                'email' => 'siti@sekolah.ac.id',
                'nip' => '19860202002',
                'gender' => 'P',
                'phone' => '081234567892',
                'address' => 'Jl. Guru No. 2, Jakarta',
                'birth_date' => '1986-02-20',
                'birth_place' => 'Surabaya',
                'subjects' => ['Bahasa Indonesia', 'Bahasa Inggris']
            ],
            [
                'username' => 'ahmad_teacher',
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad@sekolah.ac.id',
                'nip' => '19870303003',
                'gender' => 'L',
                'phone' => '081234567893',
                'address' => 'Jl. Guru No. 3, Jakarta',
                'birth_date' => '1987-03-25',
                'birth_place' => 'Medan',
                'subjects' => ['Kimia', 'Biologi']
            ],
            [
                'username' => 'maya_teacher',
                'name' => 'Maya Wijaya',
                'email' => 'maya@sekolah.ac.id',
                'nip' => '19880404004',
                'gender' => 'P',
                'phone' => '081234567894',
                'address' => 'Jl. Guru No. 4, Jakarta',
                'birth_date' => '1988-04-10',
                'birth_place' => 'Yogyakarta',
                'subjects' => ['Sejarah', 'Geografi']
            ],
        ];
        
        foreach ($teachers as $teacher) {
            User::updateOrCreate(
                ['username' => $teacher['username']],
                [
                    'name' => $teacher['name'],
                    'email' => $teacher['email'],
                    'password' => Hash::make('password'),
                    'role_id' => $teacherRole->id,
                    'nip' => $teacher['nip'],
                    'gender' => $teacher['gender'],
                    'phone' => $teacher['phone'],
                    'address' => $teacher['address'],
                    'birth_date' => $teacher['birth_date'],
                    'birth_place' => $teacher['birth_place'],
                    'status' => 'active'
                ]
            );
            
            // Also create teacher entry in teachers table if it exists
            if (Schema::hasTable('teachers')) {
                $user = User::where('username', $teacher['username'])->first();
                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $teacher['nip'],
                        'name' => $teacher['name'],
                        'email' => $teacher['email'],
                        'address' => $teacher['address'],
                        'phone' => $teacher['phone'],
                    ]
                );
            }
        }
        
        // Create student users - 5 students per class (10X, 11X, 12X)
        $classes = ['10A', '10B', '11A', '11B', '12A', '12B'];
        $counter = 1;
        
        foreach ($classes as $class) {
            for ($i = 1; $i <= 5; $i++) {
                $gender = $i % 2 === 0 ? 'P' : 'L';
                $firstName = $gender === 'L' ? 
                    ['Andi', 'Bimo', 'Candra', 'Dimas', 'Eko'][$i-1] : 
                    ['Ani', 'Bunga', 'Citra', 'Dewi', 'Eka'][$i-1];
                
                $lastName = ['Pratama', 'Wijaya', 'Saputra', 'Nugraha', 'Kusuma'][$i-1];
                $fullName = $firstName . ' ' . $lastName;
                $username = strtolower($firstName) . '_' . strtolower($lastName);
                
                $user = User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $fullName,
                        'email' => strtolower($firstName . $counter) . '@student.sekolah.ac.id',
                        'password' => Hash::make('password'),
                        'role_id' => $studentRole->id,
                        'nisn' => '10' . str_pad($counter, 8, '0', STR_PAD_LEFT),
                        'gender' => $gender,
                        'phone' => '08' . rand(1000000000, 9999999999),
                        'address' => 'Jl. Siswa No. ' . $counter . ', Jakarta',
                        'birth_date' => rand(2005, 2008) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                        'birth_place' => ['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Medan'][rand(0, 4)],
                        'status' => 'active'
                    ]
                );
                
                // Also create student entry in students table if it exists
                if (Schema::hasTable('students')) {
                    $classObj = SchoolClass::where('name', $class)->first();
                    
                    if ($classObj) {
                        Student::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'nis' => '10' . str_pad($counter, 6, '0', STR_PAD_LEFT),
                                'name' => $fullName,
                                'gender' => $gender,
                                'class_id' => $classObj->id,
                                'birth_date' => $user->birth_date,
                                'address' => $user->address,
                                'phone_number' => $user->phone,
                                'email' => $user->email,
                                'status' => 'active'
                            ]
                        );
                    }
                }
                
                $counter++;
            }
        }
        
        echo "Users seeded successfully!\n";
    }
    
    /**
     * Seed school classes data
     */
    private function seedSchoolClasses()
    {
        $classes = [
            ['name' => '10A', 'grade' => 10, 'year' => 2024],
            ['name' => '10B', 'grade' => 10, 'year' => 2024],
            ['name' => '11A', 'grade' => 11, 'year' => 2024],
            ['name' => '11B', 'grade' => 11, 'year' => 2024],
            ['name' => '12A', 'grade' => 12, 'year' => 2024],
            ['name' => '12B', 'grade' => 12, 'year' => 2024],
        ];
        
        foreach ($classes as $class) {
            SchoolClass::updateOrCreate(
                ['name' => $class['name']],
                [
                    'grade' => $class['grade'],
                    'year' => $class['year'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        echo "School Classes seeded successfully!\n";
    }
    
    /**
     * Seed subjects data
     */
    private function seedSubjects()
    {
        $subjects = [
            ['code' => 'MAT', 'name' => 'Matematika', 'description' => 'Mata pelajaran matematika'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'description' => 'Mata pelajaran bahasa Indonesia'],
            ['code' => 'BIG', 'name' => 'Bahasa Inggris', 'description' => 'Mata pelajaran bahasa Inggris'],
            ['code' => 'FIS', 'name' => 'Fisika', 'description' => 'Mata pelajaran fisika'],
            ['code' => 'KIM', 'name' => 'Kimia', 'description' => 'Mata pelajaran kimia'],
            ['code' => 'BIO', 'name' => 'Biologi', 'description' => 'Mata pelajaran biologi'],
            ['code' => 'SEJ', 'name' => 'Sejarah', 'description' => 'Mata pelajaran sejarah'],
            ['code' => 'GEO', 'name' => 'Geografi', 'description' => 'Mata pelajaran geografi'],
        ];
        
        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['code' => $subject['code']],
                [
                    'name' => $subject['name'],
                    'description' => $subject['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        echo "Subjects seeded successfully!\n";
    }
    
    /**
     * Seed subject-teacher relationships
     */
    private function seedSubjectTeacher()
    {
        $teacherSubjects = [
            'budi_teacher' => ['MAT', 'FIS'],
            'siti_teacher' => ['BIN', 'BIG'],
            'ahmad_teacher' => ['KIM', 'BIO'],
            'maya_teacher' => ['SEJ', 'GEO'],
        ];
        
        foreach ($teacherSubjects as $username => $subjectCodes) {
            $teacher = User::where('username', $username)->first();
            
            if ($teacher) {
                foreach ($subjectCodes as $code) {
                    $subject = Subject::where('code', $code)->first();
                    
                    if ($subject) {
                        DB::table('subject_teacher')->updateOrInsert(
                            ['teacher_id' => $teacher->id, 'subject_id' => $subject->id],
                            [
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                    }
                }
            }
        }
        
        echo "Subject-Teacher relationships seeded successfully!\n";
    }
    
    /**
     * Seed schedules data
     */
    private function seedSchedules()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $classes = SchoolClass::all();
        $subjects = Subject::all();
        $teachers = User::where('role_id', 2)->get(); // Role ID 2 for teachers
        
        foreach ($classes as $class) {
            foreach ($days as $dayIndex => $day) {
                // Create 4 sessions per day
                for ($session = 1; $session <= 4; $session++) {
                    // Randomize subjects and teachers
                    $subjectIndex = rand(0, count($subjects) - 1);
                    $teacherIndex = rand(0, count($teachers) - 1);
                    
                    // Start times by session
                    $startTimes = ['07:30:00', '09:30:00', '11:30:00', '13:30:00'];
                    // End times by session (90 minutes per session)
                    $endTimes = ['09:00:00', '11:00:00', '13:00:00', '15:00:00'];
                    
                    DB::table('schedules')->updateOrInsert(
                        [
                            'class_id' => $class->id,
                            'day' => $day,
                            'session' => $session
                        ],
                        [
                            'subject_id' => $subjects[$subjectIndex]->id,
                            'teacher_id' => $teachers[$teacherIndex]->id,
                            'start_time' => $startTimes[$session - 1],
                            'end_time' => $endTimes[$session - 1],
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
                }
            }
        }
        
        echo "Schedules seeded successfully!\n";
    }
    
    /**
     * Seed assignments data
     */
    private function seedAssignments()
    {
        $subjects = Subject::all();
        $classes = SchoolClass::all();
        
        $assignmentTypes = ['Homework', 'Project', 'Essay'];
        $descriptions = [
            'Kerjakan soal pada halaman 45-47',
            'Buatlah makalah mengenai topik yang telah dibahas',
            'Kerjakan latihan pada buku halaman 78',
            'Buatlah presentasi kelompok',
            'Selesaikan tugas praktikum',
        ];
        
        foreach ($subjects as $subject) {
            foreach ($classes as $class) {
                // Create 2 assignments per subject per class
                for ($i = 1; $i <= 2; $i++) {
                    $type = $assignmentTypes[rand(0, count($assignmentTypes) - 1)];
                    $desc = $descriptions[rand(0, count($descriptions) - 1)];
                    $dueDate = Carbon::now()->addDays(rand(7, 14))->format('Y-m-d H:i:s');
                    
                    Assignment::create([
                        'title' => "Tugas $i $subject->name untuk kelas $class->name",
                        'description' => $desc,
                        'subject_id' => $subject->id,
                        'class_id' => $class->id,
                        'due_date' => $dueDate,
                        'type' => $type,
                        'max_score' => 100,
                        'status' => 'published',
                    ]);
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
        $subjects = Subject::all();
        $classes = SchoolClass::all();
        
        $quizTypes = ['Multiple Choice', 'Essay', 'Mixed'];
        
        foreach ($subjects as $subject) {
            foreach ($classes as $class) {
                // Create 1 quiz per subject per class
                $type = $quizTypes[rand(0, count($quizTypes) - 1)];
                $startDate = Carbon::now()->addDays(rand(1, 5))->format('Y-m-d H:i:s');
                $endDate = Carbon::parse($startDate)->addHours(2)->format('Y-m-d H:i:s');
                
                Quiz::create([
                    'title' => "Quiz $subject->name untuk kelas $class->name",
                    'description' => "Quiz untuk menguji pemahaman materi $subject->name",
                    'subject_id' => $subject->id,
                    'class_id' => $class->id,
                    'start_time' => $startDate,
                    'end_time' => $endDate,
                    'duration' => 60, // 60 minutes
                    'total_questions' => rand(5, 15),
                    'passing_grade' => 70,
                    'status' => 'published',
                ]);
            }
        }
        
        echo "Quizzes seeded successfully!\n";
    }
    
    /**
     * Seed attendance data
     */
    private function seedAttendance()
    {
        $classes = SchoolClass::all();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $statuses = ['present', 'absent', 'sick', 'permission'];
        $statusWeights = [80, 5, 10, 5]; // Probability weights
        
        // For the past 2 weeks
        for ($dayOffset = 14; $dayOffset >= 1; $dayOffset--) {
            $date = Carbon::now()->subDays($dayOffset);
            $dayName = $date->format('l');
            
            // Skip weekends
            if ($dayName === 'Saturday' || $dayName === 'Sunday') {
                continue;
            }
            
            foreach ($classes as $class) {
                // Create an attendance record for this day
                $attendanceRecord = Attendance::create([
                    'class_id' => $class->id,
                    'date' => $date->format('Y-m-d'),
                    'status' => 'completed',
                ]);
                
                // Get students in this class
                $students = User::where('role_id', 3) // Role ID 3 for students
                              ->whereHas('student', function($query) use($class) {
                                  $query->where('class_id', $class->id);
                              })
                              ->get();
                
                // Alternatively, if there's no relationship set up yet, just get random students
                if ($students->count() === 0) {
                    $students = User::where('role_id', 3)->inRandomOrder()->limit(rand(20, 30))->get();
                }
                
                // For each student, create an attendance detail
                foreach ($students as $student) {
                    // Choose status based on probability weights
                    $status = $this->weightedRandom($statuses, $statusWeights);
                    
                    DB::table('attendance_records')->insert([
                        'attendance_id' => $attendanceRecord->id,
                        'student_id' => $student->id,
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        
        echo "Attendance seeded successfully!\n";
    }
    
    /**
     * Seed announcements data
     */
    private function seedAnnouncements()
    {
        $teachers = User::where('role_id', 2)->get(); // Role ID 2 for teachers
        $classes = SchoolClass::all();
        
        $announcementTitles = [
            'Pengumuman Ujian Tengah Semester',
            'Jadwal Remedial',
            'Info Kegiatan Sekolah',
            'Perubahan Jadwal Pelajaran',
            'Pengumuman Libur Sekolah'
        ];
        
        $announcementContents = [
            'Ujian tengah semester akan dilaksanakan pada tanggal 15-20 Juni 2025. Harap seluruh siswa mempersiapkan diri dengan baik.',
            'Bagi siswa yang belum mencapai nilai KKM, remedial akan dilaksanakan pada hari Sabtu, 27 Juni 2025.',
            'Kegiatan ekstrakurikuler akan diadakan setiap hari Jumat setelah jam sekolah. Harap siswa dapat berpartisipasi.',
            'Terjadi perubahan jadwal pelajaran untuk minggu depan. Silakan cek di papan pengumuman sekolah.',
            'Sekolah akan libur pada tanggal 5 Juni 2025 sehubungan dengan hari raya nasional.'
        ];
        
        foreach ($teachers as $teacher) {
            // Create 2 announcements per teacher
            for ($i = 0; $i < 2; $i++) {
                $titleIndex = rand(0, count($announcementTitles) - 1);
                $contentIndex = rand(0, count($announcementContents) - 1);
                
                // Choose random classes to target (1 to 3 classes)
                $targetClassCount = rand(1, 3);
                $targetClasses = $classes->random($targetClassCount);
                $targetClassIds = $targetClasses->pluck('id')->toArray();
                
                $announcement = DB::table('announcements')->insertGetId([
                    'title' => $announcementTitles[$titleIndex],
                    'content' => $announcementContents[$contentIndex],
                    'author_id' => $teacher->id,
                    'created_at' => Carbon::now()->subDays(rand(1, 14)),
                    'updated_at' => now()
                ]);
                
                // Assign announcement to target classes
                foreach ($targetClassIds as $classId) {
                    DB::table('announcement_class')->insert([
                        'announcement_id' => $announcement,
                        'class_id' => $classId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        
        echo "Announcements seeded successfully!\n";
    }
    
    /**
     * Helper function for weighted random selection
     */
    private function weightedRandom($items, $weights)
    {
        $totalWeight = array_sum($weights);
        $rand = mt_rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($items as $key => $item) {
            $currentWeight += $weights[$key];
            if ($rand <= $currentWeight) {
                return $item;
            }
        }
        
        return $items[0]; // Default to first item
    }
}
