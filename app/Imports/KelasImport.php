<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $kelasList = [];
        foreach ($rows as $row) {
            // Konversi baris menjadi array untuk pemeriksaan yang aman
            $rowData = $row->toArray();

            // Pengecekan Paling Aman:
            // 1. Cek apakah baris punya 'nama_kelas' DAN 'tingkat'
            // 2. Cek apakah nilainya tidak kosong
            if (
                !isset($rowData['nama_kelas']) || !isset($rowData['tingkat']) ||
                empty($rowData['nama_kelas'])
            ) {
                continue; // Jika salah satu syarat tidak terpenuhi, ABAIKAN BARIS INI
            }

            $kelasList[] = [
                'nama_kelas' => $rowData['nama_kelas'],
                'tingkat'    => $rowData['tingkat'],
            ];
        }

        // Simpan semua kelas sekaligus
        if (!empty($kelasList)) {
            Kelas::insert($kelasList);
        }
    }
}
