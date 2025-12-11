@extends('layouts.master')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dasbor Wali Kelas
    </h2>
@endsection

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Info Kelas & Tombol Beralih --}}
            <div
                class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col md:flex-row justify-between items-center gap-4">

                {{-- Bagian Kiri: Info Kelas --}}
                <div class="text-center md:text-left">
                    <h3 class="text-2xl font-bold text-gray-800">Kelas {{ $kelas->nama_kelas }}</h3>
                    <p class="text-gray-500">Wali Kelas: {{ $guru->nama_guru }}</p>
                </div>

                {{-- Bagian Kanan: Tombol & Tanggal --}}
                <div class="flex flex-col md:flex-row items-center gap-4">

                    {{-- TOMBOL AKSES DASHBOARD GURU --}}
                    <a href="{{ route('guru.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition ease-in-out duration-150 shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                            </path>
                        </svg>
                        Ke Dashboard Guru
                    </a>

                    <div class="text-center md:text-right hidden md:block border-l pl-4 border-gray-200">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Hari ini</p>
                        <p class="text-base font-bold text-gray-700">
                            {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Statistik Cards (Data dari Absensi Mapel) --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                {{-- Total Siswa --}}
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-gray-500">
                    <p class="text-xs text-gray-500 font-bold uppercase">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalSiswa }}</p>
                </div>
                {{-- Hadir --}}
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-500">
                    <p class="text-xs text-green-600 font-bold uppercase">Hadir (KBM)</p>
                    <p class="text-2xl font-bold text-green-700">{{ $hadir }}</p>
                </div>
                {{-- Sakit --}}
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-500">
                    <p class="text-xs text-yellow-600 font-bold uppercase">Sakit</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $sakit }}</p>
                </div>
                {{-- Izin --}}
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <p class="text-xs text-blue-600 font-bold uppercase">Izin</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $izin }}</p>
                </div>
                {{-- Alfa --}}
                <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                    <p class="text-xs text-red-600 font-bold uppercase">Alfa / Belum Diabsen</p>
                    <p class="text-2xl font-bold text-red-700">{{ $alfa + $belumAbsen }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Tabel Masalah Kehadiran (SUMBER: ABSENSI GURU MAPEL) --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h4 class="font-bold text-lg mb-1 text-gray-800">Masalah Kehadiran di Kelas (Mapel)</h4>
                    <p class="text-xs text-gray-500 mb-4">Data berdasarkan input Guru Mata Pelajaran hari ini.</p>

                    @if ($siswaBermasalah->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mapel
                                        </th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($siswaBermasalah as $absen)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900 font-medium">
                                                {{ $absen->siswa->nama_siswa }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-600">
                                                {{-- Menampilkan Nama Mapel & Jam --}}
                                                <div class="font-semibold">
                                                    {{ $absen->jadwal->mataPelajaran->nama_mapel ?? '-' }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $absen->jadwal ? \Carbon\Carbon::parse($absen->jadwal->jam_mulai)->format('H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $absen->status == 'Sakit' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $absen->status == 'Izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $absen->status == 'Alfa' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $absen->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Semua siswa hadir di semua mata pelajaran hari ini. 👍</p>
                        </div>
                    @endif
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h4 class="font-bold text-lg mb-4 text-gray-800">Menu Cepat</h4>
                    <div class="grid grid-cols-1 gap-4">

                        {{-- Tombol ke Rekap Harian Detail (QR/Gerbang) --}}
                        <a href="{{ route('walikelas.rekap.harian') }}"
                            class="flex items-center p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition border border-emerald-100">
                            <div class="p-3 bg-emerald-600 rounded-full text-white mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-emerald-700">Cek Absensi Harian (QR)</p>
                                <p class="text-sm text-emerald-600">Lihat siapa yang datang terlambat/tepat waktu & fotonya.
                                </p>
                            </div>
                        </a>

                        {{-- Tombol ke Rekap Bulanan (Mapel) --}}
                        <a href="{{ route('guru.dashboard', ['kelas_id' => $kelas->kelas_id]) }}"
                            class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition border border-indigo-100">
                            <div class="p-3 bg-indigo-600 rounded-full text-white mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-indigo-700">Rekap Mapel Bulanan</p>
                                <p class="text-sm text-indigo-500">Lihat rekap kehadiran siswa di setiap mata pelajaran.</p>
                            </div>
                        </a>

                        {{-- Tombol ke Riwayat Mengajar (Fitur Guru Biasa) --}}
                        <a href="{{ route('guru.absensi.history') }}"
                            class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200">
                            <div class="p-3 bg-gray-600 rounded-full text-white mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-700">Riwayat Mengajar Saya</p>
                                <p class="text-sm text-gray-500">Lihat jadwal dan absensi mapel Anda.</p>
                            </div>
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
