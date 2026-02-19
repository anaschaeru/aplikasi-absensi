<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Laporan Absensi Harian') }}
    </h2>
  </x-slot>

  <div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- WIDGET STATISTIK --}}
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div
          class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center transition hover:shadow-md">
          <p class="text-sm text-gray-500 font-semibold uppercase tracking-wider mb-1">Hadir</p>
          <p class="text-4xl font-extrabold text-green-500">{{ $totalHadir }}</p>
        </div>
        <div
          class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center transition hover:shadow-md">
          <p class="text-sm text-gray-500 font-semibold uppercase tracking-wider mb-1">Sakit / Izin</p>
          <p class="text-4xl font-extrabold text-yellow-500">{{ $totalIzinSakit }}</p>
        </div>
        <div
          class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center transition hover:shadow-md">
          <p class="text-sm text-gray-500 font-semibold uppercase tracking-wider mb-1">Alfa</p>
          <p class="text-4xl font-extrabold text-red-500">{{ $totalAlpa }}</p>
        </div>
        <div
          class="bg-indigo-600 p-6 rounded-2xl shadow-sm border border-indigo-500 flex flex-col items-center justify-center transition hover:shadow-md">
          <p class="text-sm text-indigo-100 font-semibold uppercase tracking-wider mb-1">Total Terdata</p>
          <p class="text-4xl font-extrabold text-white">{{ $absensi->count() }}</p>
        </div>
      </div>

      {{-- HEADER TABEL & FILTER TANGGAL + KELAS --}}
      <div
        class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
        <div>
          <h3 class="text-lg font-bold text-gray-900">Data Kehadiran</h3>
          <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
        </div>

        <form method="GET" action="{{ route('admin.laporan.harian') }}"
          class="flex flex-col sm:flex-row w-full xl:w-auto items-center gap-2">

          {{-- Input Tanggal --}}
          <input type="date" name="tanggal" value="{{ $tanggal }}" required
            class="w-full sm:w-auto border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">

          {{-- Dropdown Pilih Kelas --}}
          <select name="kelas_id"
            class="w-full sm:w-auto border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
            <option value="">-- Semua Kelas --</option>
            @foreach ($kelasList as $kelas)
              {{-- Catatan: Ganti $kelas->kelas_id menjadi $kelas->id jika Primary Key Anda adalah 'id' --}}
              <option value="{{ $kelas->kelas_id }}" {{ $kelas_id == $kelas->kelas_id ? 'selected' : '' }}>
                {{ $kelas->nama_kelas }}
              </option>
            @endforeach
          </select>

          <div class="flex w-full sm:w-auto gap-2">
            {{-- Tombol Filter --}}
            <button type="submit"
              class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold py-2 px-5 rounded-lg transition duration-150 shadow-sm flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Filter
            </button>

            {{-- Tombol Reset (Muncul jika sedang menggunakan filter) --}}
            @if (request('kelas_id') || (request('tanggal') && request('tanggal') != \Carbon\Carbon::today()->toDateString()))
              <a href="{{ route('admin.laporan.harian') }}"
                class="w-full sm:w-auto bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold py-2 px-5 rounded-lg transition duration-150 shadow-sm flex items-center justify-center border border-red-200">
                Reset
              </a>
            @endif

            {{-- Tombol Export Excel --}}
            <a href="{{ route('admin.laporan.harian.excel', request()->all()) }}"
              class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-sm flex items-center justify-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
              </svg>
              Excel
            </a>

            {{-- Tombol Export PDF --}}
            <a href="{{ route('admin.laporan.harian.pdf', request()->all()) }}"
              class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition duration-150 shadow-sm flex items-center justify-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
              </svg>
              PDF
            </a>
          </div>

        </form>
      </div>

      {{-- TABEL DATA ABSENSI --}}
      <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead class="bg-gray-50 border-b border-gray-100">
              <tr>
                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Siswa</th>
                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Masuk
                </th>
                <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Pulang
                </th>
                <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse ($absensi as $index => $absen)
                <tr class="hover:bg-gray-50/80 transition-colors">
                  <td class="py-4 px-6 text-sm text-gray-500">{{ $index + 1 }}</td>

                  {{-- Info Siswa --}}
                  <td class="py-4 px-6">
                    <div class="flex items-center">
                      <div
                        class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3">
                        {{ substr($absen->siswa->nama_siswa ?? '?', 0, 2) }}
                      </div>
                      <div>
                        <div class="text-sm font-bold text-gray-900">{{ $absen->siswa->nama_siswa ?? 'Data Terhapus' }}
                        </div>
                        <div class="text-xs text-gray-500">NISN: {{ $absen->siswa->nis ?? '-' }}</div>
                      </div>
                    </div>
                  </td>

                  <td class="py-4 px-6 text-sm font-medium text-gray-700">
                    {{ $absen->siswa->kelas->nama_kelas ?? '-' }}
                  </td>

                  {{-- Jam Masuk --}}
                  <td class="py-4 px-6 text-center">
                    @if ($absen->waktu_masuk)
                      <div class="inline-flex flex-col items-center">
                        <span
                          class="bg-green-50 text-green-700 px-2.5 py-1 rounded-md border border-green-200 text-sm font-bold shadow-sm">
                          {{ \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i') }}
                        </span>
                        @if ($absen->foto_masuk)
                          <a href="{{ asset('storage/' . $absen->foto_masuk) }}" target="_blank"
                            class="text-[10px] text-indigo-500 hover:text-indigo-700 mt-1 flex items-center">
                            Lihat Foto
                          </a>
                        @endif
                      </div>
                    @else
                      <span class="text-gray-400 font-medium">-</span>
                    @endif
                  </td>

                  {{-- Jam Pulang --}}
                  <td class="py-4 px-6 text-center">
                    @if ($absen->waktu_pulang)
                      <div class="inline-flex flex-col items-center">
                        <span
                          class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md border border-blue-200 text-sm font-bold shadow-sm">
                          {{ \Carbon\Carbon::parse($absen->waktu_pulang)->format('H:i') }}
                        </span>
                        @if ($absen->foto_pulang)
                          <a href="{{ asset('storage/' . $absen->foto_pulang) }}" target="_blank"
                            class="text-[10px] text-indigo-500 hover:text-indigo-700 mt-1 flex items-center">
                            Lihat Foto
                          </a>
                        @endif
                      </div>
                    @else
                      <span class="text-gray-400 font-medium">-</span>
                    @endif
                  </td>

                  {{-- Status Kehadiran --}}
                  <td class="py-4 px-6 text-center">
                    @if ($absen->status == 'Hadir')
                      <span
                        class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold shadow-sm">Hadir</span>
                    @elseif(in_array($absen->status, ['Sakit', 'Izin']))
                      <span
                        class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold shadow-sm">{{ ucfirst($absen->status) }}</span>
                    @else
                      <span
                        class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold shadow-sm">Alfa</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                      <div class="bg-gray-50 p-4 rounded-full mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                          </path>
                        </svg>
                      </div>
                      <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak Ada Data</h3>
                      <p class="text-gray-500 text-sm">Belum ada rekam absensi untuk tanggal dan kelas yang dipilih.
                      </p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
