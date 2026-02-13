<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistem Absensi Digital - Modern & Efisien</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 text-gray-800 font-sans">

  {{-- NAVBAR --}}
  <header class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
      {{-- LOGO --}}
      <div class="flex items-center space-x-2">
        <x-application-logo class="w-9 h-9 fill-current text-indigo-600" />
        <span class="font-bold text-xl tracking-tight text-gray-900">Absensi<span
            class="text-indigo-600">Digital</span></span>
      </div>

      {{-- MENU DESKTOP --}}
      <div class="hidden md:flex items-center space-x-6">
        <a href="#fitur" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Fitur</a>
        <a href="#tentang" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Tentang</a>

        <div class="h-6 w-px bg-gray-300 mx-2"></div> {{-- Divider --}}

        {{-- TOMBOL MODE PIKET --}}
        <a href="{{ route('piket.dashboard') }}"
          class="group inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-full font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-100 active:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 group-hover:scale-110 transition-transform"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Mode Piket
        </a>

        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-indigo-600 transition">
          Masuk
        </a>

        {{-- Uncomment jika pendaftaran dibuka umum --}}
        {{-- <a href="{{ route('register') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg text-sm transition shadow-lg shadow-indigo-500/30">
                    Daftar
                </a> --}}
      </div>

      {{-- TOMBOL MENU MOBILE --}}
      <button id="mobile-menu-button" class="md:hidden text-gray-600 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
      </button>
    </nav>

    {{-- MOBILE MENU --}}
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 px-6 pt-4 pb-6 space-y-3 shadow-lg">
      <a href="#fitur" class="block text-base font-medium text-gray-600 hover:text-indigo-600">Fitur</a>
      <a href="{{ route('piket.dashboard') }}" class="block text-base font-medium text-indigo-600">Mode Piket</a>
      <hr class="border-gray-100">
      <a href="{{ route('login') }}" class="block text-base font-medium text-gray-600 hover:text-indigo-600">Masuk</a>
    </div>
  </header>

  <main>
    {{-- HERO SECTION --}}
    <section class="relative bg-white overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-indigo-50 to-white -z-10"></div>

      <div class="container mx-auto px-6 py-16 md:py-24 grid md:grid-cols-2 gap-12 items-center">
        {{-- Teks Hero --}}
        <div class="text-center md:text-left animate-fade-in-up">
          <span
            class="inline-block py-1 px-3 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold tracking-wide mb-4">
            REVOLUSI PENDIDIKAN 4.0
          </span>
          <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
            Absensi Sekolah <br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Lebih Cerdas &
              Cepat</span>
          </h1>
          <p class="text-lg text-gray-600 mb-8 leading-relaxed">
            Tinggalkan cara manual yang memakan waktu. Gunakan sistem QR Code terintegrasi untuk memantau kehadiran
            siswa, guru, dan staf secara real-time dan akurat.
          </p>
          <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-4">
            <a href="{{ route('login') }}"
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-1">
              Mulai Sekarang
            </a>
            <a href="#fitur"
              class="bg-white border border-gray-200 text-gray-700 font-bold py-3 px-8 rounded-xl hover:bg-gray-50 transition">
              Pelajari Fitur
            </a>
          </div>

          {{-- Stats Kecil --}}
          <div
            class="mt-10 flex items-center justify-center md:justify-start space-x-6 text-sm text-gray-500 font-medium">
            <div class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg> Real-time Data</div>
            <div class="flex items-center"><svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg> Anti-Curang</div>
          </div>
        </div>

        {{-- Gambar Hero --}}
        <div class="relative">
          {{-- Blob Dekorasi --}}
          <div
            class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob">
          </div>
          <div
            class="absolute -bottom-8 -left-20 w-72 h-72 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000">
          </div>

          {{-- Foto Utama --}}
          <img
            src="https://images.unsplash.com/photo-1531482615713-2afd69097998?ixlib=rb-4.0.3&auto=format&fit=crop&w=1740&q=80"
            alt="Siswa Belajar Digital"
            class="relative z-10 rounded-2xl shadow-2xl border-4 border-white transform rotate-2 hover:rotate-0 transition duration-500">
        </div>
      </div>
    </section>

    {{-- FITUR SECTION --}}
    <section id="fitur" class="py-20 bg-white">
      <div class="container mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan</h2>
          <p class="text-gray-600">Platform kami dirancang untuk memudahkan administrasi sekolah dengan teknologi
            terkini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
          {{-- Kartu 1 --}}
          <div
            class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-2">
            <div
              class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center mb-6 group-hover:bg-indigo-600 transition-colors duration-300">
              <svg class="w-7 h-7 text-indigo-600 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4v1m6 11h-1m-1 6v-1M4 12H3m1-6V4M7.757 7.757l-.707-.707M17.657 16.243l-.707.707M16.243 7.757l.707-.707M6.343 16.243l.707.707M12 21a9 9 0 100-18 9 9 0 000 18z">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Scan QR Code Instan</h3>
            <p class="text-gray-600 leading-relaxed">Proses absensi super cepat. Siswa cukup menunjukkan kartu QR,
              sistem mencatat dalam hitungan detik.</p>
          </div>

          {{-- Kartu 2 --}}
          <div
            class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-2">
            <div
              class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors duration-300">
              <svg class="w-7 h-7 text-blue-600 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5z">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Monitoring Real-time</h3>
            <p class="text-gray-600 leading-relaxed">Data kehadiran langsung tersaji di Dashboard Admin dan Guru Piket
              tanpa perlu rekap manual.</p>
          </div>

          {{-- Kartu 3 --}}
          <div
            class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-2">
            <div
              class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors duration-300">
              <svg class="w-7 h-7 text-purple-600 group-hover:text-white transition-colors" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                </path>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Laporan Terintegrasi</h3>
            <p class="text-gray-600 leading-relaxed">Cetak laporan harian, bulanan, atau semesteran dengan mudah.
              Format Excel dan PDF siap unduh.</p>
          </div>
        </div>
      </div>
    </section>

    {{-- VISUAL SECTION (IMAGE LEFT) --}}
    <section class="py-20 bg-gray-50 overflow-hidden">
      <div class="container mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
        <div class="order-2 md:order-1 relative">
          <div
            class="absolute -top-4 -left-4 w-24 h-24 bg-indigo-200 rounded-full mix-blend-multiply filter blur-md opacity-70">
          </div>
          <img
            src="https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?ixlib=rb-4.0.3&auto=format&fit=crop&w=1740&q=80"
            alt="Manajemen Sekolah" class="relative rounded-2xl shadow-xl border-4 border-white">
        </div>
        <div class="order-1 md:order-2">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Kelola Sekolah Anda dari Mana Saja</h2>
          <p class="text-lg text-gray-600 mb-6">
            Sistem kami berbasis cloud, memungkinkan Kepala Sekolah, Guru, dan Admin memantau kedisiplinan siswa
            kapanpun dan dimanapun.
          </p>
          <ul class="space-y-4">
            <li class="flex items-start">
              <svg class="w-6 h-6 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span class="text-gray-700">Akses Multi-Device (Laptop, Tablet, HP)</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span class="text-gray-700">Notifikasi Keterlambatan</span>
            </li>
            <li class="flex items-start">
              <svg class="w-6 h-6 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span class="text-gray-700">Keamanan Data Terjamin</span>
            </li>
          </ul>
        </div>
      </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="py-20 bg-indigo-900 relative overflow-hidden">
      <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10">
      </div>
      <div class="container mx-auto px-6 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Siap Digitalisasi Sekolah Anda?</h2>
        <p class="text-indigo-200 text-lg max-w-2xl mx-auto mb-10">
          Bergabunglah dengan sekolah-sekolah modern lainnya yang telah beralih ke sistem absensi digital yang efisien.
        </p>
        <a href="{{ route('login') }}"
          class="inline-block bg-white text-indigo-900 font-bold text-lg py-4 px-10 rounded-full shadow-2xl hover:bg-gray-100 hover:scale-105 transition transform duration-200">
          Masuk Sekarang
        </a>
      </div>
    </section>
  </main>

  <footer class="bg-gray-900 text-gray-300 border-t border-gray-800">
    <div class="container mx-auto px-6 py-12 grid md:grid-cols-4 gap-8">
      <div class="col-span-1 md:col-span-2">
        <div class="flex items-center space-x-2 mb-4">
          <x-application-logo class="w-8 h-8 fill-current text-indigo-500" />
          <span class="font-bold text-xl text-white">Absensi Digital</span>
        </div>
        <p class="text-sm leading-relaxed max-w-xs">
          Solusi terbaik untuk manajemen kehadiran sekolah. Cepat, tepat, dan akurat dengan teknologi QR Code.
        </p>
      </div>
      <div>
        <h4 class="font-bold text-white mb-4">Tautan</h4>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:text-indigo-400">Beranda</a></li>
          <li><a href="#fitur" class="hover:text-indigo-400">Fitur</a></li>
          <li><a href="{{ route('piket.dashboard') }}" class="hover:text-indigo-400">Mode Piket</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold text-white mb-4">Hubungi Kami</h4>
        <p class="text-sm mb-2">support@absensidigital.sch.id</p>
        <p class="text-sm">+62 812 3456 7890</p>
      </div>
    </div>
    <div class="border-t border-gray-800">
      <div class="container mx-auto px-6 py-6 text-center text-sm">
        &copy; {{ date('Y') }} Sistem Absensi Digital. All rights reserved.
      </div>
    </div>
  </footer>

  {{-- SCRIPT SEDERHANA UNTUK MOBILE MENU --}}
  <script>
    const btn = document.getElementById('mobile-menu-button');
    const menu = document.getElementById('mobile-menu');

    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });
  </script>
</body>

</html>
