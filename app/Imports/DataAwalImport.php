<?php

namespace App\Imports;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class DataAwalImport implements WithMultipleSheets, WithEvents
{
    protected $importId;

    public function __construct(string $importId)
    {
        $this->importId = $importId;
    }

    public function sheets(): array
    {
        return [
            'Kelas' => new KelasImport(),
            'Mata Pelajaran' => new MataPelajaranImport(),
            'Guru' => new GuruImport(),
            'Siswa' => new SiswaImport(),
            'Jadwal Pelajaran' => new JadwalPelajaranImport(),
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheetName = $event->getSheet()->getTitle();
                Log::info('Event impor berjalan untuk sheet: ' . $sheetName);

                $progressMap = [
                    'Kelas' => 20,
                    'Mata Pelajaran' => 35,
                    'Guru' => 50,
                    'Siswa' => 75,
                    'Jadwal Pelajaran' => 90,
                ];

                Cache::put($this->importId, [
                    'status' => 'Mengimpor sheet: ' . $sheetName . '...',
                    'progress' => $progressMap[$sheetName] ?? 15
                ]);

                sleep(1);
            },
        ];
    }
}
