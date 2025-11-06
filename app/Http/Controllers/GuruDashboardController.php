<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\RekapAbsensiTrait;

class GuruDashboardController extends Controller
{
    use RekapAbsensiTrait;

    public function index(Request $request)
    {
        $guru = Auth::user()->guru;

        // --- Bagian 1: Jadwal Hari Ini (Tidak berubah) ---
        $namaHariIni = Carbon::now()->translatedFormat('l');
        $jadwalHariIni = JadwalPelajaran::where('guru_id', $guru->guru_id)
            ->where('hari', $namaHariIni)
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy('jam_mulai')
            ->get();

        // --- Bagian 2: Jadwal Seminggu Penuh (Baru) ---
        $jadwalSeminggu = JadwalPelajaran::where('guru_id', $guru->guru_id)
            ->with(['kelas', 'mataPelajaran'])
            // Urutkan berdasarkan hari (Senin-Sabtu) lalu jam mulai
            ->orderBy(DB::raw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')"))
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari'); // Kelompokkan berdasarkan hari

        // --- Bagian 3: Rekap Absensi (Tidak berubah) ---
        $kelasList = JadwalPelajaran::where('guru_id', $guru->guru_id)
            ->with('kelas')->get()->pluck('kelas.nama_kelas', 'kelas.kelas_id')->unique();

        $rekapData = null;
        $selectedKelasId = $request->input('kelas_id');
        // Ambil input tanggal, default ke 7 hari terakhir
        $selectedTanggalMulai = $request->input('tanggal_mulai', Carbon::now()->subDays(6)->toDateString());
        $selectedTanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->toDateString());

        if ($selectedKelasId) {
            // Panggil dengan argumen baru
            $rekapData = $this->prosesRekapData($guru->guru_id, $selectedKelasId, $selectedTanggalMulai, $selectedTanggalAkhir);
        }


        // Mengirim semua data (jadwal & rekap) ke satu view
        return view('guru.dashboard', compact(
            'guru',
            'jadwalHariIni',
            'jadwalSeminggu',
            'namaHariIni',
            'kelasList',
            'rekapData',
            'selectedKelasId',
            'selectedTanggalMulai',
            'selectedTanggalAkhir'
        ));
    }

    public function history()
    {
        $guru = Auth::user()->guru;

        // Ambil daftar sesi unik (berdasarkan jadwal dan tanggal)
        $sesiAbsensi = Absensi::where('dicatat_oleh_guru_id', $guru->guru_id)
            ->with('jadwal.kelas', 'jadwal.mataPelajaran') // Eager load relasi
            ->select('jadwal_id', 'tanggal_absensi')
            ->groupBy('jadwal_id', 'tanggal_absensi')
            ->orderBy('tanggal_absensi', 'desc')
            ->paginate(15);

        return view('guru.riwayat', compact('guru', 'sesiAbsensi'));
    }

    /**
     * BARU: Menampilkan detail absensi untuk sesi tertentu.
     */
    public function historyShow($jadwal_id, $tanggal)
    {
        $guru = Auth::user()->guru;

        // Ambil detail jadwal untuk header halaman
        $jadwal = JadwalPelajaran::with('kelas', 'mataPelajaran')->findOrFail($jadwal_id);

        // Ambil semua data absensi untuk sesi dan tanggal tersebut
        // Kode perbaikan yang benar
        $detailAbsensi = Absensi::where('dicatat_oleh_guru_id', $guru->guru_id)
            ->where('jadwal_id', $jadwal_id)
            ->whereDate('tanggal_absensi', $tanggal)
            ->with(['siswa' => function ($query) {
                $query->orderBy('nama_siswa', 'asc');
            }])
            ->get();

        // Jika tidak ada data (mencegah guru lain melihat), kembalikan error
        if ($detailAbsensi->isEmpty()) {
            abort(404);
        }

        return view('guru.riwayat-detail', compact('guru', 'jadwal', 'detailAbsensi', 'tanggal'));
    }
}
