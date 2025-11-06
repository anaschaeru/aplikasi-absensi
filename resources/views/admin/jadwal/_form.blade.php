<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Input Kelas (Tidak ada perubahan) --}}
    <div>
        <label for="kelas_id" class="block font-medium text-sm text-gray-700">Kelas</label>
        <select name="kelas_id" id="kelas_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
            <option value="">Pilih Kelas</option>
            @foreach ($kelasList as $kelas)
                <option value="{{ $kelas->kelas_id }}" @selected(old('kelas_id', $jadwal->kelas_id ?? '') == $kelas->kelas_id)>
                    {{ $kelas->nama_kelas }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Input Hari (Tidak ada perubahan) --}}
    <div>
        <label for="hari" class="block font-medium text-sm text-gray-700">Hari</label>
        <select name="hari" id="hari" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
            <option value="">Pilih Hari</option>
            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                <option value="{{ $hari }}" @selected(old('hari', $jadwal->hari ?? '') == $hari)>{{ $hari }}</option>
            @endforeach
        </select>
    </div>

    {{-- Input Mata Pelajaran (Tidak ada perubahan) --}}
    <div>
        <label for="mapel_id" class="block font-medium text-sm text-gray-700">Mata Pelajaran</label>
        <select name="mapel_id" id="mapel_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
            <option value="">Pilih Mata Pelajaran</option>
            @foreach ($mapelList as $mapel)
                <option value="{{ $mapel->mapel_id }}" @selected(old('mapel_id', $jadwal->mapel_id ?? '') == $mapel->mapel_id)>
                    {{ $mapel->nama_mapel }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Input Guru (Tidak ada perubahan) --}}
    <div>
        <label for="guru_id" class="block font-medium text-sm text-gray-700">Guru Pengajar</label>
        <select name="guru_id" id="guru_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
            <option value="">Pilih Guru</option>
            @foreach ($guruList as $guru)
                <option value="{{ $guru->guru_id }}" @selected(old('guru_id', $jadwal->guru_id ?? '') == $guru->guru_id)>
                    {{ $guru->nama_guru }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Input Jam Mulai (Diperbarui) --}}
    <div>
        <label for="jam_mulai" class="block font-medium text-sm text-gray-700">Jam Mulai</label>
        <input type="time" name="jam_mulai" id="jam_mulai"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('jam_mulai', isset($jadwal->jam_mulai) ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '') }}"
            required>
    </div>

    {{-- Input Jam Selesai (Diperbarui) --}}
    <div>
        <label for="jam_selesai" class="block font-medium text-sm text-gray-700">Jam Selesai</label>
        <input type="time" name="jam_selesai" id="jam_selesai"
            class="block mt-1 w-full rounded-md shadow-sm border-gray-300"
            value="{{ old('jam_selesai', isset($jadwal->jam_selesai) ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '') }}"
            required>
    </div>
</div>
