<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // <-- Pastikan ini ada jika menggunakan DB::raw

class JadwalPelajaranController extends Controller
{
    /**
     * Menampilkan daftar semua jadwal, dikelompokkan per kelas.
     */
    public function index()
    {
        $jadwals = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])
            ->get()
            // Mengurutkan berdasarkan nama kelas, hari (sesuai urutan), dan jam mulai
            ->sortBy([
                ['kelas.nama_kelas', 'asc'],
                // Pastikan Anda memiliki kolom numerik (misal: hari_id) untuk urutan hari
                // Jika tidak ada, pengurutan mungkin tidak sesuai (misal: Jumat sebelum Kamis)
                // Opsi lain: Gunakan DB::raw("FIELD(...)") jika database mendukung
                ['hari_id', 'asc'], // Ganti 'hari_id' dengan kolom urutan hari Anda jika ada
                ['jam_mulai', 'asc'],
            ])
            ->groupBy('kelas.nama_kelas'); // Kelompokkan berdasarkan nama kelas

        return view('admin.jadwal.index', compact('jadwals'));
    }

    /**
     * Menampilkan form untuk membuat jadwal baru.
     */
    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $guruList = Guru::orderBy('nama_guru')->get();
        $mapelList = MataPelajaran::orderBy('nama_mapel')->get();

        return view('admin.jadwal.create', compact('kelasList', 'guruList', 'mapelList'));
    }

    /**
     * Menyimpan jadwal baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'mapel_id' => 'required|exists:mata_pelajaran,mapel_id',
            'guru_id' => 'required|exists:guru,guru_id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            // Tambahkan validasi untuk hari_id jika Anda menggunakannya
            // 'hari_id' => 'required|integer|between:1,6',
        ]);

        // Cek jadwal tumpang tindih (Logika Eksklusif)
        $isConflict = JadwalPelajaran::where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                // Tumpang tindih jika:
                // Jadwal lama dimulai SEBELUM jadwal baru berakhir
                $query->where('jam_mulai', '<', $request->jam_selesai)
                    // DAN Jadwal lama berakhir SETELAH jadwal baru dimulai
                    ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($isConflict) {
            return back()->withErrors(['jam_mulai' => 'Jadwal tumpang tindih dengan yang sudah ada untuk kelas dan hari ini.'])->withInput();
        }

        // Simpan data, termasuk hari_id jika ada
        JadwalPelajaran::create($request->all());

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jadwal.
     */
    public function edit(JadwalPelajaran $jadwal)
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $guruList = Guru::orderBy('nama_guru')->get();
        $mapelList = MataPelajaran::orderBy('nama_mapel')->get();

        return view('admin.jadwal.edit', compact('jadwal', 'kelasList', 'guruList', 'mapelList'));
    }

    /**
     * Memperbarui data jadwal di database.
     */
    public function update(Request $request, JadwalPelajaran $jadwal)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'mapel_id' => 'required|exists:mata_pelajaran,mapel_id',
            'guru_id' => 'required|exists:guru,guru_id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            // Tambahkan validasi untuk hari_id jika Anda menggunakannya
            // 'hari_id' => 'required|integer|between:1,6',
        ]);

        // Cek jadwal tumpang tindih (tidak termasuk jadwal saat ini - Logika Eksklusif)
        $isConflict = JadwalPelajaran::where('jadwal_id', '!=', $jadwal->jadwal_id) // Kecualikan jadwal ini
            ->where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                // Tumpang tindih jika:
                // Jadwal lama dimulai SEBELUM jadwal baru berakhir
                $query->where('jam_mulai', '<', $request->jam_selesai)
                    // DAN Jadwal lama berakhir SETELAH jadwal baru dimulai
                    ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($isConflict) {
            return back()->withErrors(['jam_mulai' => 'Jadwal tumpang tindih dengan yang sudah ada untuk kelas dan hari ini.'])->withInput();
        }

        // Update data, termasuk hari_id jika ada
        $jadwal->update($request->all());

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus data jadwal dari database.
     */
    public function destroy(JadwalPelajaran $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
