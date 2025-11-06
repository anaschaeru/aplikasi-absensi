<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\Siswa; // Tambahkan ini jika belum ada
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SiswaDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard siswa.
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa ? $user->siswa->load('kelas') : null;

        if (!$siswa) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan untuk user ini.');
        }

        // 1. Ambil Jadwal Hari Ini
        $namaHariIni = Carbon::now()->locale('id')->translatedFormat('l'); // Set lokal ke Indonesia
        $jadwalHariIni = JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
            ->where('hari', $namaHariIni)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        // 2. Ambil Rekap Absensi Bulan Ini
        $rekapAbsensi = Absensi::where('siswa_id', $siswa->siswa_id)
            ->whereMonth('tanggal_absensi', Carbon::now()->month)
            ->whereYear('tanggal_absensi', Carbon::now()->year)
            ->selectRaw("
                SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'Alfa' THEN 1 ELSE 0 END) as alfa
            ")
            ->first();

        // 3. Ambil Jadwal Lengkap Seminggu & Urutkan Harinya
        $jadwalSemingguQuery = JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru'])
            ->get() // Ambil semua data dulu
            ->sortBy([
                // Urutkan berdasarkan FIELD jika database mendukung dan efisien,
                // atau gunakan kolom numerik hari_id jika ada
                ['hari_id', 'asc'], // Ganti 'hari_id' dengan kolom urutan hari Anda
                ['jam_mulai', 'asc'],
            ])
            ->groupBy('hari'); // Baru kelompokkan berdasarkan nama hari

        // Definisikan urutan hari yang benar
        $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Buat collection baru dengan urutan hari yang benar
        $jadwalSeminggu = collect($urutanHari)->mapWithKeys(function ($hari) use ($jadwalSemingguQuery) {
            return [$hari => $jadwalSemingguQuery->get($hari, collect())]; // Ambil data hari, jika tidak ada, beri collection kosong
        });

        // 4. Kirim semua data ke view
        return view('siswa.dashboard', compact(
            'siswa',
            'jadwalHariIni',
            'namaHariIni',
            'rekapAbsensi',
            'jadwalSeminggu',
            'urutanHari' // Kirim urutan hari ke view untuk digunakan di tabel
        ));
    }

    /**
     * Menampilkan halaman QR Code siswa.
     */
    public function showMyQrCode()
    {
        $user = Auth::user();
        $siswa = $user->siswa ? $user->siswa->load('kelas') : null;

        if (!$siswa) {
            abort(404, 'Profil siswa tidak ditemukan.');
        }

        // Data yang akan dimasukkan ke QR Code (contoh: ID Siswa)
        $qrData = $siswa->siswa_id;

        // Generate QR Code
        $qrCode = QrCode::size(250)->generate($qrData);

        return view('siswa.my-qrcode', compact('siswa', 'qrCode'));
    }
}
