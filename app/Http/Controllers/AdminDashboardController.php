<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\AbsensiHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Statistik Umum (Tidak berubah)
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();

        // =====================================================================
        // 2. QUERY BARU: REKAP STATUS AKHIR SISWA HARI INI (Untuk Grafik)
        // =====================================================================
        $statusPriorityQuery = "CASE
            WHEN status = 'Alfa' THEN 4
            WHEN status = 'Sakit' THEN 3
            WHEN status = 'Izin' THEN 2
            WHEN status = 'Hadir' THEN 1
            ELSE 0
        END";

        // Subquery untuk menentukan status prioritas tertinggi setiap siswa
        $studentDailyStatuses = DB::table('absensi')
            ->select('siswa_id', DB::raw("MAX({$statusPriorityQuery}) as max_priority"))
            ->whereDate('tanggal_absensi', $today)
            ->groupBy('siswa_id');

        // Query utama untuk menghitung jumlah siswa berdasarkan status akhir mereka
        $rekapAbsensiHariIni = DB::table($studentDailyStatuses, 'daily_statuses')
            ->select(
                DB::raw("SUM(CASE WHEN max_priority = 4 THEN 1 ELSE 0 END) as alfa"),
                DB::raw("SUM(CASE WHEN max_priority = 3 THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN max_priority = 2 THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN max_priority = 1 THEN 1 ELSE 0 END) as hadir")
            )
            ->first();


        // 3. Aktivitas Absensi Gerbang (Piket) Terbaru (Tidak berubah)
        $aktivitasTerbaru = AbsensiHarian::whereDate('tanggal_absensi', $today)
            ->with('siswa.kelas')
            ->latest()
            // ->take(5)
            ->get();

        // 4. Kelas dengan absensi "Alfa" terbanyak hari ini (Tidak berubah)
        $kelasAbsensiTerbanyak = Absensi::whereDate('tanggal_absensi', $today)
            ->where('status', 'Alfa')
            ->join('siswa', 'absensi.siswa_id', '=', 'siswa.siswa_id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.kelas_id')
            ->select('kelas.nama_kelas', DB::raw('COUNT(*) as jumlah_alfa'))
            ->groupBy('kelas.nama_kelas')
            ->orderByDesc('jumlah_alfa')
            ->limit(5)
            ->get();

        // Pastikan variabel yang dikirim ke view sama
        return view('admin.dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'rekapAbsensiHariIni', // Variabel ini sekarang berisi data baru
            'aktivitasTerbaru',
            'kelasAbsensiTerbanyak'
        ));
    }
}
