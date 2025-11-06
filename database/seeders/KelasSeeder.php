<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- 1. Tambahkan ini

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Tambahkan baris ini untuk menonaktifkan pengecekan
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Kelas::truncate(); // Sekarang baris ini bisa berjalan

        // 3. Tambahkan baris ini untuk mengaktifkan kembali
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data seeder tetap di sini
        Kelas::create(['nama_kelas' => 'X IPA 1', 'tingkat' => 10]);
        Kelas::create(['nama_kelas' => 'X IPS 1', 'tingkat' => 10]);
        Kelas::create(['nama_kelas' => 'XI IPA 1', 'tingkat' => 11]);
        Kelas::create(['nama_kelas' => 'XI IPS 1', 'tingkat' => 11]);
        Kelas::create(['nama_kelas' => 'XII IPA 1', 'tingkat' => 12]);
        Kelas::create(['nama_kelas' => 'XII IPS 1', 'tingkat' => 12]);
    }
}
