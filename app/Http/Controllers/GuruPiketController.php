<?php

namespace App\Http\Controllers;

// Import Fasad & Class Pihak Ketiga
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// Import Model Aplikasi Anda
use App\Models\AbsensiHarian;
use App\Models\Izin;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;


class GuruPiketController extends Controller
{
    /**
     * Menampilkan dasbor guru piket.
     */
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();

        // 1. Ambil data statistik
        $totalSiswa = Siswa::count();
        $siswaHadirIds = AbsensiHarian::whereDate('tanggal_absensi', $today)->pluck('siswa_id');
        $jumlahHadir = $siswaHadirIds->count();
        $jumlahBelumHadir = $totalSiswa - $jumlahHadir;

        // 2. Ambil aktivitas absensi terbaru
        $aktivitasTerbaru = AbsensiHarian::whereDate('tanggal_absensi', $today)
            ->with('siswa.kelas')
            ->latest('updated_at') // Updated at agar kelihatan aktivitas pulang juga
            ->limit(10)
            ->get();

        // 3. Ambil daftar siswa yang belum hadir
        $siswaBelumHadir = Siswa::whereNotIn('siswa_id', $siswaHadirIds)
            ->with('kelas')
            ->orderBy('kelas_id')
            ->orderBy('nama_siswa')
            ->get();

        // 4. Ambil Jadwal Pelajaran Lengkap
        $jadwals = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])
            ->get()
            ->sortBy([
                ['kelas.nama_kelas', 'asc'],
                ['hari_id', 'asc'],
                ['jam_mulai', 'asc'],
            ])
            ->groupBy('kelas.nama_kelas');

        return view('guru-piket.dashboard', compact(
            'totalSiswa',
            'jumlahHadir',
            'jumlahBelumHadir',
            'aktivitasTerbaru',
            'siswaBelumHadir',
            'jadwals'
        ));
    }

    /**
     * Merekam absensi: Otomatis deteksi Masuk atau Pulang.
     */
    public function record(Request $request)
    {
        // Validasi input
        $request->validate([
            'siswa_id' => 'required|string', // NIS
            'image' => 'nullable|string',    // Foto Base64
        ]);

        $nis = $request->siswa_id;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 1. Cari Siswa
        $siswa = Siswa::with('kelas')->where('nis', $nis)->first();

        if (!$siswa) {
            return response()->json([
                'message' => 'Gagal: QR Code tidak valid/Siswa tidak ditemukan.'
            ], 404);
        }

        // 2. Cek apakah sudah ada data absensi hari ini?
        $absensi = AbsensiHarian::where('siswa_id', $siswa->siswa_id)
            ->whereDate('tanggal_absensi', $today)
            ->first();

        // --- SKENARIO 1: ABSEN MASUK (Data belum ada) ---
        if (!$absensi) {

            // Proses Simpan Foto Masuk
            $fotoPath = null;
            if ($request->filled('image')) {
                $fotoPath = $this->simpanFoto($request->image, $siswa->nis, 'masuk');
            }

            AbsensiHarian::create([
                'siswa_id' => $siswa->siswa_id,
                'tanggal_absensi' => $today,
                'waktu_masuk' => $now->toTimeString(),
                'status' => 'Hadir',
                'foto_masuk' => $fotoPath
            ]);

            return response()->json([
                'message' => 'Selamat Datang, ' . $siswa->nama_siswa . '!'
            ]);
        }

        // --- SKENARIO 2: ABSEN PULANG ---
        elseif ($absensi->waktu_pulang == null) {

            // 1. Gabungkan Tanggal dan Jam Masuk
            $waktuMasukString = $absensi->tanggal_absensi . ' ' . $absensi->waktu_masuk;

            // 2. Parse waktu masuk
            $waktuMasuk = Carbon::parse($waktuMasukString);

            // 3. Hitung selisih dalam DETIK (Gunakan abs() agar selalu positif)
            // abs() mengubah -1933 menjadi 1933
            $selisihDetik = abs($now->diffInSeconds($waktuMasuk));

            // Jika selisih kurang dari 60 detik (1 menit), tolak.
            // Sekarang logika: 1933 < 60 adalah FALSE -> Lanjut Absen Pulang (Benar)
            if ($selisihDetik < 7200) {
                $sisaDetik = 7200 - intval($selisihDetik);
                return response()->json([
                    'message' => "Baru saja absen masuk. Tunggu {$sisaDetik} detik lagi untuk pulang."
                ], 422);
            }

            // --- PROSES SIMPAN FOTO & UPDATE DATA ---
            $fotoPath = null;
            if ($request->filled('image')) {
                $fotoPath = $this->simpanFoto($request->image, $siswa->nis, 'pulang');
            }

            AbsensiHarian::where('siswa_id', $siswa->siswa_id)
                ->whereDate('tanggal_absensi', $today)
                ->update([
                    'waktu_pulang' => $now->toTimeString(),
                    'foto_pulang' => $fotoPath,
                    'updated_at' => $now
                ]);

            return response()->json([
                'message' => 'Hati-hati di jalan, ' . $siswa->nama_siswa . '!'
            ]);
        }

        // --- SKENARIO 3: SUDAH SELESAI (Masuk & Pulang sudah ada) ---
        else {
            return response()->json([
                'message' => 'Halo ' . $siswa->nama_siswa . ', Anda sudah melengkapi absen hari ini.'
            ], 409);
        }
    }

    /**
     * Helper untuk menyimpan foto Base64
     */
    private function simpanFoto($imageBase64, $nis, $tipe)
    {
        try {
            if (strpos($imageBase64, 'data:image') === 0) {
                $imageBase64 = explode(',', $imageBase64)[1];
            }
            $imageBase64 = str_replace(' ', '+', $imageBase64);
            $today = Carbon::today()->toDateString();

            // Nama file: absensi/2023-10-27/NIS_masuk_timestamp.jpg
            $imageName = 'absensi/' . $today . '/' . $nis . '_' . $tipe . '_' . time() . '.jpg';

            Storage::disk('public')->put($imageName, base64_decode($imageBase64));
            return $imageName;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Menyediakan data dashboard secara real-time via AJAX.
     */
    public function getDashboardData()
    {
        $today = Carbon::today()->toDateString();

        $totalSiswa = Siswa::count();
        $siswaHadirIds = AbsensiHarian::whereDate('tanggal_absensi', $today)->pluck('siswa_id');

        $jumlahHadir = $siswaHadirIds->count();
        $jumlahBelumHadir = $totalSiswa - $jumlahHadir;

        $aktivitasTerbaru = AbsensiHarian::whereDate('tanggal_absensi', $today)
            ->with('siswa.kelas')
            ->latest('updated_at') // Ubah ke updated_at agar absen pulang muncul paling atas
            ->limit(10)
            ->get();

        $siswaBelumHadir = Siswa::whereNotIn('siswa_id', $siswaHadirIds)
            ->with('kelas')
            ->orderBy('kelas_id')
            ->orderBy('nama_siswa')
            ->get();

        return response()->json([
            'jumlahHadir' => $jumlahHadir,
            'jumlahBelumHadir' => $jumlahBelumHadir,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'siswaBelumHadir' => $siswaBelumHadir,
        ]);
    }

    /**
     * Merekam absensi secara manual oleh guru piket.
     * (Hanya untuk Absen Masuk)
     */
    public function hadirkanManual(Request $request)
    {
        $request->validate(['siswa_id' => 'required|integer|exists:siswa,siswa_id']);

        $siswa = Siswa::with('kelas')->find($request->siswa_id);
        $today = Carbon::today()->toDateString();

        $alreadyExists = AbsensiHarian::where('siswa_id', $siswa->siswa_id)
            ->whereDate('tanggal_absensi', $today)
            ->exists();

        if ($alreadyExists) {
            return response()->json(['message' => 'Gagal: ' . $siswa->nama_siswa . ' sudah tercatat hadir.'], 409);
        }

        AbsensiHarian::create([
            'siswa_id' => $siswa->siswa_id,
            'tanggal_absensi' => $today,
            'waktu_masuk' => Carbon::now()->toTimeString(),
            'status' => 'Hadir'
        ]);

        return response()->json([
            'message' => 'Berhasil: ' . $siswa->nama_siswa . ' (' . ($siswa->kelas->nama_kelas ?? 'N/A') . ')'
        ]);
    }

    /**
     * Menampilkan daftar pengajuan izin.
     */
    public function indexIzin()
    {
        $izins = Izin::with('siswa.kelas')
            ->where('status', 'pending')
            ->orWhereDate('updated_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru-piket.izin-index', compact('izins'));
    }

    /**
     * Menyetujui pengajuan izin.
     */
    public function approveIzin(Izin $izin): RedirectResponse
    {
        $izin->update([
            'status' => 'disetujui',
            'diapprove_oleh' => Auth::id()
        ]);

        AbsensiHarian::updateOrCreate(
            [
                'siswa_id' => $izin->siswa_id,
                'tanggal_absensi' => $izin->tanggal_izin
            ],
            [
                'status' => 'Izin',
                'waktu_masuk' => Carbon::now()->toTimeString()
            ]
        );

        $izin->load('siswa');
        $namaSiswa = $izin->siswa ? $izin->siswa->nama_siswa : 'Siswa (ID: ' . $izin->siswa_id . ')';

        return redirect()->route('guru.piket.izin.index')->with('success', 'Izin untuk ' . $namaSiswa . ' telah disetujui.');
    }

    /**
     * Menolak pengajuan izin.
     */
    public function rejectIzin(Izin $izin): RedirectResponse
    {
        $izin->update([
            'status' => 'ditolak',
            'diapprove_oleh' => Auth::id()
        ]);

        AbsensiHarian::where('siswa_id', $izin->siswa_id)
            ->where('tanggal_absensi', $izin->tanggal_izin)
            ->where('status', 'Izin')
            ->delete();

        $izin->load('siswa');
        $namaSiswa = $izin->siswa ? $izin->siswa->nama_siswa : 'Siswa (ID: ' . $izin->siswa_id . ')';

        return redirect()->route('guru.piket.izin.index')->with('success', 'Izin untuk ' . $namaSiswa . ' telah ditolak.');
    }

    public function scan()
    {
        return redirect()->route('guru.piket.dashboard');
    }
}
