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
        Schema::create('scheduled_messages', function (Blueprint $table) {
            $table->id();
            $table->string('api_key'); // API Key untuk layanan WhatsApp
            $table->string('sender_number'); // Nomor pengirim WhatsApp
            $table->string('recipient_number'); // Nomor penerima WhatsApp
            $table->text('message_body'); // Isi pesan
            $table->string('footer_text')->nullable(); // Teks footer jika ada
            $table->timestamp('scheduled_at'); // Waktu pengiriman yang dijadwalkan
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable(); // Waktu aktual pengiriman
            $table->text('error_message')->nullable(); // Pesan error jika gagal
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_messages');
    }
};
