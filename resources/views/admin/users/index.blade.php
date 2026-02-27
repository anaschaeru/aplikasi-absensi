@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Manajemen Pengguna
  </h2>
@endsection

@section('content')
  <div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6 text-gray-900">
          <h3 class="text-xl font-bold text-gray-800 mb-6">Daftar Pengguna</h3>

          <table id="usersTable" class="display" style="width:100%">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th style="width: 25%;">Peran (Role)</th>
                <th class="no-export">Aksi</th>
              </tr>
            </thead>
            {{-- Tbody dikosongkan karena akan diisi oleh DataTables Server-Side --}}
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  @if (session('success'))
    <script>
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
      });
    </script>
  @endif

  <script>
    function confirmReset(id, name) {
      Swal.fire({
        title: 'Reset Password?',
        html: `Anda yakin ingin mereset password untuk <b>${name}</b>?<br><small>Password akan diubah menjadi "password".</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, reset!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('reset-form-' + id).submit();
        }
      });
    }
  </script>

  {{-- Inisialisasi DataTables Server-Side --}}
  <script>
    $(document).ready(function() {
      new DataTable('#usersTable', {
        processing: true,
        serverSide: true, // Wajib bernilai true untuk Server-Side Processing
        ajax: "{{ route('admin.users.index') }}", // Rute mengambil data
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          }, // Nomor urut otomatis
          {
            data: 'name',
            name: 'name'
          },
          {
            data: 'email',
            name: 'email'
          },
          {
            data: 'role_form',
            name: 'role',
            orderable: false,
            searchable: false
          }, // Kolom ubah role
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
          } // Kolom tombol aksi
        ],
        responsive: true,
        language: {
          "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
          "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
          "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
          "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
          "infoThousands": "'",
          "lengthMenu": "Tampilkan _MENU_ entri",
          "loadingRecords": "Sedang memuat...",
          "processing": "Sedang mengambil data...", // Teks loading saat ganti halaman/cari data
          "search": "Cari:",
          "zeroRecords": "Tidak ditemukan data yang sesuai",
          "thousands": "'",
          "paginate": {
            "first": "Pertama",
            "last": "Terakhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
          },
          "aria": {
            "sortAscending": ": aktifkan untuk mengurutkan kolom ke atas",
            "sortDescending": ": aktifkan untuk mengurutkan kolom ke bawah"
          }
        },
        lengthMenu: [
          [10, 25, 50, 100, 250, 500, 1000, -1],
          [10, 25, 50, 100, 250, 500, 1000, "Semua"]
        ],
        dom: '<"flex flex-col sm:flex-row sm:justify-between gap-4 mb-4"lf>rt<"flex flex-col sm:flex-row sm:justify-between gap-4 mt-4"ip>',
      });
    });
  </script>
@endpush
