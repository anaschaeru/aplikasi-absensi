<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;        // Untuk Dashboard (Absensi Mapel)
use App\Models\AbsensiHarian;  // Untuk Rekap Harian (Scan QR/Gerbang)
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class WalikelasController extends Controller
{
    /**
     * Dashboard Utama (Statistik Mapel)
     */
    public function dashboard()
    {
        $guru = Auth::user()->guru;
        $today = Carbon::today()->toDateString();

        // 1. Cari Kelas Wali (Cara Aman)
        $kelas = Kelas::where('wali_kelas_id', $guru->guru_id)->first();

        if (!$kelas) {
            return view('walikelas.no-class', compact('guru'));
        }

        $totalSiswa = Siswa::where('kelas_id', $kelas->kelas_id)->count();

        // Ambil Absensi MAPEL
        $absensiMapelHariIni = Absensi::whereDate('tanggal_absensi', $today)
            ->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->kelas_id);
            })
            ->with(['siswa', 'jadwal.mataPelajaran'])
            ->get();

        // Statistik Dashboard
        $hadir = $absensiMapelHariIni->where('status', 'Hadir')->count();
        $sakit = $absensiMapelHariIni->where('status', 'Sakit')->count();
        $izin = $absensiMapelHariIni->where('status', 'Izin')->count();
        $alfa = $absensiMapelHariIni->where('status', 'Alfa')->count();

        $siswaYangSudahDiabsenGuru = $absensiMapelHariIni->unique('siswa_id')->count();
        $belumAbsen = $totalSiswa - $siswaYangSudahDiabsenGuru;

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
     * Halaman Tabel Rekap Harian (View HTML)
     */
    public function rekapHarian(Request $request)
    {
        $guru = Auth::user()->guru;
        $kelas = Kelas::where('wali_kelas_id', $guru->guru_id)->first();

        if (!$kelas) {
            return redirect()->route('walikelas.dashboard')->with('error', 'Anda tidak memiliki kelas binaan.');
        }

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Ambil semua siswa di kelas ini
        $siswas = Siswa::where('kelas_id', $kelas->kelas_id)
            ->orderBy('nama_siswa')
            ->get();

        // Ambil data Absensi HARIAN (QR)
        $absensis = AbsensiHarian::whereDate('tanggal_absensi', $tanggal)
            ->whereIn('siswa_id', $siswas->pluck('siswa_id'))
            ->get()
            ->keyBy('siswa_id');

        // Statistik Harian
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

    /**
     * Update Status Manual
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,siswa_id', // Pastikan nama tabel 'siswas' atau 'siswa' sesuai DB Anda
            'tanggal'  => 'required|date',
            'status'   => 'required|in:Hadir,Sakit,Izin,Alfa',
        ]);

        AbsensiHarian::updateOrCreate(
            [
                'siswa_id'        => $request->siswa_id,
                'tanggal_absensi' => $request->tanggal,
            ],
            [
                'status'       => $request->status,
                // Workaround agar tidak error SQL jika kolom waktu tidak boleh null
                'waktu_masuk'  => Carbon::now()->toTimeString(),
                'waktu_pulang' => null,
            ]
        );

        return redirect()->back()->with('success', 'Status absensi berhasil diperbarui.');
    }

    /**
     * Export PDF (Fix Logic)
     */
    public function cetakPdf(Request $request)
    {
        // 1. Konfigurasi Memori (Wajib untuk PDF)
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        // 2. Ambil Parameter
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $user = Auth::user();

        // 3. Cari Kelas (Konsisten dengan method lain)
        // Gunakan where wali_kelas_id agar lebih pasti daripada $user->guru->kelas
        $kelas = Kelas::where('wali_kelas_id', $user->guru->guru_id)->first();

        if (!$kelas) {
            return redirect()->back()->with('error', 'Data kelas tidak ditemukan.');
        }

        // 4. QUERY DATA (BAGIAN YANG HILANG DI KODE SEBELUMNYA)
        // Kita ambil data siswa, lalu kita "tempelkan" data absensinya secara manual
        // agar mudah dibaca di view PDF: $siswa->absensi->status
        $dataAbsensi = Siswa::where('kelas_id', $kelas->kelas_id)
            ->orderBy('nama_siswa', 'asc')
            ->get()
            ->map(function ($siswa) use ($tanggal) {
                // Cari satu record absensi harian untuk tanggal ini
                $absen = AbsensiHarian::where('siswa_id', $siswa->siswa_id)
                    ->whereDate('tanggal_absensi', $tanggal)
                    ->first();

                // Tempelkan ke objek siswa sebagai relasi 'absensi'
                // Karena view PDF Anda menggunakan $siswa->absensi->first() (koleksi)
                // atau $siswa->absensi (objek langsung), kita sesuaikan:

                // OPSI: Kita jadikan collection agar view Anda yg pakai ->first() tetap jalan
                // Atau jika view PDF Anda sudah kita ubah jadi $absen (objek langsung), sesuaikan.
                // UNTUK AMANNYA, kita buat relasi manual:
                $siswa->setRelation('absensi', collect([$absen]));

                return $siswa;
            });

        // 5. Generate PDF
        $pdf = Pdf::loadView('walikelas.rekap.pdf_view', [
            'dataAbsensi' => $dataAbsensi, // Variable ini sekarang sudah ada isinya
            'tanggal'     => $tanggal,
            'nama_kelas'  => $kelas->nama_kelas
        ]);

        $pdf->setPaper('a4', 'portrait');

        // 6. Download
        $namaFile = 'Laporan_Harian_' . $kelas->nama_kelas . '_' . $tanggal . '.pdf';
        return $pdf->download($namaFile);

        // return view('walikelas.rekap.pdf_view', [
        //     'dataAbsensi' => $dataAbsensi,
        //     'tanggal' => $tanggal,
        //     'nama_kelas' => $kelas->nama_kelas
        // ]);
    }
}
