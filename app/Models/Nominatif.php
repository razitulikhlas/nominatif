<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nominatif extends Model
{
    protected $table = 'tbl_nominatif'; // Nama tabel

    protected $fillable = [
        'KD_CAB_KONSOL',
        'NAMA_SINGKAT',
        'PLAFOND',
        'NILAI_WAJAR',
        'KD_CAB',
        'KD_AO',
        'NO_REK',
        'NOHP',
        'NO_PK',
        'KOLEKTIBILITY',
        'KD_PRD',
        'GL_PRD_NAME',
        'TUNGG_POKOK',
        'TUNGG_BUNGA',
        'SALDO_AKHIR',
        'SEKTOR_EKONOMI',
        'TANGGAL',
    ];

    public $timestamps = false; // Jika tabel tidak memiliki kolom created_at dan updated_at
}
