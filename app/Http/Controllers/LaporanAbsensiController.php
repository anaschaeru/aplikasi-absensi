<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAbsensiExport; // Akan kita buat
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $hasilLaporan = null;

        // Logika untuk memproses filter
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query = Absensi::query()->with(['siswa.kelas', 'jadwal.mataPelajaran', 'guru']);

            // Filter berdasarkan rentang tanggal (wajib)
            $query->whereBetween('tanggal_absensi', [$request->tanggal_mulai, $request->tanggal_akhir]);

            // Filter berdasarkan kelas (opsional)
            if ($request->filled('kelas_id')) {
                $query->whereHas('siswa', function ($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            // Clone query untuk ringkasan dan detail
            $queryRingkasan = clone $query;
            $queryDetail = clone $query;

            // Ambil data untuk ringkasan
            $ringkasan = $queryRingkasan
                ->select(
                    DB::raw("COUNT(CASE WHEN status = 'Hadir' THEN 1 END) as hadir"),
                    DB::raw("COUNT(CASE WHEN status = 'Sakit' THEN 1 END) as sakit"),
                    DB::raw("COUNT(CASE WHEN status = 'Izin' THEN 1 END) as izin"),
                    DB::raw("COUNT(CASE WHEN status = 'Alfa' THEN 1 END) as alfa")
                )->first();

            // Ambil data untuk tabel detail dengan paginasi
            $detail = $queryDetail->latest('tanggal_absensi')->paginate(25)->withQueryString();

            $hasilLaporan = [
                'ringkasan' => $ringkasan,
                'detail' => $detail,
            ];
        }

        return view('admin.laporan.absensi', compact('kelasList', 'hasilLaporan'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['tanggal_mulai', 'tanggal_akhir', 'kelas_id']);
        $namaFile = 'laporan-absensi-' . Carbon::now()->format('d-m-Y') . '.xlsx';

        return Excel::download(new LaporanAbsensiExport($filters), $namaFile);
    }

    public function exportPdf(Request $request)
    {
        $filters = $request->only(['tanggal_mulai', 'tanggal_akhir', 'kelas_id']);

        $query = Absensi::query()->with(['siswa.kelas', 'jadwal.mataPelajaran', 'guru']);
        $query->whereBetween('tanggal_absensi', [$filters['tanggal_mulai'], $filters['tanggal_akhir']]);
        if (!empty($filters['kelas_id'])) {
            $query->whereHas('siswa', function ($q) use ($filters) {
                $q->where('kelas_id', $filters['kelas_id']);
            });
        }
        $dataLaporan = $query->latest('tanggal_absensi')->get();

        $kelas = !empty($filters['kelas_id']) ? Kelas::find($filters['kelas_id']) : null;

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'dataLaporan' => $dataLaporan,
            'filters' => $filters,
            'kelas' => $kelas,
        ]);

        $namaFile = 'laporan-absensi-' . Carbon::now()->format('d-m-Y') . '.pdf';
        return $pdf->download($namaFile);
    }
}
