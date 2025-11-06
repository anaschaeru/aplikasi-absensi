<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru'; // <-- TAMBAHKAN BARIS INI
    protected $primaryKey = 'guru_id'; // Definisikan primary key

    protected $fillable = [
        'user_id',
        'nip',
        'nama_guru',
        'kontak'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
