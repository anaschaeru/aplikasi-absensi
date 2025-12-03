<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalikelasController extends Controller
{
    public function dashboard()
    {
        $guru = Auth::user()->guru;
        $today = Carbon::today()->toDateString();

        // 1. Cari Kelas yang diampu oleh Guru ini
        // Asumsi: tabel 'kelas' punya kolom 'wali_kelas_id' yang berelasi ke 'guru_id'
        $kelas = Kelas::where('wali_kelas_id', $guru->guru_id)->first();

        // Jika guru ini bukan wali kelas manapun
        if (!$kelas) {
            return view('walikelas.no-class', compact('guru'));
        }

        // 2. Ambil Semua Siswa di Kelas Ini
        $totalSiswa = Siswa::where('kelas_id', $kelas->kelas_id)->count();

        // 3. Hitung Statistik Absensi Hari Ini
        $absensiHariIni = Absensi::whereDate('tanggal_absensi', $today)
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
}
