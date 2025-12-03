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
                    <div class="flex items-center">
                        {{-- TOMBOL BERALIH KE WALI KELAS (Hanya muncul jika role = walikelas) --}}
                        @if (auth()->user()->role == 'walikelas')
                            <a href="{{ route('walikelas.dashboard') }}"
                                class="mr-3 px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition shadow-sm flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Dasbor Wali Kelas
                            </a>
                        @endif

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

                {{-- =================================== --}}
                {{-- KARTU JADWAL MENGAJAR LENGKAP --}}
                {{-- =================================== --}}
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
                    <button @click="open = !open"
                        class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Rekap Absensi</h3>
                            <p class="text-sm text-gray-500 mt-1">Gunakan filter untuk melihat rekapitulasi kehadiran siswa
                                per kelas dan mata pelajaran.</p>
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
                                {{-- UPDATE GRID: Menjadi 5 kolom pada layar besar agar muat input Mapel --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                                    {{-- 1. PILIH KELAS --}}
                                    <div>
                                        <label for="kelas_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                                        <select name="kelas_id" id="kelas_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                            <option value="">Pilih Kelas</option>
                                            @foreach ($kelasList as $id => $nama)
                                                <option value="{{ $id }}"
                                                    {{ $selectedKelasId == $id ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 2. PILIH MATA PELAJARAN (BARU) --}}
                                    <div>
                                        <label for="mapel_id" class="block text-sm font-medium text-gray-700">Mata
                                            Pelajaran</label>
                                        <select name="mapel_id" id="mapel_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Semua Mapel</option>
                                            @foreach ($mapelList as $id => $nama)
                                                <option value="{{ $id }}"
                                                    {{ $selectedMapelId == $id ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- 3. TANGGAL MULAI --}}
                                    <div>
                                        <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tgl
                                            Mulai</label>
                                        <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                            value="{{ $selectedTanggalMulai }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                    </div>

                                    {{-- 4. TANGGAL AKHIR --}}
                                    <div>
                                        <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tgl
                                            Akhir</label>
                                        <input type="date" name="tanggal_akhir" id="tanggal_akhir"
                                            value="{{ $selectedTanggalAkhir }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                    </div>

                                    {{-- 5. TOMBOL SUBMIT --}}
                                    <div>
                                        <button type="submit"
                                            class="w-full py-2 px-4 rounded-md text-white bg-indigo-600 hover:bg-indigo-700 font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                            Tampilkan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Tabel Hasil Rekap --}}
                        @if ($rekapData)
                            <div class="p-6 overflow-x-auto">
                                <div class="flex flex-col md:flex-row justify-between items-start mb-4 gap-4">
                                    <div>
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Hasil Rekapitulasi</h3>
                                        <div class="mt-1 text-sm text-gray-500 space-y-1">
                                            <p>Kelas: <span
                                                    class="font-medium text-gray-700">{{ $rekapData['info']['nama_kelas'] }}</span>
                                            </p>
                                            {{-- Tampilkan nama mapel jika dipilih --}}
                                            @if ($selectedMapelId && isset($mapelList[$selectedMapelId]))
                                                <p>Mapel: <span
                                                        class="font-medium text-gray-700">{{ $mapelList[$selectedMapelId] }}</span>
                                                </p>
                                            @endif
                                            <p>Periode: <span
                                                    class="font-medium text-gray-700">{{ $rekapData['info']['periode'] }}</span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Tombol Export (Updated links with mapel_id) --}}
                                    <div class="flex-shrink-0 flex space-x-2">
                                        <a href="{{ route('guru.rekap.export.excel', ['kelas_id' => $selectedKelasId, 'mapel_id' => $selectedMapelId, 'tanggal_mulai' => $selectedTanggalMulai, 'tanggal_akhir' => $selectedTanggalAkhir]) }}"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                                            <svg class="mr-2 h-4 w-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                </path>
                                            </svg>
                                            Excel
                                        </a>
                                        <a href="{{ route('guru.rekap.export.pdf', ['kelas_id' => $selectedKelasId, 'mapel_id' => $selectedMapelId, 'tanggal_mulai' => $selectedTanggalMulai, 'tanggal_akhir' => $selectedTanggalAkhir]) }}"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                                            <svg class="mr-2 h-4 w-4 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            PDF
                                        </a>
                                    </div>
                                </div>

                                <table
                                    class="min-w-full divide-y divide-gray-200 mt-4 border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                No</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                NIS</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nama
                                                Siswa</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-green-600 uppercase tracking-wider bg-green-50">
                                                H</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-yellow-600 uppercase tracking-wider bg-yellow-50">
                                                S</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-blue-600 uppercase tracking-wider bg-blue-50">
                                                I</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-medium text-red-600 uppercase tracking-wider bg-red-50">
                                                A</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($rekapData['rekap'] as $data)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $data['nis'] }}</td>
                                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                    {{ $data['nama_siswa'] }}</td>
                                                <td
                                                    class="px-6 py-4 text-sm text-center font-bold text-green-700 bg-green-50/50">
                                                    {{ $data['kehadiran']['Hadir'] }}</td>
                                                <td
                                                    class="px-6 py-4 text-sm text-center font-bold text-yellow-700 bg-yellow-50/50">
                                                    {{ $data['kehadiran']['Sakit'] }}</td>
                                                <td
                                                    class="px-6 py-4 text-sm text-center font-bold text-blue-700 bg-blue-50/50">
                                                    {{ $data['kehadiran']['Izin'] }}</td>
                                                <td
                                                    class="px-6 py-4 text-sm text-center font-bold text-red-700 bg-red-50/50">
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

@push('scripts')
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
@endpush
