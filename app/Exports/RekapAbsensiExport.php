<?php

namespace App\Exports; // <-- PASTIKAN NAMESPACE INI BENAR

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

// PASTIKAN NAMA CLASS INI BENAR
class RekapAbsensiExport implements FromCollection, WithTitle, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $collection = collect();

        // Info Header (ini tidak akan memiliki heading)
        $collection->push(['REKAPITULASI ABSENSI']);
        $collection->push(['Kelas:', $this->data['info']['nama_kelas']]);
        $collection->push(['Periode:', $this->data['info']['periode']]);
        $collection->push(['Guru:', $this->data['info']['nama_guru']]);
        $collection->push([]); // Baris kosong sebagai pemisah

        // Tambahkan heading untuk data tabel
        $collection->push($this->headings());

        // Data rekap
        foreach ($this->data['rekap'] as $row) {
            $collection->push([
                'NIS' => $row['nis'],
                'Nama Siswa' => $row['nama_siswa'],
                'Hadir' => $row['kehadiran']['Hadir'],
                'Sakit' => $row['kehadiran']['Sakit'],
                'Izin' => $row['kehadiran']['Izin'],
                'Alfa' => $row['kehadiran']['Alfa'],
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        // Header untuk tabel data
        return [
            'NIS',
            'Nama Siswa',
            'Hadir',
            'Sakit',
            'Izin',
            'Alfa',
        ];
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }
}
