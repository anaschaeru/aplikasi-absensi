@extends('layouts.master')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pindai QR Code Absensi</h2>
@endsection
@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Area untuk menampilkan kamera --}}
                    <div id="reader" width="100%"></div>

                    {{-- Area untuk menampilkan hasil scan --}}
                    <div id="result" class="mt-4 text-center font-medium"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk QR Scanner --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const resultDiv = document.getElementById('result');

        function onScanSuccess(decodedText, decodedResult) {
            // Hentikan scanner agar tidak scan berulang kali
            html5QrcodeScanner.pause();

            // Tampilkan status "memproses"
            resultDiv.innerHTML = `<span class="text-yellow-600">Memproses...</span>`;

            // Kirim hasil scan (ID Siswa) ke server
            axios.post('{{ route('guru.piket.record') }}', {
                    siswa_id: decodedText
                })
                .then(function(response) {
                    // Tampilkan pesan sukses
                    resultDiv.innerHTML = `<span class="text-green-600">${response.data.message}</span>`;
                })
                .catch(function(error) {
                    // Tampilkan pesan error
                    resultDiv.innerHTML = `<span class="text-red-600">${error.response.data.message}</span>`;
                })
                .finally(function() {
                    // Lanjutkan scan setelah 2 detik
                    setTimeout(() => {
                        html5QrcodeScanner.resume();
                        resultDiv.innerHTML = '';
                    }, 2000);
                });
        }

        function onScanFailure(error) {
            // Tidak perlu melakukan apa-apa saat scan gagal
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            },
            /* verbose= */
            false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
@endsection
