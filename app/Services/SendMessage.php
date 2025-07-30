<?php

namespace App\Services;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable; // Import Throwable untuk menangkap semua jenis error

class SendMessage {

    public function __invoke()
    {
         // Ambil URL API dan API Key dari konfigurasi (lebih aman)
         $apiUrl = config('services.whatsapp_sender.url');
         $apiKey = config('services.whatsapp_sender.api_key');

         if (!$apiUrl || !$apiKey) {
            //  $this->error('WhatsApp API URL or API Key is not configured. Please check config/services.php and your .env file.');
             Log::channel('scheduler')->error('WhatsApp API URL or API Key not configured for scheduled send.');
             return Command::FAILURE; // Menandakan command gagal
         }

         $payload = [
             "api_key" => $apiKey,
             "sender"  => "6282381002236", // Anda bisa membuat ini dinamis jika perlu
             "number"  => "6282169146904", // atau mengambil dari database
             "message" => "Hello World - Pesan Terjadwal Otomatis",
             "footer"  => "Sent via mpwa (Otomatis)"
         ];

         try {
            //  $this->info('Attempting to send scheduled WhatsApp message...');
             $response = Http::timeout(30)->post($apiUrl, $payload); // Timeout 30 detik

             if ($response->successful()) {
                //  $this->info('Scheduled WhatsApp message sent successfully!');
                 Log::channel('scheduler')->info('Scheduled WhatsApp message sent successfully.', ['response_body' => $response->json()]);
                 return Command::SUCCESS; // Menandakan command sukses
             } else {
                //  $this->error('Failed to send scheduled WhatsApp message. API responded with status: ' . $response->status());
                 Log::channel('scheduler')->error('Failed to send scheduled WhatsApp message.', [
                     'status' => $response->status(),
                     'body'   => $response->body(),
                     'payload_sent' => $payload // Log payload untuk debugging
                 ]);
                 return Command::FAILURE;
             }
         } catch (Throwable $e) { // Menangkap semua jenis error (ConnectionException, RequestException, dll.)
            //  $this->error('An error occurred while sending the message: ' . $e->getMessage());
             Log::channel('scheduler')->critical('Exception during scheduled WhatsApp message sending.', [
                 'error_message' => $e->getMessage(),
                 'payload_sent' => $payload,
                 'trace' => $e->getTraceAsString() // Untuk debugging lebih detail jika perlu
             ]);
             return Command::FAILURE;
         }
    }


}
