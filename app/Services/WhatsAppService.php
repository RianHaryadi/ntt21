<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private ?string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /**
     * Kirim pesan WhatsApp via Fonnte.
     * Nomor otomatis dinormalisasi ke format 62xxxxxxxxxx.
     */
    public function send(string $phone, string $message): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('WhatsAppService: token Fonnte belum diatur.');
            return false;
        }

        $target = $this->normalizePhone($phone);
        if (!$target) {
            Log::warning('WhatsAppService: nomor telepon tidak valid.', ['phone' => $phone]);
            return false;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $this->token])
                ->asForm()
                ->timeout(15)
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                ]);

            if ($response->failed() || $response->json('status') === false) {
                Log::error('WhatsAppService: gagal mengirim pesan.', [
                    'phone' => $target,
                    'response' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('WhatsAppService exception: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (empty($digits)) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}
