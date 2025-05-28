<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find admin user for created_by field
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            // If no admin found, get the first user
            $admin = User::first();
            
            if (!$admin) {
                // If no user exists, exit
                $this->command->info('No users found in database. Please run UserSeeder first.');
                return;
            }
        }
        
        $events = [
            [
                'title' => 'Rapat Guru',
                'description' => 'Rapat koordinasi guru untuk membahas kurikulum semester baru dan pembagian tugas mengajar.',
                'start_date' => Carbon::now()->addDays(3)->setHour(9)->setMinute(0),
                'end_date' => Carbon::now()->addDays(3)->setHour(12)->setMinute(0),
                'location' => 'Ruang Rapat Guru',
                'audience' => 'teachers',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Upacara Bendera Peringatan Hari Kemerdekaan',
                'description' => 'Upacara bendera dalam rangka memperingati Hari Kemerdekaan Republik Indonesia. Seluruh warga sekolah wajib hadir dengan seragam lengkap.',
                'start_date' => Carbon::now()->addDays(7)->setHour(7)->setMinute(30),
                'end_date' => Carbon::now()->addDays(7)->setHour(9)->setMinute(0),
                'location' => 'Lapangan Sekolah',
                'audience' => 'all',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Persiapan Ujian Semester',
                'description' => 'Pertemuan siswa untuk persiapan ujian semester. Akan dibahas mengenai materi-materi penting dan tips menghadapi ujian.',
                'start_date' => Carbon::now()->addDays(14)->setHour(13)->setMinute(0),
                'end_date' => Carbon::now()->addDays(14)->setHour(15)->setMinute(0),
                'location' => 'Aula Sekolah',
                'audience' => 'students',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Maintenance Sistem Informasi Sekolah',
                'description' => 'Jadwal maintenance rutin sistem informasi sekolah. Beberapa fitur mungkin tidak dapat diakses selama maintenance berlangsung.',
                'start_date' => Carbon::now()->addDays(5)->setHour(22)->setMinute(0),
                'end_date' => Carbon::now()->addDays(6)->setHour(2)->setMinute(0),
                'location' => 'Online',
                'audience' => 'admin',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
            [
                'title' => 'Pelatihan Penggunaan Google Classroom',
                'description' => 'Pelatihan untuk guru tentang cara menggunakan Google Classroom sebagai media pembelajaran online.',
                'start_date' => Carbon::now()->addDays(10)->setHour(10)->setMinute(0),
                'end_date' => Carbon::now()->addDays(10)->setHour(12)->setMinute(0),
                'location' => 'Laboratorium Komputer',
                'audience' => 'teachers',
                'is_active' => true,
                'created_by' => $admin->id,
            ],
        ];
        
        foreach ($events as $event) {
            Event::create($event);
        }
        
        $this->command->info('Sample events has been seeded!');
    }
}
