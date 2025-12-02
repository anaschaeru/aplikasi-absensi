<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Rekap Absensi - {{ $rekapData['info']['nama_kelas'] }}</title>
  <style>
    /* 1. SETUP KERTAS F4 DENGAN MARGIN LEBIH TIPIS (1 CM) */
    @page {
      size: 215mm 330mm;
      /* Ukuran F4 */
      margin: 10mm 10mm 10mm 10mm;
      /* Margin diperkecil jadi 1cm agar muat banyak */
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      color: #000;
      line-height: 1.2;
      /* Line height dirapatkan */
    }

    /* Helper Classes */
    .text-center {
      text-align: center;
    }

    .text-bold {
      font-weight: bold;
    }

    .uppercase {
      text-transform: uppercase;
    }

    /* Header yang Lebih Kompak */
    .header-title {
      font-size: 14px;
      /* Font judul sedikit diperkecil */
      margin-bottom: 2px;
    }

    .header-subtitle {
      font-size: 11px;
      margin-bottom: 10px;
    }

    hr {
      border: 0;
      border-top: 1px solid #000;
      margin-bottom: 10px;
    }

    /* Info Container (Rapat) */
    .info-container {
      width: 100%;
      margin-bottom: 10px;
      border: none;
    }

    .info-container td {
      vertical-align: top;
      padding: 1px 0;
      /* Padding baris info dirapatkan */
      border: none;
    }

    .col-label {
      width: 90px;
      font-weight: bold;
    }

    .col-sep {
      width: 10px;
    }

    /* Tabel Data (Sangat Hemat Tempat) */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
      /* Jarak ke tanda tangan diperkecil */
    }

    .data-table th,
    .data-table td {
      border: 1px solid #000;
      padding: 3px 4px;
      /* PADDING DIPERKECIL AGAR TABEL PENDEK */
      vertical-align: middle;
      font-size: 10px;
      /* Font tabel diperkecil sedikit */
    }

    .data-table th {
      background-color: #f0f0f0;
      text-align: center;
      font-weight: bold;
      height: 20px;
    }

    /* Kolom Width */
    .col-no {
      width: 5%;
      text-align: center;
    }

    .col-nis {
      width: 15%;
      text-align: center;
    }

    .col-nama {
      width: 44%;
    }

    .col-stat {
      width: 9%;
      text-align: center;
    }

    /* 2. TEKNIK AGAR TANDA TANGAN TIDAK PISAH HALAMAN */
    .signature-wrapper {
      width: 100%;
      /* Ini mencegah blok tanda tangan terpotong di tengah jalan */
      page-break-inside: avoid;
      /* Ini mencoba mencegah pemisahan dari elemen sebelumnya (tabel) */
      page-break-before: auto;
    }

    .signature-box {
      float: right;
      width: 220px;
      text-align: center;
      margin-top: 10px;
    }

    .ttd-space {
      height: 50px;
      /* Ruang tanda tangan sedikit dipadatkan */
    }
  </style>
</head>

<body>

  {{-- JUDUL --}}
  <div class="text-center">
    <div class="header-title text-bold uppercase">LAPORAN REKAPITULASI ABSENSI SISWA</div>
    <div class="header-subtitle">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</div>
  </div>

  <hr>

  {{-- INFORMASI --}}
  <table class="info-container">
    <tr>
      <td width="55%">
        <table>
          <tr>
            <td class="col-label">Kelas</td>
            <td class="col-sep">:</td>
            <td>{{ $rekapData['info']['nama_kelas'] }}</td>
          </tr>
          <tr>
            <td class="col-label">Mata Pelajaran</td>
            <td class="col-sep">:</td>
            <td>{{ $rekapData['info']['nama_mapel'] }}</td>
          </tr>
          <tr>
            <td class="col-label">Wali Kelas</td>
            <td class="col-sep">:</td>
            <td>{{ $rekapData['info']['wali_kelas'] }}</td>
          </tr>
        </table>
      </td>
      <td width="45%">
        <table>
          <tr>
            <td class="col-label">Periode</td>
            <td class="col-sep">:</td>
            <td>{{ $rekapData['info']['periode'] }}</td>
          </tr>
          <tr>
            <td class="col-label">Guru Mapel</td>
            <td class="col-sep">:</td>
            <td>{{ $rekapData['info']['nama_guru'] }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  {{-- TABEL DATA --}}
  <table class="data-table">
    <thead>
      <tr>
        <th class="col-no">No</th>
        <th class="col-nis">NIS</th>
        <th class="col-nama" style="text-align: left; padding-left: 5px;">Nama Siswa</th>
        <th class="col-stat">Hadir</th>
        <th class="col-stat">Sakit</th>
        <th class="col-stat">Izin</th>
        <th class="col-stat">Alfa</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rekapData['rekap'] as $data)
        <tr>
          <td class="col-no">{{ $loop->iteration }}</td>
          <td class="col-nis">{{ $data['nis'] }}</td>
          <td class="col-nama" style="padding-left: 5px;">{{ $data['nama_siswa'] }}</td>
          <td class="col-stat">{{ $data['kehadiran']['Hadir'] }}</td>
          <td class="col-stat">{{ $data['kehadiran']['Sakit'] }}</td>
          <td class="col-stat">{{ $data['kehadiran']['Izin'] }}</td>
          <td class="col-stat">{{ $data['kehadiran']['Alfa'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{-- TANDA TANGAN (Dibungkus Wrapper agar solid) --}}
  <div class="signature-wrapper">
    <div class="signature-box">
      <p style="margin-bottom: 5px;">Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
      <p style="margin-bottom: 0;">Guru Pengampu,</p>

      <div class="ttd-space"></div>

      <p class="text-bold" style="text-decoration: underline;">{{ $rekapData['info']['nama_guru'] }}</p>
    </div>
    {{-- Clearfix untuk float --}}
    <div style="clear: both;"></div>
  </div>

</body>

</html>
