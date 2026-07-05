<?php

namespace App\Services;

use App\Models\TravelChatSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TravelChatService
{
    private string $systemPrompt = <<<PROMPT
Kamu adalah Ara, pemandu wisata NTT (Nusa Tenggara Timur) yang ramah dan antusias. Tugasmu membantu merencanakan perjalanan ke hidden gems NTT dengan tanya jawab santai — SATU pertanyaan per pesan saja.

FASE 1 — Tanya satu per satu:
1. Wilayah NTT yang ingin dikunjungi (Flores, Sumba, Timor, Labuan Bajo, Rote, Alor)?
2. Total budget perjalanan?
3. Berapa orang dan berapa hari?
4. Jenis pengalaman (alam, budaya, petualangan, relaksasi)?
5. Preferensi akomodasi?

FASE 2 — Setelah info cukup, buat itinerary singkat:
- Destinasi hidden gem + alasan uniknya
- Itinerary per hari (ringkas)
- 2 pilihan akomodasi dengan estimasi harga
- Rekomendasi kuliner lokal
- Estimasi budget
- Tips praktis

Aturan:
- Satu pertanyaan per pesan, jangan semua sekaligus
- Jawab dalam bahasa yang sama dengan user (Indonesia/English)
- Jika budget terlalu kecil, sarankan alternatif dengan ramah
- Respons ringkas dan padat, hindari paragraf panjang

PENTING: Saat itinerary lengkap selesai, akhiri dengan tag ini di baris baru:
[RECOMMENDATION_READY]
PROMPT;

    public function send(TravelChatSession $session, string $userMessage): array
    {
        // Simpan pesan user
        $session->messages()->create([
            'role' => 'user',
            'content' => $userMessage,
        ]);

        $apiKey          = config('services.anthropic.key');
        $apiUrl          = config('services.anthropic.url', 'https://api.anthropic.com/v1/messages');
        $model           = config('services.anthropic.model', 'claude-3-5-sonnet-20241022');
        $useOpenAiFormat = config('services.anthropic.use_openai_format', false);

        if (!$useOpenAiFormat && (empty($apiKey) || $apiKey === 'your_anthropic_api_key_here')) {
            $errorMsg = "Halo! Maaf sekali, asisten perjalanan AI Pesona NTTbelum dapat merespons karena API Key Anthropic belum diatur di server. Silakan hubungi admin proyek untuk mengisi ANTHROPIC_API_KEY di file .env.";
            
            $session->messages()->create([
                'role' => 'assistant',
                'content' => $errorMsg,
            ]);

            return [
                'message' => $errorMsg,
                'is_ready' => false,
            ];
        }

        // Ambil seluruh riwayat untuk dikirim ke API
        $history = $session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])->toArray();

        try {
            if ($useOpenAiFormat) {
                // Format OpenAI Chat Completions (digunakan oleh 9Router)
                $messages = [];
                $messages[] = [
                    'role' => 'system',
                    'content' => $this->systemPrompt
                ];

                foreach ($history as $msg) {
                    $messages[] = [
                        'role' => $msg['role'],
                        'content' => $msg['content']
                    ];
                }

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(60)->post($apiUrl, [
                    'model'      => $model,
                    'messages'   => $messages,
                    'max_tokens' => 1024,
                    'stream'     => false,
                ]);

                if ($response->failed()) {
                    Log::error('9Router OpenAI API error response', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    $errorMsg = "Maaf, terjadi kendala teknis saat memproses tanggapan dari AI (9Router). Silakan coba kirim ulang pesan Anda.";
                    $session->messages()->create([
                        'role' => 'assistant',
                        'content' => $errorMsg,
                    ]);

                    return [
                        'message' => $errorMsg,
                        'is_ready' => false,
                    ];
                }

                $botMessage = $response->json('choices.0.message.content');

            } else {
                // Format asli Anthropic Messages
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($apiUrl, [
                    'model' => $model,
                    'max_tokens' => 2000,
                    'system' => $this->systemPrompt,
                    'messages' => $history,
                ]);

                if ($response->failed()) {
                    Log::error('Anthropic API error response', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    $errorMsg = "Maaf, terjadi kendala teknis saat memproses tanggapan dari AI. Silakan coba kirim ulang pesan Anda.";
                    $session->messages()->create([
                        'role' => 'assistant',
                        'content' => $errorMsg,
                    ]);

                    return [
                        'message' => $errorMsg,
                        'is_ready' => false,
                    ];
                }

                $botMessage = $response->json('content.0.text');
            }

            if (empty($botMessage)) {
                $botMessage = "Maaf, saya tidak menerima respons yang valid dari asisten AI. Silakan coba beberapa saat lagi.";
            }

            // Cek apakah rekomendasi sudah siap
            $isReady = str_contains($botMessage, '[RECOMMENDATION_READY]');
            $cleanMessage = trim(str_replace('[RECOMMENDATION_READY]', '', $botMessage));

            // Simpan pesan bot
            $session->messages()->create([
                'role' => 'assistant',
                'content' => $cleanMessage,
            ]);

            if ($isReady) {
                $session->update([
                    'status' => 'completed',
                    'recommendation_raw' => $cleanMessage,
                ]);
            }

            return [
                'message' => $cleanMessage,
                'is_ready' => $isReady,
            ];

        } catch (\Exception $e) {
            Log::error('TravelChatService Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMsg = "Maaf, terjadi kesalahan koneksi jaringan. Silakan periksa koneksi internet Anda dan coba lagi.";
            $session->messages()->create([
                'role' => 'assistant',
                'content' => $errorMsg,
            ]);

            return [
                'message' => $errorMsg,
                'is_ready' => false,
            ];
        }
    }
}
