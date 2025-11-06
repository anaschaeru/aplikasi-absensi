<?php

namespace App\Http\Traits;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait RekapAbsensiTrait
{
    // app/Http/Traits/RekapAbsensiTrait.php

    private function prosesRekapData($guruId, $kelasId, $tanggalMulai, $tanggalAkhir)
    {
        // Ambil semua siswa di kelas tersebut
        $siswas = \App\Models\Siswa::where('kelas_id', $kelasId)->orderBy('nama_siswa')->get();

        // Ambil data absensi dalam rentang waktu yang dipilih
        $absensi = \App\Models\Absensi::where('dicatat_oleh_guru_id', $guruId)
            ->whereBetween('tanggal_absensi', [$tanggalMulai, $tanggalAkhir])
            ->whereIn('siswa_id', $siswas->pluck('siswa_id'))
            ->get()
            ->groupBy('siswa_id');

        $rekap = [];
        foreach ($siswas as $siswa) {
            $kehadiran = [
                'Hadir' => 0,
                'Sakit' => 0,
                'Izin' => 0,
                'Alfa' => 0,
            ];
            if (isset($absensi[$siswa->siswa_id])) {
                foreach ($absensi[$siswa->siswa_id] as $absen) {
                    $kehadiran[$absen->status]++;
                }
            }
            $rekap[] = [
                'nama_siswa' => $siswa->nama_siswa,
                'nis' => $siswa->nis,
                'kehadiran' => $kehadiran,
            ];
        }

        return [
            'rekap' => $rekap,
            'info' => [
                'nama_kelas' => \App\Models\Kelas::find($kelasId)->nama_kelas,
                'periode' => \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($tanggalAkhir)->translatedFormat('d M Y'),
                'wali_kelas' => \App\Models\Kelas::find($kelasId)->waliKelas->nama_guru ?? 'N/A',
                'nama_guru' => \Illuminate\Support\Facades\Auth::user()->name,
            ]
        ];
    }
}
