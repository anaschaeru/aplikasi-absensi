<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Absensi Digital - Modern & Efisien</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-white text-gray-800">
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <x-application-logo class="w-8 h-8 fill-current text-indigo-600" />
                <span class="font-bold text-xl">Absensi Digital</span>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="#fitur" class="text-gray-600 hover:text-indigo-600">Fitur</a>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600">Masuk</a>
                <a href="{{ route('register') }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">Daftar
                    Sekarang</a>
            </div>
            <button id="mobile-menu-button" class="md:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
            </button>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden px-6 pt-2 pb-4 space-y-2">
            <a href="#fitur" class="block text-gray-600 hover:text-indigo-600">Fitur</a>
            <a href="{{ route('login') }}" class="block text-gray-600 hover:text-indigo-600">Masuk</a>
            <a href="{{ route('register') }}"
                class="block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">Daftar
                Sekarang</a>
        </div>
    </header>

    <main>
        <section class="bg-gray-50">
            <div class="container mx-auto px-6 py-20 md:py-32 grid md:grid-cols-2 gap-12 items-center">
                <div class="text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                        Transformasi Absensi Sekolah ke Era Digital
                    </h1>
                    <p class="mt-4 text-lg text-gray-600">
                        Kelola kehadiran siswa dengan cepat, akurat, dan efisien menggunakan sistem absensi berbasis QR
                        Code. Ucapkan selamat tinggal pada metode manual!
                    </p>
                    <div class="mt-8">
                        <a href="{{ route('register') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-md transition duration-300 text-lg">
                            Mulai Sekarang
                        </a>
                    </div>
                </div>
                <div>

                </div>
            </div>
        </section>

        <section id="fitur" class="py-20">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-900">Fitur Unggulan Kami</h2>
                <p class="mt-2 text-gray-600">Semua yang Anda butuhkan untuk manajemen kehadiran yang lebih baik.</p>
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-center mb-4">
                            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h-1m-1 6v-1M4 12H3m1-6V4M7.757 7.757l-.707-.707M17.657 16.243l-.707.707M16.243 7.757l.707-.707M6.343 16.243l.707.707M12 21a9 9 0 100-18 9 9 0 000 18z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18M10 3v18"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold">Absensi QR Code</h3>
                        <p class="mt-2 text-gray-600">Siswa melakukan absensi dengan cepat hanya dengan memindai QR Code
                            unik mereka.</p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-center mb-4">
                            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold">Laporan Real-time</h3>
                        <p class="mt-2 text-gray-600">Admin dan guru dapat memantau rekapitulasi kehadiran secara
                            langsung dari dasbor.</p>
                    </div>
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-center mb-4">
                            <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold">Manajemen Terpusat</h3>
                        <p class="mt-2 text-gray-600">Kelola data siswa, guru, kelas, dan jadwal pelajaran dalam satu
                            platform terintegrasi.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-indigo-700 text-white">
            <div class="container mx-auto px-6 py-16 text-center">
                <h2 class="text-3xl font-bold">Siap Meningkatkan Efisiensi Sekolah Anda?</h2>
                <p class="mt-2">Bergabunglah sekarang dan rasakan kemudahan manajemen absensi digital.</p>
                <div class="mt-8">
                    <a href="{{ route('register') }}"
                        class="bg-white hover:bg-gray-200 text-indigo-700 font-bold py-3 px-8 rounded-md transition duration-300 text-lg">
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white">
        <div class="container mx-auto px-6 py-8 text-center">
            <p>&copy; {{ date('Y') }} Sistem Absensi Digital. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Script untuk mobile menu
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>

</body>

</html>
