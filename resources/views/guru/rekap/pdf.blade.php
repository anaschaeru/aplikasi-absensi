<!DOCTYPE html>
<html>

<head>
    <title>Rekap Absensi</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header-info {
            margin-bottom: 20px;
        }

        .header-info h2 {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="header-info">
        <h2>Rekapitulasi Absensi</h2>
        <p>
            <strong>Kelas:</strong> {{ $rekapData['info']['nama_kelas'] }}<br>
            <strong>Periode:</strong> {{ $rekapData['info']['periode'] }}<br>
            <strong>Guru:</strong> {{ $rekapData['info']['nama_guru'] }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alfa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekapData['rekap'] as $data)
                <tr>
                    <td>{{ $data['nis'] }}</td>
                    <td>{{ $data['nama_siswa'] }}</td>
                    <td style="text-align: center;">{{ $data['kehadiran']['Hadir'] }}</td>
                    <td style="text-align: center;">{{ $data['kehadiran']['Sakit'] }}</td>
                    <td style="text-align: center;">{{ $data['kehadiran']['Izin'] }}</td>
                    <td style="text-align: center;">{{ $data['kehadiran']['Alfa'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
