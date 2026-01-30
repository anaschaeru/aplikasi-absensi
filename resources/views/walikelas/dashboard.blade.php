@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Dashboard Wali Kelas
  </h2>
@endsection

@section('content')
  <div class="py-6 md:py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- 1. Info Kelas & Tombol Beralih --}}
      <div
        class="mb-6 bg-white overflow-hidden shadow-sm rounded-xl p-4 md:p-6 flex flex-col md:flex-row justify-between items-center gap-4 border border-gray-100">

        {{-- Bagian Kiri: Info Kelas --}}
        <div class="text-center md:text-left w-full md:w-auto">
          <h3 class="text-xl md:text-2xl font-bold text-gray-800">Kelas {{ $kelas->nama_kelas }}</h3>
          <p class="text-sm md:text-base text-gray-500 mt-1">Wali Kelas: <span
              class="font-medium text-gray-700">{{ $guru->nama_guru }}</span></p>
        </div>

        {{-- Bagian Kanan: Tombol & Tanggal --}}
        <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">

          {{-- TOMBOL AKSES DASHBOARD GURU --}}
          <a href="{{ route('guru.dashboard') }}"
            class="w-full md:w-auto justify-center inline-flex items-center px-4 py-3 md:py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring ring-indigo-300 transition ease-in-out duration-150 shadow-md">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
              </path>
            </svg>
            Ke Dashboard Guru
          </a>

          {{-- Tanggal (Hanya di Desktop untuk menghemat tempat di HP) --}}
          <div class="hidden md:block text-right border-l pl-4 border-gray-200 ml-2">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Hari ini</p>
            <p class="text-base font-bold text-gray-700 whitespace-nowrap">
              {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
            </p>
          </div>
        </div>
      </div>

      {{-- 2. Statistik Cards --}}
      {{-- Grid Mobile: 2 Kolom, Grid Desktop: 5 Kolom --}}
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 mb-6 md:mb-8">
        {{-- Total Siswa --}}
        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border-l-4 border-gray-500 relative overflow-hidden">
          <p class="text-[10px] md:text-xs text-gray-500 font-bold uppercase tracking-wider">Total Siswa</p>
          <p class="text-xl md:text-2xl font-bold text-gray-800 mt-1">{{ $totalSiswa }}</p>
        </div>
        {{-- Hadir --}}
        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border-l-4 border-green-500">
          <p class="text-[10px] md:text-xs text-green-600 font-bold uppercase tracking-wider">Hadir</p>
          <p class="text-xl md:text-2xl font-bold text-green-700 mt-1">{{ $hadir }}</p>
        </div>
        {{-- Sakit --}}
        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border-l-4 border-yellow-500">
          <p class="text-[10px] md:text-xs text-yellow-600 font-bold uppercase tracking-wider">Sakit</p>
          <p class="text-xl md:text-2xl font-bold text-yellow-700 mt-1">{{ $sakit }}</p>
        </div>
        {{-- Izin --}}
        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border-l-4 border-blue-500">
          <p class="text-[10px] md:text-xs text-blue-600 font-bold uppercase tracking-wider">Izin</p>
          <p class="text-xl md:text-2xl font-bold text-blue-700 mt-1">{{ $izin }}</p>
        </div>
        {{-- Alfa --}}
        {{-- Di HP, card terakhir ini akan jadi lebar penuh jika ganjil (col-span-2) --}}
        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border-l-4 border-red-500 col-span-2 md:col-span-1">
          <p class="text-[10px] md:text-xs text-red-600 font-bold uppercase tracking-wider">Alfa / Belum</p>
          <p class="text-xl md:text-2xl font-bold text-red-700 mt-1">{{ $alfa + $belumAbsen }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- 3. Tabel Masalah Kehadiran --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
          <div class="p-4 md:p-6 border-b border-gray-100">
            <h4 class="font-bold text-lg text-gray-800 flex items-center">
              <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
              </svg>
              Masalah Kehadiran (Mapel)
            </h4>
            <p class="text-xs text-gray-500 mt-1">Data input Guru Mapel hari ini.</p>
          </div>

          <div class="flex-grow p-0">
            @if ($siswaBermasalah->count() > 0)
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Siswa</th>
                      <th class="px-3 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Mapel</th>
                      <th
                        class="px-3 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ket</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($siswaBermasalah as $absen)
                      <tr>
                        {{-- Nama Siswa (Allow Wrap) --}}
                        <td class="px-3 md:px-6 py-3 whitespace-normal">
                          <div class="text-sm font-medium text-gray-900 leading-tight">
                            {{ $absen->siswa->nama_siswa }}
                          </div>
                        </td>

                        {{-- Mapel & Jam --}}
                        <td class="px-3 md:px-6 py-3 whitespace-normal">
                          <div class="text-xs md:text-sm text-gray-800 font-medium">
                            {{ $absen->jadwal->mataPelajaran->nama_mapel ?? '-' }}
                          </div>
                          <div class="text-[10px] md:text-xs text-gray-500">
                            {{ $absen->jadwal ? \Carbon\Carbon::parse($absen->jadwal->jam_mulai)->format('H:i') : '-' }}
                            WIB
                          </div>
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-3 md:px-6 py-3 text-center whitespace-nowrap">
                          <span
                            class="px-2 py-1 inline-flex text-[10px] leading-tight font-bold rounded-full
                                                        {{ $absen->status == 'Sakit' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $absen->status == 'Izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $absen->status == 'Alfa' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ strtoupper($absen->status) }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                <div class="bg-green-100 rounded-full p-3 mb-3">
                  <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <p class="text-gray-800 font-medium">Nihil Masalah!</p>
                <p class="text-sm text-gray-500">Semua siswa hadir di semua mapel hari ini.</p>
              </div>
            @endif
          </div>
        </div>

        {{-- 4. Menu Cepat --}}
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border border-gray-100">
          <h4 class="font-bold text-lg mb-4 text-gray-800">Menu Cepat</h4>
          <div class="grid grid-cols-1 gap-3">

            {{-- Tombol QR --}}
            <a href="{{ route('walikelas.rekap.harian') }}"
              class="group relative flex items-start p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition border border-emerald-100 active:scale-[0.98]">
              <div
                class="flex-shrink-0 p-3 bg-white rounded-lg text-emerald-600 shadow-sm mr-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                  </path>
                </svg>
              </div>
              <div>
                <p class="font-bold text-emerald-800 text-sm md:text-base">Cek Absensi Harian (QR)</p>
                <p class="text-xs md:text-sm text-emerald-600 mt-1 leading-snug">Lihat kedatangan siswa, jam masuk, & foto
                  wajah.</p>
              </div>
            </a>

            {{-- Tombol Rekap Mapel --}}
            <a href="{{ route('guru.dashboard', ['kelas_id' => $kelas->kelas_id]) }}"
              class="group relative flex items-start p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition border border-indigo-100 active:scale-[0.98]">
              <div
                class="flex-shrink-0 p-3 bg-white rounded-lg text-indigo-600 shadow-sm mr-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
              </div>
              <div>
                <p class="font-bold text-indigo-800 text-sm md:text-base">Rekap Mapel Bulanan</p>
                <p class="text-xs md:text-sm text-indigo-600 mt-1 leading-snug">Cek rekap kehadiran siswa di setiap mata
                  pelajaran.</p>
              </div>
            </a>

            {{-- Tombol Riwayat Mengajar --}}
            <a href="{{ route('guru.absensi.history') }}"
              class="group relative flex items-start p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition border border-gray-200 active:scale-[0.98]">
              <div
                class="flex-shrink-0 p-3 bg-white rounded-lg text-gray-600 shadow-sm mr-4 group-hover:bg-gray-600 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <p class="font-bold text-gray-800 text-sm md:text-base">Riwayat Mengajar Saya</p>
                <p class="text-xs md:text-sm text-gray-500 mt-1 leading-snug">Jurnal mengajar & absensi mapel pribadi
                  Anda.</p>
              </div>
            </a>

          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
