<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use App\Models\TravelChatSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TravelChatService
{
    /**
     * Bangun system prompt dengan katalog produk aktif disisipkan sebagai konteks,
     * agar itinerary yang dibuat AI hanya menyebut destinasi/hotel/tour yang benar-benar
     * ada dan bisa langsung dibooking — bukan hasil karangan model (grounding).
     */
    private function buildSystemPrompt(): string
    {
        $catalog = $this->buildCatalogBlock();

        return <<<PROMPT
Kamu adalah Ara, pemandu wisata NTT (Nusa Tenggara Timur) yang ramah dan antusias. Tugasmu membantu merencanakan perjalanan ke hidden gems NTT dengan tanya jawab santai — SATU pertanyaan per pesan saja.

{$catalog}

FASE 1 — Tanya satu per satu:
1. Wilayah NTT yang ingin dikunjungi (Flores, Sumba, Timor, Labuan Bajo, Rote, Alor)?
2. Total budget perjalanan?
3. Berapa orang dan berapa hari?
4. Jenis pengalaman (alam, budaya, petualangan, relaksasi)?
5. Preferensi akomodasi?

FASE 2 — Setelah info cukup, buat itinerary singkat menggunakan HANYA nama-nama dari KATALOG PRODUK di atas:
- Destinasi hidden gem (sebutkan nama persis seperti di katalog) + alasan uniknya
- Itinerary per hari (ringkas)
- 2 pilihan akomodasi dari katalog dengan harga sesuai katalog
- Rekomendasi kuliner lokal
- Estimasi budget
- Tips praktis

Aturan:
- Satu pertanyaan per pesan, jangan semua sekaligus
- Jawab dalam bahasa yang sama dengan user (Indonesia/English)
- Jika budget terlalu kecil, sarankan alternatif dengan ramah
- Respons ringkas dan padat, hindari paragraf panjang
- JANGAN mengarang nama destinasi, hotel, atau paket tour yang tidak ada di KATALOG PRODUK. Jika wilayah yang diminta user tidak ada produknya di katalog, sampaikan dengan jujur dan tawarkan wilayah terdekat yang tersedia.
- JANGAN mengarang harga — gunakan harga yang tercantum di katalog.

PENTING: Saat itinerary lengkap selesai, akhiri dengan tag ini di baris baru:
[RECOMMENDATION_READY]
PROMPT;
    }

    private function buildCatalogBlock(): string
    {
        $destinations = Destination::orderByDesc('rating')
            ->take(40)
            ->get(['name', 'location', 'category', 'price']);

        $hotels = Hotel::orderByDesc('id')
            ->take(20)
            ->get(['name', 'location', 'single_room_price', 'double_room_price', 'family_room_price']);

        $tours = TourPackage::orderByDesc('rating')
            ->take(20)
            ->get(['name', 'location', 'days', 'price', 'includes_hotel']);

        $lines = ["KATALOG PRODUK TERSEDIA (data real, harga dalam Rupiah):", "", "Destinasi:"];
        foreach ($destinations as $d) {
            $lines[] = "- {$d->name} ({$d->location}) — kategori {$d->category}, Rp" . number_format($d->price, 0, ',', '.');
        }

        $lines[] = "";
        $lines[] = "Hotel:";
        foreach ($hotels as $h) {
            $prices = array_filter([$h->single_room_price, $h->double_room_price, $h->family_room_price]);
            if (empty($prices)) {
                continue;
            }
            $lines[] = "- {$h->name} ({$h->location}) — mulai Rp" . number_format(min($prices), 0, ',', '.') . "/malam";
        }

        $lines[] = "";
        $lines[] = "Paket Tour:";
        foreach ($tours as $t) {
            $bundle = $t->includes_hotel ? ', sudah termasuk hotel' : '';
            $lines[] = "- {$t->name} ({$t->location}) — {$t->days} hari, Rp" . number_format($t->price, 0, ',', '.') . $bundle;
        }

        return implode("\n", $lines);
    }

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

        $systemPrompt = $this->buildSystemPrompt();

        try {
            if ($useOpenAiFormat) {
                // Format OpenAI Chat Completions (digunakan oleh 9Router)
                $messages = [];
                $messages[] = [
                    'role' => 'system',
                    'content' => $systemPrompt
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
                    'system' => $systemPrompt,
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
