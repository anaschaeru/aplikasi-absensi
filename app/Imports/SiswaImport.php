<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Tambahkan DB
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
// Tambahkan BatchSize agar memori lebih hemat
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    // Potong proses per 100 baris agar RAM tidak meledak
    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        // --- OPTIMASI 1: HASH PASSWORD DI LUAR LOOP ---
        // Server hanya mikir 1 kali untuk enkripsi password.
        // Jika ditaruh di dalam loop, server mikir 1000 kali (Penyebab Lemot).
        $passwordDefault = Hash::make('Akusmkn4');

        // --- OPTIMASI 2: AMBIL CACHE KELAS ---
        // Ambil semua ID kelas ke memori, agar tidak query DB berulang-ulang
        $kelasCache = Kelas::pluck('kelas_id', 'nama_kelas')->mapWithKeys(function ($item, $key) {
            return [trim(strtoupper($key)) => $item];
        });

        // --- OPTIMASI 3: GUNAKAN TRANSACTION ---
        // Jika gagal di tengah, semua batal (Database bersih).
        // Ini juga mempercepat proses insert ke database.
        DB::transaction(function () use ($rows, $passwordDefault, $kelasCache) {

            foreach ($rows as $row) {
                // Validasi data kosong
                if (empty($row['nis']) || empty($row['nama_siswa'])) continue;
                $nis = trim($row['nis']);
                $nama_siswa = trim($row['nama_siswa']);

                // Cari ID Kelas dari Cache (Cepat)
                $namaKelasExcel = trim(strtoupper($row['nama_kelas']));
                $kelasId = $kelasCache[$namaKelasExcel] ?? null;

                // Jika kelas tidak ditemukan, lewati (atau set default)
                if (!$kelasId) continue;

                // 1. Buat/Cari User (Pakai password yang sudah di-hash di atas)
                $user = User::firstOrCreate(
                    ['email' => $nis . '@siswa.id'], // Dummy email pakai NIS
                    [
                        'name' => $nama_siswa,
                        'password' => $passwordDefault, // <--- PAKAI INI (CEPAT!)
                        'role' => 'siswa',
                    ]
                );

                // 2. Update/Buat Siswa
                Siswa::updateOrCreate(
                    ['nis' => $row['nis']],
                    [
                        'user_id' => $user->id,
                        'kelas_id' => $kelasId,
                        'nama_siswa' => $nama_siswa,
                        'alamat' => $row['alamat'] ?? '-',
                    ]
                );
            }
        });
    }
}
