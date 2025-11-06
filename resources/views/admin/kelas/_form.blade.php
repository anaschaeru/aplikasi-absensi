<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="nama_kelas" class="block font-medium text-sm text-gray-700">Nama Kelas</label>
        <input type="text" name="nama_kelas" id="nama_kelas"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('nama_kelas', $kelas->nama_kelas ?? '') }}" required>
    </div>
    <div>
        <label for="tingkat" class="block font-medium text-sm text-gray-700">Tingkat</label>
        <select name="tingkat" id="tingkat" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
            <option value="10" @selected(old('tingkat', $kelas->tingkat ?? '') == 10)>10</option>
            <option value="11" @selected(old('tingkat', $kelas->tingkat ?? '') == 11)>11</option>
            <option value="12" @selected(old('tingkat', $kelas->tingkat ?? '') == 12)>12</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label for="wali_kelas_id" class="block font-medium text-sm text-gray-700">Wali Kelas (Opsional)</label>
        <select name="wali_kelas_id" id="wali_kelas_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
            <option value="">-- Tidak Ada Wali Kelas --</option>
            @foreach ($guruList as $guru)
                <option value="{{ $guru->guru_id }}" @selected(old('wali_kelas_id', $kelas->wali_kelas_id ?? '') == $guru->guru_id)>
                    {{ $guru->nama_guru }}
                </option>
            @endforeach
        </select>
    </div>
</div>
