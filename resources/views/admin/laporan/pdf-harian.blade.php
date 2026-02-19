<!DOCTYPE html>
<html>

<head>
  <title>Laporan Absensi Harian</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th,
    td {
      border: 1px solid #333;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
      text-align: center;
    }

    .text-center {
      text-align: center;
    }
  </style>
</head>

<body>
  <h2 class="text-center">Laporan Absensi Harian</h2>
  <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br>
    <strong>Kelas:</strong> {{ $kelas ? $kelas->nama_kelas : 'Semua Kelas' }}
  </p>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Siswa</th>
        <th>Kelas</th>
        <th>Masuk</th>
        <th>Pulang</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($absensi as $index => $absen)
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td>{{ $absen->siswa->nama_siswa ?? '-' }}</td>
          <td class="text-center">{{ $absen->siswa->kelas->nama_kelas ?? '-' }}</td>
          <td class="text-center">
            {{ $absen->waktu_masuk ? \Carbon\Carbon::parse($absen->waktu_masuk)->format('H:i') : '-' }}</td>
          <td class="text-center">
            {{ $absen->waktu_pulang ? \Carbon\Carbon::parse($absen->waktu_pulang)->format('H:i') : '-' }}</td>
          <td class="text-center">{{ $absen->status }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
