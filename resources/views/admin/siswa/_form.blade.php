<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="nama_siswa" class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
        <input type="text" name="nama_siswa" id="nama_siswa"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('nama_siswa', $siswa->nama_siswa ?? '') }}" required>
    </div>
    <div>
        <label for="nis" class="block font-medium text-sm text-gray-700">NIS (Nomor Induk Siswa)</label>
        <input type="text" name="nis" id="nis"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('nis', $siswa->nis ?? '') }}"
            required>
    </div>
    <div>
        <label for="email" class="block font-medium text-sm text-gray-700">Email (untuk Login)</label>
        {{-- Saat edit, user diambil dari relasi $siswa->user --}}
        <input type="email" name="email" id="email"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('email', $siswa->user->email ?? '') }}" required>
    </div>
    <div>
        <label for="kelas_id" class="block font-medium text-sm text-gray-700">Kelas</label>
        <select name="kelas_id" id="kelas_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
            <option value="">Pilih Kelas</option>
            @foreach ($kelasList as $kelas)
                <option value="{{ $kelas->kelas_id }}" @selected(old('kelas_id', $siswa->kelas_id ?? '') == $kelas->kelas_id)>
                    {{ $kelas->nama_kelas }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label for="alamat" class="block font-medium text-sm text-gray-700">Alamat</label>
        <textarea name="alamat" id="alamat" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
    </div>
</div>
