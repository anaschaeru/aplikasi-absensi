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
        'waktu_pulang',
        'foto_masuk',
        'foto_pulang',
        // tambahkan field lain jika ada
    ];

    /**
     * Relasi ke Siswa.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'siswa_id'); // <-- Pastikan foreign key-nya juga benar
    }
}
