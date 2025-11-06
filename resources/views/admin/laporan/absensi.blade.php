@extends('layouts.master')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Absensi</h2>
@endsection
@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Form Filter --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('admin.laporan.absensi.index') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal
                                    Mulai</label>
                                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal
                                    Akhir</label>
                                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>
                            <div>
                                <label for="kelas_id" class="block text-sm font-medium text-gray-700">Kelas
                                    (Opsional)</label>
                                <select name="kelas_id" id="kelas_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Semua Kelas</option>
                                    @foreach ($kelasList as $kelas)
                                        <option value="{{ $kelas->kelas_id }}"
                                            {{ request('kelas_id') == $kelas->kelas_id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="self-end">
                                <button type="submit"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Tampilkan
                                    Laporan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Hasil Laporan --}}
            @if ($hasilLaporan)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Hasil Laporan</h3>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.laporan.absensi.export.excel', request()->query()) }}"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Export
                                    Excel</a>
                                <a href="{{ route('admin.laporan.absensi.export.pdf', request()->query()) }}"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Export
                                    PDF</a>
                            </div>
                        </div>

                        {{-- Ringkasan --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center mb-6">
                            <div class="p-4 bg-green-100 rounded-lg">
                                <div class="text-2xl font-bold">{{ $hasilLaporan['ringkasan']->hadir }}</div>
                                <div class="text-sm">Hadir</div>
                            </div>
                            <div class="p-4 bg-yellow-100 rounded-lg">
                                <div class="text-2xl font-bold">{{ $hasilLaporan['ringkasan']->sakit }}</div>
                                <div class="text-sm">Sakit</div>
                            </div>
                            <div class="p-4 bg-blue-100 rounded-lg">
                                <div class="text-2xl font-bold">{{ $hasilLaporan['ringkasan']->izin }}</div>
                                <div class="text-sm">Izin</div>
                            </div>
                            <div class="p-4 bg-red-100 rounded-lg">
                                <div class="text-2xl font-bold">{{ $hasilLaporan['ringkasan']->alfa }}</div>
                                <div class="text-sm">Alfa</div>
                            </div>
                        </div>

                        {{-- Tabel Detail --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata
                                            Pelajaran
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oleh
                                            Guru</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($hasilLaporan['detail'] as $absensi)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ \Carbon\Carbon::parse($absensi->tanggal_absensi)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                                {{ $absensi->jadwal->mataPelajaran->nama_mapel }}</td>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                                {{ $absensi->siswa->nama_siswa ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ $absensi->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $absensi->status }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ $absensi->guru->nama_guru ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">Tidak ada data yang cocok dengan
                                                filter Anda.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $hasilLaporan['detail']->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
