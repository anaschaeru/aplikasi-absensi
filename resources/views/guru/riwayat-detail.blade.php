@extends('layouts.master')

@section('header')
    {{-- Kita kosongkan header default agar bisa membuat header sticky sendiri --}}
@endsection

@section('content')
    <div x-data="attendanceApp()" class="bg-gray-100 min-h-screen">
        <form action="{{ route('guru.absensi.update') }}" method="POST">
            @csrf
            @method('PUT')

            {{-- =================================== --}}
            {{-- HEADER STICKY --}}
            {{-- =================================== --}}
            <div class="sticky top-0 z-10 bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        {{-- Informasi Pelajaran --}}
                        <div>
                            <h2 class="font-bold text-xl text-gray-800">{{ $jadwal->mataPelajaran->nama_mapel }}</h2>
                            <p class="text-sm text-gray-500">{{ $jadwal->kelas->nama_kelas }} &middot;
                                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}</p>
                        </div>
                        {{-- Tombol Aksi Cepat --}}
                        <div>
                            <button @click="setAllStatus('Hadir')" type="button"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                Hadirkan Semua
                            </button>
                        </div>
                    </div>
                </div>
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                        class="bg-green-100 border-t border-green-200 text-green-800 px-4 py-2 text-sm text-center">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            {{-- =================================== --}}
            {{-- DAFTAR SISWA (KONTEN UTAMA) --}}
            {{-- =================================== --}}
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">

                    {{-- ======================================================= --}}
                    {{-- HEADER TABEL (th) - DENGAN 4 KOLOM MENGGUNAKAN GRID --}}
                    {{-- ======================================================= --}}
                    <div
                        class="grid grid-cols-[auto_1fr_auto_auto] gap-x-4 items-center p-4 bg-gray-50 border-b border-gray-200">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">No</span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Siswa</span>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 text-center">Status</span>
                        <span
                            class="text-xs font-semibold uppercase tracking-wider text-gray-500 text-center">Catatan</span>
                    </div>

                    @foreach ($detailAbsensi as $absensi)
                        {{-- Setiap siswa memiliki state 'showNotes' sendiri --}}
                        <div x-data="{ selectedStatus: '{{ $absensi->status }}', showNotes: {{ json_encode(!empty($absensi->catatan)) }} }">
                            <div class="p-4 flex items-center justify-between">
                                {{-- Info Siswa --}}
                                <div class="flex items-center">
                                    <span
                                        class="text-sm font-medium text-gray-500 w-8 flex-shrink-0">{{ $loop->iteration }}.</span>
                                    <div>
                                        {{-- NIS dengan font kecil di atas nama --}}
                                        <div class="text-xs text-gray-500">{{ $absensi->siswa->nis ?? 'N/A' }}</div>
                                        <div class="font-semibold text-gray-800">{{ $absensi->siswa->nama_siswa ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Kontrol Absensi --}}
                                <div class="flex items-center space-x-2">
                                    {{-- Grup Tombol Status (H,S,I,A) --}}
                                    <div class="flex rounded-lg border border-gray-300 p-0.5">
                                        @foreach (['Hadir', 'Sakit', 'Izin', 'Alfa'] as $status)
                                            <label @click="selectedStatus = '{{ $status }}'"
                                                class="relative cursor-pointer">
                                                <input type="radio" name="absensi[{{ $absensi->absensi_id }}][status]"
                                                    value="{{ $status }}" class="sr-only" x-model="selectedStatus"
                                                    required>
                                                <div class="px-3 py-1 text-sm font-bold rounded-md transition-colors"
                                                    :class="{
                                                        'bg-indigo-600 text-white': selectedStatus === '{{ $status }}',
                                                        'text-gray-500 hover:bg-gray-100': selectedStatus !== '{{ $status }}'
                                                    }">
                                                    {{ substr($status, 0, 1) }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    {{-- Tombol untuk menampilkan catatan --}}
                                    <button @click="showNotes = !showNotes" type="button"
                                        class="p-2 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                                        :class="{ 'bg-indigo-100 text-indigo-600': showNotes }">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Area Input Catatan (Tersembunyi) --}}
                            <div x-show="showNotes" x-transition class="px-4 pb-4">
                                <input type="text" name="absensi[{{ $absensi->absensi_id }}][catatan]"
                                    value="{{ $absensi->catatan }}"
                                    class="block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Tambahkan catatan...">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- =================================== --}}
            {{-- FOOTER STICKY --}}
            {{-- =================================== --}}
            <div class="sticky bottom-0 z-10 bg-white/80 backdrop-blur-sm border-t border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-4">
                        <a href="{{ route('guru.absensi.history') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-800">
                            &larr; Kembali
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-sm hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Script untuk Alpine.js --}}
    <script>
        function attendanceApp() {
            return {
                setAllStatus(status) {
                    // Cari semua input radio yang valuenya sesuai dengan status yang di-klik
                    document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(radio => {
                        radio.checked = true;
                        // Memicu event 'change' agar Alpine bisa mendeteksi perubahan jika diperlukan
                        radio.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    });
                }
            }
        }
    </script>
@endsection
