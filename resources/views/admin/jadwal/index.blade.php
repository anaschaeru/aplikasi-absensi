@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Manajemen Jadwal Pelajaran
  </h2>
@endsection

@section('content')
  <div class="py-6 md:py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6">

          {{-- Header: Judul, Pencarian, Tombol Aksi --}}
          <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <h3 class="text-xl font-bold text-gray-800">Jadwal Pelajaran per Kelas</h3>

            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
              {{-- Input Pencarian --}}
              <div class="w-full sm:w-64">
                <input type="text" id="jadwal-search-input"
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm"
                  placeholder="Cari Kelas, Mapel, Guru...">
              </div>

              {{-- Tombol Import --}}
              <button onclick="document.getElementById('importModal').showModal()"
                class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Import
              </button>

              {{-- Tombol Tambah --}}
              <a href="{{ route('admin.jadwal.create') }}"
                class="inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
              </a>
            </div>
          </div>

          {{-- Navigasi Tab (Kelas) --}}
          <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-6 overflow-x-auto pb-1" aria-label="Tabs">
              @foreach ($jadwals->keys() as $namaKelas)
                <button
                  class="tab-button whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $loop->first ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                  onclick="changeTab({{ $loop->index }})">
                  {{ $namaKelas }}
                </button>
              @endforeach
            </nav>
          </div>

          {{-- Konten Tab --}}
          <div>
            @foreach ($jadwals as $namaKelas => $jadwalPerKelas)
              <div id="tab-content-{{ $loop->index }}" class="tab-content {{ $loop->first ? '' : 'hidden' }}">
                @php
                  $jadwalPerHari = $jadwalPerKelas->groupBy('hari');
                  $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                @endphp

                {{-- Tabel Jadwal Mingguan --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        @foreach ($urutanHari as $hari)
                          <th scope="col"
                            class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r last:border-r-0 min-w-[160px]">
                            {{ $hari }}
                          </th>
                        @endforeach
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <tr>
                        @foreach ($urutanHari as $hari)
                          <td class="p-2 align-top border-r last:border-r-0 day-column bg-gray-50/30">
                            <div class="space-y-3">
                              @forelse ($jadwalPerHari[$hari] ?? [] as $jadwal)
                                <div
                                  class="bg-white border border-indigo-100 rounded-lg p-3 shadow-sm hover:shadow-md transition schedule-item group relative">

                                  {{-- Informasi Jadwal --}}
                                  <div class="mb-1">
                                    <span
                                      class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 mb-1">
                                      {{ date('H:i', strtotime($jadwal->jam_mulai)) }} -
                                      {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                    </span>
                                    <h4 class="font-bold text-gray-800 text-sm leading-tight">
                                      {{ $jadwal->mataPelajaran->nama_mapel ?? 'Mapel Terhapus' }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                      {{ $jadwal->guru->nama_guru ?? 'Guru Terhapus' }}
                                    </p>
                                  </div>

                                  {{-- Aksi (Edit/Hapus) --}}
                                  <div
                                    class="flex items-center justify-end gap-2 mt-2 pt-2 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.jadwal.edit', $jadwal->jadwal_id) }}"
                                      class="text-blue-600 hover:text-blue-800 p-1 bg-blue-50 rounded hover:bg-blue-100 transition"
                                      title="Edit">
                                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                      </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form id="delete-form-{{ $jadwal->jadwal_id }}"
                                      action="{{ route('admin.jadwal.destroy', $jadwal->jadwal_id) }}" method="POST">
                                      @csrf @method('DELETE')
                                      <button type="button" onclick="confirmDelete({{ $jadwal->jadwal_id }})"
                                        class="text-red-600 hover:text-red-800 p-1 bg-red-50 rounded hover:bg-red-100 transition"
                                        title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                          viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                      </button>
                                    </form>
                                  </div>
                                </div>
                              @empty
                                <div class="text-center py-4 empty-day-message">
                                  <span class="text-xs text-gray-400 italic">-- Kosong --</span>
                                </div>
                              @endforelse

                              {{-- Pesan Hasil Pencarian Nihil --}}
                              <div class="hidden text-center py-4 no-results-message">
                                <span class="text-xs text-gray-400 italic">-- Tidak ditemukan --</span>
                              </div>
                            </div>
                          </td>
                        @endforeach
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            @endforeach
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- MODAL IMPORT EXCEL --}}
  <dialog id="importModal" class="p-0 rounded-lg shadow-xl w-11/12 md:w-1/3 backdrop:bg-gray-900/50">
    <div class="bg-white rounded-lg">
      <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
        <h3 class="text-lg font-semibold text-gray-900">Import Jadwal Pelajaran</h3>
        <form method="dialog">
          <button class="text-gray-400 hover:text-gray-600 focus:outline-none transition">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </form>
      </div>

      <form action="{{ route('admin.jadwal.import') }}" method="POST" enctype="multipart/form-data" class="p-6">
        @csrf

        {{-- Info Template Update --}}
        <div class="mb-5 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r">
          <p class="text-sm font-bold text-blue-800 mb-2">Format Kolom Excel (Baris 1):</p>
          <div class="overflow-x-auto pb-2">
            <code class="bg-blue-100 px-2 py-1 rounded font-mono text-blue-800 text-xs whitespace-nowrap">
              nama_kelas | nama_mapel | nama_guru | hari | jam_mulai | jam_selesai
            </code>
          </div>
          <ul class="list-disc list-inside mt-2 text-xs text-blue-700 space-y-1">
            <li>Pastikan penulisan <b>Nama Kelas</b> persis dengan Database (spasi/huruf besar).</li>
            <li>Format Jam: HH:MM (Contoh: 07:30).</li>
          </ul>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
          <input type="file" name="file" accept=".xlsx, .xls" required
            class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-full file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100 border border-gray-300 rounded-lg cursor-pointer focus:outline-none transition">
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button type="button" onclick="document.getElementById('importModal').close()"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition font-medium text-sm">
            Batal
          </button>
          <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition flex items-center font-medium text-sm shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Upload & Import
          </button>
        </div>
      </form>
    </div>
  </dialog>
@endsection

@push('scripts')
  {{-- Notifikasi Sukses --}}
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

  {{-- Notifikasi Error --}}
  @if (session('error'))
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: @json(session('error')),
      });
    </script>
  @endif

  {{-- Logic Delete, Tab, dan Search --}}
  <script>
    function confirmDelete(id) {
      Swal.fire({
        title: 'Hapus Jadwal?',
        text: "Jadwal yang dihapus tidak dapat dikembalikan!",
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

    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const searchInput = document.getElementById('jadwal-search-input');

    function changeTab(tabIndex) {
      // Reset state tombol tab
      tabButtons.forEach((button, index) => {
        const isActive = index === tabIndex;
        button.classList.toggle('border-indigo-500', isActive);
        button.classList.toggle('text-indigo-600', isActive);
        button.classList.toggle('border-transparent', !isActive);
        button.classList.toggle('text-gray-500', !isActive);
      });

      // Tampilkan konten tab yang sesuai
      tabContents.forEach((content, index) => {
        content.classList.toggle('hidden', index !== tabIndex);
      });

      // Reset search saat pindah tab
      searchInput.value = '';
      searchInput.dispatchEvent(new Event('input'));
    }

    // Logic Pencarian (Client-Side filtering)
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();

      // Filter tombol Tab (Jika nama kelas cocok dengan pencarian)
      tabButtons.forEach(button => {
        const tabName = button.textContent.toLowerCase().trim();
        // Jika sedang mencari, sembunyikan tab yang tidak cocok
        if (searchTerm.length > 0) {
          button.style.display = tabName.includes(searchTerm) ? 'inline-block' : 'none';
        } else {
          button.style.display = 'inline-block';
        }
      });

      // Filter Item Jadwal di dalam Tab Aktif
      const activeTabContent = document.querySelector('.tab-content:not(.hidden)');
      if (!activeTabContent) return;

      const dayColumns = activeTabContent.querySelectorAll('.day-column');
      dayColumns.forEach(column => {
        const scheduleItems = column.querySelectorAll('.schedule-item');
        const noResultsMessage = column.querySelector('.no-results-message');
        const emptyDayMessage = column.querySelector('.empty-day-message');
        let hasVisibleItems = false;

        scheduleItems.forEach(item => {
          const itemText = item.textContent.toLowerCase();
          const isVisible = itemText.includes(searchTerm);
          item.classList.toggle('hidden', !isVisible);
          if (isVisible) hasVisibleItems = true;
        });

        const isSearching = searchTerm.length > 0;

        // Atur visibilitas pesan kosong/tidak ditemukan
        if (emptyDayMessage) {
          emptyDayMessage.classList.toggle('hidden', isSearching || hasVisibleItems);
        }

        if (noResultsMessage) {
          noResultsMessage.classList.toggle('hidden', hasVisibleItems || !isSearching);
        }
      });
    });
  </script>
@endpush
