<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Afiliasi extends Model
{
    protected $table = 'tbl_afiliasi'; // Nama tabel

    protected $fillable = [
        'NO_REK',
        'NO_REK_AFILIASI'
    ];

    public $timestamps = false; // Jika tabel tidak memiliki kolom created_at dan updated_at
}
