<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MataPelajaran;
use Illuminate\Validation\Rule;
use App\Imports\MataPelajaranImport;
use Maatwebsite\Excel\Facades\Excel;

class MataPelajaranController extends Controller
{
    public function index()
    {
        $mapels = MataPelajaran::orderBy('nama_mapel', 'asc')->get();

        return view('admin.mapel.index', compact('mapels'));
    }

    public function create()
    {
        return view('admin.mapel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:255|unique:mata_pelajaran,nama_mapel',
        ]);

        MataPelajaran::create($request->all());

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(MataPelajaran $mapel)
    {
        return view('admin.mapel.edit', compact('mapel'));
    }

    public function update(Request $request, MataPelajaran $mapel)
    {
        $request->validate([
            'kode_mapel' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mata_pelajaran')->ignore($mapel->mapel_id, 'mapel_id')
            ],
            'nama_mapel' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mata_pelajaran')->ignore($mapel->mapel_id, 'mapel_id')
            ],
        ]);

        $mapel->update($request->all());

        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(MataPelajaran $mapel)
    {
        // Tambahkan proteksi agar mapel yang sudah dipakai di jadwal tidak bisa dihapus
        if ($mapel->jadwalPelajaran()->exists()) {
            return redirect()->route('admin.mapel.index')
                ->with('error', 'Mata pelajaran tidak bisa dihapus karena sudah digunakan di jadwal.');
        }

        $mapel->delete();
        return redirect()->route('admin.mapel.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new MataPelajaranImport, $request->file('file'));
            return redirect()->route('admin.mapel.index')->with('success', 'Data Mata Pelajaran Berhasil Diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }
}
