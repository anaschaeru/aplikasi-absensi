@extends('layouts.master')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dasbor Administrator
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <a href="{{ route('admin.siswa.index') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-4 rounded-lg shadow-md text-center transition duration-300 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span>Manajemen Siswa</span>
                </a>
                <a href="{{ route('admin.laporan.absensi.index') }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-4 px-4 rounded-lg shadow-md text-center transition duration-300 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>Laporan Absensi</span>
                </a>
                <a href="{{ route('admin.jadwal.index') }}"
                    class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-4 px-4 rounded-lg shadow-md text-center transition duration-300 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>Manajemen Jadwal</span>
                </a>
                <a href="{{ route('admin.import.index') }}"
                    class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-4 px-4 rounded-lg shadow-md text-center transition duration-300 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    <span>Import Data</span>
                </a>
            </div>

            {{-- Grafik dan Statistik Utama --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Kolom Kiri: Grafik Absensi --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-lg mb-4">Grafik Absensi Kelas Hari Ini</h3>
                    <div class="h-80">
                        <canvas id="absensiChart"></canvas>
                    </div>
                </div>

                {{-- Kolom Kanan: Statistik Utama --}}
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">Total Guru</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalGuru }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">Total Siswa</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalSiswa }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">Total Kelas</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalKelas }}</p>
                    </div>
                </div>
            </div>

            {{-- Aktivitas Piket & Kelas Bermasalah --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-lg mb-4">Aktivitas Absensi Gerbang Terbaru</h3>
                    {{-- Tambahkan wrapper div dengan class untuk scrolling --}}
                    <div class="overflow-y-auto h-80">
                        <div class="space-y-4">
                            @forelse ($aktivitasTerbaru as $absensi)
                                <div class="flex items-center space-x-3 border-b pb-2 last:border-b-0">
                                    <span
                                        class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                        {{ substr($absensi->siswa->nama_siswa, 0, 1) }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $absensi->siswa->nama_siswa }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $absensi->siswa->kelas->nama_kelas }} | Masuk pukul
                                            {{ \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada aktivitas absensi gerbang hari ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="font-bold text-lg mb-4">Kelas dengan "Alfa" Terbanyak Hari Ini</h3>
                    <div class="space-y-3">
                        @forelse ($kelasAbsensiTerbanyak as $kelas)
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">{{ $kelas->nama_kelas }}</span>
                                <span
                                    class="font-bold text-sm text-white bg-red-500 px-2 py-1 rounded-full">{{ $kelas->jumlah_alfa }}
                                    Siswa</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Tidak ada absensi "Alfa" yang tercatat hari ini. Bagus!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mengambil data dari controller yang di-passing sebagai JSON
            const absensiData = @json($rekapAbsensiHariIni);

            const data = {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alfa'],
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: [
                        absensiData.hadir ?? 0,
                        absensiData.sakit ?? 0,
                        absensiData.izin ?? 0,
                        absensiData.alfa ?? 0
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.7)', // Green-500
                        'rgba(234, 179, 8, 0.7)', // Yellow-500
                        'rgba(59, 130, 246, 0.7)', // Blue-500
                        'rgba(239, 68, 68, 0.7)' // Red-500
                    ],
                    borderColor: [
                        'rgba(22, 163, 74, 1)', // Green-600
                        'rgba(202, 138, 4, 1)', // Yellow-600
                        'rgba(37, 99, 235, 1)', // Blue-600
                        'rgba(220, 38, 38, 1)' // Red-600
                    ],
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'doughnut', // Tipe grafik: doughnut, pie, bar, line
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: 'Ringkasan Absensi Hari Ini'
                        }
                    }
                },
            };

            // Render grafik di canvas
            const absensiChart = new Chart(
                document.getElementById('absensiChart'),
                config
            );
        });
    </script>
@endpush
