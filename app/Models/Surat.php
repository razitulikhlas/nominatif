<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Surat extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'tbl_surat'; // Nama tabel

    protected $fillable = [
        'nomor_rekening',
        'nomor_surat',
        'tanggal_surat',
        'jenis_surat',
        'tunggakan_pokok',
        'tunggakan_bunga',
        'denda_pokok',
        'denda_bunga',
    ];

    // Jika Anda tidak menggunakan timestamps (created_at, updated_at)
    public $timestamps = true;


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

