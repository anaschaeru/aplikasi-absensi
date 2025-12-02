<table>
  <thead>
    {{-- JUDUL & INFORMASI HEADER --}}
    <tr>
      <td colspan="7" style="font-weight: bold; text-align: center; font-size: 14px;">REKAPITULASI ABSENSI SISWA</td>
    </tr>
    <tr>
      <td colspan="7" style="text-align: center;">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</td>
    </tr>
    <tr></tr> {{-- Baris Kosong --}}

    <tr>
      <td style="font-weight: bold;">Kelas</td>
      <td colspan="6">: {{ $rekapData['info']['nama_kelas'] }}</td>
    </tr>
    {{-- INI BAGIAN BARU: MATA PELAJARAN --}}
    <tr>
      <td style="font-weight: bold;">Mata Pelajaran</td>
      <td colspan="6">: {{ $rekapData['info']['nama_mapel'] }}</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Wali Kelas</td>
      <td colspan="6">: {{ $rekapData['info']['wali_kelas'] }}</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Guru Pengampu</td>
      <td colspan="6">: {{ $rekapData['info']['nama_guru'] }}</td>
    </tr>
    <tr>
      <td style="font-weight: bold;">Periode</td>
      <td colspan="6">: {{ $rekapData['info']['periode'] }}</td>
    </tr>
    <tr></tr> {{-- Baris Kosong --}}

    {{-- HEADER TABEL DATA --}}
    <tr>
      <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9;">No</th>
      <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9;">NIS</th>
      <th
        style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9; width: 30px;">
        Nama Siswa</th>
      <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9;">Hadir
      </th>
      <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9;">Sakit
      </th>
      <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9;">Izin</th>
      <th style="font-weight: bold; border: 1px solid #000000; text-align: center; background-color: #d9d9d9;">Alfa</th>
    </tr>
  </thead>
  <tbody>
    {{-- ISI DATA --}}
    @foreach ($rekapData['rekap'] as $data)
      <tr>
        <td style="border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
        <td style="border: 1px solid #000000; text-align: left;">{{ $data['nis'] }}</td>
        <td style="border: 1px solid #000000;">{{ $data['nama_siswa'] }}</td>
        <td style="border: 1px solid #000000; text-align: center;">{{ $data['kehadiran']['Hadir'] }}</td>
        <td style="border: 1px solid #000000; text-align: center;">{{ $data['kehadiran']['Sakit'] }}</td>
        <td style="border: 1px solid #000000; text-align: center;">{{ $data['kehadiran']['Izin'] }}</td>
        <td style="border: 1px solid #000000; text-align: center;">{{ $data['kehadiran']['Alfa'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
