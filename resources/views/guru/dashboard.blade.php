@extends('layouts.master')

@section('header')
  {{-- Header default dikosongkan untuk diganti dengan header sticky kustom --}}
@endsection

@section('content')
  <div class="bg-gray-50 min-h-screen pb-10">
    {{-- =================================== --}}
    {{-- HEADER STICKY KUSTOM --}}
    {{-- =================================== --}}
    <div
      class="sticky top-0 z-20 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200 transition-all duration-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Flex Column di Mobile, Row di MD ke atas --}}
        <div class="flex flex-col md:flex-row justify-between items-center py-4 gap-4">
          <div class="text-center md:text-left w-full md:w-auto">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
              Halo, {{ Str::limit($guru->nama_guru, 20) }}! 👋
            </h2>
            <p class="text-sm text-gray-500 mt-1">Jadwal: {{ $namaHariIni }}.</p>
          </div>

          {{-- Container Tombol Navigasi --}}
          <div class="flex flex-wrap justify-center md:justify-end gap-2 w-full md:w-auto">
            {{-- TOMBOL BERALIH KE WALI KELAS --}}
            @if (auth()->user()->role == 'walikelas')
              <a href="{{ route('walikelas.dashboard') }}"
                class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                  </path>
                </svg>
                Wali Kelas
              </a>
            @endif

            <a href="{{ route('guru.absensi.history') }}"
              class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-sm whitespace-nowrap">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Riwayat
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="py-6 lg:py-10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 lg:space-y-8">

        {{-- =================================== --}}
        {{-- KARTU DAFTAR JADWAL HARI INI --}}
        {{-- =================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="p-4 sm:p-6 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center space-x-2">
              <div class="w-1 h-6 bg-indigo-500 rounded-full"></div>
              <h3 class="text-lg font-bold text-gray-800">Jadwal Hari Ini</h3>
            </div>
          </div>
          <div class="divide-y divide-gray-100">
            @forelse ($jadwalHariIni as $jadwal)
              <div class="p-4 sm:p-5 hover:bg-indigo-50/30 transition duration-150 ease-in-out group">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                  {{-- Info Kiri: Jam & Mapel --}}
                  <div class="flex flex-col sm:flex-row items-start sm:items-center w-full gap-3 sm:gap-5">
                    {{-- Badge Jam --}}
                    <div
                      class="flex-shrink-0 flex sm:flex-col items-center justify-center space-x-2 sm:space-x-0 bg-indigo-50 text-indigo-700 px-3 py-2 rounded-lg border border-indigo-100 min-w-[100px] sm:text-center">
                      <span
                        class="text-sm font-bold tracking-tight">{{ date('H:i', strtotime($jadwal->jam_mulai)) }}</span>
                      <span class="text-[10px] uppercase text-indigo-400 font-semibold px-1">-</span>
                      <span
                        class="text-sm font-bold tracking-tight">{{ date('H:i', strtotime($jadwal->jam_selesai)) }}</span>
                    </div>

                    {{-- Detail Mapel --}}
                    <div class="flex-grow">
                      <h4 class="font-bold text-lg text-gray-900 group-hover:text-indigo-700 transition-colors">
                        {{ $jadwal->mataPelajaran->nama_mapel }}
                      </h4>
                      <div class="flex items-center text-sm text-gray-600 mt-1">
                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                          </path>
                        </svg>
                        Kelas <span
                          class="font-semibold ml-1 bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $jadwal->kelas->nama_kelas }}</span>
                      </div>
                    </div>
                  </div>

                  {{-- Tombol Aksi --}}
                  <div class="w-full sm:w-auto flex-shrink-0 mt-2 sm:mt-0">
                    <a href="{{ route('guru.absensi.create', ['jadwal' => $jadwal->jadwal_id]) }}"
                      class="block w-full sm:w-auto text-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-md">
                      Ambil Absensi
                    </a>
                  </div>
                </div>
              </div>
            @empty
              <div class="p-10 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <p class="text-gray-500 font-medium">Tidak ada jadwal mengajar hari ini.</p>
                <p class="text-gray-400 text-sm mt-1">Selamat beristirahat! ☕</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- =================================== --}}
        {{-- PANEL REKAP ABSENSI (ACCORDION) --}}
        {{-- =================================== --}}
        <div x-data="{ open: {{ $rekapData ? 'true' : 'false' }} }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <button @click="open = !open"
            class="w-full flex justify-between items-center p-5 sm:p-6 text-left hover:bg-gray-50 transition focus:outline-none">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Rekapitulasi Absensi</h3>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Filter & Export data kehadiran siswa</p>
              </div>
            </div>
            <svg class="h-5 w-5 text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': open }"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <div x-show="open" x-transition class="border-t border-gray-200">
            {{-- Form Filter --}}
            <div class="p-5 sm:p-6 bg-gray-50/50">
              <form action="{{ route('guru.dashboard') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 lg:items-end">
                  {{-- Input Fields --}}
                  <div>
                    <label for="kelas_id"
                      class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Kelas</label>
                    <select name="kelas_id" id="kelas_id"
                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                      required>
                      <option value="">Pilih Kelas</option>
                      @foreach ($kelasList as $id => $nama)
                        <option value="{{ $id }}" {{ $selectedKelasId == $id ? 'selected' : '' }}>
                          {{ $nama }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div>
                    <label for="mapel_id"
                      class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Mata
                      Pelajaran</label>
                    <select name="mapel_id" id="mapel_id"
                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10">
                      <option value="">Semua Mapel</option>
                      @foreach ($mapelList as $id => $nama)
                        <option value="{{ $id }}" {{ $selectedMapelId == $id ? 'selected' : '' }}>
                          {{ $nama }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div>
                    <label for="tanggal_mulai"
                      class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Tgl Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $selectedTanggalMulai }}"
                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                      required>
                  </div>

                  <div>
                    <label for="tanggal_akhir"
                      class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Tgl Akhir</label>
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ $selectedTanggalAkhir }}"
                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10"
                      required>
                  </div>

                  <div>
                    <button type="submit"
                      class="w-full h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                      Tampilkan
                    </button>
                  </div>
                </div>
              </form>
            </div>

            {{-- Tabel Hasil Rekap --}}
            @if ($rekapData)
              <div class="px-5 sm:px-6 pb-6 pt-2">
                <div
                  class="flex flex-col md:flex-row justify-between items-start md:items-end mb-4 gap-4 border-b border-gray-100 pb-4">
                  <div>
                    <h4 class="font-bold text-gray-800">Hasil Pencarian</h4>
                    <div class="mt-1 text-sm text-gray-600 space-y-0.5">
                      <p><span class="text-gray-400 w-16 inline-block">Kelas</span> : <span
                          class="font-medium text-gray-900">{{ $rekapData['info']['nama_kelas'] }}</span></p>
                      @if ($selectedMapelId && isset($mapelList[$selectedMapelId]))
                        <p><span class="text-gray-400 w-16 inline-block">Mapel</span> : <span
                            class="font-medium text-gray-900">{{ $mapelList[$selectedMapelId] }}</span></p>
                      @endif
                      <p><span class="text-gray-400 w-16 inline-block">Periode</span> : <span
                          class="font-medium text-gray-900">{{ $rekapData['info']['periode'] }}</span></p>
                    </div>
                  </div>

                  <div class="flex space-x-2 w-full md:w-auto">
                    <a href="{{ route('guru.rekap.export.excel', ['kelas_id' => $selectedKelasId, 'mapel_id' => $selectedMapelId, 'tanggal_mulai' => $selectedTanggalMulai, 'tanggal_akhir' => $selectedTanggalAkhir]) }}"
                      class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-green-50 hover:text-green-700 hover:border-green-200 focus:outline-none transition">
                      <svg class="mr-2 h-4 w-4 text-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                      </svg>
                      Excel
                    </a>
                    <a href="{{ route('guru.rekap.export.pdf', ['kelas_id' => $selectedKelasId, 'mapel_id' => $selectedMapelId, 'tanggal_mulai' => $selectedTanggalMulai, 'tanggal_akhir' => $selectedTanggalAkhir]) }}"
                      class="flex-1 md:flex-none inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-red-50 hover:text-red-700 hover:border-red-200 focus:outline-none transition">
                      <svg class="mr-2 h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                      </svg>
                      PDF
                    </a>
                  </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-10">No
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-24">NIS
                        </th>
                        <th
                          class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[150px]">
                          Nama Siswa</th>
                        <th class="px-2 py-3 text-center text-xs font-bold text-green-700 bg-green-50 uppercase w-12">H
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-bold text-yellow-700 bg-yellow-50 uppercase w-12">S
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-bold text-blue-700 bg-blue-50 uppercase w-12">I
                        </th>
                        <th class="px-2 py-3 text-center text-xs font-bold text-red-700 bg-red-50 uppercase w-12">A</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      @foreach ($rekapData['rekap'] as $data)
                        <tr class="hover:bg-gray-50 transition">
                          <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ $loop->iteration }}</td>
                          <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $data['nis'] }}</td>
                          <td class="px-4 py-3 text-sm font-medium text-gray-900 truncate max-w-[200px]"
                            title="{{ $data['nama_siswa'] }}">
                            {{ $data['nama_siswa'] }}
                          </td>
                          <td class="px-2 py-3 text-sm text-center font-bold text-green-700 bg-green-50/30">
                            {{ $data['kehadiran']['Hadir'] }}</td>
                          <td class="px-2 py-3 text-sm text-center font-bold text-yellow-700 bg-yellow-50/30">
                            {{ $data['kehadiran']['Sakit'] }}</td>
                          <td class="px-2 py-3 text-sm text-center font-bold text-blue-700 bg-blue-50/30">
                            {{ $data['kehadiran']['Izin'] }}</td>
                          <td class="px-2 py-3 text-sm text-center font-bold text-red-700 bg-red-50/30">
                            {{ $data['kehadiran']['Alfa'] }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            @endif
          </div>
        </div>

        {{-- =================================== --}}
        {{-- KARTU JADWAL MENGAJAR LENGKAP --}}
        {{-- =================================== --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
          <div class="p-4 sm:p-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Jadwal Mengajar Mingguan</h3>
          </div>
          <div class="p-4 sm:p-6 bg-gray-50/30">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @forelse ($jadwalSeminggu as $hari => $jadwals)
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 h-full">
                  <h4
                    class="font-bold text-indigo-700 uppercase tracking-wider text-sm border-b border-gray-100 pb-2 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $hari }}
                  </h4>
                  <div class="space-y-3">
                    @foreach ($jadwals as $jadwal)
                      <div class="relative pl-3 border-l-2 border-indigo-200 hover:border-indigo-500 transition-colors">
                        <p class="text-sm font-bold text-gray-800">{{ $jadwal->mataPelajaran->nama_mapel }}</p>
                        <div class="text-xs text-gray-500 mt-0.5 flex flex-wrap gap-x-2">
                          <span class="bg-gray-100 px-1.5 rounded">{{ $jadwal->kelas->nama_kelas }}</span>
                          <span>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                  Tidak ada jadwal mengajar yang ditetapkan.
                </div>
              @endforelse
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- Notifikasi Sukses & Error dengan SweetAlert2 --}}
  @if (session('success'))
    <script>
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
          popup: 'colored-toast'
        }
      });
    </script>
  @endif
  @if (session('error'))
    <script>
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: "{{ session('error') }}",
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true
      });
    </script>
  @endif
@endpush
