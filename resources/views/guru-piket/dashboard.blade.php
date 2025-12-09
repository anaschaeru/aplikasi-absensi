@extends('layouts.master')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Dasbor Guru Piket
  </h2>
@endsection

@section('content')
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      {{-- Statistik Hari Ini --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
          <h3 class="text-sm font-medium text-gray-500">Total Siswa</h3>
          <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalSiswa }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm">
          <h3 class="text-sm font-medium text-gray-500">Sudah Hadir</h3>
          <p id="statistik-hadir" class="mt-1 text-3xl font-semibold text-green-600">{{ $jumlahHadir }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm">
          <h3 class="text-sm font-medium text-gray-500">Belum Hadir</h3>
          <p id="statistik-belum-hadir" class="mt-1 text-3xl font-semibold text-red-600">{{ $jumlahBelumHadir }}
          </p>
        </div>
      </div>

      {{-- Grid Utama 3 Kolom --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom 1: Aktivitas Terbaru --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm">
          <h3 class="font-bold text-lg mb-4">Aktivitas Terbaru</h3>
          <div class="overflow-y-auto h-96">
            <div id="aktivitas-terbaru" class="space-y-4">
              @include('guru-piket._aktivitas_terbaru', [
                  'aktivitasTerbaru' => $aktivitasTerbaru,
              ])
            </div>
          </div>
        </div>

        {{-- Kolom 2: Area Absensi (Scanner & Kamera) --}}
        <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="font-bold text-lg mb-4 text-center">Absensi Siswa</h3>

            {{-- TAB SWITCHER --}}
            <div class="flex justify-center mb-4 bg-gray-100 p-1 rounded-lg">
              <button onclick="switchMode('camera')" id="btn-mode-camera"
                class="w-1/2 py-2 px-4 rounded-md text-sm font-medium transition-all bg-white text-indigo-600 shadow-sm">
                Kamera
              </button>
              <button onclick="switchMode('scanner')" id="btn-mode-scanner"
                class="w-1/2 py-2 px-4 rounded-md text-sm font-medium text-gray-500 hover:text-gray-700 transition-all">
                Alat Scanner
              </button>
            </div>

            {{-- INPUT ALAT SCANNER (Hanya muncul di Mode Scanner) --}}
            <div id="mode-scanner-container" class="hidden mb-4">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                    </path>
                  </svg>
                </div>
                {{-- Input ini menerima tembakan scanner --}}
                <input type="text" id="scanner_input"
                  class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono"
                  placeholder="Klik disini & Scan..." autocomplete="off">
              </div>
              <p class="text-[10px] text-gray-500 mt-1 text-center">Scan otomatis submit & ambil foto.</p>
            </div>

            {{-- PREVIEW KAMERA (Selalu Muncul di Kedua Mode) --}}
            <div id="mode-camera-container" class="transition-all duration-300">
              <div class="max-w-xs mx-auto relative group">
                {{-- Video Element --}}
                <div id="reader"
                  class="w-full aspect-square bg-black rounded-lg overflow-hidden border-2 border-gray-200"></div>

                {{-- Overlay Text (Berubah sesuai mode) --}}
                <div class="absolute bottom-2 left-0 right-0 text-center">
                  <span id="camera-overlay-text"
                    class="inline-block px-2 py-1 bg-black/50 text-white text-xs rounded-full backdrop-blur-sm">
                    Mode Kamera Aktif
                  </span>
                </div>
              </div>
            </div>

            <div id="scan-status" class="mt-4 h-14"></div>
          </div>
        </div>

        {{-- Kolom 3: Daftar Siswa Belum Hadir --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm">
          <h3 id="judul-belum-hadir" class="font-bold text-lg mb-4">Daftar Siswa Belum Hadir
            ({{ $siswaBelumHadir->count() }})</h3>
          <div id="daftar-belum-hadir" class="overflow-y-auto h-96 space-y-2 pr-2">
            @include('guru-piket._siswa_belum_hadir', ['siswaBelumHadir' => $siswaBelumHadir])
          </div>
        </div>
      </div>

      {{-- BAGIAN JADWAL --}}
      <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4 sm:p-6">
          <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <h3 class="text-xl font-bold text-gray-800">Jadwal Pelajaran Lengkap</h3>
            <div class="w-full md:w-80">
              <input type="text" id="jadwal-search-input"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                placeholder="Cari Kelas, Mapel, Guru...">
            </div>
          </div>
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
              @forelse ($jadwals->keys() as $namaKelas)
                <button
                  class="tab-button whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm {{ $loop->first ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                  onclick="changeTab({{ $loop->index }})">{{ $namaKelas }}</button>
              @empty <p class="text-sm text-gray-500 py-3">Belum ada jadwal.</p>
              @endforelse
            </nav>
          </div>
          <div class="mt-6">
            @forelse ($jadwals as $namaKelas => $jadwalPerKelas)
              <div id="tab-content-{{ $loop->index }}" class="tab-content {{ $loop->first ? '' : 'hidden' }}">
                <div class="overflow-x-auto">
                  <table class="min-w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-50">
                      <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                          <th class="border border-gray-200 px-4 py-2">{{ $hari }}</th>
                        @endforeach
                      </tr>
                    </thead>
                    <tbody class="align-top">
                      <tr>
                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                          <td class="border border-gray-200 p-2 day-column align-top">
                            <div class="space-y-2">
                              @forelse ($jadwalPerKelas->where('hari', $hari) as $jadwal)
                                <div
                                  class="bg-indigo-50 rounded-lg p-3 text-sm schedule-item hover:bg-indigo-100 transition">
                                  <p class="font-bold text-indigo-800">{{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}
                                  </p>
                                  <p class="text-gray-600">{{ $jadwal->guru->nama_guru ?? 'N/A' }}</p>
                                  <p class="text-xs text-gray-500 font-mono mt-1">
                                    {{ date('H:i', strtotime($jadwal->jam_mulai)) }} -
                                    {{ date('H:i', strtotime($jadwal->jam_selesai)) }}</p>
                                </div>
                              @empty <div class="text-center text-xs text-gray-400 p-4 empty-day-message">-- Kosong --
                                </div>
                              @endforelse
                            </div>
                          </td>
                        @endforeach
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              @empty
              @endforelse
            </div>
          </div>
        </div>

      </div>
    </div>
  @endsection

  @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
      #reader video {
        width: 100% !important;
        height: auto !important;
        object-fit: cover !important;
        border-radius: 8px;
      }
    </style>

    <script>
      const scanStatusContainer = document.getElementById('scan-status');
      let statusTimeout;
      let isScanning = true;
      let html5QrCode;
      let activeMode = 'camera';
      let scannerDebounceTimer;

      // --- 1. CAPTURE FOTO (Bekerja di kedua mode) ---
      function capturePhoto() {
        let imageBase64 = null;
        const videoElement = document.querySelector('#reader video');

        if (videoElement && videoElement.readyState === 4) {
          const canvas = document.createElement('canvas');
          canvas.width = videoElement.videoWidth;
          canvas.height = videoElement.videoHeight;
          const context = canvas.getContext('2d');
          context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
          imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
        }
        return imageBase64;
      }

      // --- 2. GANTI MODE (TAMPILAN) ---
      function switchMode(mode) {
        activeMode = mode;
        const btnCamera = document.getElementById('btn-mode-camera');
        const btnScanner = document.getElementById('btn-mode-scanner');
        const scannerContainer = document.getElementById('mode-scanner-container');
        const scannerInput = document.getElementById('scanner_input');
        const overlayText = document.getElementById('camera-overlay-text');

        const activeClass = ['bg-white', 'text-indigo-600', 'shadow-sm'];
        const inactiveClass = ['text-gray-500', 'hover:text-gray-700'];

        if (mode === 'camera') {
          // UI Button
          btnCamera.classList.add(...activeClass);
          btnCamera.classList.remove(...inactiveClass);
          btnScanner.classList.remove(...activeClass);
          btnScanner.classList.add(...inactiveClass);

          // UI Content: Sembunyikan Input, Ubah teks overlay
          scannerContainer.classList.add('hidden');
          overlayText.textContent = "Arahkan QR ke Kamera";

        } else {
          // UI Button
          btnScanner.classList.add(...activeClass);
          btnScanner.classList.remove(...inactiveClass);
          btnCamera.classList.remove(...activeClass);
          btnCamera.classList.add(...inactiveClass);

          // UI Content: Tampilkan Input, Ubah teks overlay
          scannerContainer.classList.remove('hidden');
          overlayText.textContent = "Kamera Aktif (Preview)";

          // Fokus ke input
          setTimeout(() => {
            scannerInput.focus();
          }, 200);
        }
      }

      // --- 3. PROSES ABSENSI ---
      function processAbsensi(nis, imageBase64 = null) {
        if (!nis || nis.trim() === "") return;

        showStatusMessage('Memproses data...', 'success');

        axios.post('{{ route('guru.piket.record') }}', {
            siswa_id: nis,
            image: imageBase64,
            _token: "{{ csrf_token() }}"
          })
          .then(response => {
            showStatusMessage(response.data.message, 'success');

            let swalOptions = {
              title: 'Absensi Berhasil!',
              text: response.data.message,
              icon: 'success',
              timer: 500,
              showConfirmButton: false
            };

            if (imageBase64) {
              swalOptions.imageUrl = imageBase64;
              swalOptions.imageHeight = 200;
              swalOptions.imageAlt = 'Foto Siswa';
              delete swalOptions.icon;
            }

            Swal.fire(swalOptions);

            axios.get('{{ route('guru.piket.dashboard.data') }}').then(res => renderDashboard(res.data));

            if (activeMode === 'scanner') {
              const input = document.getElementById('scanner_input');
              input.value = '';
              input.focus();
            }
          })
          .catch(error => {
            let msg = error.response ? error.response.data.message : 'Terjadi kesalahan sistem';
            showStatusMessage(msg, 'error');

            Swal.fire({
              title: 'Gagal!',
              text: msg,
              icon: 'error',
              timer: 1500,
              showConfirmButton: false
            });

            if (activeMode === 'scanner') {
              const input = document.getElementById('scanner_input');
              input.value = '';
              input.focus();
            }
          })
          .finally(() => {
            if (activeMode === 'camera' && html5QrCode) {
              setTimeout(() => {
                isScanning = true;
                html5QrCode.resume();
              }, 2000);
            }
          });
      }

      // --- 4. LOGIKA SCANNER GUN (AUTO SUBMIT) ---
      const scannerInput = document.getElementById('scanner_input');
      if (scannerInput) {
        scannerInput.addEventListener('input', function() {
          clearTimeout(scannerDebounceTimer);
          scannerDebounceTimer = setTimeout(() => {
            const nis = this.value.trim();
            if (nis.length > 0) {
              // Ambil foto dari kamera yang sedang menyala di bawah
              let photo = capturePhoto();
              processAbsensi(nis, photo);
              this.value = '';
            }
          }, 300); // Tunggu 300ms setelah scan selesai
        });

        // Handle Enter key juga (backup)
        scannerInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(scannerDebounceTimer);
            const nis = this.value.trim();
            if (nis.length > 0) {
              let photo = capturePhoto();
              processAbsensi(nis, photo);
              this.value = '';
            }
          }
        });

        // Fokus jika area diklik
        document.getElementById('mode-scanner-container').addEventListener('click', function() {
          scannerInput.focus();
        });
      }

      // --- 5. LOGIKA KAMERA WEB ---
      function onScanSuccess(decodedText, decodedResult) {
        // Hanya proses scan kamera JIKA mode kamera aktif
        // Jika mode scanner, kita abaikan scan kamera (biar tidak double input dari kamera + alat)
        if (!isScanning || activeMode === 'scanner') return;

        isScanning = false;
        html5QrCode.pause();

        let photo = capturePhoto();
        processAbsensi(decodedText, photo);
      }

      function startCamera() {
        if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
        const config = {
          fps: 10,
          qrbox: {
            width: 200,
            height: 200
          },
          aspectRatio: 1.0
        };

        Html5Qrcode.getCameras().then(devices => {
          if (devices && devices.length) {
            html5QrCode.start(devices[0].id, config, onScanSuccess).catch(err => {
              console.log("Error start:", err);
            });
          } else {
            showStatusMessage("Kamera tidak ditemukan.", "error");
          }
        }).catch(err => {
          console.log("Error cam:", err);
        });
      }

      function showStatusMessage(message, type = 'success') {
        clearTimeout(statusTimeout);
        let color = type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        scanStatusContainer.innerHTML =
          `<div class="p-3 rounded-lg ${color} text-sm font-medium text-center">${message}</div>`;
        statusTimeout = setTimeout(() => {
          scanStatusContainer.innerHTML = '';
        }, 4000);
      }

      // --- INIT ---
      document.addEventListener('DOMContentLoaded', () => {
        startCamera(); // Kamera langsung nyala untuk kedua mode
      });

      // --- FUNGSI LAIN (Manual & Dashboard) ---
      function hadirkanManual(siswaId, siswaNama) {
        Swal.fire({
          title: 'Konfirmasi Kehadiran',
          html: `Anda akan menghadirkan siswa:<br><b>${siswaNama}</b><br><small>(Tanpa foto bukti)</small>`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#16a34a',
          cancelButtonColor: '#6b7280',
          confirmButtonText: 'Ya, Hadirkan!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            showStatusMessage(`Memproses ${siswaNama}...`, 'success');
            axios.post('{{ route('guru.piket.hadirkan.manual') }}', {
                siswa_id: siswaId,
                _token: "{{ csrf_token() }}"
              })
              .then(response => {
                showStatusMessage(response.data.message, 'success');
                const siswaRow = document.getElementById(`belum-hadir-${siswaId}`);
                if (siswaRow) {
                  siswaRow.classList.add('opacity-0', 'scale-95');
                  setTimeout(() => {
                    axios.get('{{ route('guru.piket.dashboard.data') }}').then(res => renderDashboard(res.data));
                  }, 300);
                }
              }).catch(error => {
                showStatusMessage(error.response.data.message, 'error');
              });
          }
        });
      }

      function renderDashboard(data) {
        document.getElementById('statistik-hadir').textContent = data.jumlahHadir;
        document.getElementById('statistik-belum-hadir').textContent = data.jumlahBelumHadir;

        let aktivitasHtml = '';
        if (data.aktivitasTerbaru.length > 0) {
          data.aktivitasTerbaru.forEach(absensi => {
            const waktu = new Date(`1970-01-01T${absensi.waktu_masuk}`).toLocaleTimeString('id-ID', {
              hour: '2-digit',
              minute: '2-digit'
            });
            let fotoHtml =
              `<span class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center text-sm font-bold text-green-700">${absensi.siswa.nama_siswa.substring(0, 1)}</span>`;
            if (absensi.foto_masuk) {
              fotoHtml =
                `<div class="h-8 w-8 rounded-full overflow-hidden border border-green-200 cursor-pointer" onclick="Swal.fire({imageUrl: '/storage/${absensi.foto_masuk}', showConfirmButton: false})"><img src="/storage/${absensi.foto_masuk}" class="w-full h-full object-cover"></div>`;
            }
            aktivitasHtml +=
              `<div class="flex items-center space-x-3">${fotoHtml}<div><p class="text-sm font-medium text-gray-900">${absensi.siswa.nama_siswa}</p><p class="text-xs text-gray-500">${absensi.siswa.kelas.nama_kelas} | ${waktu}</p></div></div>`;
          });
        } else {
          aktivitasHtml = `<p class="text-sm text-gray-500">Belum ada aktivitas.</p>`;
        }
        document.getElementById('aktivitas-terbaru').innerHTML = aktivitasHtml;

        let belumHadirHtml = '';
        if (data.siswaBelumHadir.length > 0) {
          data.siswaBelumHadir.forEach(siswa => {
            const escapedNamaSiswa = siswa.nama_siswa.replace(/'/g, "\\'");
            belumHadirHtml +=
              `<div id="belum-hadir-${siswa.siswa_id}" class="flex items-center justify-between p-2 rounded-md hover:bg-gray-50 transition-all duration-300"><div class="flex items-center space-x-3"><span class="h-8 w-8 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-sm font-bold flex-shrink-0">${siswa.nama_siswa.substring(0, 1)}</span><div><p class="text-sm font-medium text-gray-900">${siswa.nama_siswa}</p><p class="text-xs text-gray-500">${siswa.kelas.nama_kelas}</p></div></div><button onclick="hadirkanManual('${siswa.siswa_id}', '${escapedNamaSiswa}')" class="px-2 py-1 bg-green-500 text-white text-xs font-semibold rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 transition duration-150">Hadirkan</button></div>`;
          });
        } else {
          belumHadirHtml =
            `<div class="text-center py-8"><p class="text-sm text-gray-500">Semua siswa sudah hadir. 👍</p></div>`;
        }
        document.getElementById('daftar-belum-hadir').innerHTML = belumHadirHtml;
      }

      const tabButtons = document.querySelectorAll('.tab-button');
      const tabContents = document.querySelectorAll('.tab-content');
      const searchInputTab = document.getElementById('jadwal-search-input');

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
        if (searchInputTab) {
          searchInputTab.value = '';
          searchInputTab.dispatchEvent(new Event('input'));
        }
      }
      if (searchInputTab) {
        searchInputTab.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          tabButtons.forEach(button => {
            button.classList.toggle('hidden', searchTerm.length > 0 && !button.textContent.toLowerCase().includes(
              searchTerm));
          });
          const activeTabContent = document.querySelector('.tab-content:not(.hidden)');
          if (!activeTabContent) return;
          const dayColumns = activeTabContent.querySelectorAll('.day-column');
          dayColumns.forEach(column => {
            const scheduleItems = column.querySelectorAll('.schedule-item');
            let hasVisibleItems = false;
            scheduleItems.forEach(item => {
              const isVisible = item.textContent.toLowerCase().includes(searchTerm);
              item.classList.toggle('hidden', !isVisible);
              if (isVisible) hasVisibleItems = true;
            });
            const emptyDayMessage = column.querySelector('.empty-day-message');
            if (emptyDayMessage) emptyDayMessage.classList.toggle('hidden', searchTerm.length > 0);
            const noResultsMessage = column.querySelector('.no-results-message');
            if (noResultsMessage) noResultsMessage.classList.toggle('hidden', hasVisibleItems || !searchTerm
              .length > 0);
          });
        });
      }
      document.addEventListener('DOMContentLoaded', () => {
        if (tabContents.length > 0) {
          tabContents.forEach((c, i) => {
            if (i !== 0) c.classList.add('hidden');
          });
        }
        if (tabButtons.length > 0) {
          tabButtons.forEach((b, i) => {
            if (i !== 0) {
              b.classList.remove('border-indigo-500', 'text-indigo-600');
              b.classList.add('border-transparent', 'text-gray-500');
            } else {
              b.classList.add('border-indigo-500', 'text-indigo-600');
              b.classList.remove('border-transparent', 'text-gray-500');
            }
          });
        }
      });
    </script>
  @endpush
