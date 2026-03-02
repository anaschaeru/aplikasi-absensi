@forelse ($aktivitasTerbaru as $absensi)
  <div
    class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 border border-transparent hover:border-gray-100 group">

    {{-- Bagian Kiri: Avatar & Info Siswa --}}
    <div class="flex items-center space-x-2.5 min-w-0 flex-1">

      {{-- Avatar / Foto Bukti --}}
      @if ($absensi->foto_masuk)
        <div
          class="h-8 w-8 rounded-full overflow-hidden border border-emerald-200 flex-shrink-0 cursor-pointer shadow-sm group-hover:border-emerald-400 transition-colors"
          onclick="Swal.fire({imageUrl: '{{ asset('storage/' . $absensi->foto_masuk) }}', showConfirmButton: false, customClass: { popup: 'rounded-2xl' }})">
          <img src="{{ asset('storage/' . $absensi->foto_masuk) }}" alt="Foto {{ $absensi->siswa->nama_siswa }}"
            class="w-full h-full object-cover">
        </div>
      @else
        <span
          class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm border border-emerald-200">
          {{ strtoupper(substr($absensi->siswa->nama_siswa, 0, 1)) }}
        </span>
      @endif

      {{-- Detail Info Siswa --}}
      <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-gray-800 truncate" title="{{ $absensi->siswa->nama_siswa }}">
          {{ $absensi->siswa->nama_siswa }}
        </p>
        <p class="text-[11px] text-gray-500 truncate mt-0.5">
          {{ $absensi->siswa->kelas->nama_kelas ?? '-' }}
        </p>
      </div>
    </div>

    {{-- Bagian Kanan: Waktu Absensi (Lebih Minimalis) --}}
    <div class="flex flex-col items-end flex-shrink-0 ml-2 space-y-1">
      {{-- Waktu Masuk (Hijau) --}}
      <span
        class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 tracking-wide"
        title="Waktu Masuk">
        &darr; {{ \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') }}
      </span>

      {{-- Waktu Pulang (Biru) --}}
      @if ($absensi->waktu_pulang)
        <span
          class="text-[10px] text-blue-700 font-bold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 tracking-wide"
          title="Waktu Pulang">
          &uarr; {{ \Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i') }}
        </span>
      @endif
    </div>

  </div>
@empty
  {{-- State Kosong Aktivitas (Dibuat lebih ringkas) --}}
  <div class="flex flex-col items-center justify-center h-full py-8 opacity-70">
    <svg class="w-6 h-6 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <p class="text-xs font-medium text-gray-500 text-center">Belum ada aktivitas.</p>
  </div>
@endforelse
