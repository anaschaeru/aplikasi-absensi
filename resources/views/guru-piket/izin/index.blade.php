@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengajuan Izin</h2>
@endsection

@section('content')
  <div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6 text-gray-900">
          <h3 class="text-xl font-bold text-gray-800 mb-6">Daftar Pengajuan Izin</h3>

          {{-- Tampilkan pesan error validasi jika ada --}}
          @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
              <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <table id="izinTable" class="display" style="width:100%">
            <thead>
              <tr>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tanggal Izin</th>
                <th>Alasan</th>
                <th>Status</th>
                <th class="no-export">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($izins as $izin)
                <tr>
                  <td>{{ $izin->siswa->nama_siswa ?? 'Siswa Dihapus' }}</td>
                  <td>{{ $izin->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                  <td>{{ \Carbon\Carbon::parse($izin->tanggal_izin)->format('d M Y') }}</td>
                  <td class="whitespace-normal">{{ $izin->alasan }}</td>
                  <td>
                    <span
                      class="px-2 py-1 font-semibold leading-tight text-xs rounded-full
                                            {{-- Sesuaikan logika warna dengan status di database (english) --}}
                                            @if ($izin->status == 'pending') bg-yellow-100 text-yellow-700
                                            @elseif ($izin->status == 'approved' || $izin->status == 'disetujui') bg-green-100 text-green-700
                                            @elseif ($izin->status == 'rejected' || $izin->status == 'ditolak') bg-red-100 text-red-700 @endif">
                      {{ ucfirst($izin->status) }}
                    </span>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      @if ($izin->status == 'pending')
                        {{-- TOMBOL SETUJUI --}}
                        <form id="approve-form-{{ $izin->id }}" action="{{ route('piket.izin.update', $izin->id) }}"
                          method="POST">
                          @csrf
                          @method('PUT')
                          {{-- Input Hidden untuk mengirim status approved --}}
                          <input type="hidden" name="status" value="approved">

                          <button type="button"
                            onclick="confirmAction('approve', {{ $izin->id }}, '{{ $izin->siswa->nama_siswa ?? 'Siswa Ini' }}')"
                            class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-md hover:bg-green-600 transition"
                            title="Setujui Izin">
                            Setujui
                          </button>
                        </form>

                        {{-- TOMBOL TOLAK --}}
                        <form id="reject-form-{{ $izin->id }}" action="{{ route('piket.izin.update', $izin->id) }}"
                          method="POST">
                          @csrf
                          @method('PUT')
                          {{-- Input Hidden untuk mengirim status rejected --}}
                          <input type="hidden" name="status" value="rejected">

                          <button type="button"
                            onclick="confirmAction('reject', {{ $izin->id }}, '{{ $izin->siswa->nama_siswa ?? 'Siswa Ini' }}')"
                            class="px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded-md hover:bg-red-600 transition"
                            title="Tolak Izin">
                            Tolak
                          </button>
                        </form>
                      @elseif ($izin->status == 'approved' || $izin->status == 'disetujui')
                        {{-- TOMBOL BATALKAN (Ubah jadi rejected atau pending) --}}
                        <form id="cancel-form-{{ $izin->id }}" action="{{ route('piket.izin.update', $izin->id) }}"
                          method="POST">
                          @csrf
                          @method('PUT')
                          {{-- Kembalikan ke rejected atau pending --}}
                          <input type="hidden" name="status" value="rejected">

                          <button type="button"
                            onclick="confirmAction('cancel', {{ $izin->id }}, '{{ $izin->siswa->nama_siswa ?? 'Siswa Ini' }}')"
                            class="px-2 py-1 bg-gray-500 text-white text-xs font-semibold rounded-md hover:bg-gray-600 transition"
                            title="Batalkan Persetujuan">
                            Batalkan
                          </button>
                        </form>
                      @else
                        <span class="text-xs text-gray-500">Selesai</span>
                      @endif
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
        timer: 3000,
        timerProgressBar: true
      });
    </script>
  @endif

  {{-- Script Konfirmasi Aksi --}}
  <script>
    function confirmAction(actionType, id, studentName) {
      let config = {};
      if (actionType === 'approve') {
        config = {
          title: 'Setujui Izin?',
          html: `Anda akan menyetujui izin untuk <b>${studentName}</b>.`,
          icon: 'question',
          confirmButtonText: 'Ya, Setujui!',
          confirmButtonColor: '#16a34a',
          formId: 'approve-form-' + id
        };
      } else if (actionType === 'reject') {
        config = {
          title: 'Tolak Izin?',
          html: `Anda akan menolak izin untuk <b>${studentName}</b>.`,
          icon: 'warning',
          confirmButtonText: 'Ya, Tolak!',
          confirmButtonColor: '#d33',
          formId: 'reject-form-' + id
        };
      } else if (actionType === 'cancel') {
        config = {
          title: 'Batalkan Izin?',
          html: `Anda akan membatalkan izin yang sudah disetujui untuk <b>${studentName}</b>.`,
          icon: 'warning',
          confirmButtonText: 'Ya, Batalkan!',
          confirmButtonColor: '#d33',
          formId: 'cancel-form-' + id
        };
      }

      Swal.fire({
        title: config.title,
        html: config.html,
        icon: config.icon,
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: config.confirmButtonColor,
        confirmButtonText: config.confirmButtonText,
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById(config.formId).submit();
        }
      });
    }

    $(document).ready(function() {
      new DataTable('#izinTable', {
        responsive: true,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' // Opsional: Bahasa Indonesia otomatis
        },
        dom: '<"flex flex-col sm:flex-row sm:justify-between gap-4 mb-4"lf>rt<"flex flex-col sm:flex-row sm:justify-between gap-4 mt-4"ip>',
        order: [
          [4, 'asc'] // Urutkan status
        ]
      });
    });
  </script>
@endpush
