<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanAbsensiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Absensi::query()->with(['siswa.kelas', 'jadwal.mataPelajaran', 'guru']);

        $query->whereBetween('tanggal_absensi', [$this->filters['tanggal_mulai'], $this->filters['tanggal_akhir']]);

        if (!empty($this->filters['kelas_id'])) {
            $query->whereHas('siswa', function ($q) {
                $q->where('kelas_id', $this->filters['kelas_id']);
            });
        }

        return $query->latest('tanggal_absensi');
    }

    public function headings(): array
    {
        return ['Tanggal', 'NIS', 'Nama Siswa', 'Kelas', 'Mata Pelajaran', 'Status', 'Catatan', 'Dicatat oleh'];
    }

    public function map($absensi): array
    {
        return [
            $absensi->tanggal_absensi,
            $absensi->siswa->nis ?? 'N/A',
            $absensi->siswa->nama_siswa ?? 'N/A',
            $absensi->siswa->kelas->nama_kelas ?? 'N/A',
            $absensi->jadwal->mataPelajaran->nama_mapel ?? 'N/A',
            $absensi->status,
            $absensi->catatan,
            $absensi->guru->nama_guru ?? 'N/A',
        ];
    }
}
