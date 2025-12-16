<?php

namespace App\Imports;

use App\Models\MataPelajaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class MataPelajaranImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * Membaca file per 100 baris untuk menjaga kestabilan memori server.
     */
    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $mapelList = [];
            $kodesInBatch = [];

            // 1. Kumpulkan semua kode mapel di batch ini untuk pengecekan
            foreach ($rows as $row) {
                if (!empty($row['kode_mapel'])) {
                    // Bersihkan kode: Hapus spasi, jadikan huruf besar (misal: "mtk " -> "MTK")
                    $kodesInBatch[] = trim(strtoupper($row['kode_mapel']));
                }
            }

            // 2. Ambil Kode Mapel yang SUDAH ADA di database
            // Ini mencegah error "Duplicate Entry" jika file Excel diupload ulang
            $existingMapel = MataPelajaran::whereIn('kode_mapel', $kodesInBatch)
                ->pluck('kode_mapel')
                ->toArray();

            $now = now(); // Timestamp seragam

            foreach ($rows as $row) {
                // Validasi: Kode dan Nama wajib ada
                if (empty($row['kode_mapel']) || empty($row['nama_mapel'])) {
                    continue;
                }

                // Normalisasi data
                $kodeClean = trim(strtoupper($row['kode_mapel']));
                $namaClean = trim($row['nama_mapel']);

                // 3. CEK DUPLIKASI
                // Jika kode mapel sudah ada di DB, lewati.
                if (in_array($kodeClean, $existingMapel)) {
                    continue;
                }

                // Cek duplikasi internal (jika di excel ada 2 baris kode yang sama)
                if (isset($mapelList[$kodeClean])) {
                    continue;
                }

                // Masukkan ke array sementara
                // Gunakan $kodeClean sebagai key array untuk mencegah duplikat dalam satu batch
                $mapelList[$kodeClean] = [
                    'kode_mapel' => $kodeClean,
                    'nama_mapel' => $namaClean,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // 4. Simpan ke Database
            if (!empty($mapelList)) {
                // array_values() mengubah array asosiatif ['KD01' => [...]] menjadi array index [0 => [...]]
                // agar cocok dengan format insert bulk Laravel
                MataPelajaran::insert(array_values($mapelList));
            }
        });
    }
}
