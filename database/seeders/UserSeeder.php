<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $guruRole = Role::where('slug', 'guru')->first();
        $siswaRole = Role::where('slug', 'siswa')->first();

        // Create admin users
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@sman1.edu',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // Create teacher users
        $teachers = [
            ['name' => 'Ahmad Matematika', 'subject' => 'Matematika'],
            ['name' => 'Budi Fisika', 'subject' => 'Fisika'],
            ['name' => 'Citra Biologi', 'subject' => 'Biologi'],
            ['name' => 'Dewi Kimia', 'subject' => 'Kimia'],
            ['name' => 'Eko B.Indonesia', 'subject' => 'Bahasa Indonesia'],
            ['name' => 'Farah B.Inggris', 'subject' => 'Bahasa Inggris'],
        ];

        foreach ($teachers as $teacher) {
            User::firstOrCreate(
                ['email' => Str::slug($teacher['name']) . '@sman1.edu'],
                [
                    'name' => $teacher['name'],
                    'username' => Str::slug($teacher['name']),
                    'password' => Hash::make('password'),
                    'role_id' => $guruRole->id,
                ]
            );
        }

        // Create student users for each grade level (X, XI, XII)
        $grades = ['X', 'XI', 'XII'];
        $classes = ['A', 'B', 'C'];

        foreach ($grades as $grade) {
            foreach ($classes as $class) {
                for ($i = 1; $i <= 10; $i++) { // 10 students per class
                    $name = "Siswa {$grade}-{$class}-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $nis = date('Y') . str_pad($i, 3, '0', STR_PAD_LEFT);
                    
                    User::firstOrCreate(
                        ['username' => Str::slug($name)],
                        [
                            'name' => $name,
                            'email' => Str::slug($name) . '@siswa.sman1.edu',
                            'password' => Hash::make('password'),
                            'role_id' => $siswaRole->id,
                            'nis' => $nis,
                        ]
                    );
                }
            }
        }
    }
}
