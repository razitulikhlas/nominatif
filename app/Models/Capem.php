<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capem extends Model
{
    protected $table = 'tbl_capem'; // Nama tabel

    protected $fillable = [
        'id_cabang',
        'kode_capem',
        'nama_capem',
    ];

    public $timestamps = false; // Jika tabel tidak memiliki kolom created_at dan updated_at
}
