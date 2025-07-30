<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'tbl_cabang'; // Nama tabel

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
    ];

    public $timestamps = false; // Jika tabel tidak memiliki kolom created_at dan updated_at
}
