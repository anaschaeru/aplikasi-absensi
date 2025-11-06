<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $primaryKey = 'kelas_id';
    protected $fillable = ['nama_kelas', 'tingkat', 'wali_kelas_id'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }
}
