@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Manajemen Siswa
  </h2>
@endsection

@section('content')
  <div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6 text-gray-900">

          {{-- Header Halaman Responsif --}}
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
            <h3 class="text-xl font-bold text-gray-800">Daftar Siswa</h3>

            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
              {{-- Tombol Import Excel --}}
              <button onclick="document.getElementById('importModal').showModal()"
                class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 w-full sm:w-auto transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Import Excel
              </button>

              {{-- Tombol Tambah Siswa --}}
              <a href="{{ route('admin.siswa.create') }}"
                class="inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 w-full sm:w-auto transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Siswa
              </a>
            </div>
          </div>

          {{-- Tabel untuk DataTables --}}
          <table id="siswaTable" class="display" style="width:100%">
            <thead>
              <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Email (Login)</th>
                <th class="no-export">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($siswas as $siswa)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>
                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-mono font-bold text-gray-600">
                      {{ $siswa->nis }}
                    </span>
                  </td>
                  <td class="font-medium">{{ $siswa->nama_siswa }}</td>
                  <td>
                    @if ($siswa->kelas)
                      <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                        {{ $siswa->kelas->nama_kelas }}
                      </span>
                    @else
                      <span class="text-gray-400 italic text-xs">Tanpa Kelas</span>
                    @endif
                  </td>
                  <td class="text-sm text-gray-600">{{ $siswa->user->email ?? 'N/A' }}</td>
                  <td>
                    <div class="flex justify-end items-center gap-2">
                      <a href="{{ route('admin.siswa.edit', $siswa->siswa_id) }}"
                        class="text-blue-600 hover:text-blue-800 transition p-1 rounded-md hover:bg-blue-100"
                        title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-4">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                      </a>
                      <form id="delete-form-{{ $siswa->siswa_id }}"
                        action="{{ route('admin.siswa.destroy', $siswa->siswa_id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $siswa->siswa_id }})" title="Hapus"
                          class="text-red-600 hover:text-red-800 transition p-1 rounded-md hover:bg-red-100">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                          </svg>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL IMPORT EXCEL --}}
  <dialog id="importModal" class="p-0 rounded-lg shadow-xl w-11/12 md:w-1/3 backdrop:bg-gray-900/50">
    <div class="bg-white">
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Import Data Siswa</h3>
        <form method="dialog">
          <button class="text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </form>
      </div>

      <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf

        {{-- Instruksi Template --}}
        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 text-sm text-yellow-800">
          <p class="font-bold mb-2">Format Header Excel (Baris 1):</p>
          <code class="bg-yellow-100 px-2 py-1 rounded font-mono font-bold text-xs block mb-2 overflow-x-auto">nis |
            nama_siswa | nama_kelas | alamat</code>

          <ul class="list-disc list-inside mt-3 text-xs space-y-1">
            <li><strong>Nama Kelas:</strong> Harus SAMA PERSIS dengan data di aplikasi (Contoh: "X RPL 1").</li>
            <li>Sistem otomatis membuat akun login Siswa.</li>
            <li><strong>Email Login:</strong> [NIS]@siswa.id</li>
            <li><strong>Password Default:</strong> password123</li>
          </ul>
        </div>

        {{-- Input File --}}
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
          <input type="file" name="file" accept=".xlsx, .xls" required
            class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer focus:outline-none">
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3 mt-6">
          <button type="button" onclick="document.getElementById('importModal').close()"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition font-medium text-sm">
            Batal
          </button>
          <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center font-medium text-sm shadow-sm">
            Upload & Import
          </button>
        </div>
      </form>
    </div>
  </dialog>
@endsection

@push('scripts')
  {{-- Notifikasi --}}
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
        icon: 'error',
        title: 'Gagal Import',
        text: @json(session('error')),
      });
    </script>
  @endif

  {{-- Script Konfirmasi Hapus --}}
  <script>
    function confirmDelete(id) {
      Swal.fire({
        title: 'Hapus Siswa?',
        text: "Data siswa dan akun login mereka akan dihapus permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('delete-form-' + id).submit();
        }
      });
    }
  </script>

  {{-- Inisialisasi DataTables --}}
  <script>
    $(document).ready(function() {
      new DataTable('#siswaTable', {
        responsive: true,
        language: {
          "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
          "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
          "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
          "lengthMenu": "Tampilkan _MENU_ entri",
          "loadingRecords": "Sedang memuat...",
          "processing": "Sedang memproses...",
          "search": "Cari:",
          "zeroRecords": "Tidak ditemukan data yang sesuai",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
          }
        },
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, "Semua"]
        ],
        dom: '<"flex flex-col sm:flex-row sm:justify-between gap-4 mb-4"lf>rt<"flex flex-col sm:flex-row sm:justify-between gap-4 mt-4"ip>'
      });
    });
  </script>
@endpush
