<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWhatsAppTunggakan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    /**
     * Create a new job instance.
     *
     * @param array $payload
     * @return void
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $apiUrl = config('services.whatsapp_sender.url');

        try {
            $response = Http::timeout(30)->post("https://app.wapanels.com/api/create-message", $this->payload);

            if (!$response->successful()) {
                Log::channel('scheduler')->error('Failed to send queued WhatsApp message.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'payload_sent' => $this->payload
                ]);
            }
        } catch (Throwable $e) {
            Log::channel('scheduler')->critical('Exception during queued WhatsApp message sending.', [
                'error_message' => $e->getMessage(),
                'payload_sent' => $this->payload,
            ]);
        }
    }
}
