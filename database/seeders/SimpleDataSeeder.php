<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SimpleDataSeeder extends Seeder
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
     */
    private function seedUsers()
    {
        // Get role IDs
        $adminRole = Role::where('slug', 'admin')->first();
        $teacherRole = Role::where('slug', 'guru')->first();
        $studentRole = Role::where('slug', 'siswa')->first();
        
        if (!$adminRole || !$teacherRole || !$studentRole) {
            echo "Roles not found. Please run the RoleSeeder first.\n";
            return;
        }
        
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
                'id_number' => 'NIP19850101001'
            ],
            [
                'username' => 'siti_teacher',
                'name' => 'Siti Rahayu',
                'email' => 'siti@sekolah.ac.id',
                'nip' => '19860202002',
                'id_number' => 'NIP19860202002'
            ],
            [
                'username' => 'ahmad_teacher',
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad@sekolah.ac.id',
                'nip' => '19870303003',
                'id_number' => 'NIP19870303003'
            ],
            [
                'username' => 'maya_teacher',
                'name' => 'Maya Wijaya',
                'email' => 'maya@sekolah.ac.id',
                'nip' => '19880404004',
                'id_number' => 'NIP19880404004'
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
                    'id_number' => $teacher['id_number']
                ]
            );
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
                $username = strtolower($firstName) . '_' . strtolower($lastName) . $counter;
                $nisn = '10' . str_pad($counter, 8, '0', STR_PAD_LEFT);
                
                User::updateOrCreate(
                    ['username' => $username],
                    [
                        'name' => $fullName,
                        'email' => strtolower($firstName . $counter) . '@student.sekolah.ac.id',
                        'password' => Hash::make('password'),
                        'role_id' => $studentRole->id,
                        'nisn' => $nisn,
                        'id_number' => 'NISN' . $nisn,
                        'nis' => str_pad($counter, 6, '0', STR_PAD_LEFT)
                    ]
                );
                
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
        // Check if the subject_teacher table exists
        if (!Schema::hasTable('subject_teacher')) {
            echo "subject_teacher table not found. Skipping subject-teacher relationships seeding.\n";
            return;
        }
        
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
     * Seed announcements data
     */
    private function seedAnnouncements()
    {
        // Check if the announcements table exists
        if (!Schema::hasTable('announcements')) {
            echo "Announcements table not found. Skipping announcements seeding.\n";
            return;
        }
        
        $teachers = User::where('role_id', 2)->get(); // Role ID 2 for teachers
        $classes = SchoolClass::all();
        
        if ($teachers->isEmpty() || $classes->isEmpty()) {
            echo "Missing required data. Skipping announcements seeding.\n";
            return;
        }
        
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
            // Create 1 announcement per teacher
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
                'updated_at' => now()
            ]);
            
            // Check if announcement_class table exists
            if (Schema::hasTable('announcement_class')) {
                // Assign announcement to target classes
                foreach ($targetClassIds as $classId) {
                    DB::table('announcement_class')->insert([
                        'announcement_id' => $announcementId,
                        'class_id' => $classId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
        
        echo "Announcements seeded successfully!\n";
    }
}
