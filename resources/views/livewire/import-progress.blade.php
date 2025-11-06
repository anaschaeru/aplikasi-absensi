<div>
    <div class="flex items-center justify-between mb-4">
        <a href="/templates/template_data_awal.xlsx"
            class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-700">
            Unduh Template Excel
        </a>
    </div>

    @if ($importFinished && $progress >= 100)
        @php($data = \Illuminate\Support\Facades\Cache::get($importId))
        @if (isset($data['error']) && $data['error'])
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong>Gagal!</strong> Terjadi kesalahan saat impor: {{ $status }}
            </div>
        @else
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <strong>Berhasil!</strong> Semua data telah selesai diimpor.
            </div>
        @endif
    @endif

    <form wire:submit.prevent="import">
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700">Pilih File Excel (.xlsx)</label>
            <input type="file" wire:model="file" id="file"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                required @if ($isImporting) disabled @endif>
            @error('file')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex justify-end mt-4">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-800"
                @if ($isImporting) disabled @endif>
                <span wire:loading.remove wire:target="import">Mulai Import</span>
                <span wire:loading wire:target="import">Mengunggah...</span>
            </button>
        </div>
    </form>

    @if ($isImporting)
        <div class="mt-4" wire:poll.1s="getImportStatus">
            <h3 class="text-lg font-medium">{{ $status }}</h3>
            <div class="w-full bg-gray-200 rounded-full mt-2">
                <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"
                    style="width: {{ $progress }}%"> {{ $progress }}% </div>
            </div>
        </div>
    @endif
</div>
