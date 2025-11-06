@extends('layouts.master')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Manajemen Jadwal Pelajaran
    </h2>
@endsection

@section('content')
    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">

            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                <h3 class="text-xl font-bold text-gray-800">Jadwal Pelajaran per Kelas</h3>
                <div class="flex flex-col-reverse md:flex-row gap-4 md:items-center w-full md:w-auto">
                    <div class="w-full md:w-80">
                        <input type="text" id="jadwal-search-input"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Cari Kelas, Mapel, Guru...">
                    </div>
                    <a href="{{ route('admin.jadwal.create') }}"
                        class="inline-flex justify-center items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Tambah Jadwal
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    {{-- Navigasi Tab --}}
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                            @foreach ($jadwals->keys() as $namaKelas)
                                <button
                                    class="tab-button whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm {{ $loop->first ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                    onclick="changeTab({{ $loop->index }})">
                                    {{ $namaKelas }}
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    {{-- Konten Tab --}}
                    <div class="mt-6">
                        @foreach ($jadwals as $namaKelas => $jadwalPerKelas)
                            <div id="tab-content-{{ $loop->index }}"
                                class="tab-content {{ $loop->first ? '' : 'hidden' }}">
                                @php
                                    $jadwalPerHari = $jadwalPerKelas->groupBy('hari');
                                    $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                @endphp

                                {{-- Tampilan Tabel Mingguan --}}
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border-collapse border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                                @foreach ($urutanHari as $hari)
                                                    <th class="border border-gray-200 px-4 py-2">{{ $hari }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="align-top">
                                            <tr>
                                                @foreach ($urutanHari as $hari)
                                                    <td class="border border-gray-200 p-2 day-column">
                                                        <div class="space-y-2">
                                                            @forelse ($jadwalPerHari[$hari] ?? [] as $jadwal)
                                                                <div
                                                                    class="bg-indigo-50 rounded-lg p-3 text-sm schedule-item hover:bg-indigo-100 transition">
                                                                    <p class="font-bold text-indigo-800">
                                                                        {{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}
                                                                    </p>
                                                                    <p class="text-gray-600">
                                                                        {{ $jadwal->guru->nama_guru ?? 'N/A' }}</p>
                                                                    <p class="text-xs text-gray-500 font-mono mt-1">
                                                                        {{ date('H:i', strtotime($jadwal->jam_mulai)) }} -
                                                                        {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                                                    </p>

                                                                    {{-- AREA Ikon Edit & Hapus --}}
                                                                    <div class="flex items-center gap-2 mt-2 text-xs">
                                                                        <a href="{{ route('admin.jadwal.edit', $jadwal->jadwal_id) }}"
                                                                            class="text-blue-600 hover:text-blue-800 transition p-1 rounded-md hover:bg-blue-100"
                                                                            title="Edit Jadwal">
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke-width="1.5" stroke="currentColor"
                                                                                class="size-4">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                                            </svg>
                                                                        </a>
                                                                        <form id="delete-form-{{ $jadwal->jadwal_id }}"
                                                                            action="{{ route('admin.jadwal.destroy', $jadwal->jadwal_id) }}"
                                                                            method="POST">
                                                                            @csrf @method('DELETE')
                                                                            <button type="button"
                                                                                class="text-red-600 hover:text-red-800 transition p-1 rounded-md hover:bg-red-100"
                                                                                onclick="confirmDelete({{ $jadwal->jadwal_id }})"
                                                                                title="Hapus Jadwal">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke-width="1.5" stroke="currentColor"
                                                                                    class="size-4">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                                                </svg>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div
                                                                    class="text-center text-xs text-gray-400 p-4 empty-day-message">
                                                                    -- Kosong --</div>
                                                            @endforelse
                                                            <div
                                                                class="text-center text-xs text-gray-400 p-4 hidden no-results-message">
                                                                -- Tidak ada hasil --</div>
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

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Jadwal yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
            tabButtons.forEach((button, index) => {
                const isActive = index === tabIndex;
                button.classList.toggle('border-indigo-500', isActive);
                button.classList.toggle('text-indigo-600', isActive);
                button.classList.toggle('border-transparent', !isActive);
                button.classList.toggle('text-gray-500', !isActive);
            });
            tabContents.forEach((content, index) => {
                content.classList.toggle('hidden', index !== tabIndex);
            });
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            tabButtons.forEach(button => {
                const tabName = button.textContent.toLowerCase().trim();
                button.classList.toggle('hidden', searchTerm.length > 0 && !tabName.includes(searchTerm));
            });

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
                if (emptyDayMessage) emptyDayMessage.classList.toggle('hidden', isSearching);
                if (noResultsMessage) noResultsMessage.classList.toggle('hidden', hasVisibleItems || !
                    isSearching);
            });
        });
    </script>
@endpush
