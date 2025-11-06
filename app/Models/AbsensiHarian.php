<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiHarian extends Model
{
    use HasFactory;
    protected $table = 'absensi_harian';
    protected $fillable = [
        'siswa_id',
        'tanggal_absensi',
        'status',
        'waktu_masuk',
        // tambahkan field lain jika ada
    ];

    /**
     * Relasi ke Siswa.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
