<?php

namespace App\Jobs;

use App\Imports\DataAwalImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $importId;

    public function __construct(string $filePath, string $importId)
    {
        $this->filePath = $filePath;
        $this->importId = $importId;
    }

    public function handle(): void
    {
        try {
            Cache::put($this->importId, ['status' => 'Mempersiapkan database...', 'progress' => 10]);
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            \App\Models\JadwalPelajaran::query()->delete();
            \App\Models\Siswa::query()->delete();
            \App\Models\Guru::query()->delete();
            \App\Models\MataPelajaran::query()->delete();
            \App\Models\Kelas::query()->delete();
            \App\Models\User::where('role', '!=', 'admin')->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            sleep(1);

            Excel::import(new DataAwalImport($this->importId), $this->filePath);

            Cache::put($this->importId, ['status' => 'Selesai!', 'progress' => 100]);
        } catch (Throwable $e) {
            Cache::put($this->importId, [
                'status' => 'Error: ' . $e->getMessage(),
                'progress' => 100,
                'error' => true
            ]);
        }
    }
}
