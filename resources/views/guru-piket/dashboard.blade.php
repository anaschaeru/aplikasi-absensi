@extends('layouts.master')

{{-- @section('header')
  <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-tight">
    Dasbor Guru Piket
  </h2>
@endsection --}}


@section('content')
  <div class="py-5 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      {{-- STATISTIK HARI INI --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card Total --}}
        <div
          class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 flex justify-between items-center">
          <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Siswa</h3>
            <p class="mt-1 text-4xl font-extrabold text-gray-800">{{ $totalSiswa }}</p>
          </div>
          <div class="p-3 bg-blue-50 rounded-lg text-blue-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
              </path>
            </svg>
          </div>
        </div>
        {{-- Card Hadir --}}
        <div
          class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 flex justify-between items-center">
          <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sudah Hadir</h3>
            <p id="statistik-hadir" class="mt-1 text-4xl font-extrabold text-emerald-500">{{ $jumlahHadir }}</p>
          </div>
          <div class="p-3 bg-emerald-50 rounded-lg text-emerald-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
        {{-- Card Belum Hadir --}}
        <div
          class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 flex justify-between items-center">
          <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Belum Hadir</h3>
            <p id="statistik-belum-hadir" class="mt-1 text-4xl font-extrabold text-rose-500">{{ $jumlahBelumHadir }}</p>
          </div>
          <div class="p-3 bg-rose-50 rounded-lg text-rose-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      {{-- GRID UTAMA 3 KOLOM --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom 1: Daftar Siswa Belum Hadir --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col h-[38rem]">
          <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
            <h3 id="judul-belum-hadir" class="font-bold text-gray-800 text-lg">Belum Hadir</h3>
            <span
              class="bg-rose-100 text-rose-600 py-1 px-3 rounded-full text-xs font-bold">{{ $siswaBelumHadir->count() }}
              Siswa</span>
          </div>
          <div id="daftar-belum-hadir" class="overflow-y-auto custom-scrollbar flex-1 space-y-2 pr-2">
            @include('guru-piket._siswa_belum_hadir', ['siswaBelumHadir' => $siswaBelumHadir])
          </div>
        </div>

        {{-- Kolom 2: Area Absensi (Scanner & Kamera) --}}
        <div
          class="lg:col-span-1 bg-white rounded-xl shadow-md border-t-4 border-indigo-500 flex flex-col h-[38rem] relative overflow-hidden">
          <div class="p-4 flex-1 flex flex-col">
            <h3 class="font-extrabold text-xl mb-2 text-center text-gray-800 tracking-tight">Panel Absensi</h3>

            {{-- INPUT ALAT SCANNER (Tidak lagi flex-1 agar tingginya pas) --}}
            <div id="mode-scanner-container" class="hidden flex-col flex-none mb-2">
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="h-6 w-6 text-indigo-400 group-focus-within:text-indigo-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                    </path>
                  </svg>
                </div>
                <input type="text" id="scanner_input"
                  class="w-full pl-12 pr-4 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl shadow-sm focus:bg-white focus:ring-0 focus:border-indigo-500 text-base font-mono tracking-widest transition-all"
                  placeholder="Klik & Scan Barcode..." autocomplete="off">
              </div>
            </div>

            {{-- PREVIEW KAMERA (Selalu Tampil, tapi teks berubah) --}}
            <div id="mode-camera-container"
              class="transition-all duration-300 flex-1 flex items-center justify-center mb-2 mt-2">
              {{-- UBAH DISINI: Mengganti max-w-[15rem] menjadi max-w-[20rem] atau max-w-sm --}}
              <div class="w-full max-w-[22rem] mx-auto relative group">
                {{-- Video Element dengan Frame Modern --}}
                <div class="p-1.5 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-2xl shadow-lg">
                  {{-- Frame kamera tetap persegi (aspect-square) --}}
                  <div id="reader" class="w-full aspect-square bg-black rounded-xl overflow-hidden"></div>
                </div>

                {{-- Posisi teks disesuaikan agar tidak menabrak bingkai bawah --}}
                <div class="absolute -bottom-5 left-0 right-0 text-center z-10">
                  <span id="camera-overlay-text"
                    class="inline-block px-4 py-1.5 bg-gray-900/80 text-white text-xs font-bold rounded-full backdrop-blur-md shadow-sm border border-gray-700/50">
                    Mode Kamera Aktif
                  </span>
                </div>
              </div>
            </div>

            <div id="scan-status" class="h-8 flex items-center justify-center my-2"></div>

            {{-- TAB SWITCHER MODERN (Anchor di bawah) --}}
            <div class="flex p-1 bg-gray-100 mb-4 rounded-lg shadow-inner mt-auto">
              <button onclick="switchMode('camera')" id="btn-mode-camera"
                class="w-1/2 py-2 px-4 rounded-md text-sm font-bold transition-all bg-white text-indigo-600 shadow-sm ring-1 ring-black/5">
                Kamera
              </button>
              <button onclick="switchMode('scanner')" id="btn-mode-scanner"
                class="w-1/2 py-2 px-4 rounded-md text-sm font-medium text-gray-500 hover:text-gray-800 transition-all">
                Alat Scanner
              </button>
            </div>

          </div>
        </div>

        {{-- Kolom 3: Aktivitas Terbaru --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col h-[38rem]">
          <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-800 text-lg">Aktivitas Terbaru</h3>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="overflow-y-auto custom-scrollbar flex-1 pr-2">
            <div id="aktivitas-terbaru" class="space-y-0.5">
              @include('guru-piket._aktivitas_terbaru', [
                  'aktivitasTerbaru' => $aktivitasTerbaru,
              ])
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- Library QR & Axios --}}
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  {{-- Library Deteksi Wajah --}}
  <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

  <style>
    /* Custom Scrollbar Tipis */
    .custom-scrollbar::-webkit-scrollbar {
      width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    #reader video {
      width: 100% !important;
      height: auto !important;
      object-fit: cover !important;
      border-radius: 8px;
    }

    /* --- TAMBAHAN CSS UNTUK TOAST JUMBO --- */
    .toast-besar {
      width: 400px !important;
      padding: 10px !important;
      border-radius: 16px !important;
    }

    .toast-judul-besar {
      font-size: 18px !important;
    }

    .toast-pesan-besar {
      font-size: 26px !important;
      font-weight: bold !important;
    }
  </style>

  <script>
    const scanStatusContainer = document.getElementById('scan-status');
    let statusTimeout;
    let isScanning = true;
    let html5QrCode;
    let activeMode = 'camera';
    let isFaceModelLoaded = false;

    // Konfigurasi Notifikasi Cepat
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      customClass: {
        popup: 'toast-besar',
        title: 'toast-judul-besar',
        htmlContainer: 'toast-pesan-besar'
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

    // --- 2. DETEKSI WAJAH ---
    async function checkFaceExist() {
      if (!isFaceModelLoaded) return false;

      const videoElement = document.querySelector('#reader video');
      if (!videoElement || videoElement.paused || videoElement.ended) return false;

      const options = new faceapi.TinyFaceDetectorOptions({
        inputSize: 160,
        scoreThreshold: 0.4
      });
      const detections = await faceapi.detectSingleFace(videoElement, options);
      return !!detections;
    }

    // --- 3. CAPTURE FOTO ---
    function capturePhoto() {
      let imageBase64 = null;
      const videoElement = document.querySelector('#reader video');
      if (videoElement && videoElement.readyState === 4) {
        const canvas = document.createElement('canvas');
        const MAX_WIDTH = 400;
        const scale = MAX_WIDTH / videoElement.videoWidth;
        canvas.width = MAX_WIDTH;
        canvas.height = videoElement.videoHeight * scale;

        const context = canvas.getContext('2d');
        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

        imageBase64 = canvas.toDataURL('image/jpeg', 0.6);
      }
      return imageBase64;
    }

    // --- 4. LOGIKA UTAMA ---
    async function validateAndSubmit(nis) {
      scanStatusContainer.innerHTML =
        `<div class="p-2 rounded-lg bg-blue-100 text-blue-700 text-sm font-bold text-center w-full">Memproses...</div>`;

      let hasFace = true;
      if (activeMode === 'scanner') {
        hasFace = await checkFaceExist();
      }

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
        return;
      }

      let photo = capturePhoto();
      processAbsensi(nis, photo);
    }

    // --- 5. PROSES KIRIM DATA ---
    function processAbsensi(nis, imageBase64 = null) {
      if (!nis || nis.trim() === "") return;

      let namaSiswa = nis;
      let kelasSiswa = "";
      const rowBelumHadir = document.getElementById(`belum-hadir-${nis}`);

      if (rowBelumHadir) {
        const pElements = rowBelumHadir.querySelectorAll('p');
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
          let infoSiswa = kelasSiswa ? `${namaSiswa} (${kelasSiswa})` : namaSiswa;
          let pesanBackend = response.data.message || 'Berhasil absen!';

          showStatusMessage(`Berhasil: ${infoSiswa}`, 'success');

          Toast.fire({
            icon: 'success',
            text: pesanBackend
          });

          updateDashboardLocally(nis);
        })
        .catch(error => {
          let msg = 'Terjadi kesalahan sistem';

          if (error.response) {
            if (error.response.status === 422 && error.response.data.errors) {
              const validationErrors = error.response.data.errors;
              msg = Object.values(validationErrors)[0][0];
            } else {
              msg = error.response.data.message || msg;
            }
          }

          showStatusMessage(msg, 'error');
          Toast.fire({
            icon: 'error',
            title: msg
          });
        })
        .finally(() => {
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

    // --- FUNGSI UPDATE UI LOKAL ---
    function updateDashboardLocally(nis) {
      const rowBelumHadir = document.getElementById(`belum-hadir-${nis}`);
      if (rowBelumHadir) {
        rowBelumHadir.remove();

        let statHadir = document.getElementById('statistik-hadir');
        let statBelum = document.getElementById('statistik-belum-hadir');

        statHadir.innerText = parseInt(statHadir.innerText) + 1;
        let sisa = parseInt(statBelum.innerText) - 1;
        statBelum.innerText = sisa < 0 ? 0 : sisa;
      }
    }

    // --- SINKRONISASI LATAR BELAKANG ---
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
      const cameraContainer = document.getElementById('mode-camera-container');
      const scannerInput = document.getElementById('scanner_input');
      const overlayText = document.getElementById('camera-overlay-text');

      const activeClass = ['bg-white', 'text-indigo-600', 'shadow-sm'];
      const inactiveClass = ['text-gray-500', 'hover:text-gray-700'];

      // KAMERA TETAP TAMPIL DI KEDUA MODE, hanya merubah status tampilan Input dan Teks
      if (mode === 'camera') {
        btnCamera.classList.add(...activeClass);
        btnCamera.classList.remove(...inactiveClass);
        btnScanner.classList.remove(...activeClass);
        btnScanner.classList.add(...inactiveClass);

        scannerContainer.classList.remove('flex');
        scannerContainer.classList.add('hidden');

        overlayText.textContent = "Arahkan QR ke Kamera";
        overlayText.className =
          "inline-block px-3 py-1.5 bg-gray-900/80 text-white text-xs font-medium rounded-full backdrop-blur-md shadow-sm border border-gray-700/50";
      } else {
        btnScanner.classList.add(...activeClass);
        btnScanner.classList.remove(...inactiveClass);
        btnCamera.classList.remove(...activeClass);
        btnCamera.classList.add(...inactiveClass);

        scannerContainer.classList.remove('hidden');
        scannerContainer.classList.add('flex');

        overlayText.textContent = "Deteksi Wajah Aktif";
        overlayText.className =
          "inline-block px-3 py-1.5 bg-indigo-600/90 text-white text-xs font-medium rounded-full backdrop-blur-md shadow-sm border border-indigo-400";
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
        `<div class="p-2 w-full rounded-lg ${color} text-sm font-medium text-center truncate">${message}</div>`;
      statusTimeout = setTimeout(() => scanStatusContainer.innerHTML = '', 3000);
    }

    document.addEventListener('DOMContentLoaded', () => {
      loadFaceModels();
      startCamera();
      switchMode('scanner');
    });

    // Render Dashboard (Sinkronisasi Latar Belakang)
    function renderDashboard(data) {
      // 1. Update Statistik Angka
      document.getElementById('statistik-hadir').textContent = data.jumlahHadir;
      document.getElementById('statistik-belum-hadir').textContent = data.jumlahBelumHadir;

      const judulBelumHadir = document.getElementById('judul-belum-hadir');
      if (judulBelumHadir) {
        judulBelumHadir.innerText = `Belum Hadir`;
      }

      // 2. Render Aktivitas Terbaru (Desain Compact)
      let aktivitasHtml = '';
      if (data.aktivitasTerbaru.length > 0) {
        data.aktivitasTerbaru.forEach(absensi => {
          // Format Waktu Masuk
          const waktuMasuk = new Date(`1970-01-01T${absensi.waktu_masuk}`).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
          });

          // Format Waktu Pulang (jika ada)
          let waktuPulangHtml = '';
          if (absensi.waktu_pulang) {
            const waktuPulang = new Date(`1970-01-01T${absensi.waktu_pulang}`).toLocaleTimeString('id-ID', {
              hour: '2-digit',
              minute: '2-digit'
            });
            waktuPulangHtml =
              `<span class="text-[10px] text-blue-700 font-bold bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 tracking-wide" title="Waktu Pulang">&uarr; ${waktuPulang}</span>`;
          }

          const inisial = absensi.siswa.nama_siswa.substring(0, 1).toUpperCase();
          const namaKelas = absensi.siswa.kelas ? absensi.siswa.kelas.nama_kelas : '-';

          // Set Avatar (Foto atau Inisial)
          let fotoHtml =
            `<span class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold flex-shrink-0 shadow-sm border border-emerald-200">${inisial}</span>`;
          if (absensi.foto_masuk) {
            fotoHtml =
              `<div class="h-8 w-8 rounded-full overflow-hidden border border-emerald-200 flex-shrink-0 cursor-pointer shadow-sm group-hover:border-emerald-400 transition-colors" onclick="Swal.fire({imageUrl: '/storage/${absensi.foto_masuk}', showConfirmButton: false, customClass: { popup: 'rounded-2xl' }})"><img src="/storage/${absensi.foto_masuk}" alt="Foto ${absensi.siswa.nama_siswa}" class="w-full h-full object-cover"></div>`;
          }

          aktivitasHtml += `
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-all duration-200 border border-transparent hover:border-gray-100 group">
              <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                ${fotoHtml}
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-semibold text-gray-800 truncate" title="${absensi.siswa.nama_siswa}">${absensi.siswa.nama_siswa}</p>
                  <p class="text-[11px] text-gray-500 truncate mt-0.5">${namaKelas}</p>
                </div>
              </div>
              <div class="flex flex-col items-end flex-shrink-0 ml-2 space-y-1">
                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 tracking-wide" title="Waktu Masuk">&darr; ${waktuMasuk}</span>
                ${waktuPulangHtml}
              </div>
            </div>`;
        });
      } else {
        aktivitasHtml = `
          <div class="flex flex-col items-center justify-center h-full py-8 opacity-70">
            <svg class="w-6 h-6 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-xs font-medium text-gray-500 text-center">Belum ada aktivitas.</p>
          </div>`;
      }
      document.getElementById('aktivitas-terbaru').innerHTML = aktivitasHtml;

      // 3. Render Siswa Belum Hadir
      let belumHadirHtml = '';
      if (data.siswaBelumHadir.length > 0) {
        data.siswaBelumHadir.forEach(siswa => {
          const escapedNamaSiswa = siswa.nama_siswa.replace(/'/g, "\\'");
          const inisial = siswa.nama_siswa.substring(0, 1).toUpperCase();
          const namaKelas = siswa.kelas ? siswa.kelas.nama_kelas : '-';

          belumHadirHtml += `
            <div id="belum-hadir-${siswa.siswa_id}" class="flex items-center justify-between w-full p-2.5 rounded-lg hover:bg-gray-100 transition-all duration-300 group">
              <div class="flex items-center space-x-3 min-w-0 flex-1 pr-3">
                <span class="h-9 w-9 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-extrabold flex-shrink-0 shadow-sm border border-rose-200">${inisial}</span>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-bold text-gray-800 truncate" title="${siswa.nama_siswa}">${siswa.nama_siswa}</p>
                  <p class="text-xs text-gray-500 truncate">${namaKelas}</p>
                </div>
              </div>
              <button onclick="hadirkanManual('${siswa.siswa_id}', '${escapedNamaSiswa}')" class="flex-shrink-0 px-3 py-1.5 bg-emerald-500 text-white text-xs font-bold rounded-md shadow-sm hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1 transition-all duration-200 transform hover:scale-105 opacity-90 group-hover:opacity-100">
                Hadirkan
              </button>
            </div>`;
        });
      } else {
        belumHadirHtml = `
          <div class="flex flex-col items-center justify-center h-full py-12 opacity-80">
            <div class="p-4 bg-emerald-50 rounded-full text-emerald-500 mb-3"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
            <p class="text-sm font-bold text-gray-600 text-center">Semua siswa sudah hadir.</p>
            <p class="text-xs text-gray-400 mt-1 text-center">Kerja bagus hari ini! 🎉</p>
          </div>`;
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
  </script>
@endpush
