@extends('layouts.master')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kartu Pelajar Digital
    </h2>
@endsection

@section('content')
    <div class="py-12 bg-gray-50 flex flex-col items-center justify-center min-h-screen">

        {{-- Tombol Cetak --}}
        <div class="mb-6 print-hidden">
            <button onclick="window.print()"
                class="px-5 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg shadow-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all">
                <i class="fas fa-print mr-2"></i> Cetak Kartu
            </button>
        </div>

        {{-- DESAIN KARTU PELAJAR (ID CARD STYLE) --}}
        <div id="student-card"
            class="bg-white w-[350px] h-[550px] rounded-2xl shadow-xl border border-gray-200 relative overflow-hidden flex flex-col items-center text-center p-0">

            {{-- Header Biru --}}
            <div class="w-full h-32 bg-indigo-600 relative">
                <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2">
                    <div class="w-24 h-24 rounded-full border-4 border-white bg-gray-200 overflow-hidden shadow-sm">
                        @if ($siswa->foto_profil)
                            <img src="{{ asset('storage/' . $siswa->foto_profil) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Informasi Siswa --}}
            <div class="mt-12 px-6 w-full">
                <h2 class="text-2xl font-bold text-gray-800 leading-tight">{{ $siswa->nama_siswa }}</h2>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mt-1">Siswa</p>

                <div class="mt-6 space-y-3 w-full text-left">
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-xs text-gray-400 font-semibold uppercase">NIS</span>
                        <span class="text-sm font-bold text-gray-700 font-mono">{{ $siswa->nis }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-2">
                        <span class="text-xs text-gray-400 font-semibold uppercase">Kelas</span>
                        <span class="text-sm font-bold text-gray-700">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- QR Code Area --}}
            <div class="mt-auto mb-8 flex flex-col items-center">
                <div class="p-2 border border-gray-200 rounded-lg bg-white shadow-sm">
                    {!! QrCode::size(120)->generate($siswa->nis) !!}
                </div>
                <p class="text-[10px] text-gray-400 mt-2 tracking-wider">SCAN UNTUK ABSENSI</p>
            </div>

            {{-- Footer Sekolah --}}
            <div class="w-full py-3 bg-gray-50 border-t border-gray-100">
                <p class="text-xs font-bold text-indigo-600 tracking-widest">SMA HARAPAN BANGSA</p>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        /* CSS Khusus Print agar Ukuran Pas ID Card (8.5cm x 5.4cm - Vertikal) */
        @media print {
            body * {
                visibility: hidden;
            }

            .print-hidden,
            nav,
            header {
                display: none !important;
            }

            body {
                background: white;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }

            #student-card,
            #student-card * {
                visibility: visible;
            }

            #student-card {
                position: relative;
                width: 54mm !important;
                /* Lebar ID Card Standar */
                height: 86mm !important;
                /* Tinggi ID Card Standar */
                box-shadow: none;
                border: 1px solid #ddd;
                border-radius: 8px;
                /* Radius print sedikit lebih tajam */
                left: auto;
                top: auto;
                transform: scale(1);
                /* Pastikan skala 100% */
            }

            /* Penyesuaian elemen dalam saat mode print ID card kecil */
            #student-card h2 {
                font-size: 14pt;
            }

            #student-card .w-24 {
                width: 60px;
                height: 60px;
            }

            /* Foto lebih kecil */
            #student-card .h-32 {
                height: 60px;
            }

            /* Header lebih pendek */
            #student-card .mt-12 {
                margin-top: 35px;
            }

            /* Jarak foto ke teks */
            #student-card svg {
                width: 80px;
                height: 80px;
            }

            /* QR Code */
        }
    </style>
@endpush
