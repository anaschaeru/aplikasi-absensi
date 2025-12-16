<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB; // Penting untuk Transaction
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading; // Penting untuk performa

class GuruImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * Memproses data per 100 baris untuk mencegah memory limit / timeout
     */
    public function chunkSize(): int
    {
        return 20;
    }

    public function collection(Collection $rows)
    {
        DB::reconnect();
        DB::transaction(function () use ($rows) {
            $usersData = [];
            $gurusData = [];
            $emailsInBatch = [];

            // 1. Kumpulkan semua email di batch ini untuk pengecekan duplikasi
            foreach ($rows as $row) {
                if (!empty($row['email'])) {
                    $emailsInBatch[] = trim($row['email']);
                }
            }

            // 2. Cek email mana yang SUDAH ada di database (agar tidak error Duplicate Entry)
            $existingEmails = User::whereIn('email', $emailsInBatch)
                ->pluck('email')
                ->toArray();

            $now = now(); // Timestamp seragam untuk batch ini

            foreach ($rows as $row) {
                // Validasi sederhana
                if (empty($row['nama_guru']) || empty($row['email'])) continue;

                $email = trim($row['email']);

                // Jika email sudah ada di DB, skip baris ini
                if (in_array($email, $existingEmails)) {
                    continue;
                }

                // Persiapkan data User
                $usersData[] = [
                    'name'       => $row['nama_guru'],
                    'email'      => $email,
                    'password'   => Hash::make('password'), // Password default
                    'role'       => 'guru',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Persiapkan data Guru (User ID diisi nanti)
                $gurusData[$email] = [
                    'nip'        => $row['nip'],
                    'nama_guru'  => $row['nama_guru'],
                    'kontak'     => $row['kontak'],
                    'created_at' => $now, // Wajib manual kalau pakai insert
                    'updated_at' => $now,
                ];
            }

            // 3. Eksekusi Simpan User
            if (!empty($usersData)) {
                User::insert($usersData);

                // 4. Ambil ID User yang baru saja dibuat
                $newUsers = User::whereIn('email', array_keys($gurusData))->pluck('id', 'email');

                $finalGurusData = [];
                foreach ($gurusData as $email => $guru) {
                    if (isset($newUsers[$email])) {
                        $guru['user_id'] = $newUsers[$email];
                        $finalGurusData[] = $guru;
                    }
                }

                // 5. Simpan semua guru sekaligus
                if (!empty($finalGurusData)) {
                    Guru::insert($finalGurusData);
                }
            }
        });
    }
}
