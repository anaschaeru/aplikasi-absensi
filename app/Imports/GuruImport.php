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
        $passwordDefault = Hash::make('Akusmkn4');

        foreach ($rows as $row) {
            // 1. Validasi: Nama & NIP wajib ada
            if (empty($row['nama_guru']) || empty($row['nip'])) continue;

            $nip = trim($row['nip']);
            $nama = trim($row['nama_guru']);
            $kontak = $row['kontak'] ?? '-';

            // 2. Buat atau Ambil User (Akun Login)
            // Username = NIP, Password Default = password123
            $user = User::updateOrCreate(
                ['email' => $nip . '@guru.id'], // Dummy email pakai NIP
                [
                    'name'     => $nama,
                    'password' => $passwordDefault,
                    'role'     => 'guru',
                    // Jika login pakai username, tambahkan 'username' => $nip
                ]
            );

            // 3. Simpan Data Guru & Sambungkan user_id
            Guru::updateOrCreate(
                ['nip' => $nip], // Cek berdasarkan NIP
                [
                    'user_id'   => $user->id, // Sambungkan Relasi
                    'nama_guru' => $nama,
                    'kontak'    => $kontak,
                ]
            );
        }
    }
}
