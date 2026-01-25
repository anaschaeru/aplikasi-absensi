<?php

namespace App\Imports;

use App\Models\JadwalPelajaran; // Asumsi nama model jadwal Anda
use App\Models\Kelas;
use App\Models\MataPelajaran;   // SESUAI REQUEST (Model MataPelajaran)
use App\Models\Guru;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JadwalPelajaranImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Validasi Data Dasar
            if (empty($row['nama_kelas']) || empty($row['nama_mapel']) || empty($row['hari'])) {
                continue;
            }

            // 2. Cari ID Kelas
            $kelas = Kelas::where('nama_kelas', trim($row['nama_kelas']))->first();
            if (!$kelas) continue;

            // 3. Cari ID Mata Pelajaran (Update Model)
            // Pastikan kolom di tabel mata_pelajaran namanya 'nama_mapel' atau 'nama_pelajaran'
            // Sesuaikan 'nama_mapel' dibawah dengan nama kolom di database Anda
            $mapel = MataPelajaran::where('nama_mapel', trim($row['nama_mapel']))->first();

            if (!$mapel) continue;

            // 4. Cari ID Guru
            $guru = Guru::where('nama_guru', trim($row['nama_guru']))->first();
            $guruId = $guru ? $guru->guru_id : null;

            // 5. Konversi Waktu
            try {
                $jamMulai = $this->transformTime($row['jam_mulai']);
                $jamSelesai = $this->transformTime($row['jam_selesai']);
            } catch (\Exception $e) {
                continue;
            }

            // 6. Simpan ke Tabel Jadwal Pelajaran
            JadwalPelajaran::create([
                'kelas_id'          => $kelas->id,

                // PENTING: Sesuaikan foreign key ini dengan tabel database Anda.
                // Jika Modelnya MataPelajaran, biasanya kolomnya 'mata_pelajaran_id'
                'mata_pelajaran_id' => $mapel->id,

                'guru_id'           => $guruId,
                'hari'              => ucfirst(strtolower(trim($row['hari']))),
                'jam_mulai'         => $jamMulai,
                'jam_selesai'       => $jamSelesai,
            ]);
        }
    }

    private function transformTime($value)
    {
        if (is_numeric($value)) {
            return Date::excelToDateTimeObject($value)->format('H:i:s');
        }
        return date('H:i:s', strtotime($value));
    }
}
