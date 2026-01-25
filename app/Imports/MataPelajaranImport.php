<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataPelajaranImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validasi: Nama Mapel wajib ada
            if (empty($row['nama_mapel'])) continue;

            // Jika kode_mapel kosong di excel, generate otomatis 3 huruf depan
            $kodeMapel = $row['kode_mapel'] ?? strtoupper(substr($row['nama_mapel'], 0, 3));

            // Simpan atau Update
            // Kita pakai 'kode_mapel' sebagai patokan (Key)
            MataPelajaran::updateOrCreate(
                ['kode_mapel' => strtoupper(trim($kodeMapel))],
                [
                    'nama_mapel' => $row['nama_mapel'],
                ]
            );
        }
    }
}
