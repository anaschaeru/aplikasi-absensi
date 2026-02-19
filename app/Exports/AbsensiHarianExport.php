<?php

namespace App\Exports;

use App\Models\AbsensiHarian;
use App\Models\Kelas; // Tambahkan ini untuk mengambil nama kelas
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles; // Tambahkan ini untuk styling Excel
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiHarianExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tanggal;
    protected $kelas_id;
    protected $nama_kelas;

    public function __construct($tanggal, $kelas_id)
    {
        $this->tanggal = $tanggal;
        $this->kelas_id = $kelas_id;

        // Cari nama kelas berdasarkan ID yang difilter
        if ($kelas_id) {
            $kelas = Kelas::find($kelas_id);
            $this->nama_kelas = $kelas ? $kelas->nama_kelas : 'Semua Kelas';
        } else {
            $this->nama_kelas = 'Semua Kelas';
        }
    }

    public function collection()
    {
        $query = AbsensiHarian::with(['siswa.kelas'])->where('tanggal_absensi', $this->tanggal);

        if ($this->kelas_id) {
            $query->whereHas('siswa', function ($q) {
                $q->where('kelas_id', $this->kelas_id);
            });
        }

        return $query->orderBy('waktu_masuk', 'asc')->get();
    }

    public function map($absen): array
    {
        return [
            $absen->siswa->nama_siswa ?? '-',
            $absen->siswa->nis ?? '-',
            $absen->siswa->kelas->nama_kelas ?? '-',
            $absen->waktu_masuk ? \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i') : '-',
            $absen->waktu_pulang ? \Carbon\Carbon::parse($absen->waktu_pulang)->format('H:i') : '-',
            $absen->status
        ];
    }

    public function headings(): array
    {
        // Format tanggal agar lebih rapi (Contoh: 20 Februari 2026)
        $tanggalFormat = Carbon::parse($this->tanggal)->translatedFormat('d F Y');

        return [
            ['LAPORAN ABSENSI HARIAN'],
            ['Tanggal', ':', $tanggalFormat],
            ['Kelas', ':', $this->nama_kelas],
            [''], // Baris kosong sebagai jarak / spasi
            ['Nama Siswa', 'NIS', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Status'] // Header Tabel (Ini jatuh di baris ke-5)
        ];
    }

    // Fungsi untuk mempercantik tampilan Excel
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris 1: Judul Laporan (Tebal & Ukuran Font 14)
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Baris 5: Header Tabel (Dibuat Tebal)
            5 => ['font' => ['bold' => true]],
        ];
    }
}
