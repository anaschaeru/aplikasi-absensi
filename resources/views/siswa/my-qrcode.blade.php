@extends('layouts.master')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Kartu Pelajar Digital
    </h2>
@endsection

@section('content')
    <div class="py-12 bg-slate-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col items-center">

            {{-- Tombol Cetak (Akan hilang saat dicetak) --}}
            <div class="mb-8 w-full max-w-2xl flex justify-end print-hidden">
                <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 transition-colors print-hidden">
                    Cetak Kartu
                </button>
            </div>

            <div id="student-card"
                class="bg-white w-full max-w-2xl rounded-xl shadow-lg border border-gray-200/80 overflow-hidden">

                <div class="p-8 grid grid-cols-3 gap-8">
                    @if ($siswa->foto_profil)
                        {{-- TAMPILAN JIKA ADA FOTO --}}
                        <div class="col-span-1">
                            <div class="aspect-w-3 aspect-h-4 rounded-lg overflow-hidden border border-slate-200">
                                <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="Foto Profil"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>

                        <div class="col-span-2 flex flex-col">
                            {{-- Bagian Atas: Informasi Utama --}}
                            <div class="flex-grow">
                                <p class="text-sm font-bold text-indigo-600 uppercase tracking-wider">Kartu Tanda Siswa</p>
                                <h1 class="text-4xl font-extrabold text-slate-800 mt-2 leading-tight">
                                    {{ $siswa->nama_siswa }}</h1>

                                <div class="mt-8 grid grid-cols-2 gap-x-6 gap-y-5">
                                    <div>
                                        <p class="text-xs text-slate-500 font-semibold uppercase">NIS</p>
                                        <p class="font-mono text-slate-900 text-lg">{{ $siswa->nis }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 font-semibold uppercase">Kelas</p>
                                        <p class="font-mono text-slate-900 text-lg">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Bagian Bawah: Tanggal & QR Code --}}
                            <div class="mt-6 flex items-end justify-between">
                                <div>
                                    <p class="text-xs text-slate-400 font-medium">Diterbitkan:
                                        {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                                </div>
                                <div class="p-1.5 border border-slate-200 rounded-md">
                                    {!! QrCode::size(100)->generate($siswa->siswa_id) !!}
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- TAMPILAN JIKA TIDAK ADA FOTO --}}
                        <div class="col-span-1 flex items-center justify-center">
                            <div class="p-2 border-2 border-slate-200 rounded-xl">
                                {!! QrCode::size(160)->generate($siswa->siswa_id) !!}
                            </div>
                        </div>

                        <div class="col-span-2 flex flex-col justify-center">
                            <p class="text-sm font-bold text-indigo-600 uppercase tracking-wider">Kartu Tanda Siswa</p>
                            <h1 class="text-5xl font-extrabold text-slate-800 mt-2 leading-tight">{{ $siswa->nama_siswa }}
                            </h1>
                            <div class="mt-10 grid grid-cols-2 gap-y-5 gap-x-6">
                                <div>
                                    <p class="text-sm text-slate-500 font-semibold uppercase">NIS</p>
                                    <p class="font-mono text-slate-900 text-2xl">{{ $siswa->nis }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500 font-semibold uppercase">Kelas</p>
                                    <p class="font-mono text-slate-900 text-2xl">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 font-medium mt-10">Diterbitkan:
                                {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                        </div>
                    @endif
                </div>
                <div class="bg-slate-800 p-4 text-center">
                    <p class="text-white text-sm font-semibold tracking-widest">SMA HARAPAN BANGSA</p>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* ATURAN UNTUK MENCETAK */
        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            /* Sembunyikan elemen umum seperti nav, header, dan tombol secara paksa */
            nav,
            header,
            .print-hidden {
                display: none !important;
            }

            /* Atur body agar bersih saat dicetak */
            body {
                background-color: white !important;
            }

            /* Sembunyikan semua elemen lain sebagai pengaman */
            body * {
                visibility: hidden;
            }

            /* Tampilkan KEMBALI HANYA kartu pelajar dan isinya */
            #student-card,
            #student-card * {
                visibility: visible;
            }

            /* Atur posisi dan ukuran kartu di atas kertas virtual */
            #student-card {
                position: absolute;
                left: 1cm;
                top: 1cm;
                width: 8.56cm;
                height: 5.398cm;
                box-shadow: none !important;
                border: 1px solid #ccc;
            }
        }
    </style>
@endpush
