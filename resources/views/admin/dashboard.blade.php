@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Dashboard Administrator
  </h2>
@endsection

@section('content')
  {{-- Ubah py-12 jadi py-6 di mobile agar tidak terlalu jarak --}}
  <div class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- Quick Actions --}}
      {{-- Mobile: grid-cols-2, Tablet: grid-cols-3, Desktop: grid-cols-5 (Agar ke-5 menu muat) --}}
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">

        <a href="{{ route('admin.siswa.index') }}"
          class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 sm:py-4 px-3 rounded-lg shadow-md text-center transition duration-300 flex flex-col sm:flex-row items-center justify-center">
          <svg class="w-8 h-8 sm:w-6 sm:h-6 mb-1 sm:mb-0 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
          </svg>
          <span class="text-xs sm:text-base">Siswa</span>
        </a>

        {{-- MENU BARU: LAPORAN HARIAN --}}
        <a href="{{ route('admin.laporan.harian') }}"
          class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 sm:py-4 px-3 rounded-lg shadow-md text-center transition duration-300 flex flex-col sm:flex-row items-center justify-center">
          <svg class="w-8 h-8 sm:w-6 sm:h-6 mb-1 sm:mb-0 sm:mr-2" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
            </path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14h.01M16 14h.01M8 14h.01">
            </path>
          </svg>
          <span class="text-xs sm:text-base">Lap. Harian</span>
        </a>

        <a href="{{ route('admin.laporan.absensi.index') }}"
          class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 sm:py-4 px-3 rounded-lg shadow-md text-center transition duration-300 flex flex-col sm:flex-row items-center justify-center">
          <svg class="w-8 h-8 sm:w-6 sm:h-6 mb-1 sm:mb-0 sm:mr-2" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
          </svg>
          <span class="text-xs sm:text-base">Lap. Umum</span>
        </a>

        <a href="{{ route('admin.jadwal.index') }}"
          class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 sm:py-4 px-3 rounded-lg shadow-md text-center transition duration-300 flex flex-col sm:flex-row items-center justify-center">
          <svg class="w-8 h-8 sm:w-6 sm:h-6 mb-1 sm:mb-0 sm:mr-2" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
            </path>
          </svg>
          <span class="text-xs sm:text-base">Jadwal</span>
        </a>

        <a href="{{ route('admin.import.index') }}"
          class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-3 sm:py-4 px-3 rounded-lg shadow-md text-center transition duration-300 flex flex-col sm:flex-row items-center justify-center">
          <svg class="w-8 h-8 sm:w-6 sm:h-6 mb-1 sm:mb-0 sm:mr-2" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
            </path>
          </svg>
          <span class="text-xs sm:text-base">Import</span>
        </a>
      </div>

      {{-- Grafik dan Statistik Utama --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Kolom Kiri: Grafik Absensi --}}
        <div class="lg:col-span-2 bg-white p-4 sm:p-6 rounded-lg shadow-sm">
          <h3 class="font-bold text-lg mb-4 text-gray-800">Absensi Hari Ini</h3>
          <div class="h-64 sm:h-80 w-full relative">
            <canvas id="absensiChart"></canvas>
          </div>
        </div>

        {{-- Kolom Kanan: Statistik Utama --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-1 gap-4">
          <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm flex flex-col justify-center">
            <h3 class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Guru</h3>
            <p class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalGuru }}</p>
          </div>
          <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm flex flex-col justify-center">
            <h3 class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Siswa</h3>
            <p class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalSiswa }}</p>
          </div>
          <div
            class="bg-white p-4 sm:p-6 rounded-lg shadow-sm flex flex-col justify-center col-span-2 sm:col-span-1 lg:col-span-1">
            <h3 class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Kelas</h3>
            <p class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalKelas }}</p>
          </div>
        </div>
      </div>

      {{-- Aktivitas Piket & Kelas Bermasalah --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- List Aktivitas --}}
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm">
          <h3 class="font-bold text-lg mb-4 text-gray-800">Aktivitas Gerbang</h3>
          <div class="overflow-y-auto h-64 sm:h-80 pr-2">
            <div class="space-y-4">
              @forelse ($aktivitasTerbaru as $absensi)
                <div class="flex items-center space-x-3 border-b border-gray-100 pb-3 last:border-b-0 last:pb-0">
                  <span
                    class="shrink-0 h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                    {{ substr($absensi->siswa->nama_siswa, 0, 1) }}
                  </span>
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ $absensi->siswa->nama_siswa }}
                    </p>
                    <p class="text-xs text-gray-500 truncate">
                      {{ $absensi->siswa->kelas->nama_kelas }} •
                      Masuk: {{ \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') }} •
                      Pulang:
                      {{ $absensi->waktu_pulang ? \Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i') : '-' }}
                    </p>
                  </div>
                  <div class="shrink-0">
                    <span class="px-2 py-1 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                      {{ $absensi->status }}
                    </span>
                  </div>
                </div>
              @empty
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                  <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <p class="text-sm">Belum ada aktivitas.</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>

        {{-- List Alfa Terbanyak --}}
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm">
          <h3 class="font-bold text-lg mb-4 text-gray-800">Top Alfa Hari Ini</h3>
          <div class="overflow-y-auto h-64 sm:h-80 pr-2">
            <div class="space-y-3">
              @forelse ($kelasAbsensiTerbanyak as $kelas)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                  <span class="text-sm font-medium text-gray-700">{{ $kelas->nama_kelas }}</span>
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    {{ $kelas->jumlah_alfa }} Siswa
                  </span>
                </div>
              @empty
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                  <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <p class="text-sm">Nihil (Semua hadir/izin).</p>
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const absensiData = @json($rekapAbsensiHariIni);

      const data = {
        labels: ['Hadir', 'Sakit', 'Izin', 'Alfa'],
        datasets: [{
          data: [
            absensiData.hadir ?? 0,
            absensiData.sakit ?? 0,
            absensiData.izin ?? 0,
            absensiData.alfa ?? 0
          ],
          backgroundColor: [
            '#22c55e', // Green-500
            '#eab308', // Yellow-500
            '#3b82f6', // Blue-500
            '#ef4444' // Red-500
          ],
          borderWidth: 0,
          hoverOffset: 4
        }]
      };

      const config = {
        type: 'doughnut',
        data: data,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 20,
                usePointStyle: true,
              }
            }
          },
          cutout: '65%',
        },
      };

      new Chart(
        document.getElementById('absensiChart'),
        config
      );
    });
  </script>
@endpush
