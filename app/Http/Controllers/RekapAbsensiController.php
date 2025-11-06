<?php

namespace App\Http\Controllers;

use App\Http\Traits\RekapAbsensiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsensiExport;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapAbsensiController extends Controller
{
    use RekapAbsensiTrait;
    /**
     * Menangani ekspor ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $guruId = Auth::user()->guru->guru_id;
        $kelasId = $request->query('kelas_id');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalAkhir = $request->query('tanggal_akhir');
        $rekapData = $this->prosesRekapData($guruId, $kelasId, $tanggalMulai, $tanggalAkhir);
        $namaFile = "rekap-absensi-{$rekapData['info']['nama_kelas']}-{$tanggalMulai}-sd-{$tanggalAkhir}.xlsx";
        return Excel::download(new RekapAbsensiExport($rekapData), $namaFile);
    }

    /**
     * Menangani ekspor ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $guruId = Auth::user()->guru->guru_id;
        $kelasId = $request->query('kelas_id');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalAkhir = $request->query('tanggal_akhir');
        $rekapData = $this->prosesRekapData($guruId, $kelasId, $tanggalMulai, $tanggalAkhir);
        $namaFile = "rekap-absensi-{$rekapData['info']['nama_kelas']}-{$tanggalMulai}-sd-{$tanggalAkhir}.pdf";

        $pdf = Pdf::loadView('guru.rekap.pdf', compact('rekapData'));

        return $pdf->download($namaFile);
    }
}
