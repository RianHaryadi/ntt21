<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ClaudeService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private bool $openAiFormat;

    public function __construct()
    {
        $this->apiKey       = config('services.anthropic.key', '');
        $this->apiUrl       = config('services.anthropic.url', 'https://api.anthropic.com/v1/messages');
        $this->model        = config('services.anthropic.model', 'claude-3-5-sonnet-20241022');
        $this->openAiFormat = (bool) config('services.anthropic.use_openai_format', false);
    }

    public function ask(string $prompt, int $maxTokens = 1024): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('ClaudeService: API key not set.');
            return null;
        }

        try {
            if ($this->openAiFormat) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(60)->post($this->apiUrl, [
                    'model'      => $this->model,
                    'max_tokens' => $maxTokens,
                    'messages'   => [['role' => 'user', 'content' => $prompt]],
                    'stream'     => false,
                ]);

                if ($response->failed()) {
                    Log::error('ClaudeService API error', ['status' => $response->status(), 'body' => $response->body()]);
                    return null;
                }

                return $response->json('choices.0.message.content');
            }

            // Native Anthropic format
            $response = Http::withHeaders([
                'x-api-key'         => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model'      => $this->model,
                'max_tokens' => $maxTokens,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->failed()) {
                Log::error('ClaudeService API error', ['status' => $response->status(), 'body' => $response->body()]);
                return null;
            }

            return $response->json('content.0.text');
        } catch (\Exception $e) {
            Log::error('ClaudeService exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ringkasan review — di-cache 24 jam per item.
     */
    public function summarizeReviews(string $type, int $id, array $reviews): ?string
    {
        $cacheKey = "ai.review_summary.{$type}.{$id}";

        return Cache::remember($cacheKey, 86400, function () use ($type, $reviews) {
            if (empty($reviews)) return null;

            $reviewText = collect($reviews)
                ->map(fn($r) => "- [{$r['rating']}/5] {$r['body']}")
                ->join("\n");

            $prompt = <<<PROMPT
Kamu adalah AI asisten wisata. Berikut adalah kumpulan ulasan pengunjung tentang sebuah {$type} wisata:

{$reviewText}

Buatkan ringkasan singkat (3-4 kalimat) dalam Bahasa Indonesia yang:
1. Menyebutkan poin-poin positif yang sering disebut
2. Menyebutkan kekurangan yang perlu diperhatikan (jika ada)
3. Kesimpulan apakah tempat ini direkomendasikan

Tulis langsung ringkasannya tanpa intro seperti "Berikut ringkasannya:" atau "Berdasarkan ulasan:".
PROMPT;

            return $this->ask($prompt, 512);
        });
    }

    /**
     * Parse natural language search query → structured filters.
     */
    public function parseSearchQuery(string $query): array
    {
        $prompt = <<<PROMPT
Kamu adalah AI parser. Ekstrak informasi dari query pencarian wisata berikut ke format JSON.

Query: "{$query}"

Kembalikan HANYA JSON valid (tanpa penjelasan, tanpa markdown) dengan struktur:
{
  "category": "Beach|Mountain|Culture|Nature|null",
  "max_price": number_or_null,
  "min_rating": number_or_null,
  "keywords": "string_or_null",
  "location": "string_or_null"
}

Contoh: query "pantai murah di flores rating bagus" → {"category":"Beach","max_price":null,"min_rating":4,"keywords":"murah","location":"flores"}
PROMPT;

        $result = $this->ask($prompt, 256);
        if (!$result) return [];

        // Bersihkan dari markdown code block jika ada
        $result = preg_replace('/```json\s*|\s*```/', '', trim($result));

        return json_decode($result, true) ?? [];
    }

    /**
     * Waktu terbaik mengunjungi destinasi.
     */
    public function bestTimeToVisit(string $destinationName, string $location, string $category): ?string
    {
        $cacheKey = 'ai.best_time.' . md5($destinationName);

        return Cache::remember($cacheKey, 604800, function () use ($destinationName, $location, $category) {
            $prompt = <<<PROMPT
Kamu adalah pakar wisata NTT (Nusa Tenggara Timur), Indonesia.

Berikan rekomendasi waktu terbaik untuk mengunjungi:
- Nama: {$destinationName}
- Lokasi: {$location}
- Kategori: {$category}

Jawab dalam 2-3 kalimat Bahasa Indonesia yang informatif dan praktis.
Sebutkan bulan terbaik dan alasannya (cuaca, musim, kondisi alam, dll).
Jangan gunakan intro seperti "Tentu saja" atau "Berikut adalah".
PROMPT;

            return $this->ask($prompt, 256);
        });
    }

    /**
     * Rekomendasi personal berdasarkan riwayat user.
     */
    public function personalRecommendations(array $history, array $availableDestinations): ?string
    {
        if (empty($history)) return null;

        $historyText = collect($history)
            ->map(fn($h) => "- {$h['name']} ({$h['type']}, kategori: {$h['category']})")
            ->join("\n");

        $destList = collect($availableDestinations)
            ->map(fn($d) => "- ID:{$d['id']} | {$d['name']} | {$d['category']} | {$d['location']}")
            ->join("\n");

        $prompt = <<<PROMPT
Kamu adalah AI rekomendasi wisata NTT. Berdasarkan riwayat perjalanan user berikut:

{$historyText}

Dari daftar destinasi yang tersedia:
{$destList}

Rekomendasikan 3 destinasi yang paling cocok untuk user ini.
Format jawaban (HANYA JSON array, tanpa penjelasan lain):
[{"id": 1, "reason": "alasan singkat 1 kalimat"}, ...]
PROMPT;

        $result = $this->ask($prompt, 512);
        if (!$result) return null;

        $result = preg_replace('/```json\s*|\s*```/', '', trim($result));
        return $result;
    }

    /**
     * Susun itinerary terstruktur (JSON) untuk AI Itinerary Builder.
     *
     * Kunci grounding: prompt menyertakan katalog produk berikut ID-nya, dan model
     * diminta HANYA mereferensikan ID yang ada — sehingga hasilnya bisa langsung
     * dipetakan ke destinasi/hotel/tour nyata yang bisa dibooking (bukan karangan).
     *
     * @param array  $params  ['region','days','pax','budget','interests']
     * @param string $catalog Blok teks katalog produk beserta ID
     * @return array|null      Struktur itinerary hasil decode, atau null jika gagal
     */
    public function generateItinerary(array $params, string $catalog): ?array
    {
        $region    = $params['region'] ?: 'mana pun di NTT';
        $days      = (int) $params['days'];
        $pax       = (int) $params['pax'];
        $budget    = !empty($params['budget'])
            ? 'sekitar Rp' . number_format((float) $params['budget'], 0, ',', '.')
            : 'fleksibel';
        $interests = !empty($params['interests'])
            ? implode(', ', $params['interests'])
            : 'umum';

        $prompt = <<<PROMPT
Kamu adalah perencana perjalanan ahli untuk Nusa Tenggara Timur (NTT).
Susun itinerary {$days} hari untuk {$pax} orang di wilayah {$region}.
Minat wisatawan: {$interests}. Perkiraan budget: {$budget}.

{$catalog}

ATURAN PENTING:
- HANYA gunakan ID produk yang ADA di katalog di atas. JANGAN mengarang ID, nama, atau harga.
- Setiap hari isi dengan 1-3 destinasi yang relevan dengan wilayah & minat wisatawan.
- Pilih SATU hotel utama (hotel_id) sebagai akomodasi selama menginap.
- Pilih 0-2 paket tour yang relevan (tour_ids) hanya bila benar-benar cocok; boleh kosong.
- Usahakan total perkiraan biaya realistis terhadap budget bila memungkinkan.
- Aktivitas & kuliner ditulis ringkas dalam Bahasa Indonesia.

Balas HANYA dengan JSON valid (tanpa markdown, tanpa penjelasan tambahan) dengan struktur PERSIS:
{
  "title": "judul singkat perjalanan",
  "summary": "2-3 kalimat ringkas tentang tema perjalanan",
  "hotel_id": <id hotel dari katalog, atau null>,
  "tour_ids": [<id paket tour dari katalog>],
  "days": [
    {"day": 1, "theme": "tema hari", "destination_ids": [<id destinasi>], "activities": ["aktivitas 1", "aktivitas 2"], "food": "rekomendasi kuliner lokal"}
  ],
  "tips": ["tips praktis 1", "tips praktis 2", "tips praktis 3"]
}
PROMPT;

        $result = $this->ask($prompt, 3000);
        if (!$result) {
            return null;
        }

        // Bersihkan pagar markdown & ambil blok JSON pertama jika model menyisipkan teks lain.
        $result = preg_replace('/```json\s*|\s*```/', '', trim($result));
        if (preg_match('/\{.*\}/s', $result, $m)) {
            $result = $m[0];
        }

        $parsed = json_decode($result, true);

        return is_array($parsed) ? $parsed : null;
    }
}
