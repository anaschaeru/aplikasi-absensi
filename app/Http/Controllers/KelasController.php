<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru; // Import model Guru untuk dropdown wali kelas
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import Rule untuk validasi unik

class KelasController extends Controller
{
    // ... (method index, create, store sudah ada) ...
    public function index()
    {
        $kelasList = Kelas::with('waliKelas')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        return view('admin.kelas.index', compact('kelasList'));
    }

    public function create()
    {
        $guruList = Guru::orderBy('nama_guru')->get();
        return view('admin.kelas.create', compact('guruList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'tingkat' => 'required|integer|min:10|max:12',
            'wali_kelas_id' => 'nullable|exists:guru,guru_id',
        ]);

        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }


    /**
     * Menampilkan form untuk mengedit kelas.
     */
    public function edit(Kelas $kela) // Menggunakan Route Model Binding
    {
        $guruList = Guru::orderBy('nama_guru')->get();
        return view('admin.kelas.edit', [
            'kelas' => $kela,
            'guruList' => $guruList,
        ]);
    }

    /**
     * Memperbarui data kelas di database.
     */
    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:255',
                // Pastikan nama kelas unik, kecuali untuk kelas itu sendiri
                Rule::unique('kelas')->ignore($kela->kelas_id, 'kelas_id')
            ],
            'tingkat' => 'required|integer|min:10|max:12',
            'wali_kelas_id' => 'nullable|exists:guru,guru_id',
        ]);

        $kela->update($request->all());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Menghapus data kelas dari database.
     */
    public function destroy(Kelas $kela)
    {
        $kela->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
