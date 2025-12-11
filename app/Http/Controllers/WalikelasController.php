<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
// Kita butuh kedua model ini
use App\Models\Absensi;        // Untuk Dashboard (Absensi Guru Mapel)
use App\Models\AbsensiHarian;  // Untuk Rekap Harian (Scan QR Gerbang)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalikelasController extends Controller
{
    /**
     * Menampilkan Dashboard Utama Wali Kelas
     * SUMBER DATA: Absensi Mata Pelajaran (Guru)
     */
    public function dashboard()
    {
        $guru = Auth::user()->guru;
        $today = Carbon::today()->toDateString();

        // 1. Cari Kelas Wali
        $kelas = Kelas::where('wali_kelas_id', $guru->guru_id)->first();

        if (!$kelas) {
            return view('walikelas.no-class', compact('guru'));
        }

        // 2. Total Siswa
        $totalSiswa = Siswa::where('kelas_id', $kelas->kelas_id)->count();

        // 3. Ambil Absensi MATA PELAJARAN Hari Ini untuk kelas ini
        // Menggunakan model 'Absensi', bukan 'AbsensiHarian'
        $absensiMapelHariIni = Absensi::whereDate('tanggal_absensi', $today)
            ->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->kelas_id);
            })
            ->with(['siswa', 'jadwal.mataPelajaran', 'jadwal.guru']) // Eager load untuk detail
            ->get();

        // Hitung Statistik (Ini menghitung total kejadian/record di semua mapel)
        // Misal: Budi Sakit di Math, Izin di Fisika -> Terhitung 2 kejadian
        $hadir = $absensiMapelHariIni->where('status', 'Hadir')->count();
        $sakit = $absensiMapelHariIni->where('status', 'Sakit')->count();
        $izin = $absensiMapelHariIni->where('status', 'Izin')->count();
        $alfa = $absensiMapelHariIni->where('status', 'Alfa')->count();

        // Menghitung siswa yang belum diabsen sama sekali oleh GURU MAPEL hari ini
        // (Total Siswa - Jumlah Siswa Unik yang ada di tabel absensi mapel hari ini)
        $siswaYangSudahDiabsenGuru = $absensiMapelHariIni->unique('siswa_id')->count();
        $belumAbsen = $totalSiswa - $siswaYangSudahDiabsenGuru;

        // 4. Ambil Detail Siswa Bermasalah (Dari Absensi Guru)
        // Menampilkan data siswa yang S/I/A di mata pelajaran apapun hari ini
        $siswaBermasalah = $absensiMapelHariIni->whereIn('status', ['Sakit', 'Izin', 'Alfa'])
            ->sortByDesc('created_at');

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
     * Menampilkan Rekap Absensi Harian Detail
     * SUMBER DATA: Absensi Harian (Scan QR/Gerbang) -> TETAP SAMA
     */
    public function rekapHarian(Request $request)
    {
        $guru = Auth::user()->guru;
        $kelas = Kelas::where('wali_kelas_id', $guru->guru_id)->first();

        if (!$kelas) {
            return redirect()->route('walikelas.dashboard')->with('error', 'Anda tidak memiliki kelas binaan.');
        }

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $siswas = Siswa::where('kelas_id', $kelas->kelas_id)
            ->orderBy('nama_siswa')
            ->get();

        // TETAP MENGGUNAKAN AbsensiHarian (Scan QR)
        $absensis = AbsensiHarian::whereDate('tanggal_absensi', $tanggal)
            ->whereIn('siswa_id', $siswas->pluck('siswa_id'))
            ->get()
            ->keyBy('siswa_id');

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
