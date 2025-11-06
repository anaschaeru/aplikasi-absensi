<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="nama_guru" class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
        <input type="text" name="nama_guru" id="nama_guru" class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('nama_guru', $guru->nama_guru ?? '') }}" required>
    </div>
    <div>
        <label for="nip" class="block font-medium text-sm text-gray-700">NIP (Nomor Induk Pegawai)</label>
        <input type="text" name="nip" id="nip"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('nip', $guru->nip ?? '') }}"
            required>
    </div>
    <div>
        <label for="email" class="block font-medium text-sm text-gray-700">Email (untuk Login)</label>
        <input type="email" name="email" id="email"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('email', $guru->user->email ?? '') }}" required>
    </div>
    <div>
        <label for="kontak" class="block font-medium text-sm text-gray-700">Nomor Kontak</label>
        <input type="text" name="kontak" id="kontak"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('kontak', $guru->kontak ?? '') }}">
    </div>
</div>
