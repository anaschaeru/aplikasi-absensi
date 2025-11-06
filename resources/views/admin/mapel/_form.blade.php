<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="kode_mapel" class="block font-medium text-sm text-gray-700">Kode Mata Pelajaran</label>
        <input type="text" name="kode_mapel" id="kode_mapel"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('kode_mapel', $mapel->kode_mapel ?? '') }}" required autofocus>
    </div>
    <div>
        <label for="nama_mapel" class="block font-medium text-sm text-gray-700">Nama Mata Pelajaran</label>
        <input type="text" name="nama_mapel" id="nama_mapel"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('nama_mapel', $mapel->nama_mapel ?? '') }}" required>
    </div>
</div>
