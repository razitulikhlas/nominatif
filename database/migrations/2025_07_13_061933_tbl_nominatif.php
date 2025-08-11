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
            $table->string('KD_CAB_KONSOL',7)->nullable();
            $table->string('KD_CAB',7)->nullable();
            $table->string('NO_REK',30)->nullable();
            $table->string('NO_NASABAH',20)->nullable();
            $table->string('NAMA_SINGKAT',40)->nullable();
            $table->date('TGL_JT')->nullable();
            $table->integer('JNK_WKT_BL')->nullable();
            $table->double('PRS_BUNGA')->nullable();
            $table->double('PLAFOND')->nullable();
            $table->double('PLAFOND_AWAL')->nullable();
            $table->double('LONGGAR_TARIK')->nullable();
            $table->string('KD_STATUS',10)->nullable();
            $table->double('BUNGA')->nullable();
            $table->double('POKOK')->nullable();
            $table->string('KOLEKTIBILITY',5)->nullable();
            $table->string('KD_PRD',10)->nullable();
            $table->string('GL_PRD_NAME',10)->nullable();
            $table->string('PRD_NAME',50)->nullable();
            $table->double('SALDO_AKHIR')->nullable();
            $table->double('SALDO_AKHIR_NERACA')->nullable();
            $table->double('AMORSISA')->nullable();
            $table->string('KODE_APL',5)->nullable();
            $table->double('AMOR_BLN_INI')->nullable();
            $table->double('TOTAGUNAN')->nullable();
            $table->double('TOTAGUNAN_YDP')->nullable();
            $table->double('IMPAIREMENT')->nullable();
            $table->double('CKPN')->nullable();
            $table->date('TGLMULAI')->nullable();
            $table->double('AKMAMOR')->nullable();
            $table->string('JENIS_KREDIT',5)->nullable();
            $table->string('SEKTOR_EKONOMI',5)->nullable();
            $table->string('GOLONGAN',5)->nullable();
            $table->string('HUB_BANK',5)->nullable();
            $table->double('ACRU_BLN')->nullable();
            $table->double('TUNGG_POKOK')->nullable();
            $table->double('TUNGG_BUNGA')->nullable();
            $table->text('TREASURID',)->nullable();
            $table->text('CRNBR')->nullable();
            $table->double('AMTPENPASD')->nullable();
            $table->string('JENIS_GUNA',2)->nullable();
            $table->string('GOL_NSB_LBU',7)->nullable();
            $table->string('GOL_NSB_SID',7)->nullable();
            $table->string('GOL_KRD_SID',7)->nullable();
            $table->string('CATPORTOLBU',7)->nullable();
            $table->date('TGL_PK')->nullable();
            $table->text('NO_PK')->nullable();
            $table->string('KD_AO',8)->nullable();
            $table->double('ANGS_POKOK')->nullable();
            $table->double('ANGS_BUNGA')->nullable();
            $table->double('DENDA_TUNGGBNG')->nullable();
            $table->double('DENDA_TUNGGPKK')->nullable();
            $table->string('JNSBUNGA',8)->nullable();
            $table->string('RATING',5)->nullable();
            $table->double('RECOVERY_RATE')->nullable();
            $table->double('NILAI_AGUNAN')->nullable();
            $table->string('GROUPID',3)->nullable();
            $table->string('QUALITYID',3)->nullable();
            $table->integer('JML_HARI_TUNGPKK')->nullable();
            $table->integer('JML_HARI_TUNGBNG')->nullable();
            $table->string('NOHP',20)->nullable();
            $table->double('NILAI_WAJAR')->nullable();
            $table->string('NIK',25)->nullable();
            $table->string('NPWP',25)->nullable();
            $table->double('NEW_AGUNAN_YDP')->nullable();
            $table->double('NEW_NILAI_AGUNAN')->nullable();
            $table->string('STSIMPR',5)->nullable();
            $table->date('TGL_AWAL_RSTRK')->nullable();
            $table->date('TGL_AKHIR_RSTRK')->nullable();
            $table->integer('RESTRUKKE')->nullable();
            $table->string('QUALRPTO',5)->nullable();
            $table->string('RESTRUKTYPE',5)->nullable();
            $table->text('PARMNM')->nullable();
            $table->date('TGL_PENCAIRAN')->nullable();
            $table->string('SUMBER_DANA',7)->nullable();
            $table->string('KT_DEBITUR',7)->nullable();
            $table->string('INKLUSIF_MACROPRUDENSIAL',20)->nullable();
            $table->string('PEMBIAYAAN_BERKELANJUTAN',20)->nullable();
            $table->string('SEGMENT_KREDIT',20)->nullable();
            $table->string('SEK_EKO_LBUT',20)->nullable();
            $table->string('SEK_EKO_THI',20)->nullable();
            $table->string('AUTO_HIJAU_THI',20)->nullable();
            $table->string('KLASIFIKASI_THI',20)->nullable();
            $table->string('SKOR_KREDIT',20)->nullable();
            $table->string('RISK_GRADE',20)->nullable();
            $table->date('TANGGAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        //     $table->string('KD_CAB_KONSOL')->nullable();
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



        // Back up 2

        // Schema::create('tbl_nominatif', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('KD_CAB_KONSOL',7)->nullable();
        //     $table->string('KD_CAB',7)->nullable();
        //     $table->string('NO_REK',30)->nullable();
        //     $table->string('NO_NASABAH',20)->nullable();
        //     $table->string('NAMA_SINGKAT',40)->nullable();
        //     $table->date('TGL_JT')->nullable();
        //     $table->integer('JNK_WKT_BL')->nullable();
        //     $table->double('PRS_BUNGA')->nullable();
        //     $table->double('PLAFOND')->nullable();
        //     $table->double('PLAFOND_AWAL')->nullable();
        //     $table->double('LONGGAR_TARIK')->nullable();
        //     $table->string('KD_STATUS',10)->nullable();
        //     $table->double('BUNGA')->nullable();
        //     $table->double('POKOK')->nullable();
        //     $table->string('KOLEKTIBILITY',5)->nullable();
        //     $table->string('KD_PRD',10)->nullable();
        //     $table->string('GL_PRD_NAME',10)->nullable();
        //     $table->string('PRD_NAME',50)->nullable();
        //     $table->double('SALDO_AKHIR')->nullable();
        //     $table->double('SALDO_AKHIR_NERACA')->nullable();
        //     $table->double('AMORSISA')->nullable();
        //     $table->string('KODE_APL',5)->nullable();
        //     $table->double('AMOR_BLN_INI')->nullable();
        //     $table->double('TOTAGUNAN')->nullable();
        //     $table->double('TOTAGUNAN_YDP')->nullable();
        //     $table->double('IMPAIREMENT')->nullable();
        //     $table->double('CKPN')->nullable();
        //     $table->date('TGLMULAI')->nullable();
        //     $table->double('AKMAMOR')->nullable();
        //     $table->string('JENIS_KREDIT',5)->nullable();
        //     $table->string('SEKTOR_EKONOMI',5)->nullable();
        //     $table->string('GOLONGAN',5)->nullable();
        //     $table->string('HUB_BANK',5)->nullable();
        //     $table->double('ACRU_BLN')->nullable();
        //     $table->double('TUNGG_POKOK')->nullable();
        //     $table->double('TUNGG_BUNGA')->nullable();
        //     $table->text('TREASURID',)->nullable();
        //     $table->text('CRNBR')->nullable();
        //     $table->double('AMTPENPASD')->nullable();
        //     $table->string('JENIS_GUNA',2)->nullable();
        //     $table->string('GOL_NSB_LBU',7)->nullable();
        //     $table->string('GOL_NSB_SID',7)->nullable();
        //     $table->string('GOL_KRD_SID',7)->nullable();
        //     $table->string('CATPORTOLBU',7)->nullable();
        //     $table->date('TGL_PK')->nullable();
        //     $table->text('NO_PK')->nullable();
        //     $table->string('KD_AO',8)->nullable();
        //     $table->double('ANGS_POKOK')->nullable();
        //     $table->double('ANGS_BUNGA')->nullable();
        //     $table->double('DENDA_TUNGGBNG')->nullable();
        //     $table->double('DENDA_TUNGGPKK')->nullable();
        //     $table->string('JNSBUNGA',8)->nullable();
        //     $table->string('RATING',5)->nullable();
        //     $table->double('RECOVERY_RATE')->nullable();
        //     $table->double('NILAI_AGUNAN')->nullable();
        //     $table->string('GROUPID',3)->nullable();
        //     $table->string('QUALITYID',3)->nullable();
        //     $table->integer('JML_HARI_TUNGPKK')->nullable();
        //     $table->integer('JML_HARI_TUNGBNG')->nullable();
        //     $table->string('NOHP',20)->nullable();
        //     $table->double('NILAI_WAJAR')->nullable();
        //     $table->string('NIK',25)->nullable();
        //     $table->string('NPWP',25)->nullable();
        //     $table->double('NEW_AGUNAN_YDP')->nullable();
        //     $table->double('NEW_NILAI_AGUNAN')->nullable();
        //     $table->string('STSIMPR',5)->nullable();
        //     $table->date('TGL_AWAL_RSTRK')->nullable();
        //     $table->date('TGL_AKHIR_RSTRK')->nullable();
        //     $table->integer('RESTRUKKE')->nullable();
        //     $table->string('QUALRPTO',5)->nullable();
        //     $table->string('RESTRUKTYPE',5)->nullable();
        //     $table->text('PARMNM')->nullable();
        //     $table->date('TGL_PENCAIRAN')->nullable();
        //     $table->string('SUMBER_DANA',7)->nullable();
        //     $table->string('KT_DEBITUR',7)->nullable();
        //     $table->string('INKLUSIF_MACROPRUDENSIAL',20)->nullable();
        //     $table->string('PEMBIAYAAN_BERKELANJUTAN',20)->nullable();
        //     $table->string('SEGMENT_KREDIT',20)->nullable();
        //     $table->string('SEK_EKO_LBUT',20)->nullable();
        //     $table->string('SEK_EKO_THI',20)->nullable();
        //     $table->string('AUTO_HIJAU_THI',20)->nullable();
        //     $table->string('KLASIFIKASI_THI',20)->nullable();
        //     $table->string('SKOR_KREDIT',20)->nullable();
        //     $table->string('RISK_GRADE',20)->nullable();
        //     $table->date('TANGGAL')->nullable();
        // });
    }
};
