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
        $mapelList = [];
        foreach ($rows as $row) {
            if (empty($row['nama_mapel'])) continue;

            $mapelList[] = [
                'kode_mapel' => $row['kode_mapel'],
                'nama_mapel' => $row['nama_mapel'],
            ];
        }

        // Simpan semua mata pelajaran sekaligus
        if (!empty($mapelList)) {
            MataPelajaran::insert($mapelList);
        }
    }
}
