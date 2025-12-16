<?php

namespace App\Imports;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class JadwalPelajaranImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    /**
     * Proses per 100 baris agar aman di hosting
     */
    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            // 1. Buat Cache Mapping (Nama => ID)
            // Gunakan mapWithKeys untuk menormalisasi key menjadi UPPERCASE agar pencarian tidak sensitif huruf besar/kecil

            $kelasCache = Kelas::pluck('kelas_id', 'nama_kelas')->mapWithKeys(function ($id, $nama) {
                return [trim(strtoupper($nama)) => $id];
            });

            $mapelCache = MataPelajaran::pluck('mapel_id', 'nama_mapel')->mapWithKeys(function ($id, $nama) {
                return [trim(strtoupper($nama)) => $id];
            });

            $guruCache = Guru::pluck('guru_id', 'nama_guru')->mapWithKeys(function ($id, $nama) {
                return [trim(strtoupper($nama)) => $id];
            });

            $jadwalList = [];
            $now = now();

            foreach ($rows as $row) {
                // Validasi kolom wajib
                if (empty($row['nama_kelas']) || empty($row['nama_mapel']) || empty($row['nama_guru'])) {
                    continue;
                }

                // Normalisasi input dari Excel
                $namaKelas = trim(strtoupper($row['nama_kelas']));
                $namaMapel = trim(strtoupper($row['nama_mapel']));
                $namaGuru  = trim(strtoupper($row['nama_guru']));

                // Ambil ID dari Cache
                $kelasId = $kelasCache[$namaKelas] ?? null;
                $mapelId = $mapelCache[$namaMapel] ?? null;
                $guruId  = $guruCache[$namaGuru] ?? null;

                // Lewati jika referensi data tidak ditemukan di database
                if (!$kelasId || !$mapelId || !$guruId) {
                    continue;
                }

                // Parsing Waktu yang Aman (Menangani format Text maupun Format Time Excel)
                $jamMulai   = $this->parseTime($row['jam_mulai']);
                $jamSelesai = $this->parseTime($row['jam_selesai']);

                if (!$jamMulai || !$jamSelesai) {
                    continue; // Skip jika format waktu rusak
                }

                $jadwalList[] = [
                    'kelas_id'    => $kelasId,
                    'mapel_id'    => $mapelId,
                    'guru_id'     => $guruId,
                    'hari'        => ucfirst(strtolower(trim($row['hari']))), // Pastikan format "Senin", "Selasa" rapi
                    'jam_mulai'   => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // Simpan data sekaligus
            if (!empty($jadwalList)) {
                JadwalPelajaran::insert($jadwalList);
            }
        });
    }

    /**
     * Helper khusus untuk mengubah format waktu Excel menjadi H:i:s
     */
    private function parseTime($timeValue)
    {
        try {
            if (empty($timeValue)) return null;

            // Jika formatnya angka desimal Excel (contoh: 0.5416 untuk jam 13:00)
            if (is_numeric($timeValue)) {
                return Date::excelToDateTimeObject($timeValue)->format('H:i:s');
            }

            // Jika formatnya string biasa (contoh: "13:00")
            return Carbon::parse($timeValue)->format('H:i:s');
        } catch (\Exception $e) {
            return null; // Kembalikan null jika gagal parsing
        }
    }
}
