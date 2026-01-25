<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use App\Imports\GuruImport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class GuruController extends Controller
{
    /**
     * Menampilkan daftar semua guru dengan fitur pencarian. (READ)
     */
    public function index(Request $request)
    {
        $gurus = Guru::with('user')
            ->orderBy('nama_guru', 'asc')
            ->get();

        return view('admin.guru.index', compact('gurus'));
        return view('admin.guru.index', compact('gurus', 'search'));
    }

    /**
     * Menampilkan form untuk membuat guru baru. (CREATE)
     */
    public function create()
    {
        return view('admin.guru.create');
    }

    /**
     * Menyimpan guru baru ke database. (CREATE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => 'required|string|max:20|unique:guru,nip',
            'email' => 'required|string|email|max:255|unique:users,email',
            'kontak' => 'nullable|string|max:20',
        ]);

        // Buat user terlebih dahulu
        $user = User::create([
            'name' => $request->nama_guru,
            'email' => $request->email,
            'password' => Hash::make('password'), // Password default
            'role' => 'guru',
        ]);

        // Buat data guru dan hubungkan dengan user_id
        Guru::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'nama_guru' => $request->nama_guru,
            'kontak' => $request->kontak,
        ]);

        return redirect()->route('admin.guru.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit guru. (UPDATE)
     */
    public function edit(Guru $guru)
    {
        $guru->load('user');
        return view('admin.guru.edit', compact('guru'));
    }

    /**
     * Memperbarui data guru di database. (UPDATE)
     */
    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'max:20',
                Rule::unique('guru')->ignore($guru->guru_id, 'guru_id')
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($guru->user_id)
            ],
            'kontak' => 'nullable|string|max:20',
        ]);

        // Update data di tabel user
        if ($guru->user) {
            $guru->user->update([
                'name' => $request->nama_guru,
                'email' => $request->email,
            ]);
        }

        // Update data di tabel guru
        $guru->update($request->all());

        return redirect()->route('admin.guru.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Menghapus data guru dari database. (DELETE)
     */
    public function destroy(Guru $guru)
    {
        // Hapus data guru terlebih dahulu
        $guru->delete();

        // Hapus user yang terhubung (jika ada)
        if ($guru->user) {
            $guru->user->delete();
        }

        return redirect()->route('admin.guru.index')
            ->with('success', 'Guru berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file'));
            return redirect()->route('admin.guru.index')->with('success', 'Data Guru & Akun Login Berhasil Diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }
}
