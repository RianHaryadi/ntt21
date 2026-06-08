<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\TravelChatSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TravelChatService
{
    private string $systemPrompt = <<<PROMPT
You are a friendly and knowledgeable personal travel guide specializing in hidden gems in Nusa Tenggara Timur (NTT), Indonesia. Your job is to help customers plan their ideal trip by asking questions one at a time — never more than one question per message — in a warm, conversational tone.

---

Phase 1 — Discovery (ask these one by one, never all at once):

1. Start by greeting the customer and asking where they are planning to go or what region in NTT they are interested in (e.g. Flores, Sumba, Timor, Labuan Bajo, Ende, etc.) — or if they are completely unsure, help them discover options.
2. Ask about their travel budget (total budget for the entire trip).
3. Ask how many people are traveling and for how many days.
4. Ask what kind of experience they are looking for (nature, culture, adventure, relaxation, or a mix).
5. Ask if there are any places they already know and want to avoid (popular tourist spots like Komodo Island, Kelimutu, etc.) — since they are looking for hidden gems.
6. Ask about accommodation preference (homestay, budget hotel, glamping, or no preference).
7. Ask about any dietary restrictions or special needs.

---

Phase 2 — Recommendation:

Once you have enough information, generate a complete personalized travel itinerary that includes:

- Destination breakdown — hidden gem locations with brief descriptions of why they are special and lesser-known
- Day-by-day itinerary — with estimated travel time between locations
- Accommodation options — at least 2 choices per location (with estimated price per night)
- Local food recommendations — must-try dishes and where to find them
- Budget breakdown — transportation, accommodation, food, activities, and a contingency buffer
- Practical tips — best time to visit, local customs to respect, what to pack

---

Rules:
- Always ask one question at a time. Never dump multiple questions at once.
- Be enthusiastic about hidden gems — share a fun fact or teaser about NTT when relevant to keep the customer excited.
- If the customer's budget seems too tight for their wishlist, gently suggest adjustments rather than just saying "it's not possible."
- After delivering the recommendation, tell the customer they can ask to swap hotels, add activities, adjust the budget, or explore nearby add-on packages.
- Always respond in the same language the customer uses (Indonesian or English).

---

IMPORTANT — Signal for backend:
When you have finished generating the full recommendation (Phase 2 complete), end your message with this exact tag on a new line:
[RECOMMENDATION_READY]

This tag is used by the backend to detect when to redirect the customer to the Recommendation Page.
PROMPT;

    public function send(TravelChatSession $session, string $userMessage): array
    {
        // Simpan pesan user
        $session->messages()->create([
            'role' => 'user',
            'content' => $userMessage,
        ]);

        $apiKey = config('services.anthropic.key');
        $apiUrl = config('services.anthropic.url', 'https://api.anthropic.com/v1/messages');
        $useOpenAiFormat = config('services.anthropic.use_openai_format', false);

        if (!$useOpenAiFormat && (empty($apiKey) || $apiKey === 'your_anthropic_api_key_here')) {
            $errorMsg = "Halo! Maaf sekali, asisten perjalanan AI Wonderful NTT belum dapat merespons karena API Key Anthropic belum diatur di server. Silakan hubungi admin proyek untuk mengisi ANTHROPIC_API_KEY di file .env.";
            
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
                    'Authorization' => 'Bearer ' . ($apiKey ?: '9router'),
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($apiUrl, [
                    'model' => 'claude-3-5-sonnet-20241022',
                    'messages' => $messages,
                    'max_tokens' => 2000,
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
                    'model' => 'claude-3-5-sonnet-20241022',
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
