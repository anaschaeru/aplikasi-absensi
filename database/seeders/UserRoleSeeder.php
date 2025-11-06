<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key untuk proses truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Guru::truncate();
        Siswa::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat Akun Admin
        $adminUser = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@admin.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // // 2. Buat 10 Akun Guru (dan data guru terkait) menggunakan Factory
        // Guru::factory(10)->create();

        // // 3. Buat 100 Akun Siswa (dan data siswa terkait) menggunakan Factory
        // // Ini hanya akan berjalan jika Anda sudah punya data di tabel 'kelas'
        // if (\App\Models\Kelas::count() > 0) {
        //     Siswa::factory(100)->create();
        // } else {
        //     $this->command->warn('Tabel kelas kosong. Seeder siswa tidak dijalankan.');
        // }
    }
}
