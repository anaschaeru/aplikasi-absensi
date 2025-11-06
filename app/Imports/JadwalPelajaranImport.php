<?php

namespace App\Imports;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class JadwalPelajaranImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Buat cache data relasi sekali saja
        $kelasCache = Kelas::all()->pluck('kelas_id', 'nama_kelas');
        $mapelCache = MataPelajaran::all()->pluck('mapel_id', 'nama_mapel');
        $guruCache = Guru::all()->pluck('guru_id', 'nama_guru');

        $jadwalList = [];
        foreach ($rows as $row) {
            if (empty($row['nama_kelas'])) continue;

            $kelasId = $kelasCache[trim($row['nama_kelas'])] ?? null;
            $mapelId = $mapelCache[trim($row['nama_mapel'])] ?? null;
            $guruId = $guruCache[trim($row['nama_guru'])] ?? null;

            // Lewati jika salah satu data tidak ditemukan
            if (!$kelasId || !$mapelId || !$guruId) {
                continue;
            }

            $jadwalList[] = [
                'kelas_id' => $kelasId,
                'mapel_id' => $mapelId,
                'guru_id' => $guruId,
                'hari' => trim($row['hari']),
                'jam_mulai' => Carbon::parse($row['jam_mulai'])->format('H:i:s'),
                'jam_selesai' => Carbon::parse($row['jam_selesai'])->format('H:i:s'),
            ];
        }

        // Simpan semua jadwal sekaligus
        if (!empty($jadwalList)) {
            JadwalPelajaran::insert($jadwalList);
        }
    }
}
