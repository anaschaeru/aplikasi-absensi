@forelse ($aktivitasTerbaru as $absensi)
  <div class="flex items-center space-x-3">
    <span class="h-8 w-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold">
      {{ substr($absensi->siswa->nama_siswa, 0, 1) }}
    </span>
    <div>
      <p class="text-sm font-medium text-gray-900">{{ $absensi->siswa->nama_siswa }}</p>
      <p class="text-xs text-gray-500">
        {{ $absensi->siswa->kelas->nama_kelas }} |
        {{ \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') }}
        | {{ $absensi->waktu_pulang ? \Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i') : '-' }}
      </p>
    </div>
  </div>
@empty
  <p class="text-sm text-gray-500">Belum ada aktivitas absensi hari ini.</p>
@endforelse
