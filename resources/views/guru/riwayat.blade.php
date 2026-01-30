@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Riwayat Sesi Absensi
  </h2>
@endsection

@section('content')
  <div class="py-6 md:py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- Header Section --}}
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
          <h3 class="text-lg font-bold text-gray-900">Rekap Sesi Mengajar</h3>
          <p class="text-sm text-gray-500">Daftar riwayat absensi yang telah Anda isi.</p>
        </div>
        <a href="{{ route('guru.dashboard') }}"
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
          &larr; Kembali
        </a>
      </div>

      <div class="bg-white shadow-sm sm:rounded-xl border border-gray-200 overflow-hidden">

        {{-- ================================================== --}}
        {{-- TAMPILAN MOBILE (CARD LIST) - Visible on small screens --}}
        {{-- ================================================== --}}
        <div class="block md:hidden">
          <div class="divide-y divide-gray-100">
            @forelse ($sesiAbsensi as $sesi)
              <div class="p-4 hover:bg-gray-50 transition duration-150">
                {{-- Baris Atas: Tanggal & Kelas --}}
                <div class="flex justify-between items-start mb-2">
                  <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ \Carbon\Carbon::parse($sesi->tanggal_absensi)->translatedFormat('d M Y') }}
                  </div>
                  <span
                    class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">
                    {{ $sesi->jadwal->kelas->nama_kelas ?? '?' }}
                  </span>
                </div>

                {{-- Baris Tengah: Mapel --}}
                <div class="mb-3">
                  <h4 class="text-base font-bold text-gray-900 leading-tight">
                    {{ $sesi->jadwal->mataPelajaran->nama_mapel ?? 'Mata Pelajaran Dihapus' }}
                  </h4>
                </div>

                {{-- Baris Bawah: Jam & Tombol --}}
                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-50">
                  <div class="flex items-center text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ date('H:i', strtotime($sesi->jadwal->jam_mulai)) }} -
                    {{ date('H:i', strtotime($sesi->jadwal->jam_selesai)) }}
                  </div>

                  <a href="{{ route('guru.absensi.history.show', ['jadwal' => $sesi->jadwal_id, 'tanggal' => $sesi->tanggal_absensi]) }}"
                    class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    Lihat Detail <span class="ml-1">&rarr;</span>
                  </a>
                </div>
              </div>
            @empty
              <div class="p-8 text-center text-gray-500">
                <p class="text-sm">Belum ada riwayat absensi.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- ================================================== --}}
        {{-- TAMPILAN DESKTOP (TABLE) - Visible on medium+ screens --}}
        {{-- ================================================== --}}
        <div class="hidden md:block overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran
                </th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jam</th>
                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @forelse ($sesiAbsensi as $sesi)
                <tr class="hover:bg-gray-50 transition duration-150">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                      </svg>
                      {{ \Carbon\Carbon::parse($sesi->tanggal_absensi)->translatedFormat('d M Y') }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                      {{ $sesi->jadwal->kelas->nama_kelas ?? '-' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $sesi->jadwal->mataPelajaran->nama_mapel ?? '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                    {{ date('H:i', strtotime($sesi->jadwal->jam_mulai)) }} -
                    {{ date('H:i', strtotime($sesi->jadwal->jam_selesai)) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('guru.absensi.history.show', ['jadwal' => $sesi->jadwal_id, 'tanggal' => $sesi->tanggal_absensi]) }}"
                      class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline">
                      Lihat Detail
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                      viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                      </path>
                    </svg>
                    <p class="text-base font-medium">Belum ada data absensi.</p>
                    <p class="text-sm mt-1">Silakan lakukan absensi pada jadwal mengajar Anda.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination (Responsif bawaan Laravel/Tailwind) --}}
        @if ($sesiAbsensi->hasPages())
          <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 sm:px-6">
            {{ $sesiAbsensi->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
