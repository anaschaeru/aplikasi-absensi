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

        {{-- Kolom 1: Daftar Siswa Belum Hadir --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-sm">
          <h3 id="judul-belum-hadir" class="font-bold text-lg mb-4">Daftar Siswa Belum Hadir
            ({{ $siswaBelumHadir->count() }})</h3>
          <div id="daftar-belum-hadir" class="overflow-y-auto h-96 space-y-2 pr-2">
            @include('guru-piket._siswa_belum_hadir', ['siswaBelumHadir' => $siswaBelumHadir])
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

        {{-- Kolom 3: Aktivitas Terbaru --}}
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
      </div>

      {{-- BAGIAN JADWAL --}}
      {{-- <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
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

      </div> --}}
    </div>
  @endsection

  @push('scripts')
    {{-- Library QR & Axios --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    {{-- Library Deteksi Wajah --}}
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <style>
      #reader video {
        width: 100% !important;
        height: auto !important;
        object-fit: cover !important;
        border-radius: 8px;
      }

      /* --- TAMBAHAN CSS UNTUK TOAST JUMBO --- */
      .toast-besar {
        width: 400px !important;
        /* Lebar popup diperbesar lagi */
        padding: 10px !important;
        /* Jarak ruang dalam diperbesar lagi */
        border-radius: 16px !important;
      }

      .toast-judul-besar {
        font-size: 18px !important;
        /* Ukuran font judul (Nama/Kelas) SANGAT BESAR */
        /* line-height: 1.5 !important; */
      }

      .toast-pesan-besar {
        font-size: 26px !important;
        font-weight: bold !important;
        /* Ukuran font pesan SANGAT BESAR */
        /* margin-top: px !important; */
      }
    </style>

    <script>
      const scanStatusContainer = document.getElementById('scan-status');
      let statusTimeout;
      let isScanning = true;
      let html5QrCode;
      let activeMode = 'camera'; // Default mode
      let isFaceModelLoaded = false;

      // Konfigurasi Notifikasi Cepat (Toast) non-blocking JUMBO
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000, // Waktu saya naikkan sedikit lagi ke 3.5 detik
        timerProgressBar: true,
        customClass: {
          popup: 'toast-besar', // Memanggil CSS ukuran popup jumbo
          title: 'toast-judul-besar', // Memanggil CSS ukuran font judul jumbo
          htmlContainer: 'toast-pesan-besar' // Memanggil CSS ukuran font teks/pesan jumbo
        }
      });

      // --- 1. LOAD MODEL WAJAH ---
      async function loadFaceModels() {
        try {
          showStatusMessage("Sedang mengunduh model AI...", "warning");
          const inputScanner = document.getElementById('scanner_input');
          if (inputScanner) inputScanner.disabled = true;

          const modelUrl = "{{ asset('models') }}";
          await faceapi.nets.tinyFaceDetector.loadFromUri(modelUrl);

          isFaceModelLoaded = true;
          showStatusMessage("Sistem Siap. Silahkan Scan.", "success");

          if (inputScanner) {
            inputScanner.disabled = false;
            inputScanner.focus();
          }
        } catch (error) {
          showStatusMessage("Gagal memuat AI Wajah.", "error");
        }
      }

      // --- 2. DETEKSI WAJAH (DIPERCEPAT) ---
      async function checkFaceExist() {
        if (!isFaceModelLoaded) return false;

        const videoElement = document.querySelector('#reader video');
        if (!videoElement || videoElement.paused || videoElement.ended) return false;

        // OPTIMASI: inputSize dikecilkan agar proses CPU 3x lipat lebih cepat
        const options = new faceapi.TinyFaceDetectorOptions({
          inputSize: 160,
          scoreThreshold: 0.4
        });
        const detections = await faceapi.detectSingleFace(videoElement, options);
        return !!detections;
      }

      // --- 3. CAPTURE FOTO (DIKOMPRESI) ---
      function capturePhoto() {
        let imageBase64 = null;
        const videoElement = document.querySelector('#reader video');
        if (videoElement && videoElement.readyState === 4) {
          const canvas = document.createElement('canvas');

          // OPTIMASI: Compress gambar agar pengiriman jaringan sangat cepat
          const MAX_WIDTH = 400;
          const scale = MAX_WIDTH / videoElement.videoWidth;
          canvas.width = MAX_WIDTH;
          canvas.height = videoElement.videoHeight * scale;

          const context = canvas.getContext('2d');
          context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

          // Kompres kualitas jpeg ke 60%
          imageBase64 = canvas.toDataURL('image/jpeg', 0.6);
        }
        return imageBase64;
      }

      // --- 4. LOGIKA UTAMA ---
      async function validateAndSubmit(nis) {
        // Tampilkan indikator instan
        scanStatusContainer.innerHTML =
          `<div class="p-3 rounded-lg bg-blue-100 text-blue-700 text-sm font-bold text-center">Memproses...</div>`;

        const hasFace = await checkFaceExist();
        const input = document.getElementById('scanner_input');

        if (!hasFace) {
          showStatusMessage('Wajah tidak terdeteksi!', 'error');
          Toast.fire({
            icon: 'warning',
            title: 'Wajah tidak terdeteksi!'
          });
          if (input) {
            input.value = '';
            input.focus();
          }

          // Jika gagal, langsung aktifkan scanner lagi
          if (activeMode === 'camera' && html5QrCode) {
            setTimeout(() => {
              isScanning = true;
              html5QrCode.resume();
            }, 500);
          }
          return;
        }

        let photo = capturePhoto();
        processAbsensi(nis, photo);
      }

      // --- 5. PROSES KIRIM DATA (TANPA RELOAD DASHBOARD BERAT) ---
      function processAbsensi(nis, imageBase64 = null) {
        if (!nis || nis.trim() === "") return;

        // AMBIL DATA NAMA DAN KELAS DARI DOM (Sebelum Dihapus)
        let namaSiswa = nis;
        let kelasSiswa = "";
        const rowBelumHadir = document.getElementById(`belum-hadir-${nis}`);

        if (rowBelumHadir) {
          const pElements = rowBelumHadir.querySelectorAll('p');
          // Element pertama biasanya berisi Nama, element kedua berisi Kelas
          if (pElements.length >= 2) {
            namaSiswa = pElements[0].innerText;
            kelasSiswa = pElements[1].innerText;
          }
        }

        axios.post('{{ route('piket.record') }}', {
            siswa_id: nis,
            image: imageBase64,
            _token: "{{ csrf_token() }}"
          }, {
            headers: {
              'X-Scanner-Secret': 'absen-smkn4tng-aman'
            }
          })
          .then(response => {
            // FORMAT PESAN SUKSES
            let infoSiswa = kelasSiswa ? `${namaSiswa} (${kelasSiswa})` : namaSiswa;
            let pesanBackend = response.data.message || 'Berhasil absen!';

            showStatusMessage(`Berhasil: ${infoSiswa}`, 'success');

            // Tampilkan SweetAlert Toast dengan Nama dan Kelas
            Toast.fire({
              icon: 'success',
              text: pesanBackend
            });

            // OPTIMASI: Update UI langsung secara lokal (Tanpa narik data AJAX server yang berat)
            updateDashboardLocally(nis);
          })
          .catch(error => {
            let msg = error.response ? error.response.data.message : 'Terjadi kesalahan sistem';
            showStatusMessage(msg, 'error');
            Toast.fire({
              icon: 'error',
              title: msg
            });
          })
          .finally(() => {
            // OPTIMASI JEDA: Kurangi jeda kamera jadi sangat cepat (300ms) agar siswa tidak antri
            if (activeMode === 'camera' && html5QrCode) {
              setTimeout(() => {
                isScanning = true;
                html5QrCode.resume();
              }, 300);
            }

            if (activeMode === 'scanner') {
              const input = document.getElementById('scanner_input');
              if (input) {
                input.value = '';
                input.focus();
              }
            }
          });
      }

      // --- FUNGSI UPDATE UI LOKAL (SUPER CEPAT) ---
      function updateDashboardLocally(nis) {
        // Hapus siswa dari list "Belum Hadir"
        const rowBelumHadir = document.getElementById(`belum-hadir-${nis}`);
        if (rowBelumHadir) {
          rowBelumHadir.remove();

          // Ubah angka statistik secara instan
          let statHadir = document.getElementById('statistik-hadir');
          let statBelum = document.getElementById('statistik-belum-hadir');

          statHadir.innerText = parseInt(statHadir.innerText) + 1;
          let sisa = parseInt(statBelum.innerText) - 1;
          statBelum.innerText = sisa < 0 ? 0 : sisa;
        }

        // Perbarui angka di judul "Daftar Siswa Belum Hadir"
        const judulBelumHadir = document.getElementById('judul-belum-hadir');
        if (judulBelumHadir && judulBelumHadir.innerText.includes('(')) {
          let sisa = document.getElementById('statistik-belum-hadir').innerText;
          judulBelumHadir.innerText = `Daftar Siswa Belum Hadir (${sisa})`;
        }
      }

      // --- SINKRONISASI LATAR BELAKANG ---
      // Minta server data baru tiap 30 detik agar PC tidak lag, tapi dasbor tetap tersinkronisasi
      setInterval(() => {
        axios.get('{{ route('piket.dashboard.data') }}').then(res => renderDashboard(res.data));
      }, 30000);

      // --- 6. EVENT LISTENER SCANNER GUN ---
      const scannerInput = document.getElementById('scanner_input');
      if (scannerInput) {
        scannerInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            const nis = this.value.trim();
            if (nis.length > 0) {
              this.value = '';
              validateAndSubmit(nis);
            }
          }
        });

        document.getElementById('mode-scanner-container').addEventListener('click', () => scannerInput.focus());
        document.addEventListener('click', (e) => {
          if (activeMode === 'scanner' && e.target.id !== 'scanner_input' && !e.target.closest('button')) {
            scannerInput.focus();
          }
        });
      }

      // --- 7. FUNGSI BAWAAN LAINNYA ---
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
          btnCamera.classList.add(...activeClass);
          btnCamera.classList.remove(...inactiveClass);
          btnScanner.classList.remove(...activeClass);
          btnScanner.classList.add(...inactiveClass);
          scannerContainer.classList.add('hidden');
          overlayText.textContent = "Arahkan QR ke Kamera";
        } else {
          btnScanner.classList.add(...activeClass);
          btnScanner.classList.remove(...inactiveClass);
          btnCamera.classList.remove(...activeClass);
          btnCamera.classList.add(...inactiveClass);
          scannerContainer.classList.remove('hidden');
          overlayText.textContent = "Kamera Aktif (Preview)";
          setTimeout(() => scannerInput.focus(), 200);
        }
      }

      function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning || activeMode === 'scanner') return;
        isScanning = false;
        html5QrCode.pause();
        validateAndSubmit(decodedText);
      }

      function startCamera() {
        if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
        const config = {
          fps: 15,
          qrbox: {
            width: 250,
            height: 250
          },
          aspectRatio: 1.0
        };
        Html5Qrcode.getCameras().then(devices => {
          if (devices && devices.length) {
            html5QrCode.start(devices[0].id, config, onScanSuccess).catch(err => console.log(err));
          } else {
            showStatusMessage("Kamera tidak ditemukan.", "error");
          }
        }).catch(err => console.log(err));
      }

      function showStatusMessage(message, type = 'success') {
        clearTimeout(statusTimeout);
        let color = type === 'success' ? 'bg-green-100 text-green-700' : (type === 'warning' ?
          'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
        scanStatusContainer.innerHTML =
          `<div class="p-3 rounded-lg ${color} text-sm font-medium text-center">${message}</div>`;
        statusTimeout = setTimeout(() => scanStatusContainer.innerHTML = '', 3000);
      }

      document.addEventListener('DOMContentLoaded', () => {
        loadFaceModels();
        startCamera();
        switchMode('scanner');
      });

      // Render Dashboard (Dipanggil saat sinkronisasi 30 detik atau klik tombol hadir manual)
      function renderDashboard(data) {
        document.getElementById('statistik-hadir').textContent = data.jumlahHadir;
        document.getElementById('statistik-belum-hadir').textContent = data.jumlahBelumHadir;

        const judulBelumHadir = document.getElementById('judul-belum-hadir');
        if (judulBelumHadir) {
          judulBelumHadir.innerText = `Daftar Siswa Belum Hadir (${data.siswaBelumHadir.length})`;
        }

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
            axios.post('{{ route('piket.hadirkan.manual') }}', {
                siswa_id: siswaId,
                _token: "{{ csrf_token() }}"
              }, {
                headers: {
                  'X-Scanner-Secret': 'absen-smkn4tng-aman'
                }
              })
              .then(response => {
                showStatusMessage(response.data.message, 'success');
                const siswaRow = document.getElementById(`belum-hadir-${siswaId}`);
                if (siswaRow) {
                  siswaRow.classList.add('opacity-0', 'scale-95');
                  setTimeout(() => {
                    axios.get('{{ route('piket.dashboard.data') }}').then(res => renderDashboard(res.data));
                  }, 300);
                }
              }).catch(error => {
                showStatusMessage(error.response.data.message, 'error');
              });
          }
        });
      }

      // --- LOGIKA TAB JADWAL PELAJARAN ---
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
