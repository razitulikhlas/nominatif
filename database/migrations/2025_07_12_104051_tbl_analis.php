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
        Schema::create('tbL_analis', function (Blueprint $table) {
            $table->id()->primary();
            $table->integer('id_cabang');
            $table->string('kode_analis',10);
            $table->string('nama_analis',20);
            $table->string('nohp',20); // Tambahkan kolom status dengan default 'Aktif'
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
