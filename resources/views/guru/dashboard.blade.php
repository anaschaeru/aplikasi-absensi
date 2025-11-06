@extends('layouts.master')

@section('header')
    {{-- Header default dikosongkan untuk diganti dengan header sticky kustom --}}
@endsection

@section('content')
    <div class="bg-gray-50 min-h-screen">
        {{-- =================================== --}}
        {{-- HEADER STICKY KUSTOM --}}
        {{-- =================================== --}}
        <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-sm shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div>
                        <h2 class="font-bold text-xl text-gray-800">Selamat Datang, {{ $guru->nama_guru }}!</h2>
                        <p class="text-sm text-gray-500">Jadwal mengajar Anda untuk hari {{ $namaHariIni }}.</p>
                    </div>
                    <div>
                        <a href="{{ route('guru.absensi.history') }}"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                            Riwayat Absensi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                {{-- =================================== --}}
                {{-- KARTU DAFTAR JADWAL HARI INI --}}
                {{-- =================================== --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Jadwal Mengajar Hari Ini</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @forelse ($jadwalHariIni as $jadwal)
                            <div class="p-4 flex justify-between items-center hover:bg-gray-50 transition">
                                <div class="flex items-center space-x-4">
                                    <div class="text-center font-mono text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">
                                        <div class="text-sm">{{ date('H:i', strtotime($jadwal->jam_mulai)) }}</div>
                                        <div class="text-xs text-indigo-400">hingga</div>
                                        <div class="text-sm">{{ date('H:i', strtotime($jadwal->jam_selesai)) }}</div>
                                    </div>
                                    <div>
                                        <div class="font-bold text-lg text-gray-800">
                                            {{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                        <div class="text-sm text-gray-600">Kelas {{ $jadwal->kelas->nama_kelas }}</div>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('guru.absensi.create', ['jadwal' => $jadwal->jadwal_id]) }}"
                                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                        Ambil Absensi
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-500">
                                <p>Tidak ada jadwal mengajar hari ini. Selamat beristirahat! ☕</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Jadwal Mengajar Lengkap</h3>
                        <div class="space-y-6">
                            @forelse ($jadwalSeminggu as $hari => $jadwals)
                                <div>
                                    <h4 class="font-bold text-md text-indigo-700">{{ $hari }}</h4>
                                    <div class="mt-2 border-l-2 border-indigo-200 pl-4 space-y-3">
                                        @foreach ($jadwals as $jadwal)
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-sm font-semibold">
                                                        {{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                                    <p class="text-xs text-gray-600">
                                                        Kelas: {{ $jadwal->kelas->nama_kelas }} |
                                                        Pukul:
                                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500">Tidak ada jadwal mengajar yang ditetapkan untuk Anda.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- =================================== --}}
                {{-- PANEL REKAP ABSENSI (ACCORDION) --}}
                {{-- =================================== --}}
                <div x-data="{ open: {{ $rekapData ? 'true' : 'false' }} }" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    {{-- Tombol untuk membuka/menutup accordion --}}
                    <button @click="open = !open" class="w-full flex justify-between items-center p-6 text-left">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Rekap Absensi</h3>
                            <p class="text-sm text-gray-500 mt-1">Gunakan filter untuk melihat rekapitulasi kehadiran siswa
                                per kelas dalam rentang waktu tertentu.</p>
                        </div>
                        <svg class="h-6 w-6 text-gray-500 transform transition-transform" :class="{ 'rotate-180': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Konten yang bisa dilipat --}}
                    <div x-show="open" x-transition class="border-t border-gray-200">
                        {{-- Form Filter --}}
                        <div class="p-6 bg-gray-50">
                            <form action="{{ route('guru.dashboard') }}" method="GET">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                    <div>
                                        <label for="kelas_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                                        <select name="kelas_id" id="kelas_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md" required>
                                            <option value="">Pilih Kelas</option>
                                            @foreach ($kelasList as $id => $nama)
                                                <option value="{{ $id }}"
                                                    {{ $selectedKelasId == $id ? 'selected' : '' }}>{{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal
                                            Mulai</label>
                                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                            value="{{ $selectedTanggalMulai }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md" required>
                                    </div>
                                    <div>
                                        <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal
                                            Akhir</label>
                                        <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                                            value="{{ $selectedTanggalAkhir }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md" required>
                                    </div>
                                    <button type="submit"
                                        class="w-full py-2 px-4 rounded-md text-white bg-indigo-600 hover:bg-indigo-700 font-semibold">Tampilkan</button>
                                </div>
                            </form>
                        </div>

                        {{-- Tabel Hasil Rekap --}}
                        @if ($rekapData)
                            <div class="p-6 overflow-x-auto">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Hasil Rekapitulasi</h3>
                                        <p class="mt-1 text-sm text-gray-500">Kelas: {{ $rekapData['info']['nama_kelas'] }}
                                            |
                                            Wali Kelas: {{ $rekapData['info']['wali_kelas'] }} |
                                            Periode: {{ $rekapData['info']['periode'] }}</p>
                                    </div>
                                    <div class="flex-shrink-0 flex space-x-2">
                                        <a href="{{ route('guru.rekap.export.excel', ['kelas_id' => $selectedKelasId, 'tanggal_mulai' => $selectedTanggalMulai, 'tanggal_akhir' => $selectedTanggalAkhir]) }}"
                                            class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Excel</a>
                                        <a href="{{ route('guru.rekap.export.pdf', ['kelas_id' => $selectedKelasId, 'tanggal_mulai' => $selectedTanggalMulai, 'tanggal_akhir' => $selectedTanggalAkhir]) }}"
                                            class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">PDF</a>
                                    </div>
                                </div>
                                <table class="min-w-full divide-y divide-gray-200 mt-4">
                                    {{-- ... isi tabel rekap Anda tetap sama ... --}}
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                                Siswa</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">H
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">S
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">I
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">A
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($rekapData['rekap'] as $data)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $data['nis'] }}</td>
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $data['nama_siswa'] }}</td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $data['kehadiran']['Hadir'] }}</td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $data['kehadiran']['Sakit'] }}</td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $data['kehadiran']['Izin'] }}</td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-500">
                                                    {{ $data['kehadiran']['Alfa'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Notifikasi Sukses & Error --}}
    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json(session('error')),
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true
            });
        </script>
    @endif
@endsection
