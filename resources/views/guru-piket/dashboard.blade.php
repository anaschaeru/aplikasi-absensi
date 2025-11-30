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

                {{-- Kolom 2: Pindai QR Code Absensi --}}
                <div class="lg:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-4 text-center">Pindai QR Code Absensi</h3>
                        <div class="max-w-xs mx-auto">
                            <div id="reader" class="w-full aspect-square"></div>
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

            {{-- BAGIAN BARU: JADWAL PELAJARAN LENGKAP --}}
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
                                    onclick="changeTab({{ $loop->index }})">
                                    {{ $namaKelas }}
                                </button>
                            @empty
                                <p class="text-sm text-gray-500 py-3">Belum ada jadwal pelajaran yang dibuat.</p>
                            @endforelse
                        </nav>
                    </div>

                    <div class="mt-6">
                        @forelse ($jadwals as $namaKelas => $jadwalPerKelas)
                            <div id="tab-content-{{ $loop->index }}"
                                class="tab-content {{ $loop->first ? '' : 'hidden' }}">
                                @php
                                    $jadwalPerHari = $jadwalPerKelas->groupBy('hari');
                                    $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                @endphp

                                <div class="overflow-x-auto">
                                    <table class="min-w-full border-collapse border border-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                                @foreach ($urutanHari as $hari)
                                                    <th class="border border-gray-200 px-4 py-2">{{ $hari }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="align-top">
                                            <tr>
                                                @foreach ($urutanHari as $hari)
                                                    <td class="border border-gray-200 p-2 day-column align-top">
                                                        <div class="space-y-2">
                                                            @forelse ($jadwalPerHari[$hari] ?? [] as $jadwal)
                                                                <div
                                                                    class="bg-indigo-50 rounded-lg p-3 text-sm schedule-item hover:bg-indigo-100 transition">
                                                                    <p class="font-bold text-indigo-800">
                                                                        {{ $jadwal->mataPelajaran->nama_mapel ?? 'N/A' }}
                                                                    </p>
                                                                    <p class="text-gray-600">
                                                                        {{ $jadwal->guru->nama_guru ?? 'N/A' }}</p>
                                                                    <p class="text-xs text-gray-500 font-mono mt-1">
                                                                        {{ date('H:i', strtotime($jadwal->jam_mulai)) }} -
                                                                        {{ date('H:i', strtotime($jadwal->jam_selesai)) }}
                                                                    </p>
                                                                </div>
                                                            @empty
                                                                <div
                                                                    class="text-center text-xs text-gray-400 p-4 empty-day-message">
                                                                    -- Kosong --</div>
                                                            @endforelse
                                                            <div
                                                                class="text-center text-xs text-gray-400 p-4 hidden no-results-message">
                                                                -- Tidak ada hasil --</div>
                                                        </div>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @empty
                                {{-- Kosongkan saja jika $jadwals kosong --}}
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endsection

    @push('scripts')
        {{-- Skrip untuk QR Scanner & Update Realtime --}}
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>
            const scanStatusContainer = document.getElementById('scan-status');
            let statusTimeout;
            let isScanning = true;

            function showStatusMessage(message, type = 'success') {
                clearTimeout(statusTimeout);
                let bgColor = type === 'success' ? 'bg-green-100' : 'bg-red-100';
                let textColor = type === 'success' ? 'text-green-700' : 'text-red-700';
                scanStatusContainer.innerHTML =
                    `<div class="p-3 rounded-lg ${bgColor} ${textColor} text-sm font-medium text-center">${message}</div>`;
                statusTimeout = setTimeout(() => {
                    scanStatusContainer.innerHTML = '';
                }, 4000);
            }

            function onScanSuccess(decodedText, decodedResult) {
                if (!isScanning) return;
                isScanning = false;
                showStatusMessage('Memproses...', 'success');
                axios.post('{{ route('guru.piket.record') }}', {
                        siswa_id: decodedText,
                        _token: "{{ csrf_token() }}"
                    })
                    .then(response => {
                        showStatusMessage(response.data.message, 'success');
                        axios.get('{{ route('guru.piket.dashboard.data') }}').then(res => renderDashboard(res.data));
                    })
                    .catch(error => {
                        showStatusMessage(error.response.data.message, 'error');
                    })
                    .finally(() => {
                        setTimeout(() => {
                            isScanning = true;
                        }, 2000);
                    });
            }

            function hadirkanManual(siswaId, siswaNama) {
                Swal.fire({
                    title: 'Konfirmasi Kehadiran',
                    html: `Anda akan menghadirkan siswa:<br><b>${siswaNama}</b>`,
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
                                        axios.get('{{ route('guru.piket.dashboard.data') }}').then(res =>
                                            renderDashboard(res.data));
                                    }, 300);
                                }
                            })
                            .catch(error => {
                                showStatusMessage(error.response.data.message, 'error');
                            });
                    }
                });
            }

            function renderDashboard(data) {
                document.getElementById('statistik-hadir').textContent = data.jumlahHadir;
                document.getElementById('statistik-belum-hadir').textContent = data.jumlahBelumHadir;
                document.getElementById('judul-belum-hadir').textContent =
                    `Daftar Siswa Belum Hadir (${data.siswaBelumHadir.length})`;

                let aktivitasHtml = '';
                if (data.aktivitasTerbaru.length > 0) {
                    data.aktivitasTerbaru.forEach(absensi => {
                        const waktuMasuk = new Date(`1970-01-01T${absensi.waktu_masuk}`).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        aktivitasHtml +=
                            `<div class="flex items-center space-x-3"><span class="h-8 w-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-sm font-bold flex-shrink-0">${absensi.siswa.nama_siswa.substring(0, 1)}</span><div><p class="text-sm font-medium text-gray-900">${absensi.siswa.nama_siswa}</p><p class="text-xs text-gray-500">${absensi.siswa.kelas.nama_kelas} | Masuk pukul ${waktuMasuk}</p></div></div>`;
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

            function onScanFailure(error) {
                /* Biarkan scanner terus berjalan */
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: {
                        width: 200,
                        height: 200
                    },
                    aspectRatio: 1.0, // Memaksa rasio kotak 1:1
                    disableFlip: false, // Coba ubah true jika kamera terbalik
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                },
                /* verbose= */
                false
            );
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        </script>

        {{-- Skrip Untuk Tab & Pencarian Jadwal --}}
        <script>
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            const searchInput = document.getElementById('jadwal-search-input');

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
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    tabButtons.forEach(button => {
                        const tabName = button.textContent.toLowerCase().trim();
                        button.classList.toggle('hidden', searchTerm.length > 0 && !tabName.includes(
                            searchTerm));
                    });
                    const activeTabContent = document.querySelector('.tab-content:not(.hidden)');
                    if (!activeTabContent) return;
                    const dayColumns = activeTabContent.querySelectorAll('.day-column');
                    dayColumns.forEach(column => {
                        const scheduleItems = column.querySelectorAll('.schedule-item');
                        const noResultsMessage = column.querySelector('.no-results-message');
                        const emptyDayMessage = column.querySelector('.empty-day-message');
                        let hasVisibleItems = false;
                        scheduleItems.forEach(item => {
                            const itemText = item.textContent.toLowerCase();
                            const isVisible = itemText.includes(searchTerm);
                            item.classList.toggle('hidden', !isVisible);
                            if (isVisible) hasVisibleItems = true;
                        });
                        const isSearching = searchTerm.length > 0;
                        if (emptyDayMessage) emptyDayMessage.classList.toggle('hidden', isSearching);
                        if (noResultsMessage) noResultsMessage.classList.toggle('hidden', hasVisibleItems || !
                            isSearching);
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
