<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  {{-- Stylesheets DataTables (LOKAL) --}}
  <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.tailwindcss.css') }}">

  {{-- Vite Assets --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
  <div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    @hasSection('header')
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          @yield('header')
        </div>
      </header>
    @endif

    <main>
      @yield('content')
    </main>
  </div>

  {{-- JAVASCRIPTS (LOKAL) --}}
  <script src="{{ asset('vendor/datatables/jquery-3.7.1.js') }}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.js') }}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.tailwindcss.js') }}"></script>
  <script src="{{ asset('vendor/datatables/dataTables.responsive.js') }}"></script>

  {{-- Pustaka lainnya bisa tetap dari CDN jika tidak bermasalah --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  @stack('scripts')
</body>
{{-- Tombol Fullscreen Mengambang di Pojok Kanan Bawah --}}
<button id="btn-fullscreen" onclick="toggleFullScreen()"
  class="fixed bottom-6 right-6 p-3 bg-slate-800 text-white rounded-full shadow-xl hover:bg-slate-700 hover:scale-110 transition-all duration-300 z-50 focus:outline-none"
  title="Mode Layar Penuh">
  {{-- Ikon Expand --}}
  <svg id="icon-expand" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
  </svg>
  {{-- Ikon Compress (Sembunyi secara default) --}}
  <svg id="icon-compress" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M4 14h6m0 0v6m0-6l-7 7m17-11h-6m0 0V4m0 6l7-7M4 10h6m0 0V4m0 6l-7-7m17 11h-6m0 0v6m0-6l7 7"></path>
  </svg>
</button>

<script>
  function toggleFullScreen() {
    const elem = document.documentElement;
    const iconExpand = document.getElementById('icon-expand');
    const iconCompress = document.getElementById('icon-compress');

    if (!document.fullscreenElement) {
      // Masuk Fullscreen
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.webkitRequestFullscreen) {
        /* Safari */
        elem.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) {
        /* IE11 */
        elem.msRequestFullscreen();
      }
      iconExpand.classList.add('hidden');
      iconCompress.classList.remove('hidden');
    } else {
      // Keluar Fullscreen
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        /* Safari */
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        /* IE11 */
        document.msExitFullscreen();
      }
      iconExpand.classList.remove('hidden');
      iconCompress.classList.add('hidden');
    }
  }

  // Mendeteksi jika user menekan tombol 'Esc' di keyboard untuk keluar fullscreen
  document.addEventListener('fullscreenchange', (event) => {
    const iconExpand = document.getElementById('icon-expand');
    const iconCompress = document.getElementById('icon-compress');
    if (!document.fullscreenElement) {
      iconExpand.classList.remove('hidden');
      iconCompress.classList.add('hidden');
    }
  });
</script>

</html>
