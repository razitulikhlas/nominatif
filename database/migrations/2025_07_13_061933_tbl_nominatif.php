<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('tbl_nominatif', function (Blueprint $table) {
            $table->id();
            $table->string('KD_CAB_KONSOL')->nullable();
            $table->string('KD_CAB')->nullable();
            $table->string('NO_REK')->nullable();
            $table->string('NO_NASABAH')->nullable();
            $table->string('NAMA_SINGKAT')->nullable();
            $table->date('TGL_JT')->nullable();
            $table->integer('JNK_WKT_BL')->nullable();
            $table->double('PRS_BUNGA')->nullable();
            $table->double('PLAFOND')->nullable();
            $table->double('PLAFOND_AWAL')->nullable();
            $table->double('LONGGAR_TARIK')->nullable();
            $table->string('KD_STATUS')->nullable();
            $table->double('BUNGA')->nullable();
            $table->double('POKOK')->nullable();
            $table->string('KOLEKTIBILITY')->nullable();
            $table->string('KD_PRD')->nullable();
            $table->string('GL_PRD_NAME')->nullable();
            $table->string('PRD_NAME')->nullable();
            $table->double('SALDO_AKHIR')->nullable();
            $table->double('SALDO_AKHIR_NERACA')->nullable();
            $table->double('AMORSISA')->nullable();
            $table->string('KODE_APL')->nullable();
            $table->double('AMOR_BLN_INI')->nullable();
            $table->double('TOTAGUNAN')->nullable();
            $table->double('TOTAGUNAN_YDP')->nullable();
            $table->double('IMPAIREMENT')->nullable();
            $table->double('CKPN')->nullable();
            $table->date('TGLMULAI')->nullable();
            $table->double('AKMAMOR')->nullable();
            $table->string('JENIS_KREDIT')->nullable();
            $table->string('SEKTOR_EKONOMI')->nullable();
            $table->string('GOLONGAN')->nullable();
            $table->string('HUB_BANK')->nullable();
            $table->double('ACRU_BLN')->nullable();
            $table->double('TUNGG_POKOK')->nullable();
            $table->double('TUNGG_BUNGA')->nullable();
            $table->string('TREASURID')->nullable();
            $table->string('CRNBR')->nullable();
            $table->double('AMTPENPASD')->nullable();
            $table->string('JENIS_GUNA')->nullable();
            $table->string('GOL_NSB_LBU')->nullable();
            $table->string('GOL_NSB_SID')->nullable();
            $table->string('GOL_KRD_SID')->nullable();
            $table->string('CATPORTOLBU')->nullable();
            $table->date('TGL_PK')->nullable();
            $table->string('NO_PK')->nullable();
            $table->string('KD_AO')->nullable();
            $table->double('ANGS_POKOK')->nullable();
            $table->double('ANGS_BUNGA')->nullable();
            $table->double('DENDA_TUNGGBNG')->nullable();
            $table->double('DENDA_TUNGGPKK')->nullable();
            $table->string('JNSBUNGA')->nullable();
            $table->string('RATING')->nullable();
            $table->double('RECOVERY_RATE')->nullable();
            $table->double('NILAI_AGUNAN')->nullable();
            $table->string('GROUPID')->nullable();
            $table->string('QUALITYID')->nullable();
            $table->integer('JML_HARI_TUNGPKK')->nullable();
            $table->integer('JML_HARI_TUNGBNG')->nullable();
            $table->string('NOHP')->nullable();
            $table->double('NILAI_WAJAR')->nullable();
            $table->string('NIK')->nullable();
            $table->string('NPWP')->nullable();
            $table->double('NEW_AGUNAN_YDP')->nullable();
            $table->double('NEW_NILAI_AGUNAN')->nullable();
            $table->string('STSIMPR')->nullable();
            $table->date('TGL_AWAL_RSTRK')->nullable();
            $table->date('TGL_AKHIR_RSTRK')->nullable();
            $table->integer('RESTRUKKE')->nullable();
            $table->string('QUALRPTO')->nullable();
            $table->string('RESTRUKTYPE')->nullable();
            $table->string('PARMNM')->nullable();
            $table->date('TGL_PENCAIRAN')->nullable();
            $table->string('SUMBER_DANA')->nullable();
            $table->string('KT_DEBITUR')->nullable();
            $table->string('INKLUSIF_MACROPRUDENSIAL')->nullable();
            $table->string('PEMBIAYAAN_BERKELANJUTAN')->nullable();
            $table->string('SEGMENT_KREDIT')->nullable();
            $table->string('SEK_EKO_LBUT')->nullable();
            $table->string('SEK_EKO_THI')->nullable();
            $table->string('AUTO_HIJAU_THI')->nullable();
            $table->string('KLASIFIKASI_THI')->nullable();
            $table->string('SKOR_KREDIT')->nullable();
            $table->string('RISK_GRADE')->nullable();
            $table->date('TANGGAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        // $table->string('KD_CAB_KONSOL')->nullable();
        //     $table->string('NAMA_SINGKAT')->nullable();
        //     $table->double('PLAFOND')->nullable();
        //     $table->double('NILAI_WAJAR')->nullable();
        //     $table->string('KD_CAB')->nullable();
        //     $table->string('KD_AO')->nullable();
        //     $table->string('NO_REK')->nullable();
        //     $table->string('NOHP')->nullable();
        //     $table->string('NO_PK')->nullable();
        //     $table->string('KOLEKTIBILITY')->nullable();
        //     $table->string('KD_PRD')->nullable();
        //     $table->string('GL_PRD_NAME')->nullable();
        //     $table->double('TUNGG_POKOK')->nullable();
        //     $table->double('TUNGG_BUNGA')->nullable();
        //     $table->double('SALDO_AKHIR')->nullable();
        //     $table->string('SEKTOR_EKONOMI')->nullable();
    }
};
