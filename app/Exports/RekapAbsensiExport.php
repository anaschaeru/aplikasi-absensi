<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAbsensiExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $rekapData;

    public function __construct(array $rekapData)
    {
        $this->rekapData = $rekapData;
    }

    /**
     * Menggunakan View Blade untuk layout Excel.
     * Ini akan mengambil tampilan dari file excel.blade.php yang baru kita buat.
     */
    public function view(): View
    {
        return view('guru.rekap.excel', [
            'rekapData' => $this->rekapData
        ]);
    }

    /**
     * Judul Sheet di Excel
     */
    public function title(): string
    {
        return 'Rekap Absensi';
    }

    /**
     * Styling tambahan (Opsional)
     * Contoh: Mengatur lebar kolom otomatis atau styling border
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style default sudah dihandle di Blade,
            // fungsi ini dibiarkan ada untuk implementasi WithStyles
        ];
    }
}
