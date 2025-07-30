<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Data extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'data';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'KD_CAB_KONSOL',
        'NILAI_WAJAR',
        'NO_NASABAH',
        'KD_CAB',
        'NO_REK',
        'NAMA_SINGKAT',
        'TGL_JT',
        'JNK_WKT_BL',
        'PRS_BUNGA',
        'PLAFOND',
        'PLAFOND_AWAL',
        'LONGGAR_TARIK',
        'KD_STATUS',
        'BUNGA',
        'POKOK',
        'KOLEKTIBILITY',
        'KD_PRD',
        'GL_PRD_NAME',
        'PRD_NAME',
        'SALDO_AKHIR',
        'SALDO_AKHIR_NERACA',
        'AMORSISA',
        'KODE_APL',
        'AMOR_BLN_INI',
        'TOTAGUNAN',
        'TOTAGUNAN_YDP',
        'IMPAIREMENT',
        'CKPN',
        'TGLMULAI',
        'AKMAMOR',
        'JENIS_KREDIT',
        'SEKTOR_EKONOMI',
        'GOLONGAN',
        'HUB_BANK',
        'ACRU_BLN',
        'TUNGG_POKOK',
        'TUNGG_BUNGA',

        'TREASURID',
        'CRNBR',
        'AMTPENPASD',
        'JENIS_GUNA',
        'GOL_NSB_LBU',
        'GOL_NSB_SID',
        'GOL_KRD_SID',
        'CATPORTOLBU',
        'TGL_PK',
        'NO_PK',
        'KD_AO',
        'ANGS_POKOK',
        'ANGS_BUNGA',
        'DENDA_TUNGGBNG',
        'DENDA_TUNGGPKK',
        'JNSBUNGA',
        'RATING',
        'RECOVERY_RATE',
        'NILAI_AGUNAN',
        'GROUPID',
        'QUALITYID',
        'JML_HARI_TUNGPKK',
        'JML_HARI_TUNGBNG',
        'NOHP',
        'NIK',
        'NPWP',
        'NEW_AGUNAN_YDP',
        'NEW_NILAI_AGUNAN',
        'STSIMPR',
        'TGL_AWAL_RSTRK',
        'TGL_AKHIR_RSTRK',
        'RESTRUKKE',
        'QUALRPTO',
        'RESTRUKTYPE',
        'PARMNM',
        'TGL_PENCAIRAN',
        'SUMBER_DANA',
        'KT_DEBITUR',
        'INKLUSIF_MACROPRUDENSIAL',
        'PEMBIAYAAN_BERKELANJUTAN',
        'SEGMENT_KREDIT',
        'SEK_EKO_LBUT',
    ];

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
