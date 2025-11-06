@forelse ($siswaBelumHadir as $siswa)
    {{-- Tambahkan ID unik untuk setiap siswa --}}
    <div id="belum-hadir-{{ $siswa->siswa_id }}"
        class="flex items-center justify-between p-2 rounded-md hover:bg-gray-50 transition-all duration-300">
        <div class="flex items-center space-x-3">
            <span
                class="h-8 w-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ substr($siswa->nama_siswa, 0, 1) }}
            </span>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $siswa->nama_siswa }}</p>
                <p class="text-xs text-gray-500">{{ $siswa->kelas->nama_kelas }}</p>
            </div>
        </div>
        <button onclick="hadirkanManual('{{ $siswa->siswa_id }}', '{{ addslashes($siswa->nama_siswa) }}')"
            class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 transition duration-150">
            Hadirkan
        </button>
    </div>
@empty
    <div class="text-center py-8">
        <p class="text-sm text-gray-500">Semua siswa sudah hadir hari ini. Kerja bagus! 👍</p>
    </div>
@endforelse
