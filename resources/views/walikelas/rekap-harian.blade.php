@extends('layouts.master')

@section('header')
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Rekap Harian
      </h2>
      <p class="text-sm text-gray-500 mt-1">Kelas {{ $kelas->nama_kelas }}</p>
    </div>
    <a href="{{ route('walikelas.dashboard') }}"
      class="text-sm text-indigo-600 hover:text-indigo-900 flex items-center bg-white px-3 py-1 rounded-md shadow-sm border border-gray-200">
      &larr; <span class="ml-1">Dashboard</span>
    </a>
  </div>
@endsection

@section('content')
  <div class="py-6 md:py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      {{-- 1. FILTER TANGGAL & STATISTIK --}}
      <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('walikelas.rekap.harian') }}" method="GET" class="mb-6">
          <div class="flex flex-col md:flex-row items-end gap-4">

            {{-- Input Tanggal --}}
            <div class="w-full md:w-auto">
              <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
              <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}"
                class="w-full md:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                onchange="this.form.submit()">
            </div>

            <div class="flex-grow hidden md:block"></div>

            {{-- Tombol Export --}}
            <div class="w-full md:w-auto">
              <a href="{{ route('walikelas.rekap.export', ['tanggal' => $tanggal]) }}"
                class="flex justify-center items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition w-full shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
              </a>
            </div>
          </div>
        </form>

        {{-- Statistik Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-center">
          <div class="p-3 bg-green-50 rounded-lg border border-green-100">
            <span class="block text-[10px] md:text-xs text-green-600 font-bold uppercase">Hadir</span>
            <span class="text-lg md:text-xl font-bold text-green-800">{{ $stats['Hadir'] }}</span>
          </div>
          <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-100">
            <span class="block text-[10px] md:text-xs text-yellow-600 font-bold uppercase">Sakit</span>
            <span class="text-lg md:text-xl font-bold text-yellow-800">{{ $stats['Sakit'] }}</span>
          </div>
          <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
            <span class="block text-[10px] md:text-xs text-blue-600 font-bold uppercase">Izin</span>
            <span class="text-lg md:text-xl font-bold text-blue-800">{{ $stats['Izin'] }}</span>
          </div>
          <div class="p-3 bg-red-50 rounded-lg border border-red-100">
            <span class="block text-[10px] md:text-xs text-red-600 font-bold uppercase">Alfa</span>
            <span class="text-lg md:text-xl font-bold text-red-800">{{ $stats['Alfa'] }}</span>
          </div>
          <div class="p-3 bg-gray-100 rounded-lg border border-gray-200 col-span-2 md:col-span-1">
            <span class="block text-[10px] md:text-xs text-gray-500 font-bold uppercase">Belum Absen</span>
            <span class="text-lg md:text-xl font-bold text-gray-800">{{ $stats['BelumAbsen'] }}</span>
          </div>
        </div>
      </div>

      {{-- 2. TAMPILAN DATA --}}

      {{-- MODE DESKTOP: TABEL (Hidden on Mobile) --}}
      <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pulang</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($siswas as $siswa)
                  @php $absen = $absensis[$siswa->siswa_id] ?? null; @endphp
                  <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3">
                      <div class="text-sm font-medium text-gray-900">{{ $siswa->nama_siswa }}</div>
                      <div class="text-xs text-gray-500 font-mono">{{ $siswa->nis }}</div>
                    </td>
                    <td class="px-4 py-3 text-center">
                      @include('components.status-badge', ['status' => $absen ? $absen->status : null])
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                      @if ($absen && $absen->waktu_masuk && $absen->status == 'Hadir')
                        <div class="font-bold">{{ \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i') }}</div>
                        @if ($absen->foto_masuk)
                          <button onclick="lihatFoto('{{ asset('storage/' . $absen->foto_masuk) }}', 'Foto Masuk')"
                            class="text-xs text-indigo-600 hover:text-indigo-900 underline">Lihat Foto</button>
                        @endif
                      @elseif($absen && $absen->status != 'Hadir')
                        <span class="text-xs text-gray-400 italic">-</span>
                      @else
                        -
                      @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-gray-600">
                      @if ($absen && $absen->waktu_pulang && $absen->status == 'Hadir')
                        <div class="font-bold">{{ \Carbon\Carbon::parse($absen->waktu_pulang)->format('H:i') }}</div>
                        @if ($absen->foto_pulang)
                          <button onclick="lihatFoto('{{ asset('storage/' . $absen->foto_pulang) }}', 'Foto Pulang')"
                            class="text-xs text-indigo-600 hover:text-indigo-900 underline">Lihat Foto</button>
                        @endif
                      @else
                        -
                      @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                      <button
                        onclick="openEditModal('{{ $siswa->siswa_id }}', '{{ $siswa->nama_siswa }}', '{{ $absen ? $absen->status : '' }}')"
                        class="text-gray-500 hover:text-indigo-600 transition p-1.5 rounded-md border border-gray-200 hover:bg-indigo-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                          </path>
                        </svg>
                      </button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- MODE MOBILE: KARTU (Visible on Mobile) --}}
      <div class="md:hidden space-y-4">
        @foreach ($siswas as $siswa)
          @php $absen = $absensis[$siswa->siswa_id] ?? null; @endphp
          <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            {{-- Header Kartu: Nama & Status --}}
            <div class="flex justify-between items-start mb-3">
              <div>
                <h3 class="font-bold text-gray-800">{{ $siswa->nama_siswa }}</h3>
                <p class="text-xs text-gray-500 font-mono">{{ $siswa->nis }}</p>
              </div>
              <div>
                @include('components.status-badge', ['status' => $absen ? $absen->status : null])
              </div>
            </div>

            {{-- Body Kartu: Waktu Masuk & Pulang --}}
            <div class="grid grid-cols-2 gap-4 text-sm mb-4 border-t border-b border-gray-100 py-2">
              <div>
                <span class="block text-xs text-gray-500 mb-1">Masuk</span>
                @if ($absen && $absen->waktu_masuk && $absen->status == 'Hadir')
                  <span
                    class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i') }}</span>
                  @if ($absen->foto_masuk)
                    <button onclick="lihatFoto('{{ asset('storage/' . $absen->foto_masuk) }}', 'Foto Masuk')"
                      class="block text-xs text-indigo-600 underline mt-1">Lihat Foto</button>
                  @endif
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </div>
              <div class="text-right">
                <span class="block text-xs text-gray-500 mb-1">Pulang</span>
                @if ($absen && $absen->waktu_pulang && $absen->status == 'Hadir')
                  <span
                    class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($absen->waktu_pulang)->format('H:i') }}</span>
                  @if ($absen->foto_pulang)
                    <button onclick="lihatFoto('{{ asset('storage/' . $absen->foto_pulang) }}', 'Foto Pulang')"
                      class="block text-xs text-indigo-600 underline mt-1 ml-auto">Lihat Foto</button>
                  @endif
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </div>
            </div>

            {{-- Footer Kartu: Tombol Aksi --}}
            <button
              onclick="openEditModal('{{ $siswa->siswa_id }}', '{{ $siswa->nama_siswa }}', '{{ $absen ? $absen->status : '' }}')"
              class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                </path>
              </svg>
              Ubah Status
            </button>
          </div>
        @endforeach
      </div>

    </div>
  </div>

  {{-- MODAL EDIT STATUS --}}
  <div id="editStatusModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
        onclick="closeEditModal()"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div
        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
        <form action="{{ route('walikelas.rekap.harian.update') }}" method="POST">
          @csrf
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                  </path>
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Update Status Absensi
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500 mb-4 bg-gray-50 p-3 rounded-md">
                    Siswa: <span id="modal-siswa-nama" class="font-bold text-gray-800"></span><br>
                    Tanggal: <span class="font-semibold">{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</span>
                  </p>

                  <input type="hidden" name="siswa_id" id="modal-siswa-id">
                  <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                  <label for="status" class="block text-sm font-medium text-gray-700">Pilih Status</label>
                  <select name="status" id="modal-status-select"
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="Hadir">Hadir</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin">Izin</option>
                    <option value="Alfa">Alfa</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="submit"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
              Simpan
            </button>
            <button type="button" onclick="closeEditModal()"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

{{-- Helper Component untuk Status Badge (Agar tidak duplikat kode) --}}
{{-- Anda bisa memindahkannya ke file terpisah jika mau, tapi di sini pakai inline view saja agar praktis --}}
{{-- Catatan: Blade component tidak bisa didefinisikan di dalam section, jadi kita pakai @include logic manual di atas --}}

@push('scripts')
  <script>
    function lihatFoto(url, title) {
      Swal.fire({
        title: title,
        imageUrl: url,
        imageAlt: 'Foto Bukti',
        showConfirmButton: false,
        width: 400,
        padding: '1rem',
        background: '#fff',
        showCloseButton: true
      });
    }

    const modal = document.getElementById('editStatusModal');
    const modalSiswaId = document.getElementById('modal-siswa-id');
    const modalSiswaNama = document.getElementById('modal-siswa-nama');
    const modalStatusSelect = document.getElementById('modal-status-select');

    function openEditModal(id, nama, currentStatus) {
      modalSiswaId.value = id;
      modalSiswaNama.textContent = nama;
      modalStatusSelect.value = currentStatus ? currentStatus : 'Sakit';
      modal.classList.remove('hidden');
    }

    function closeEditModal() {
      modal.classList.add('hidden');
    }

    @if (session('success'))
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
      });
    @endif
  </script>
@endpush

@push('styles')
  <style>
    @media print {

      nav,
      header,
      button,
      input[type="date"],
      .shadow-sm,
      a[href*="export"],
      .md\:hidden {
        display: none !important;
      }

      .hidden.md\:block {
        display: block !important;
      }

      .bg-gray-50 {
        background-color: white !important;
      }

      table {
        width: 100%;
        border: 1px solid #000;
      }

      th,
      td {
        border: 1px solid #000;
        padding: 8px;
      }

      th:last-child,
      td:last-child {
        display: none;
      }
    }
  </style>
@endpush
