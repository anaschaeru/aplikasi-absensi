<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
// TAMBAHAN BARU: Import library Excel & Class Import
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar semua siswa. (READ)
     */
    public function index(Request $request)
    {
        $siswas = Siswa::with(['kelas', 'user'])
            ->orderBy('nama_siswa', 'asc')
            ->get();

        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * Menampilkan form untuk membuat siswa baru. (CREATE)
     */
    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('admin.siswa.create', compact('kelasList'));
    }

    /**
     * Menyimpan siswa baru ke database. (CREATE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'email' => 'required|string|email|max:255|unique:users,email',
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'alamat' => 'nullable|string',
        ]);

        // Buat user terlebih dahulu
        $user = User::create([
            'name' => $request->nama_siswa,
            'email' => $request->email,
            'password' => Hash::make('password'), // Password default
            'role' => 'siswa',
        ]);

        // Buat siswa dan hubungkan dengan user_id
        Siswa::create([
            'user_id' => $user->id,
            'nis' => $request->nis,
            'nama_siswa' => $request->nama_siswa,
            'kelas_id' => $request->kelas_id,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * FUNGSI BARU: IMPORT EXCEL
     * Dilengkapi dengan booster memori dan waktu eksekusi.
     */
    public function import(Request $request)
    {
        // 1. Validasi File
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        // 2. BOOSTER SERVER (PENTING UNTUK SHARED HOSTING)
        // Ini mencegah error "504 Gateway Timeout" atau "MySQL Gone Away"
        ini_set('max_execution_time', 600); // 10 Menit
        ini_set('memory_limit', '512M');    // 512 MegaBytes

        // 3. Eksekusi Import
        try {
            Excel::import(new SiswaImport, $request->file('file'));

            return back()->with('success', 'Data siswa berhasil diimpor!');
        } catch (\Exception $e) {
            // Tampilkan pesan error yang jelas jika gagal
            return back()->with('error', 'Gagal Impor: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk mengedit siswa. (UPDATE)
     */
    public function edit(Siswa $siswa)
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        // Muat relasi user agar bisa diakses di view
        $siswa->load('user');
        return view('admin.siswa.edit', compact('siswa', 'kelasList'));
    }

    /**
     * Memperbarui data siswa di database. (UPDATE)
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'nis' => [
                'required',
                'string',
                'max:20',
                'unique:siswa,nis,' . $siswa->siswa_id . ',siswa_id' // Syntax update unique Laravel yg lebih simpel
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($siswa->user_id)
            ],
            'kelas_id' => 'required|exists:kelas,kelas_id',
            'alamat' => 'nullable|string',
        ]);

        // Update data di tabel user
        $siswa->user->update([
            'name' => $request->nama_siswa,
            'email' => $request->email,
        ]);

        // Update data di tabel siswa
        $siswa->update($request->all());

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa dari database. (DELETE)
     */
    public function destroy(Siswa $siswa)
    {
        // Hapus juga data user yang terhubung untuk menjaga kebersihan database
        if ($siswa->user) {
            $siswa->user->delete();
        }

        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
