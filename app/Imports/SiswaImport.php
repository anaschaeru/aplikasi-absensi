<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SiswaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    // TURUNKAN JADI 10.
    // Hash password itu berat. Hosting murah tidak kuat hash banyak sekaligus.
    public function chunkSize(): int
    {
        return 10;
    }

    public function collection(Collection $rows)
    {
        // TRIK RAHASIA: Putus koneksi lama, buat koneksi baru yang segar.
        DB::connection()->disableQueryLog(); // Hemat memori

        // Cek koneksi, jika putus, sambung lagi
        try {
            DB::reconnect();
        } catch (\Exception $e) {
            // Abaikan jika gagal reconnect pertama kali
        }

        DB::transaction(function () use ($rows) {

            // Ambil cache kelas (Optimasi: hanya ambil nama & id)
            $kelasCache = Kelas::pluck('kelas_id', 'nama_kelas')->mapWithKeys(function ($item, $key) {
                return [trim(strtoupper($key)) => $item];
            });

            $usersData = [];
            $siswasData = [];
            $emails = [];
            $passwordHash = Hash::make('password'); // OPTIMASI: Hash sekali saja untuk semua siswa (biar cepat)
            $now = now();

            foreach ($rows as $row) {
                if (empty($row['nama_siswa']) || empty($row['email'])) continue;

                $namaKelas = trim(strtoupper($row['nama_kelas']));
                $kelasId = $kelasCache[$namaKelas] ?? null;

                if (!$kelasId) continue;

                $email = trim($row['email']);

                $usersData[] = [
                    'name'       => $row['nama_siswa'],
                    'email'      => $email,
                    'password'   => $passwordHash, // Pakai hash yang sudah dibuat di atas
                    'role'       => 'siswa',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $emails[] = $email;

                $siswasData[$email] = [
                    'kelas_id'   => $kelasId,
                    'nis'        => $row['nis'],
                    'nama_siswa' => $row['nama_siswa'],
                    'alamat'     => $row['alamat'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Simpan User
            if (!empty($usersData)) {
                // Insert Ignore (agar jika email duplikat tidak error stop)
                User::insertOrIgnore($usersData);

                // Ambil ID User
                $users = User::whereIn('email', $emails)->pluck('id', 'email');

                $finalSiswasData = [];
                foreach ($siswasData as $email => $dataSiswa) {
                    if (isset($users[$email])) {
                        $dataSiswa['user_id'] = $users[$email];
                        $finalSiswasData[] = $dataSiswa;
                    }
                }

                // Simpan Siswa
                if (!empty($finalSiswasData)) {
                    Siswa::insertOrIgnore($finalSiswasData);
                }
            }
        });
    }
}
