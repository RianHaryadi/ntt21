<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [30, 120];

    public function __construct(
        public string $phone,
        public string $message,
    ) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        // Kegagalan kirim (nomor tidak valid, API error) sudah di-log oleh service
        // dan tidak dilempar ulang — retry hanya untuk exception tak terduga.
        $whatsapp->send($this->phone, $this->message);
    }
}
