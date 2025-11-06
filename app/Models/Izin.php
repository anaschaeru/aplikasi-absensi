<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    /**
     * Atribut yang dijaga dari mass assignment.
     */
    protected $guarded = ['id'];

    /**
     * Relasi ke Siswa yang mengajukan izin.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke User (Guru Piket/Admin) yang menyetujui.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'diapprove_oleh');
    }
}
