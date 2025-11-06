<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataAwalImport;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }
    public function store(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        DB::beginTransaction();
        try {
            // Hapus data lama agar tidak duplikat (opsional tapi disarankan)
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \App\Models\JadwalPelajaran::query()->delete();
            \App\Models\Siswa::query()->delete();
            \App\Models\Guru::query()->delete();
            \App\Models\MataPelajaran::query()->delete();
            \App\Models\Kelas::query()->delete();
            \App\Models\User::where('role', '!=', 'admin')->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Excel::import(new DataAwalImport, $request->file('file'));
            DB::commit();
            return back()->with('success', 'Semua data berhasil diimpor!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => 'Terjadi error saat impor: ' . $e->getMessage()]);
        }
    }
}
