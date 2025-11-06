<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Jobs\ProcessImportJob;

class ImportProgress extends Component
{
    use WithFileUploads;

    public $file;
    public $importId;
    public $status = '';
    public $progress = 0;
    public $isImporting = false;
    public $importFinished = false;

    public function import()
    {
        $this->validate(['file' => 'required|mimes:xlsx,xls']);

        $this->isImporting = true;
        $this->importFinished = false;
        $this->importId = 'import-' . Str::uuid();
        $this->status = 'Mengunggah file...';
        $this->progress = 0;

        $path = $this->file->store('imports');

        ProcessImportJob::dispatch(storage_path('app/' . $path), $this->importId);
    }

    public function getImportStatus()
    {
        $data = Cache::get($this->importId, ['status' => 'Memulai...', 'progress' => 0]);
        $this->status = $data['status'];
        $this->progress = $data['progress'];

        if ($this->progress >= 100) {
            $this->isImporting = false;
            $this->importFinished = true;
        }
    }

    public function render()
    {
        return view('livewire.import-progress');
    }
}
