<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $usersData = [];
        $gurusData = [];

        foreach ($rows as $row) {
            if (empty($row['nama_guru'])) continue;

            // 1. Kumpulkan data user
            $usersData[] = [
                'name' => $row['nama_guru'],
                'email' => $row['email'],
                'password' => Hash::make('password'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Kumpulkan data guru (tanpa user_id dulu)
            $gurusData[$row['email']] = [
                'nip' => $row['nip'],
                'nama_guru' => $row['nama_guru'],
                'kontak' => $row['kontak'],
            ];
        }

        // 2. Simpan semua user sekaligus
        User::insert($usersData);

        // 3. Ambil kembali user yang baru dibuat untuk mendapatkan ID
        $createdUsers = User::whereIn('email', array_keys($gurusData))->pluck('id', 'email');

        // 4. Kaitkan user_id dan siapkan data guru untuk disimpan
        $finalGurusData = [];
        foreach ($gurusData as $email => $guru) {
            if (isset($createdUsers[$email])) {
                $guru['user_id'] = $createdUsers[$email];
                $finalGurusData[] = $guru;
            }
        }

        // 5. Simpan semua guru sekaligus
        Guru::insert($finalGurusData);
    }
}
