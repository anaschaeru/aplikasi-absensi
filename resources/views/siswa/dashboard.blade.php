@extends('layouts.master')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dasbor Siswa
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Bagian Welcome & Info Utama --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold">Selamat Datang, {{ $siswa->nama_siswa }}!</h3>
                    <p class="text-gray-600">NIS: {{ $siswa->nis }} | Kelas: {{ $siswa->kelas->nama_kelas }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kolom Kiri: Jadwal Hari Ini & Akses Cepat --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Card Jadwal Hari Ini --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="font-bold text-lg mb-4">Jadwal Pelajaran Hari Ini ({{ $namaHariIni }})</h4>
                            <div class="space-y-4">
                                @forelse ($jadwalHariIni as $jadwal)
                                    <div class="border-l-4 border-indigo-500 pl-4 py-2">
                                        <p class="font-semibold">{{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                        <p class="text-sm text-gray-600">
                                            Pukul: {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </p>
                                        <p class="text-sm text-gray-500">Guru: {{ $jadwal->guru->nama_guru }}</p>
                                    </div>
                                @empty
                                    <p class="text-gray-500">Tidak ada jadwal pelajaran hari ini. Selamat beristirahat!</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Rekap Absensi & QR Code --}}
                <div class="space-y-6">
                    {{-- Card Rekap Absensi --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="font-bold text-lg mb-4">Rekap Absensi Bulan Ini</h4>
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <div class="text-2xl font-bold text-green-800">{{ $rekapAbsensi->hadir ?? 0 }}</div>
                                    <div class="text-sm text-green-700">Hadir</div>
                                </div>
                                <div class="p-3 bg-yellow-100 rounded-lg">
                                    <div class="text-2xl font-bold text-yellow-800">{{ $rekapAbsensi->sakit ?? 0 }}</div>
                                    <div class="text-sm text-yellow-700">Sakit</div>
                                </div>
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-800">{{ $rekapAbsensi->izin ?? 0 }}</div>
                                    <div class="text-sm text-blue-700">Izin</div>
                                </div>
                                <div class="p-3 bg-red-100 rounded-lg">
                                    <div class="text-2xl font-bold text-red-800">{{ $rekapAbsensi->alfa ?? 0 }}</div>
                                    <div class="text-sm text-red-700">Alfa</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card Akses Cepat --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h4 class="font-bold text-lg mb-4">Akses Cepat</h4>
                            <a href="{{ route('siswa.my_qrcode') }}"
                                class="block w-full text-center bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-300">
                                Tampilkan QR Code Absensi
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- BAGIAN JADWAL LENGKAP SEMINGGU (DIUBAH JADI TABEL) --}}
            {{-- ========================================================== --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Jadwal Lengkap Seminggu</h3>
                    @if ($jadwalSeminggu->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                        {{-- Gunakan $urutanHari dari Controller --}}
                                        @foreach ($urutanHari as $hari)
                                            <th class="border border-gray-200 px-4 py-2">{{ $hari }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="align-top">
                                    <tr>
                                        @foreach ($urutanHari as $hari)
                                            <td class="border border-gray-200 p-2 day-column align-top">
                                                <div class="space-y-2">
                                                    {{-- Gunakan $jadwalSeminggu[$hari] --}}
                                                    @forelse ($jadwalSeminggu[$hari] ?? [] as $jadwal)
                                                        <div
                                                            class="bg-indigo-50 rounded-lg p-3 text-sm schedule-item hover:bg-indigo-100 transition">
                                                            <p class="font-bold text-indigo-800">
                                                                {{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}</p>
                                                            <p class="text-gray-600">
                                                                {{ $jadwal->guru->nama_guru ?? 'N/A' }}</p>
                                                            <p class="text-xs text-gray-500 font-mono mt-1">
                                                                {{ date('H:i', strtotime($jadwal->jam_mulai)) }} -
                                                                {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</p>
                                                            {{-- Tidak ada tombol edit/hapus untuk siswa --}}
                                                        </div>
                                                    @empty
                                                        <div
                                                            class="text-center text-xs text-gray-400 p-4 empty-day-message">
                                                            -- Kosong --</div>
                                                    @endforelse
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">Jadwal pelajaran belum diatur untuk kelas Anda.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

{{-- Tidak perlu @push('scripts') khusus untuk jadwal di halaman ini --}}
