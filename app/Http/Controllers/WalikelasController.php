<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
// Gunakan AbsensiHarian karena ini yang menyimpan data scan QR & Foto
use App\Models\AbsensiHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalikelasController extends Controller
{
    /**
     * Menampilkan Dashboard Utama Wali Kelas
     */
    public function dashboard()
    {
        $guru = Auth::user()->guru;
        $today = Carbon::today()->toDateString();

        // 1. Cari Kelas yang diampu oleh Guru ini
        $kelas = Kelas::where('wali_kelas_id', $guru->guru_id)->first();

        // Jika guru ini bukan wali kelas manapun
        if (!$kelas) {
            return view('walikelas.no-class', compact('guru'));
        }

        // 2. Ambil Semua Siswa di Kelas Ini
        $totalSiswa = Siswa::where('kelas_id', $kelas->kelas_id)->count();

        // 3. Hitung Statistik Absensi Hari Ini (Menggunakan AbsensiHarian)
        $absensiHariIni = AbsensiHarian::whereDate('tanggal_absensi', $today)
            ->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->kelas_id);
            })
            ->get();

        $hadir = $absensiHariIni->where('status', 'Hadir')->count();
        $sakit = $absensiHariIni->where('status', 'Sakit')->count();
        $izin = $absensiHariIni->where('status', 'Izin')->count();
        $alfa = $absensiHariIni->where('status', 'Alfa')->count();

        // Menghitung yang belum absen (Total - yang sudah ada statusnya)
        $belumAbsen = $totalSiswa - $absensiHariIni->unique('siswa_id')->count();

        // 4. Ambil Detail Siswa yang Bermasalah Hari Ini (Sakit/Izin/Alfa)
        $siswaBermasalah = $absensiHariIni->whereIn('status', ['Sakit', 'Izin', 'Alfa']);

        return view('walikelas.dashboard', compact(
            'guru',
            'kelas',
            'totalSiswa',
            'hadir',
            'sakit',
            'izin',
            'alfa',
            'belumAbsen',
            'siswaBermasalah'
        ));
    }

    /**
     * Menampilkan Rekap Absensi Harian Detail (Dengan Filter Tanggal)
     */
    public function rekapHarian(Request $request)
    {
        $guru = Auth::user()->guru;

        // 1. Ambil Kelas Wali
        $kelas = Kelas::where('wali_kelas_id', $guru->guru_id)->first();

        if (!$kelas) {
            return redirect()->route('walikelas.dashboard')->with('error', 'Anda tidak memiliki kelas binaan.');
        }

        // 2. Tentukan Tanggal (Default hari ini jika tidak ada input)
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // 3. Ambil Semua Siswa di Kelas Tersebut (Urut abjad)
        $siswas = Siswa::where('kelas_id', $kelas->kelas_id)
            ->orderBy('nama_siswa')
            ->get();

        // 4. Ambil Data Absensi Harian pada Tanggal Tersebut
        // keyBy('siswa_id') agar mudah dipanggil di view: $absensis[$id]
        $absensis = AbsensiHarian::whereDate('tanggal_absensi', $tanggal)
            ->whereIn('siswa_id', $siswas->pluck('siswa_id'))
            ->get()
            ->keyBy('siswa_id');

        // 5. Hitung Statistik Ringkas untuk Header Halaman Rekap
        $stats = [
            'Hadir' => $absensis->where('status', 'Hadir')->count(),
            'Sakit' => $absensis->where('status', 'Sakit')->count(),
            'Izin'  => $absensis->where('status', 'Izin')->count(),
            'Alfa'  => $absensis->where('status', 'Alfa')->count(),
        ];
        $stats['BelumAbsen'] = $siswas->count() - $absensis->count();

        return view('walikelas.rekap-harian', compact(
            'guru',
            'kelas',
            'siswas',
            'absensis',
            'tanggal',
            'stats'
        ));
    }
}
