<?php

namespace App\Http\Traits;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait RekapAbsensiTrait
{
    /**
     * Memproses data rekap absensi.
     * * @param int $guruId
     * @param int $kelasId
     * @param string $tanggalMulai
     * @param string $tanggalAkhir
     * @param int|null $mapelId  <-- Parameter Baru (Opsional)
     */
    private function prosesRekapData($guruId, $kelasId, $tanggalMulai, $tanggalAkhir, $mapelId = null)
    {
        // 1. Ambil semua siswa di kelas tersebut
        $siswas = Siswa::where('kelas_id', $kelasId)
            ->orderBy('nama_siswa')
            ->get();

        // 2. Mulai Query Absensi
        $queryAbsensi = Absensi::where('dicatat_oleh_guru_id', $guruId)
            ->whereBetween('tanggal_absensi', [$tanggalMulai, $tanggalAkhir])
            ->whereIn('siswa_id', $siswas->pluck('siswa_id'));

        // --- FILTER BARU: Jika Mata Pelajaran dipilih, tambahkan ke query ---
        if ($mapelId) {
            // Pastikan relasi ke jadwal ada untuk memfilter berdasarkan mapel
            $queryAbsensi->whereHas('jadwal', function ($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            });
        }

        // Eksekusi query dan kelompokkan
        $absensi = $queryAbsensi->get()->groupBy('siswa_id');

        // 3. Proses Hitung Kehadiran (Looping Siswa)
        $rekap = [];
        foreach ($siswas as $siswa) {
            $kehadiran = [
                'Hadir' => 0,
                'Sakit' => 0,
                'Izin' => 0,
                'Alfa' => 0,
            ];

            // Jika siswa punya data absensi (sesuai filter)
            if (isset($absensi[$siswa->siswa_id])) {
                foreach ($absensi[$siswa->siswa_id] as $absen) {
                    // Pastikan status valid ada di array $kehadiran untuk mencegah error
                    if (array_key_exists($absen->status, $kehadiran)) {
                        $kehadiran[$absen->status]++;
                    }
                }
            }

            $rekap[] = [
                'nama_siswa' => $siswa->nama_siswa,
                'nis' => $siswa->nis,
                'kehadiran' => $kehadiran,
            ];
        }

        // 4. Ambil Info Tambahan
        $kelas = Kelas::with('waliKelas')->find($kelasId);

        return [
            'rekap' => $rekap,
            'info' => [
                'nama_kelas' => $kelas->nama_kelas,
                // --- TAMBAHKAN BARIS INI ---
                'nama_mapel' => $mapelId ? \App\Models\MataPelajaran::find($mapelId)->nama_mapel : 'Semua Mata Pelajaran',
                // ---------------------------
                'periode' => Carbon::parse($tanggalMulai)->translatedFormat('d M Y') . ' - ' . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y'),
                'wali_kelas' => $kelas->waliKelas->nama_guru ?? 'Belum Ditentukan',
                'nama_guru' => Auth::user()->name,
            ]
        ];
    }
}
