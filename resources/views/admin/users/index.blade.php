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
                        <tbody>
                            @php
                                $roles = ['admin', 'guru', 'guru_piket', 'siswa'];
                            @endphp
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{-- FORM UNTUK MENGUBAH ROLE --}}
                                        <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST"
                                            class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role"
                                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role }}"
                                                        @if ($user->role == $role) selected @endif>
                                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                class="p-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                                title="Simpan Perubahan Role">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor" class="w-4 h-4">
                                                    <path
                                                        d="M9.25 13.5a.75.75 0 001.5 0V4.636l2.955 3.129a.75.75 0 001.09-1.03l-4.25-4.5a.75.75 0 00-1.09 0l-4.25 4.5a.75.75 0 101.09 1.03L9.25 4.636v8.864z" />
                                                    <path
                                                        d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        {{-- FORM UNTUK RESET PASSWORD --}}
                                        <form id="reset-form-{{ $user->id }}"
                                            action="{{ route('admin.users.resetPassword', $user->id) }}" method="POST">
                                            @csrf
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 transition"
                                                onclick="confirmReset({{ $user->id }}, '{{ $user->name }}')">
                                                Reset Pass
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
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

    {{-- Inisialisasi DataTables --}}
    <script>
        $(document).ready(function() {
            new DataTable('#usersTable', {
                responsive: true,
                language: {
                    "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "infoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "infoThousands": "'",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Sedang memuat...",
                    "processing": "Sedang memproses...",
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
