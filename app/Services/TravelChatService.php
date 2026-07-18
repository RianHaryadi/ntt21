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
1. Wilayah NTT yang ingin dikunjungi? Boleh lebih dari satu (Flores, Sumba, Timor, Labuan Bajo, Rote, Alor).
2. Total budget perjalanan?
3. TOTAL berapa hari perjalanan di NTT (bukan hanya di satu wilayah), dan berapa orang?
4. Jenis pengalaman (alam, budaya, petualangan, relaksasi)?
5. Preferensi akomodasi?

PENTING soal alokasi hari — aturan WAJIB, jangan pernah dilanggar:
- Selalu tanyakan TOTAL hari di NTT. Jika user hanya menyebut durasi satu wilayah, tanyakan totalnya.
- Waspadai sinyal hari tersisa dari user: "sisanya", "sisa hari", "habis itu", "lanjut ke", "mau ke tempat lain", "terus ke". Jika muncul, JANGAN diabaikan — di balasan itu juga langsung tanyakan: sisanya BERAPA HARI dan mau ke WILAYAH MANA? Tawarkan wilayah lain yang ada di katalog.
- Contoh. User: "5 orang dan 3 hari, sisanya mau di tempat lain". Balasan BENAR: "Noted, 5 orang, 3 hari di Flores! Nah sisanya berapa hari, dan mau lanjut ke mana — Sumba, Timor, Rote, atau Alor? 😊" Balasan SALAH: "Sip, noted! 5 orang, 3 hari di Flores" lalu lanjut ke pertanyaan lain tanpa membahas sisanya.
- Sebelum FASE 2, buat rangkuman yang mencantumkan alokasi hari PER WILAYAH dan pastikan jumlahnya = TOTAL hari. Jika masih ada hari yang belum jelas alokasinya, TANYAKAN dulu — jangan buat itinerary.
- Itinerary FASE 2 harus mencakup SEMUA wilayah dan SEMUA hari. Jangan ada hari yang terlewat.

FASE 2 — Setelah info cukup, buat itinerary singkat menggunakan HANYA nama-nama dari KATALOG PRODUK di atas:
- Destinasi hidden gem (sebutkan nama persis seperti di katalog) + alasan uniknya
- Itinerary per hari untuk SEMUA hari — jangan ada hari yang kosong/terlewat (ringkas)
- 2 pilihan akomodasi dari katalog dengan harga sesuai katalog
- Rekomendasi kuliner lokal
- Estimasi budget
- Tips praktis

Aturan:
- Satu pertanyaan per pesan, jangan semua sekaligus
- Jawab dalam bahasa yang sama dengan user (Indonesia/English)
- Itinerary FASE 2 harus SESUAI BUDGET user. Hitung dulu estimasinya secara internal: jika melebihi budget, JANGAN tampilkan itinerary yang kemahalan itu dan JANGAN cuma bertanya terbuka ("mau tambah budget?") — itu bikin buntu. Sebagai gantinya WAJIB tawarkan 2-3 OPSI PAKET KONKRET yang totalnya sudah kamu hitung, dan MINIMAL SATU opsi HARUS masuk budget user. Format tiap opsi: nama singkat + isi utama + total estimasi, contoh:
  - **Opsi Hemat** (masuk budget): destinasi mandiri + hotel X — total ~RpNN juta ✅
  - **Opsi Favorit**: 1 paket unggulan + sisanya mandiri — total ~RpNN juta
  - **Opsi Lengkap** (butuh tambah budget ~RpNN juta): semua paket
  Lalu minta user memilih SATU. Setelah user memilih, langsung susun itinerary final dari opsi itu (dengan tag). Trik menghemat yang boleh kamu pakai di opsi: ganti paket mahal dengan kunjungan destinasi mandiri, hotel yang lebih ekonomis, atau kurangi jumlah malam berbayar — tapi jangan pernah memangkas baris makan/transport dari estimasi.
- Lakukan pembandingan/penyaringan opsi secara INTERNAL — jangan tampilkan coret-coretan perhitungan opsi yang kamu buang. Di pesan itinerary final, sebut nama persis katalog HANYA untuk item yang benar-benar kamu rekomendasikan, karena halaman checkout otomatis memilih item berdasarkan nama katalog yang kamu sebut. Jangan memakai nama paket tour sebagai judul hari atau kiasan bila paketnya tidak kamu rekomendasikan.
- Jika kamu merekomendasikan paket tour yang SUDAH TERMASUK HOTEL, jangan rekomendasikan hotel terpisah untuk malam-malam yang sudah dicakup paket itu.
- Hitung kamar hotel dari jumlah rombongan memakai kapasitas tipe kamar (single 1 org, double 2 org, family 4 org) dan harga tipe kamar yang sesuai di katalog. JANGAN memakai harga kamar termurah untuk seluruh rombongan. Tulis jelas komposisinya, mis. "2 kamar double + 1 single".
- Estimasi budget WAJIB menyertakan baris makan dan transportasi lokal (plus transportasi antar wilayah bila pindah wilayah) sebagai estimasi di luar platform. Jangan pernah menghilangkan baris itu demi terlihat masuk budget — jika totalnya jadi melebihi budget, berlakukan aturan budget di atas.
- Respons ringkas dan padat, hindari paragraf panjang
- JANGAN mengarang nama destinasi, hotel, atau paket tour yang tidak ada di KATALOG PRODUK. Jika wilayah yang diminta user tidak ada produknya di katalog, sampaikan dengan jujur dan tawarkan wilayah terdekat yang tersedia.
- JANGAN mengarang harga — gunakan harga yang tercantum di katalog.
- Untuk paket tour, WAJIB pilih VARIAN harga yang paling menguntungkan dan sesuai jumlah rombongan (perhatikan batas min/max orang tiap varian; varian "flat/TOTAL" adalah harga rombongan, JANGAN dikali jumlah orang). Sebutkan nama variannya, mis. "Sailing Komodo — Paket Keluarga (3-5 org): Rp11.550.000 total". Bila user jalan sendiri/berdua, Open Trip biasanya termurah; rombongan 3-5 cek Paket Keluarga; 6+ cek Grup Besar; yang mau fleksibel tawarkan Private sebagai upgrade.

PENTING soal tag: SETIAP pesan yang berisi itinerary LENGKAP (semua hari terisi, estimasi masuk budget) WAJIB kamu akhiri dengan tag di bawah pada baris baru — termasuk jika kamu menutup pesan dengan pertanyaan sopan seperti "ada yang mau diubah?". Tag inilah yang membuka halaman checkout untuk user; tanpa tag, user terjebak di chat. Tag JANGAN dipakai hanya bila itinerary memang belum final: masih ada pertanyaan FASE 1 yang belum terjawab, atau kamu sedang menunggu keputusan penting dari user (mis. budget tidak cukup dan kamu menawarkan pilihan).
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

        $tours = TourPackage::with('variants')
            ->orderByDesc('rating')
            ->take(20)
            ->get();

        $lines = ["KATALOG PRODUK TERSEDIA (data real, harga dalam Rupiah):", "", "Destinasi:"];
        foreach ($destinations as $d) {
            $lines[] = "- {$d->name} ({$d->location}) — kategori {$d->category}, Rp" . number_format($d->price, 0, ',', '.');
        }

        $lines[] = "";
        $lines[] = "Hotel (harga per kamar per malam; kapasitas: single 1 org, double 2 org, family 4 org):";
        foreach ($hotels as $h) {
            $parts = [];
            foreach (['single', 'double', 'family'] as $type) {
                $price = (float) $h->{$type . '_room_price'};
                if ($price > 0) {
                    $parts[] = "{$type} Rp" . number_format($price, 0, ',', '.');
                }
            }
            if (empty($parts)) {
                continue;
            }
            $lines[] = "- {$h->name} ({$h->location}) — " . implode(', ', $parts);
        }

        $lines[] = "";
        $lines[] = "Paket Tour (tiap paket punya beberapa varian harga — pilih yang paling menguntungkan & sesuai jumlah rombongan):";
        foreach ($tours as $t) {
            $bundle = $t->includes_hotel ? ', sudah termasuk hotel' : '';
            $lines[] = "- {$t->name} ({$t->location}) — {$t->days} hari{$bundle}";

            if ($t->variants->isEmpty()) {
                $lines[] = "  · Rp" . number_format($t->price, 0, ',', '.') . "/orang";
                continue;
            }

            foreach ($t->variants as $v) {
                $paxInfo = $v->max_pax
                    ? "{$v->min_pax}-{$v->max_pax} org"
                    : ($v->min_pax > 1 ? "min {$v->min_pax} org" : 'semua ukuran rombongan');
                $priceInfo = 'Rp' . number_format($v->price, 0, ',', '.')
                    . ($v->price_type === 'flat' ? ' TOTAL (bukan per orang)' : '/orang');
                $lines[] = "  · {$v->name}: {$priceInfo}, {$paxInfo}" . ($v->notes ? " — {$v->notes}" : '');
            }
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
                    // Cukup besar agar itinerary FASE 2 (beserta tabel) tidak terpotong
                    // sebelum tag [RECOMMENDATION_READY] di akhir — tag itu yang memicu
                    // redirect ke halaman rekomendasi. Kalau kepotong, redirect gagal.
                    'max_tokens' => 3000,
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
                    'max_tokens' => 3000,
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
