<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran; // Pastikan ini sesuai nama Model Anda
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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

    /**
     * Import Jadwal dari Excel dengan Validasi Penuh
     */
    public function import(Request $request)
    {
        // Validasi file input
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        try {
            // Baca data Excel
            $data = Excel::toArray([], $request->file('file'));

            // Cek file kosong
            if (empty($data) || empty($data[0])) {
                return redirect()->back()->with('error', 'File Excel kosong atau tidak terbaca.');
            }

            $rows = $data[0];
            array_shift($rows); // Buang baris Header (Judul Kolom)

            DB::beginTransaction(); // Mulai transaksi database

            foreach ($rows as $index => $row) {
                // Ambil data dari Excel dan bersihkan spasi tambahan (trim)
                // Gunakan operator ?? '' untuk mencegah error jika sel kosong
                $namaKelasExcel = trim($row[0] ?? '');
                $namaMapelExcel = trim($row[1] ?? '');
                $namaGuruExcel  = trim($row[2] ?? '');
                $hariExcel      = trim($row[3] ?? '');

                // Lewati baris jika data utama kosong
                if (empty($namaKelasExcel) && empty($namaMapelExcel)) continue;

                // ==========================================================
                // TAHAP 1: CEK NAMA KELAS -> DAPATKAN KELAS_ID
                // ==========================================================
                $kelas = Kelas::where('nama_kelas', $namaKelasExcel)->first();

                // Jika $kelas kosong (null), berarti nama di Excel SALAH/TIDAK ADA di DB
                if (!$kelas) {
                    throw new \Exception(
                        "GAGAL pada Baris " . ($index + 2) . ". \n" .
                            "Sistem mencari Kelas: '$namaKelasExcel'. \n" .
                            "Hasil: TIDAK DITEMUKAN di Database. \n" .
                            "Solusi: Cek ejaan, spasi, atau tanda strip (-) agar sama persis dengan Data Kelas."
                    );
                }


                // ==========================================================
                // TAHAP 2: CEK NAMA MAPEL -> DAPATKAN MAPEL_ID
                // ==========================================================
                $mapel = MataPelajaran::where('nama_mapel', $namaMapelExcel)->first();

                if (!$mapel) {
                    throw new \Exception(
                        "GAGAL pada Baris " . ($index + 2) . ". \n" .
                            "Sistem mencari Mapel: '$namaMapelExcel'. \n" .
                            "Hasil: TIDAK DITEMUKAN di Database."
                    );
                }


                // ==========================================================
                // TAHAP 3: CEK NAMA GURU -> DAPATKAN GURU_ID
                // ==========================================================
                $guru = Guru::where('nama_guru', $namaGuruExcel)->first();

                if (!$guru) {
                    throw new \Exception(
                        "GAGAL pada Baris " . ($index + 2) . ". \n" .
                            "Sistem mencari Guru: '$namaGuruExcel'. \n" .
                            "Hasil: TIDAK DITEMUKAN di Database. \n" .
                            "Solusi: Pastikan gelar dan ejaan nama Guru sama persis."
                    );
                }


                // ==========================================================
                // TAHAP 4: SIMPAN KE DATABASE (Jika semua ID ditemukan)
                // ==========================================================
                // dd($kelas, $mapel, $guru, $hariExcel, $row[4] ?? '07:00', $row[5] ?? '08:00');
                JadwalPelajaran::create([
                    'kelas_id'      => $kelas->kelas_id, // Menggunakan ID hasil pencarian Tahap 1
                    'mapel_id'      => $mapel->mapel_id, // Menggunakan ID hasil pencarian Tahap 2
                    'guru_id'       => $guru->guru_id,  // Menggunakan ID hasil pencarian Tahap 3
                    'hari'          => ucfirst(strtolower($hariExcel)),
                    'jam_mulai'     => $this->formatTime($row[4] ?? '07:00'),
                    'jam_selesai'   => $this->formatTime($row[5] ?? '08:00'),
                ]);
            }


            DB::commit(); // Simpan permanen
            return redirect()->back()->with('success', 'Import Berhasil! Semua data valid.');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Helper Format Waktu (Jangan lupa sertakan function ini juga di controller)
    private function formatTime($excelTime)
    {
        if (empty($excelTime)) return '00:00:00';
        if (is_numeric($excelTime)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelTime)->format('H:i:s');
        }
        try {
            return date('H:i:s', strtotime($excelTime));
        } catch (\Exception $e) {
            return '00:00:00';
        }
    }
}
