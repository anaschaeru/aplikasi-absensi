<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    // Hapus properti $kelasCache dari sini untuk menghindari kebingungan
    // private $kelasCache;

    public function collection(Collection $rows)
    {
        // Buat cache di sini, di dalam metode collection
        $kelasCache = Kelas::all()->mapWithKeys(function ($item) {
            return [trim($item->nama_kelas) => $item->kelas_id];
        });

        $usersData = [];
        $siswasData = [];

        foreach ($rows as $row) {
            if (empty($row['nama_siswa'])) continue;

            $kelasId = $kelasCache[trim($row['nama_kelas'])] ?? null;

            // Jika kelas tidak ditemukan, lewati baris ini agar impor tidak gagal total
            if (!$kelasId) {
                continue;
            }

            // Kumpulkan data user
            $usersData[] = [
                'name' => $row['nama_siswa'],
                'email' => $row['email'],
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Kumpulkan data siswa (tanpa user_id dulu)
            $siswasData[$row['email']] = [
                'kelas_id' => $kelasId,
                'nis' => $row['nis'],
                'nama_siswa' => $row['nama_siswa'],
                'alamat' => $row['alamat'],
            ];
        }

        if (!empty($usersData)) {
            // Simpan semua user sekaligus
            User::insert($usersData);

            // Ambil kembali user yang baru dibuat untuk mendapatkan ID
            $createdUsers = User::whereIn('email', array_keys($siswasData))->pluck('id', 'email');

            // Kaitkan user_id dan siapkan data siswa untuk disimpan
            $finalSiswasData = [];
            foreach ($siswasData as $email => $siswa) {
                if (isset($createdUsers[$email])) {
                    $siswa['user_id'] = $createdUsers[$email];
                    $finalSiswasData[] = $siswa;
                }
            }

            // Simpan semua siswa sekaligus
            if (!empty($finalSiswasData)) {
                Siswa::insert($finalSiswasData);
            }
        }
    }
}
