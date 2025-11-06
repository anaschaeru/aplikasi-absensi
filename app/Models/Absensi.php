<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $primaryKey = 'absensi_id';

    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'dicatat_oleh_guru_id',
        'tanggal_absensi',
        'status',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_id');
    }

    /**
     * Mendefinisikan relasi bahwa absensi ini dicatat oleh seorang guru.
     */
    public function guru()
    {
        // 'dicatat_oleh_guru_id' adalah foreign key di tabel 'absensi'
        // 'guru_id' adalah primary key di tabel 'guru'
        return $this->belongsTo(Guru::class, 'dicatat_oleh_guru_id', 'guru_id');
    }
}
