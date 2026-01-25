<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validasi: Nama Kelas wajib ada
            if (empty($row['nama_kelas'])) continue;

            $namaKelas = strtoupper(trim($row['nama_kelas']));

            // 1. Cari Wali Kelas (Opsional)
            $waliKelasId = null;
            if (!empty($row['nama_wali_kelas'])) {
                $guru = Guru::where('nama_guru', 'LIKE', '%' . $row['nama_wali_kelas'] . '%')->first();
                if ($guru) {
                    $waliKelasId = $guru->guru_id;
                }
            }

            // 2. DETEKSI OTOMATIS TINGKAT (X, XI, XII)
            // Default tingkat
            $tingkat = '10'; // Default jika tidak terdeteksi

            // Cek awalan nama kelas
            if (str_starts_with($namaKelas, 'XII') || str_starts_with($namaKelas, '12')) {
                $tingkat = '12';
            } elseif (str_starts_with($namaKelas, 'XI') || str_starts_with($namaKelas, '11')) {
                $tingkat = '11';
            } elseif (str_starts_with($namaKelas, 'X') || str_starts_with($namaKelas, '10')) {
                $tingkat = '10';
            } else {
                // Jika nama kelas aneh (misal "VII A"), coba ambil angka pertamanya
                // atau biarkan default '10' atau sesuaikan dengan kebutuhan SMP/SD
                $tingkat = '10';
            }

            // 3. Simpan atau Update Kelas
            Kelas::updateOrCreate(
                ['nama_kelas' => $namaKelas],
                [
                    'wali_kelas_id' => $waliKelasId,
                    'tingkat'       => $tingkat, // <--- INI SOLUSINYA
                ]
            );
        }
    }
}
