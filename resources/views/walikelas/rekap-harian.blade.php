@extends('layouts.master')

@section('header')
  <div class="flex flex-col md:flex-row justify-between items-center">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2 md:mb-0">
      Rekap Harian: Kelas {{ $kelas->nama_kelas }}
    </h2>
    <a href="{{ route('walikelas.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
      &larr; Kembali ke Dashboard
    </a>
  </div>
@endsection

@section('content')
  <div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- 1. FILTER TANGGAL & STATISTIK --}}
      <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('walikelas.rekap.harian') }}" method="GET" class="mb-6">
          <div class="flex flex-col md:flex-row items-end gap-4">
            <div class="w-full md:w-auto">
              <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
              <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}"
                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                onchange="this.form.submit()">
            </div>
            <div class="flex-grow"></div>
            <div class="w-full md:w-auto">
              <button type="button" onclick="window.print()"
                class="w-full px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
              </button>
            </div>
          </div>
        </form>

        {{-- Statistik Cards Kecil --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
          <div class="p-3 bg-green-50 rounded-lg border border-green-100">
            <span class="block text-xs text-green-600 font-bold uppercase">Hadir</span>
            <span class="text-xl font-bold text-green-800">{{ $stats['Hadir'] }}</span>
          </div>
          <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100">
            <span class="block text-xs text-yellow-600 font-bold uppercase">Sakit</span>
            <span class="text-xl font-bold text-yellow-800">{{ $stats['Sakit'] }}</span>
          </div>
          <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
            <span class="block text-xs text-blue-600 font-bold uppercase">Izin</span>
            <span class="text-xl font-bold text-blue-800">{{ $stats['Izin'] }}</span>
          </div>
          <div class="p-3 bg-red-50 rounded-lg border border-red-100">
            <span class="block text-xs text-red-600 font-bold uppercase">Alfa</span>
            <span class="text-xl font-bold text-red-800">{{ $stats['Alfa'] }}</span>
          </div>
          <div class="p-3 bg-gray-100 rounded-lg border border-gray-200">
            <span class="block text-xs text-gray-500 font-bold uppercase">Belum Absen</span>
            <span class="text-xl font-bold text-gray-800">{{ $stats['BelumAbsen'] }}</span>
          </div>
        </div>
      </div>

      {{-- 2. TABEL DETAIL SISWA --}}
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Masuk
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($siswas as $siswa)
                  @php
                    // Cek apakah siswa ini ada datanya di array absensi
                    $absen = $absensis[$siswa->siswa_id] ?? null;
                  @endphp
                  <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $siswa->nis }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $siswa->nama_siswa }}</td>

                    {{-- Kolom Status --}}
                    <td class="px-4 py-3 text-center">
                      @if ($absen)
                        <span
                          class="px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $absen->status == 'Hadir' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $absen->status == 'Sakit' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $absen->status == 'Izin' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $absen->status == 'Alfa' ? 'bg-red-100 text-red-800' : '' }}">
                          {{ $absen->status }}
                        </span>
                      @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">
                          Belum Absen
                        </span>
                      @endif
                    </td>

                    {{-- Kolom Waktu --}}
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                      @if ($absen && $absen->waktu_masuk)
                        {{ \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i:s') }}
                      @else
                        -
                      @endif
                    </td>

                    {{-- Kolom Foto --}}
                    <td class="px-4 py-3 text-center">
                      @if ($absen && $absen->foto_masuk)
                        <button
                          onclick="Swal.fire({
                                                    imageUrl: '{{ asset('storage/' . $absen->foto_masuk) }}',
                                                    imageAlt: 'Foto Bukti',
                                                    showConfirmButton: false,
                                                    width: 400,
                                                    padding: '1rem',
                                                    background: '#fff'
                                                })"
                          class="inline-flex items-center text-indigo-600 hover:text-indigo-900 text-xs font-medium underline transition-colors">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          </svg>
                          Lihat Foto
                        </button>
                      @elseif($absen)
                        <span class="text-xs text-gray-400 italic">Tidak ada</span>
                      @else
                        -
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@push('styles')
  <style>
    @media print {

      /* Sembunyikan elemen navigasi dan tombol saat print */
      nav,
      header a,
      button,
      input[type="date"],
      label {
        display: none !important;
      }

      .bg-gray-50 {
        background-color: white !important;
      }

      .shadow-sm {
        box-shadow: none !important;
        border: none !important;
      }

      /* Pastikan tabel tercetak rapi */
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
      }

      th,
      td {
        border: 1px solid #000;
        padding: 8px;
        font-size: 12px;
      }

      /* Sembunyikan kolom foto saat print agar hemat tinta & rapi */
      th:nth-child(6),
      td:nth-child(6) {
        display: none;
      }
    }
  </style>
@endpush
