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

        // --- Bagian 1: Jadwal Hari Ini ---
        $namaHariIni = Carbon::now()->locale('id')->isoFormat('dddd');
        $jadwalHariIni = JadwalPelajaran::where('guru_id', $guru->guru_id)
            ->where('hari', $namaHariIni)
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy('jam_mulai')
            ->get();

        // --- Bagian 2: Jadwal Seminggu Penuh ---
        $jadwalSeminggu = JadwalPelajaran::where('guru_id', $guru->guru_id)
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy(DB::raw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')"))
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        // --- Bagian 3: Persiapan Filter Rekap Absensi ---

        // 1. Ambil Daftar Kelas (yang diajar oleh guru ini)
        $kelasList = JadwalPelajaran::where('guru_id', $guru->guru_id)
            ->with('kelas')
            ->get()
            ->pluck('kelas.nama_kelas', 'kelas.kelas_id')
            ->unique();

        // 2. Ambil Daftar Mata Pelajaran (yang diajar oleh guru ini) - BARU
        $mapelList = JadwalPelajaran::where('guru_id', $guru->guru_id)
            ->with('mataPelajaran')
            ->get()
            ->pluck('mataPelajaran.nama_mapel', 'mapel_id')
            ->unique();

        // 3. Ambil Input dari Request
        $selectedKelasId = $request->input('kelas_id');
        $selectedMapelId = $request->input('mapel_id'); // Filter baru
        $selectedTanggalMulai = $request->input('tanggal_mulai', Carbon::now()->subDays(6)->toDateString());
        $selectedTanggalAkhir = $request->input('tanggal_akhir', Carbon::now()->toDateString());

        $rekapData = null;

        // 4. Proses Rekap Data (Jika Filter Dipilih)
        if ($selectedKelasId) {
            // Panggil Trait dengan parameter tambahan mapel_id
            $rekapData = $this->prosesRekapData(
                $guru->guru_id,
                $selectedKelasId,
                $selectedTanggalMulai,
                $selectedTanggalAkhir,
                $selectedMapelId // Kirim mapel_id ke trait (opsional/null jika tidak dipilih)
            );
        }

        return view('guru.dashboard', compact(
            'guru',
            'jadwalHariIni',
            'jadwalSeminggu',
            'namaHariIni',
            'kelasList',
            'mapelList',          // <-- Data baru dikirim ke view
            'rekapData',
            'selectedKelasId',
            'selectedMapelId',    // <-- Data baru dikirim ke view
            'selectedTanggalMulai',
            'selectedTanggalAkhir'
        ));
    }

    public function history()
    {
        $guru = Auth::user()->guru;

        $sesiAbsensi = Absensi::where('dicatat_oleh_guru_id', $guru->guru_id)
            ->with('jadwal.kelas', 'jadwal.mataPelajaran')
            ->select('jadwal_id', 'tanggal_absensi')
            ->groupBy('jadwal_id', 'tanggal_absensi')
            ->orderBy('tanggal_absensi', 'desc')
            ->paginate(15);

        return view('guru.riwayat', compact('guru', 'sesiAbsensi'));
    }

    public function historyShow($jadwal_id, $tanggal)
    {
        $guru = Auth::user()->guru;

        $jadwal = JadwalPelajaran::with('kelas', 'mataPelajaran')->findOrFail($jadwal_id);

        $detailAbsensi = Absensi::where('dicatat_oleh_guru_id', $guru->guru_id)
            ->where('jadwal_id', $jadwal_id)
            ->whereDate('tanggal_absensi', $tanggal)
            ->with(['siswa' => function ($query) {
                $query->orderBy('nama_siswa', 'asc');
            }])
            ->get();

        if ($detailAbsensi->isEmpty()) {
            abort(404);
        }

        return view('guru.riwayat-detail', compact('guru', 'jadwal', 'detailAbsensi', 'tanggal'));
    }
}
