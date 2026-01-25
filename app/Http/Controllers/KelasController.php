<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Imports\KelasImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule; // Import Rule untuk validasi unik
use App\Models\Guru; // Import model Guru untuk dropdown wali kelas

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

    // TAMBAHKAN FUNCTION INI
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new KelasImport, $request->file('file'));
            return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas Berhasil Diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }
}
