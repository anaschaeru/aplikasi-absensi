@forelse ($siswaBelumHadir as $siswa)
  {{-- Tambahkan ID unik untuk setiap siswa --}}
  <div id="belum-hadir-{{ $siswa->siswa_id }}"
    class="flex items-center justify-between w-full p-2.5 rounded-lg hover:bg-gray-100 transition-all duration-300 group">

    {{-- Info Siswa (min-w-0 agar fungsi truncate teks berjalan lancar) --}}
    <div class="flex items-center space-x-3 min-w-0 flex-1 pr-3">
      <span
        class="h-9 w-9 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-extrabold flex-shrink-0 shadow-sm border border-rose-200">
        {{ strtoupper(substr($siswa->nama_siswa, 0, 1)) }}
      </span>
      <div class="min-w-0 flex-1">
        <p class="text-sm font-bold text-gray-800 truncate" title="{{ $siswa->nama_siswa }}">
          {{ $siswa->nama_siswa }}
        </p>
        <p class="text-xs text-gray-500 truncate">
          {{ $siswa->kelas->nama_kelas ?? '-' }}
        </p>
      </div>
    </div>

    {{-- Tombol Hadirkan (Hanya muncul jika user sudah login) --}}
    @auth
      <button onclick="hadirkanManual('{{ $siswa->siswa_id }}', '{{ addslashes($siswa->nama_siswa) }}')"
        class="flex-shrink-0 px-3 py-1.5 bg-emerald-500 text-white text-xs font-bold rounded-md shadow-sm hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1 transition-all duration-200 transform hover:scale-105 opacity-90 group-hover:opacity-100">
        Hadirkan
      </button>
    @endauth

  </div>
@empty
  {{-- State Kosong yang lebih menarik --}}
  <div class="flex flex-col items-center justify-center h-full py-12 opacity-80">
    <div class="p-4 bg-emerald-50 rounded-full text-emerald-500 mb-3">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
    </div>
    <p class="text-sm font-bold text-gray-600 text-center">Semua siswa sudah hadir.</p>
    <p class="text-xs text-gray-400 mt-1 text-center">Kerja bagus hari ini! 🎉</p>
  </div>
@endforelse
