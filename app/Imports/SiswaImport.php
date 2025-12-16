<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk Transaction
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading; // Wajib ada

class SiswaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * Menentukan berapa baris yang diproses dalam satu waktu.
     * Angka 100 aman untuk Shared Hosting agar tidak 'MySQL Gone Away'.
     */
    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        // Gunakan Transaction agar jika error di tengah jalan, data tidak masuk setengah-setengah
        DB::transaction(function () use ($rows) {

            // 1. Load data Kelas (Mapping Nama Kelas => ID)
            // Kita load di sini agar query kelas tetap ringan (hanya dijalankan per chunk)
            $kelasCache = Kelas::pluck('kelas_id', 'nama_kelas')->mapWithKeys(function ($item, $key) {
                return [trim(strtoupper($key)) => $item]; // Normalisasi nama kelas jadi uppercase/trim
            });

            $usersData = [];
            $siswasData = [];
            $emails = []; // Untuk melacak email dalam batch ini

            foreach ($rows as $row) {
                // Validasi sederhana: nama dan email wajib ada
                if (empty($row['nama_siswa']) || empty($row['email'])) continue;

                $namaKelas = trim(strtoupper($row['nama_kelas']));
                $kelasId = $kelasCache[$namaKelas] ?? null;

                // Jika kelas tidak ditemukan, lewati (atau bisa set default)
                if (!$kelasId) {
                    continue;
                }

                $email = trim($row['email']);
                $password = Hash::make('password'); // Default password
                $now = now();

                // Persiapkan data User
                $usersData[] = [
                    'name'       => $row['nama_siswa'],
                    'email'      => $email,
                    'password'   => $password,
                    'role'       => 'siswa',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Simpan email untuk query pengambilan ID nanti
                $emails[] = $email;

                // Persiapkan data Siswa (User ID diisi nanti)
                // Kita gunakan email sebagai key array sementara untuk mapping
                $siswasData[$email] = [
                    'kelas_id'   => $kelasId,
                    'nis'        => $row['nis'],
                    'nama_siswa' => $row['nama_siswa'],
                    'alamat'     => $row['alamat'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // 2. Eksekusi Simpan User
            if (!empty($usersData)) {
                // InsertOrIgnore berguna jika ada email duplikat, agar tidak error total
                // Tapi jika ingin update data lama, gunakan upsert (lebih kompleks)
                // Di sini kita pakai insert biasa, asumsikan data bersih
                User::insert($usersData);

                // 3. Ambil ID User yang baru saja dibuat
                // Kita ambil berdasarkan email yang ada di batch ini
                $users = User::whereIn('email', $emails)->pluck('id', 'email');

                $finalSiswasData = [];

                foreach ($siswasData as $email => $dataSiswa) {
                    if (isset($users[$email])) {
                        $dataSiswa['user_id'] = $users[$email];
                        $finalSiswasData[] = $dataSiswa;
                    }
                }

                // 4. Eksekusi Simpan Siswa
                if (!empty($finalSiswasData)) {
                    Siswa::insert($finalSiswasData);
                }
            }
        });
    }
}
