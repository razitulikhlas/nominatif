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
        Schema::create('tbL_surat', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nomor_rekening')->unique();
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->integer('jenis_surat');
            $table->float('tunggakan_pokok');
            $table->float('tunggakan_bunga');
            $table->float('denda_pokok');
            $table->float('denda_bunga');
            $table->timestamps();
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
