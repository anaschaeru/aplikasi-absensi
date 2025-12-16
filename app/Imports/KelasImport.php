<?php

namespace App\Imports;

use App\Models\Kelas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class KelasImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * Proses per 100 baris agar hemat memori di hosting
     */
    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        // Gunakan transaksi db agar aman
        DB::transaction(function () use ($rows) {
            $kelasList = [];
            $namaKelasInBatch = [];

            // 1. Kumpulkan semua nama kelas di batch ini
            foreach ($rows as $row) {
                if (!empty($row['nama_kelas'])) {
                    // Normalisasi: Huruf besar semua & hilangkan spasi di awal/akhir
                    $namaKelasInBatch[] = trim(strtoupper($row['nama_kelas']));
                }
            }

            // 2. Cek ke Database: Kelas mana yang SUDAH ada?
            // Kita ambil nama kelas yang sudah ada untuk mencegah duplikasi
            $existingKelas = Kelas::whereIn('nama_kelas', $namaKelasInBatch)
                ->pluck('nama_kelas')
                ->toArray();

            $now = now(); // Timestamp seragam

            foreach ($rows as $row) {
                $rowData = $row->toArray();

                // Validasi: Nama Kelas & Tingkat wajib ada
                if (empty($rowData['nama_kelas']) || !isset($rowData['tingkat'])) {
                    continue;
                }

                // Normalisasi nama kelas
                $namaKelasClean = trim(strtoupper($rowData['nama_kelas']));

                // 3. LOGIKA PENCEGAHAN DUPLIKAT
                // Jika nama kelas sudah ada di database, skip/lewati
                if (in_array($namaKelasClean, $existingKelas)) {
                    continue;
                }

                // Cek juga agar tidak ada duplikat ganda di dalam file Excel itu sendiri
                // (Misal di excel ada 2 baris "X RPL 1")
                if (isset($kelasList[$namaKelasClean])) {
                    continue;
                }

                // Masukkan ke array (gunakan nama kelas sebagai key sementara untuk cegah duplikat internal)
                $kelasList[$namaKelasClean] = [
                    'nama_kelas' => $namaKelasClean,
                    'tingkat'    => $rowData['tingkat'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // 4. Simpan ke Database
            if (!empty($kelasList)) {
                // array_values digunakan untuk mereset key array sebelum insert
                Kelas::insert(array_values($kelasList));
            }
        });
    }
}
