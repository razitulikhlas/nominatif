<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisKredit extends Model
{
    protected $table = 'tbL_analis'; // Nama tabel

    protected $fillable = [
        'id_cabang',
        'kode_analis',
        'nama_analis',
        'nohp'
    ];

    public $timestamps = false; // Jika tabel tidak memiliki kolom created_at dan updated_at
}
