<?php

namespace App\Livewire;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use App\Services\CartService;
use App\Services\ClaudeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

/**
 * AI Itinerary Builder.
 *
 * User memilih wilayah + tanggal + durasi + jumlah orang + minat, lalu Claude
 * menyusun rencana per hari yang HANYA mereferensikan produk nyata (destinasi,
 * hotel, paket tour) dari database. Hasilnya bisa di-toggle per item dan
 * ditambahkan sekaligus ke keranjang untuk langsung dibooking.
 */
class ItineraryBuilder extends Component
{
    // ── Input form ────────────────────────────────────────────────
    public $region    = '';
    public $startDate;
    public $days      = 3;
    public $pax       = 2;
    public $budget;
    public array $interests = [];

    // ── State hasil ───────────────────────────────────────────────
    public ?array $plan = null;   // Rencana yang sudah di-resolve ke item nyata
    public array $excluded = [];  // Key item yang di-uncheck: "hotel", "tour-{id}", "dest-{id}"
    public ?string $error = null;

    public const REGIONS = ['Labuan Bajo', 'Flores', 'Sumba', 'Timor', 'Rote', 'Alor', 'Komodo'];

    public const INTERESTS = [
        'Alam'        => 'fa-leaf',
        'Pantai'      => 'fa-umbrella-beach',
        'Budaya'      => 'fa-torii-gate',
        'Petualangan' => 'fa-hiking',
        'Relaksasi'   => 'fa-spa',
        'Kuliner'     => 'fa-utensils',
    ];

    public function mount(): void
    {
        $this->startDate = Carbon::tomorrow()->toDateString();
    }

    public function toggleInterest(string $interest): void
    {
        if (in_array($interest, $this->interests, true)) {
            $this->interests = array_values(array_filter($this->interests, fn ($i) => $i !== $interest));
        } else {
            $this->interests[] = $interest;
        }
    }

    /**
     * Panggil AI untuk menyusun itinerary, lalu resolve ID → item nyata dari DB.
     */
    public function generate(ClaudeService $claude): void
    {
        $this->validate([
            'region'    => 'nullable|string',
            'startDate' => 'required|date|after_or_equal:today',
            'days'      => 'required|integer|min:1|max:10',
            'pax'       => 'required|integer|min:1|max:20',
            'budget'    => 'nullable|numeric|min:0',
            'interests' => 'array',
        ], [], [
            'startDate' => 'tanggal mulai',
            'days'      => 'jumlah hari',
            'pax'       => 'jumlah orang',
        ]);

        // Batasi pemanggilan AI agar tidak disalahgunakan (biaya API).
        $rateKey = 'itinerary-builder:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $this->error = 'Terlalu banyak permintaan. Silakan tunggu sebentar lalu coba lagi.';
            return;
        }
        RateLimiter::hit($rateKey, 60);

        $this->error = null;
        $this->plan  = null;
        $this->excluded = [];

        // Kumpulkan katalog (dibiaskan ke wilayah bila ada kecocokan cukup).
        [$destinations, $hotels, $tours] = $this->buildCatalog();

        if ($destinations->isEmpty()) {
            $this->error = 'Belum ada destinasi yang bisa direncanakan untuk wilayah ini.';
            return;
        }

        $raw = $claude->generateItinerary([
            'region'    => $this->region,
            'days'      => $this->days,
            'pax'       => $this->pax,
            'budget'    => $this->budget,
            'interests' => $this->interests,
        ], $this->catalogBlock($destinations, $hotels, $tours));

        if (!$raw || empty($raw['days'])) {
            $this->error = 'Maaf, AI belum bisa menyusun rencana saat ini. Silakan coba lagi sebentar.';
            return;
        }

        $this->plan = $this->resolvePlan($raw, $destinations, $hotels, $tours);

        if (empty($this->plan['days'])) {
            $this->error = 'AI mengembalikan rencana yang tidak valid. Silakan coba lagi.';
            $this->plan  = null;
        }
    }

    public function startOver(): void
    {
        $this->plan = null;
        $this->error = null;
        $this->excluded = [];
    }

    public function toggle(string $key): void
    {
        if (in_array($key, $this->excluded, true)) {
            $this->excluded = array_values(array_filter($this->excluded, fn ($k) => $k !== $key));
        } else {
            $this->excluded[] = $key;
        }
    }

    public function isIncluded(string $key): bool
    {
        return !in_array($key, $this->excluded, true);
    }

    /**
     * Tambahkan seluruh item terpilih ke keranjang, lalu arahkan ke halaman keranjang.
     */
    public function addToCart(CartService $cart)
    {
        if (!auth()->check()) {
            return redirect()->to(route('login', ['redirect' => route('ai.itinerary')]));
        }

        if (!$this->plan) {
            return;
        }

        $added   = 0;
        $skipped = [];

        // 1) Hotel (satu menginap untuk seluruh trip)
        $hotel = $this->plan['hotel'] ?? null;
        if ($hotel && $this->isIncluded('hotel')) {
            $model   = Hotel::find($hotel['id']);
            $checkIn = Carbon::parse($hotel['check_in']);
            $checkOut = Carbon::parse($hotel['check_out']);

            if ($model && $model->isRoomAvailable($hotel['room_type'], $checkIn, $checkOut)) {
                $cart->add(Hotel::class, $model->id, [
                    'label'          => $model->name,
                    'room_type'      => $hotel['room_type'],
                    'check_in_date'  => $checkIn->toDateString(),
                    'check_out_date' => $checkOut->toDateString(),
                    'quantity'       => $hotel['nights'],
                    'unit_price'     => (float) $hotel['unit_price'],
                ]);
                $added++;
            } else {
                $skipped[] = $hotel['name'] . ' (kamar tidak tersedia)';
            }
        }

        // 2) Destinasi (unik — tiket per orang, tanggal sesuai hari kunjungan)
        foreach (($this->plan['destinations'] ?? []) as $dest) {
            if (!$this->isIncluded('dest-' . $dest['id'])) {
                continue;
            }
            $cart->add(Destination::class, $dest['id'], [
                'label'        => $dest['name'],
                'booking_date' => $dest['date'],
                'quantity'     => (int) $this->pax,
                'unit_price'   => (float) $dest['unit_price'],
            ]);
            $added++;
        }

        // 3) Paket tour (per orang, mulai tanggal keberangkatan)
        foreach (($this->plan['tours'] ?? []) as $tour) {
            if (!$this->isIncluded('tour-' . $tour['id'])) {
                continue;
            }
            $cart->add(TourPackage::class, $tour['id'], [
                'label'        => $tour['name'],
                'booking_date' => $this->startDate,
                'quantity'     => (int) $this->pax,
                'unit_price'   => (float) $tour['unit_price'],
            ]);
            $added++;
        }

        if ($added === 0) {
            $this->error = 'Tidak ada item terpilih untuk ditambahkan ke keranjang.';
            return;
        }

        $msg = "{$added} item dari rencana perjalananmu ditambahkan ke keranjang.";
        if ($skipped) {
            $msg .= ' Dilewati: ' . implode(', ', $skipped) . '.';
        }
        session()->flash('success', $msg);

        return redirect()->route('cart.index');
    }

    // ══════════════════════════════════════════════════════════════
    // Helper internal
    // ══════════════════════════════════════════════════════════════

    /**
     * Ambil katalog produk. Bila wilayah dipilih & menghasilkan cukup destinasi,
     * katalog dibiaskan ke wilayah itu; jika tidak, pakai katalog penuh (fallback).
     *
     * @return array{0:\Illuminate\Support\Collection,1:\Illuminate\Support\Collection,2:\Illuminate\Support\Collection}
     */
    private function buildCatalog(): array
    {
        $region = trim((string) $this->region);

        // Katalog destinasi mengikuti apa yang benar-benar tampil publik (tanpa filter
        // status 'active' — enum status destinasi tidak memakai nilai itu).
        $destQuery = Destination::query();
        if ($region !== '') {
            $regional = (clone $destQuery)->where('location', 'like', "%{$region}%");
            if ($regional->count() >= 3) {
                $destQuery = $regional;
            }
        }
        $destinations = $destQuery->orderByDesc('rating')->take(40)->get();

        $hotelQuery = Hotel::query();
        if ($region !== '') {
            $regionalHotels = (clone $hotelQuery)->where('location', 'like', "%{$region}%");
            if ($regionalHotels->count() >= 1) {
                $hotelQuery = $regionalHotels;
            }
        }
        $hotels = $hotelQuery->orderByDesc('id')->take(25)->get();

        $tourQuery = TourPackage::query();
        if ($region !== '') {
            $regionalTours = (clone $tourQuery)->where('location', 'like', "%{$region}%");
            if ($regionalTours->count() >= 1) {
                $tourQuery = $regionalTours;
            }
        }
        $tours = $tourQuery->orderByDesc('rating')->take(20)->get();

        return [$destinations, $hotels, $tours];
    }

    private function catalogBlock($destinations, $hotels, $tours): string
    {
        $lines = ['KATALOG PRODUK (gunakan ID persis seperti tertulis):', '', 'DESTINASI:'];

        foreach ($destinations as $d) {
            $lines[] = "- ID:{$d->id} | {$d->name} | {$d->location} | kategori {$d->category} | Rp"
                . number_format($d->price, 0, ',', '.');
        }

        $lines[] = '';
        $lines[] = 'HOTEL:';
        foreach ($hotels as $h) {
            $prices = array_filter([$h->single_room_price, $h->double_room_price, $h->family_room_price]);
            if (empty($prices)) {
                continue;
            }
            $lines[] = "- ID:{$h->id} | {$h->name} | {$h->location} | mulai Rp"
                . number_format(min($prices), 0, ',', '.') . '/malam';
        }

        $lines[] = '';
        $lines[] = 'PAKET TOUR:';
        foreach ($tours as $t) {
            $bundle = $t->includes_hotel ? ' (termasuk hotel)' : '';
            $lines[] = "- ID:{$t->id} | {$t->name} | {$t->location} | {$t->days} hari | Rp"
                . number_format($t->price, 0, ',', '.') . $bundle;
        }

        return implode("\n", $lines);
    }

    /**
     * Petakan JSON dari AI ke struktur berisi item nyata + harga, sambil
     * membuang ID yang tidak ada di katalog (safety terhadap halusinasi).
     */
    private function resolvePlan(array $raw, $destinations, $hotels, $tours): array
    {
        $destById  = $destinations->keyBy('id');
        $hotelById = $hotels->keyBy('id');
        $tourById  = $tours->keyBy('id');

        $start = Carbon::parse($this->startDate);
        $usedDestinations = [];

        // ── Hari-hari ──
        $days = [];
        foreach ($raw['days'] as $index => $day) {
            $dayNumber = (int) ($day['day'] ?? $index + 1);
            $date = $start->copy()->addDays($dayNumber - 1);

            $dayDestinations = [];
            foreach (($day['destination_ids'] ?? []) as $id) {
                $d = $destById->get((int) $id);
                if (!$d) {
                    continue;
                }
                $unit = $this->destUnitPrice($d);
                $entry = [
                    'id'         => $d->id,
                    'name'       => $d->name,
                    'location'   => $d->location,
                    'category'   => $d->category,
                    'image'      => $d->image,
                    'unit_price' => $unit,
                    'date'       => $date->toDateString(),
                ];
                $dayDestinations[] = $entry;

                // Simpan versi unik (untuk keranjang) — sekali per destinasi.
                if (!isset($usedDestinations[$d->id])) {
                    $usedDestinations[$d->id] = $entry;
                }
            }

            $days[] = [
                'day'          => $dayNumber,
                'date'         => $date->toDateString(),
                'date_label'   => $date->translatedFormat('l, d M Y'),
                'theme'        => (string) ($day['theme'] ?? 'Eksplorasi'),
                'activities'   => array_values(array_filter((array) ($day['activities'] ?? []))),
                'food'         => (string) ($day['food'] ?? ''),
                'destinations' => $dayDestinations,
            ];
        }

        // ── Hotel utama ──
        $hotel = null;
        $hotelModel = isset($raw['hotel_id']) ? $hotelById->get((int) $raw['hotel_id']) : null;
        if ($hotelModel) {
            $roomType = $this->pickRoomType($hotelModel, (int) $this->pax);
            if ($roomType) {
                $nights   = max((int) $this->days - 1, 1);
                $unit     = $this->hotelUnitPrice($hotelModel, $roomType);
                $checkIn  = $start->copy();
                $checkOut = $start->copy()->addDays($nights);

                $hotel = [
                    'id'         => $hotelModel->id,
                    'name'       => $hotelModel->name,
                    'location'   => $hotelModel->location,
                    'image'      => $hotelModel->image,
                    'room_type'  => $roomType,
                    'nights'     => $nights,
                    'unit_price' => $unit,
                    'subtotal'   => $unit * $nights,
                    'check_in'   => $checkIn->toDateString(),
                    'check_out'  => $checkOut->toDateString(),
                ];
            }
        }

        // ── Paket tour ──
        $tourList = [];
        foreach (($raw['tour_ids'] ?? []) as $id) {
            $t = $tourById->get((int) $id);
            if (!$t) {
                continue;
            }
            $tourList[] = [
                'id'         => $t->id,
                'name'       => $t->name,
                'location'   => $t->location,
                'days'       => $t->days,
                'image'      => $t->thumbnail,
                'unit_price' => (float) $t->price,
                'subtotal'   => (float) $t->price * (int) $this->pax,
            ];
        }

        return [
            'title'        => (string) ($raw['title'] ?? 'Rencana Perjalanan NTT'),
            'summary'      => (string) ($raw['summary'] ?? ''),
            'tips'         => array_values(array_filter((array) ($raw['tips'] ?? []))),
            'days'         => $days,
            'hotel'        => $hotel,
            'destinations' => array_values($usedDestinations),
            'tours'        => $tourList,
        ];
    }

    private function destUnitPrice(Destination $d): float
    {
        return (float) ($d->isOnFlashSale() ? $d->flash_sale_price : $d->price);
    }

    /** Pilih tipe kamar sesuai jumlah orang, jatuh ke tipe lain bila harga kosong. */
    private function pickRoomType(Hotel $hotel, int $pax): ?string
    {
        $preferred = $pax <= 1 ? 'single' : ($pax <= 2 ? 'double' : 'family');
        $order = array_values(array_unique([$preferred, 'double', 'family', 'single']));

        foreach ($order as $type) {
            if (($hotel->{$type . '_room_price'} ?? 0) > 0) {
                return $type;
            }
        }

        return null;
    }

    private function hotelUnitPrice(Hotel $hotel, string $roomType): float
    {
        $base = (float) ($hotel->{$roomType . '_room_price'} ?? 0);

        return (float) ($hotel->flashSalePrice($base) ?? $base);
    }

    /** Total perkiraan biaya dari item yang masih tercentang. */
    public function getEstimatedTotalProperty(): float
    {
        if (!$this->plan) {
            return 0;
        }

        $total = 0;

        if (($h = $this->plan['hotel'] ?? null) && $this->isIncluded('hotel')) {
            $total += $h['subtotal'];
        }

        foreach (($this->plan['destinations'] ?? []) as $d) {
            if ($this->isIncluded('dest-' . $d['id'])) {
                $total += $d['unit_price'] * (int) $this->pax;
            }
        }

        foreach (($this->plan['tours'] ?? []) as $t) {
            if ($this->isIncluded('tour-' . $t['id'])) {
                $total += $t['subtotal'];
            }
        }

        return $total;
    }

    public function render()
    {
        return view('livewire.itinerary-builder');
    }
}
