<?php

namespace App\Http\Controllers;

// Import Fasad & Class Pihak Ketiga
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // <-- TAMBAHAN: Untuk simpan foto
use Illuminate\Support\Str; // <-- TAMBAHAN: Helper string
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
        $namaHariIni = Carbon::now()->locale('id')->translatedFormat('l');

        // 1. Ambil data statistik
        $totalSiswa = Siswa::count();
        $siswaHadirIds = AbsensiHarian::whereDate('tanggal_absensi', $today)->pluck('siswa_id');
        $jumlahHadir = $siswaHadirIds->count();
        $jumlahBelumHadir = $totalSiswa - $jumlahHadir;

        // 2. Ambil aktivitas absensi terbaru
        $aktivitasTerbaru = AbsensiHarian::whereDate('tanggal_absensi', $today)
            ->with('siswa.kelas')
            ->latest('created_at') // Urutkan berdasarkan waktu pembuatan record
            ->limit(10) // Batasi jumlah aktivitas
            ->get();

        // 3. Ambil daftar siswa yang belum hadir
        $siswaBelumHadir = Siswa::whereNotIn('siswa_id', $siswaHadirIds)
            ->with('kelas')
            ->orderBy('kelas_id') // Urutkan berdasarkan kelas dulu
            ->orderBy('nama_siswa') // Lalu berdasarkan nama
            ->get();

        // 4. Ambil Jadwal Pelajaran Lengkap
        $jadwals = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])
            ->get()
            ->sortBy([
                ['kelas.nama_kelas', 'asc'],
                ['hari_id', 'asc'], // Pastikan ada kolom hari_id (Senin=1, dst)
                ['jam_mulai', 'asc'],
            ])
            ->groupBy('kelas.nama_kelas'); // Kelompokkan berdasarkan nama kelas

        // 5. Kirim semua data ke view
        return view('guru-piket.dashboard', compact(
            'totalSiswa',
            'jumlahHadir',
            'jumlahBelumHadir',
            'aktivitasTerbaru',
            'siswaBelumHadir',
            'jadwals' // <-- Kirim data jadwal
        ));
    }

    /**
     * Merekam absensi dari hasil scan QR Code + Foto Wajah.
     */
    public function record(Request $request)
    {
        // Validasi input
        $request->validate([
            'siswa_id' => 'required|string', // NIS
            'image' => 'nullable|string',    // String Base64 Foto
        ]);

        $nis = $request->siswa_id;
        $today = Carbon::today()->toDateString();

        // 1. Cari Siswa Berdasarkan NIS
        $siswa = Siswa::with('kelas')->where('nis', $nis)->first();

        // Jika siswa tidak ditemukan (QR Code salah/tidak terdaftar)
        if (!$siswa) {
            return response()->json([
                'message' => 'Gagal: QR Code tidak valid. Siswa dengan NIS tersebut tidak ditemukan.'
            ], 404);
        }

        // 2. Cek apakah siswa sudah absen hari ini
        $alreadyExists = AbsensiHarian::where('siswa_id', $siswa->siswa_id)
            ->whereDate('tanggal_absensi', $today)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Gagal: ' . $siswa->nama_siswa . ' sudah absen hari ini.'
            ], 409); // 409 Conflict
        }

        // 3. Proses Simpan Foto (Jika dikirim dari frontend)
        $fotoPath = null;
        if ($request->filled('image')) {
            try {
                $image = $request->image;
                // Bersihkan string base64
                if (strpos($image, 'data:image') === 0) {
                    $image = explode(',', $image)[1];
                }
                $image = str_replace(' ', '+', $image);

                // Nama file unik: absensi/TANGGAL/NIS_TIMESTAMP.jpg
                $imageName = 'absensi/' . $today . '/' . $siswa->nis . '_' . time() . '.jpg';

                // Simpan ke storage public
                Storage::disk('public')->put($imageName, base64_decode($image));
                $fotoPath = $imageName;
            } catch (\Exception $e) {
                // Jika gagal simpan foto, lanjut saja rekam absensi tapi tanpa foto
                // Opsional: Log error here
            }
        }

        // 4. Rekam Absensi ke Database
        AbsensiHarian::create([
            'siswa_id' => $siswa->siswa_id, // Gunakan ID asli
            'tanggal_absensi' => $today,
            'waktu_masuk' => Carbon::now()->toTimeString(),
            'status' => 'Hadir',
            'foto_masuk' => $fotoPath // Simpan path foto
        ]);

        return response()->json([
            'message' => 'Berhasil: ' . $siswa->nama_siswa . ' (' . ($siswa->kelas->nama_kelas ?? 'N/A') . ')'
        ]);
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
            ->latest('created_at')
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
     * (Ini tetap menggunakan ID karena diklik dari tombol, bukan scan)
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
            'status' => 'Hadir',
            // Manual tidak ada foto
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
}
