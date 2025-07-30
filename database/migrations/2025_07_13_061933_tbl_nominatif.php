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
            $table->string('NAMA_SINGKAT')->nullable();
            $table->double('PLAFOND')->nullable();
            $table->double('NILAI_WAJAR')->nullable();
            $table->string('KD_CAB')->nullable();
            $table->string('KD_AO')->nullable();
            $table->string('NO_REK')->nullable();
            $table->string('NOHP')->nullable();
            $table->string('NO_PK')->nullable();
            $table->string('KOLEKTIBILITY')->nullable();
            $table->string('KD_PRD')->nullable();
            $table->string('GL_PRD_NAME')->nullable();
            $table->double('TUNGG_POKOK')->nullable();
            $table->double('TUNGG_BUNGA')->nullable();
            $table->double('SALDO_AKHIR')->nullable();
            $table->string('SEKTOR_EKONOMI')->nullable();
            $table->date('TANGGAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
