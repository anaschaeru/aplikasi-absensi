<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiHarian extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'absensi_harian';

    // Primary Key (Sesuaikan dengan nama kolom ID di tabel Anda)
    // Cek di phpMyAdmin, biasanya 'id' atau 'absensi_id' atau 'id_absensi'
    protected $primaryKey = 'absensi_id';

    // Matikan auto-increment jika primary key Anda bukan integer/AI (opsional)
    // public $incrementing = true;

    // Kolom yang boleh diisi
    protected $fillable = [
        'siswa_id',
        'tanggal_absensi',
        'status',
        'waktu_masuk',
        'waktu_pulang',
        'foto_masuk',
        'foto_pulang'
    ];
}
