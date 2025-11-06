<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div
            class="w-full sm:max-w-4xl mt-6 bg-white shadow-md overflow-hidden sm:rounded-lg grid grid-cols-1 md:grid-cols-2">
            <div
                class="hidden md:flex flex-col items-center justify-center p-12 bg-gradient-to-br from-indigo-600 to-purple-600 text-white">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-white" />
                </a>
                <h2 class="mt-6 text-2xl font-bold text-center">Sistem Absensi Digital</h2>
                <p class="mt-2 text-sm text-indigo-100 text-center">Manajemen kehadiran siswa yang modern, cepat, dan
                    efisien.</p>
            </div>

            <div class="w-full px-6 py-12 sm:px-12">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
