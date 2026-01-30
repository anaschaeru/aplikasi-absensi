<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Rekap Absensi Galeri</title>
  <style>
    /* === HALAMAN === */
    @page {
      /* Margin diperkecil agar foto bisa besar tapi tetap 1 lembar */
      margin: 10px 15px;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 9pt;
      margin: 0;
      padding: 0;
    }

    /* === HEADER === */
    .header {
      text-align: center;
      border-bottom: 2px solid #444;
      padding-bottom: 1px;
      margin-bottom: 2px;
    }

    .header h2 {
      margin: 0;
      font-size: 14pt;
      text-transform: uppercase;
    }

    .header p {
      margin: 2px 0;
      font-size: 10pt;
    }

    /* === GRID TABLE (4 KOLOM) === */
    .grid-table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }

    .grid-table td {
      border: 1px solid #444;
      padding: 4px 2px;
      width: 25%;
      /* 4 Kolom */
      vertical-align: top;
      text-align: center;
      /* Tinggi baris otomatis menyesuaikan isi */
    }

    /* === ITEM SISWA === */
    .nama {
      font-weight: bold;
      font-size: 6pt;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      background: #eee;
      padding: 1px 0;
      margin-bottom: 2px;
      display: block;
      border-radius: 2px;
    }

    /* === CONTAINER FOTO (SEBELAH MENYEBELAH) === */
    .foto-wrap {
      width: 100%;
      margin-bottom: 1px;
    }

    .col-foto {
      width: 50%;
      /* Bagi 2: Kiri Masuk, Kanan Pulang */
      text-align: center;
      vertical-align: top;
    }

    /* === FOTO UTAMA (DIPERBESAR) === */
    .thumb {
      width: 80px;
      /* <--- UKURAN DIPERBESAR (Sebelumnya 38px) */
      height: 80px;
      /* Aspect Ratio Kotak */
      object-fit: cover;
      border: 1px solid #999;
      border-radius: 3px;
      display: inline-block;
    }

    /* Placeholder jika tidak ada foto */
    .no-thumb {
      width: 80px;
      height: 80px;
      border: 1px dashed #ccc;
      display: inline-block;
      line-height: 52px;
      font-size: 7pt;
      color: #aaa;
      border-radius: 3px;
    }

    /* === JAM === */
    .jam {
      display: block;
      font-size: 6pt;
      font-weight: bold;
      margin-top: 2px;
      color: #333;
    }

    /* === STATUS TEXT === */
    .status-box {
      height: 60px;
      /* Tinggi penyeimbang jika tidak ada foto */
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .st-text {
      display: block;
      margin-top: 25px;
      /* Manual centering untuk PDF */
      font-weight: bold;
      font-size: 10pt;
      text-transform: uppercase;
    }

    .cl-Alfa {
      color: #b00;
    }

    .cl-Sakit {
      color: #d90;
    }

    .cl-Izin {
      color: #009;
    }
  </style>
</head>

<body>

  <div class="header">
    <h2>Rekap Absensi Harian</h2>
    <p>{{ $nama_kelas }} | {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</p>
  </div>

  <table class="grid-table">
    {{-- CHUNK 4 = 4 Siswa Per Baris --}}
    @foreach ($dataAbsensi->chunk(4) as $rowSiswa)
      <tr>
        @foreach ($rowSiswa as $siswa)
          @php
            $absen = $siswa->absensi->first();
            $pathM = null;
            $pathP = null;
            $jamM = '-';
            $jamP = '-';

            if ($absen) {
                if ($absen->waktu_masuk) {
                    $jamM = \Carbon\Carbon::parse($absen->waktu_masuk)->format('H.i');
                }
                if ($absen->waktu_pulang) {
                    $jamP = \Carbon\Carbon::parse($absen->waktu_pulang)->format('H.i');
                }

                if ($absen->foto_masuk) {
                    $f = public_path('storage/' . $absen->foto_masuk);
                    if (file_exists($f)) {
                        $pathM = $f;
                    }
                }
                if ($absen->foto_pulang) {
                    $f = public_path('storage/' . $absen->foto_pulang);
                    if (file_exists($f)) {
                        $pathP = $f;
                    }
                }
            }
          @endphp

          <td>
            {{-- Nama Siswa --}}
            <div class="nama">{{ $siswa->nama_siswa }}</div>

            @if ($absen && $absen->status == 'Hadir')
              {{-- Tabel Foto (Masuk Kiri - Pulang Kanan) --}}
              <table class="foto-wrap">
                <tr>
                  <td class="col-foto">
                    @if ($pathM)
                      <img src="{{ $pathM }}" class="thumb">
                    @else
                      <div class="no-thumb">No Foto</div>
                    @endif
                    <span class="jam">{{ $jamM }}</span>
                  </td>
                  <td class="col-foto">
                    @if ($pathP)
                      <img src="{{ $pathP }}" class="thumb">
                    @else
                      <div class="no-thumb">-</div>
                    @endif
                    <span class="jam">{{ $jamP }}</span>
                  </td>
                </tr>
              </table>
            @elseif($absen)
              {{-- Jika Status Bukan Hadir --}}
              <div class="status-box">
                <span class="st-text cl-{{ $absen->status }}">{{ $absen->status }}</span>
              </div>
            @else
              {{-- Belum Absen --}}
              <div class="status-box">
                <span class="st-text" style="color:#ccc;">-</span>
              </div>
            @endif
          </td>
        @endforeach

        {{-- Isi Kotak Kosong (Padding) --}}
        @for ($i = 0; $i < 4 - count($rowSiswa); $i++)
          <td></td>
        @endfor
      </tr>
    @endforeach
  </table>

</body>

</html>
