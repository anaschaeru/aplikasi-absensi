<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AbsensiHarian;
use App\Models\Kelas; // Pastikan model Kelas di-import
use Carbon\Carbon;
use App\Exports\AbsensiHarianExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanHarianController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tanggal dan kelas_id dari input filter
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kelas_id = $request->input('kelas_id');

        // 2. Ambil daftar kelas untuk diisi ke dropdown filter di View
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();

        // 3. Mulai query data absensi harian beserta relasi siswa dan kelas
        $query = AbsensiHarian::with(['siswa.kelas'])
            ->where('tanggal_absensi', $tanggal);

        // 4. Jika ada filter kelas yang dipilih, tambahkan pencarian berdasarkan kelas
        if ($kelas_id) {
            $query->whereHas('siswa', function ($q) use ($kelas_id) {
                // Catatan: Jika foreign key di tabel siswa namanya bukan 'kelas_id', silakan disesuaikan
                $q->where('kelas_id', $kelas_id);
            });
        }

        // 5. Eksekusi query dan urutkan berdasarkan waktu masuk
        $absensi = $query->orderBy('waktu_masuk', 'asc')->get();

        // 6. Hitung statistik singkat (Ini akan otomatis menghitung sesuai filter kelas jika ada)
        $totalHadir = $absensi->where('status', 'Hadir')->count();
        $totalIzinSakit = $absensi->whereIn('status', ['Izin', 'Sakit'])->count();
        $totalAlpa = $absensi->where('status', 'Alfa')->count();

        // 7. Kirim semua variabel yang dibutuhkan ke View
        return view('admin.laporan.harian', compact(
            'absensi',
            'tanggal',
            'totalHadir',
            'totalIzinSakit',
            'totalAlpa',
            'kelasList', // Data untuk opsi Dropdown
            'kelas_id'   // Untuk menahan status 'selected' pada Dropdown
        ));
    }

    public function exportExcel(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kelas_id = $request->input('kelas_id');

        // 1. Tentukan nama kelas untuk penamaan file
        $nama_kelas = 'Semua_Kelas';
        if ($kelas_id) {
            $kelas = Kelas::find($kelas_id);
            // str_replace berguna untuk mengubah spasi menjadi underscore, misal: "10 A" jadi "10_A"
            $nama_kelas = $kelas ? str_replace(' ', '_', $kelas->nama_kelas) : 'Semua_Kelas';
        }

        // 2. Buat format nama file
        $fileName = 'Laporan_Absensi_' . $tanggal . '_' . $nama_kelas . '.xlsx';

        return Excel::download(new AbsensiHarianExport($tanggal, $kelas_id), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kelas_id = $request->input('kelas_id');

        $query = AbsensiHarian::with(['siswa.kelas'])->where('tanggal_absensi', $tanggal);
        if ($kelas_id) {
            $query->whereHas('siswa', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }

        $absensi = $query->orderBy('waktu_masuk', 'asc')->get();
        $kelas = $kelas_id ? Kelas::find($kelas_id) : null;

        // 1. Tentukan nama kelas untuk penamaan file
        $nama_kelas = $kelas ? str_replace(' ', '_', $kelas->nama_kelas) : 'Semua_Kelas';

        // 2. Buat format nama file
        $fileName = 'Laporan_Absensi_' . $tanggal . '_' . $nama_kelas . '.pdf';

        $pdf = Pdf::loadView('admin.laporan.pdf-harian', compact('absensi', 'tanggal', 'kelas'));

        return $pdf->download($fileName);
    }
}
