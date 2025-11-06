<!DOCTYPE html>
<html>

<head>
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px
        }

        th {
            background-color: #f2f2f2
        }
    </style>
</head>

<body>
    <h2>Laporan Absensi</h2>
    <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') }} -
        {{ \Carbon\Carbon::parse($filters['tanggal_akhir'])->format('d/m/Y') }}<br><strong>Kelas:</strong>
        {{ $kelas->nama_kelas ?? 'Semua Kelas' }}</p>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Mata Pelajaran</th>
                <th>NIS</th>
                <th>Siswa</th>
                <th>Kelas</th>
                <th>Status</th>
                <th>Oleh Guru</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataLaporan as $absensi)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($absensi->tanggal_absensi)->format('d/m/Y') }}</td>
                    <td>{{ $absensi->jadwal->mataPelajaran->nama_mapel }}</td>
                    <td>{{ $absensi->siswa->nis ?? 'N/A' }}</td>
                    <td>{{ $absensi->siswa->nama_siswa ?? 'N/A' }}</td>
                    <td>{{ $absensi->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                    <td>{{ $absensi->status }}</td>
                    <td>{{ $absensi->guru->nama_guru ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
