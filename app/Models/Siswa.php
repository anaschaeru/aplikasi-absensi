<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa'; // <-- TAMBAHKAN BARIS INI
    protected $primaryKey = 'siswa_id';
    protected $fillable = ['nis', 'nama_siswa', 'kelas_id', 'alamat', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }

    // app/Models/Siswa.php
    public function izins()
    {
        return $this->hasMany(Izin::class, 'siswa_id');
    }
}
