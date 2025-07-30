<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisKredit extends Model
{
    protected $table = 'tbl_analis'; // Nama tabel

    protected $fillable = [
        'kode_analis',
        'nama_analis',
    ];

    public $timestamps = false; // Jika tabel tidak memiliki kolom created_at dan updated_at
}
