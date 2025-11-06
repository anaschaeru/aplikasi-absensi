<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman untuk mengambil absensi.
     */
    public function create(JadwalPelajaran $jadwal)
    {
        // Load relasi siswa dari kelas yang ada di jadwal
        $jadwal->load(['kelas.siswa' => function ($query) {
            $query->orderBy('nama_siswa', 'asc');
        }, 'mataPelajaran']);

        // Cek apakah absensi untuk jadwal ini sudah pernah diambil hari ini
        $absensiHariIni = Absensi::where('jadwal_id', $jadwal->jadwal_id)
            ->whereDate('tanggal_absensi', Carbon::today())
            ->exists();

        if ($absensiHariIni) {
            // Jika sudah, beri pesan error dan kembalikan ke dasbor
            return redirect()->route('guru.dashboard')
                ->with('error', 'Absensi untuk kelas ini sudah diambil hari ini.');
        }

        return view('guru.absensi.create', compact('jadwal'));
    }

    /**
     * Menyimpan data absensi ke database.
     */
    public function store(Request $request, JadwalPelajaran $jadwal)
    {
        $request->validate([
            'absensi.*.status' => 'required|in:Hadir,Sakit,Izin,Alfa',
            'absensi.*.catatan' => 'nullable|string|max:255',
        ]);

        $guru = Auth::user()->guru;
        $tanggal = Carbon::today();

        // Loop melalui data absensi yang dikirim dari form
        foreach ($request->absensi as $siswaId => $data) {
            Absensi::create([
                'siswa_id' => $siswaId,
                'jadwal_id' => $jadwal->jadwal_id,
                'dicatat_oleh_guru_id' => $guru->guru_id,
                'tanggal_absensi' => $tanggal,
                'status' => $data['status'],
                'catatan' => $data['catatan'],
            ]);
        }

        return redirect()->route('guru.dashboard')
            ->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * Memperbarui data absensi yang sudah ada.
     */
    public function update(Request $request)
    {
        $request->validate([
            'absensi.*.status' => 'required|in:Hadir,Sakit,Izin,Alfa',
            'absensi.*.catatan' => 'nullable|string|max:255',
        ]);

        foreach ($request->absensi as $absensiId => $data) {
            $absensi = Absensi::findOrFail($absensiId);

            // Pastikan guru hanya bisa mengedit absensi miliknya sendiri
            if ($absensi->dicatat_oleh_guru_id == Auth::user()->guru->guru_id) {
                $absensi->update([
                    'status' => $data['status'],
                    'catatan' => $data['catatan'],
                ]);
            }
        }

        return back()->with('success', 'Absensi berhasil diperbarui.');
    }
}
