<?php

namespace App\Livewire;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use App\Models\TourPackageVariant;
use App\Models\TravelChatSession;
use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class TravelRecommendation extends Component
{
    public TravelChatSession $session;

    // Data dari DB
    public $hotels;
    public $tourPackages;
    public $destinations;

    // Pilihan user
    /** Daftar menginap — bisa lebih dari satu hotel (mis. beda wilayah).
     *  Tiap item: {hotel_id, room_type, rooms, nights}. Tanggalnya berantai
     *  otomatis mulai dari $startDate. */
    public array $stays = [];
    public array $selectedTourIds = [];
    /** Varian harga terpilih per paket tour: [tour_id => variant_id]. */
    public array $tourVariants = [];
    public array $selectedDestinationIds = [];

    // Opsi trip yang bisa diotak-atik
    public $pax = 2;
    public $startDate;

    /** Kapasitas tiap tipe kamar (orang per kamar). */
    public const ROOM_CAPACITY = ['single' => 1, 'double' => 2, 'family' => 4];

    // Catatan & status
    public string $customNotes = '';
    public bool $showSuccess = false;
    public bool $showAllDestinations = false;
    public ?string $cartError = null;

    public function mount(string $token)
    {
        $this->session = TravelChatSession::where('session_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        $raw = strtolower($this->session->recommendation_raw ?? '');

        // Hotel, tour & destinasi yang disebut Ara diprioritaskan di urutan atas,
        // sesuai urutan kemunculannya di teks (yang disebut duluan = paling atas).
        $mentionPos = function ($item) use ($raw) {
            $pos = $this->mentionPosition($raw, $item->name);

            return $pos === false ? PHP_INT_MAX : $pos;
        };
        $this->hotels       = Hotel::all()->sortBy($mentionPos)->values();
        $this->tourPackages = TourPackage::with('variants')->get()->sortBy($mentionPos)->values();
        $this->destinations = Destination::all()->sortBy($mentionPos)->values();

        // Pulihkan pilihan sebelumnya bila ada.
        $edited = $this->session->recommendation_edited ?? [];

        // pax HARUS dipulihkan sebelum stays: saran jumlah kamar dihitung dari pax.
        $this->pax = $edited['pax'] ?? $this->guessPax($raw);

        // Menginap: pulihkan daftar stays; format lama (satu hotel) dikonversi.
        // Pilihan "tanpa hotel" yang pernah disimpan harus dihormati.
        if (array_key_exists('stays', $edited)) {
            $this->stays = $this->sanitizeStays($edited['stays'] ?? []);
        } elseif (array_key_exists('selected_hotel_id', $edited)) {
            $this->stays = $edited['selected_hotel_id']
                ? $this->sanitizeStays([[
                    'hotel_id'  => $edited['selected_hotel_id'],
                    'room_type' => $edited['room_type'] ?? 'double',
                    'rooms'     => $edited['rooms'] ?? 0,
                    'nights'    => $edited['nights'] ?? 0,
                ]])
                : [];
        } else {
            $mentioned = $this->hotels->first(fn ($h) => $this->mentionPosition($raw, $h->name) !== false);
            $this->stays = $mentioned ? [$this->makeStay($mentioned, $this->guessNights($raw))] : [];
        }

        // Tour default: hanya paket yang disebut Ara dengan NAMA PERSIS katalog
        // (kontrak prompt: nama persis = direkomendasikan). Matching longgar cuma
        // untuk sortir/badge — pernah bikin paket Rp9jt ke-preselect gara-gara
        // namanya dipakai sebagai judul hari.
        $this->selectedTourIds = array_key_exists('selected_tour_ids', $edited)
            ? ($edited['selected_tour_ids'] ?? [])
            : $this->tourPackages
                ->filter(fn ($t) => str_contains($raw, strtolower($t->name)))
                ->pluck('id')->values()->all();

        $this->tourVariants = $edited['tour_variants'] ?? [];
        $this->syncTourVariants();

        $this->selectedDestinationIds = $edited['selected_destination_ids'] ?? [];
        $this->customNotes = $edited['custom_notes'] ?? '';

        $savedStart = $edited['start_date'] ?? null;
        $this->startDate = ($savedStart && Carbon::parse($savedStart)->gte(Carbon::today()))
            ? Carbon::parse($savedStart)->toDateString()
            : Carbon::tomorrow()->toDateString();

        // Pra-pilih destinasi yang disebut Ara (jika belum ada pilihan tersimpan).
        if (empty($this->selectedDestinationIds)) {
            $this->selectedDestinationIds = $this->destinations
                ->filter(fn ($d) => $this->mentionPosition($raw, $d->name) !== false)
                ->pluck('id')->take(6)->values()->all();
        }
    }

    /**
     * Posisi kemunculan nama item di teks Ara. AI sering menyebut nama tanpa
     * embel-embel ("Sailing Komodo 3D2N - Phinisi Experience" → "Sailing Komodo"),
     * jadi kalau nama utuh tidak ketemu, subjudul & kode durasi dipangkas dulu.
     */
    private function mentionPosition(string $raw, string $name): int|false
    {
        $name = strtolower(trim($name));
        $pos  = strpos($raw, $name);
        if ($pos !== false) {
            return $pos;
        }

        $base = preg_replace('/\s+[-—–].*$/u', '', $name);
        $base = trim(preg_replace('/\s+\d+d\d+n\b.*$/i', '', $base));

        if ($base !== $name && mb_strlen($base) >= 8) {
            return strpos($raw, $base);
        }

        return false;
    }

    /** Dipakai Blade untuk badge "Ara" pada item yang disebut di teks. */
    public function isMentioned(string $name): bool
    {
        $raw = strtolower($this->session->recommendation_raw ?? '');

        return $this->mentionPosition($raw, $name) !== false;
    }

    // ── Aksi pemilihan ────────────────────────────────────────────
    /** Klik hotel = tambah menginap; klik lagi = hapus. Bisa lebih dari satu hotel. */
    public function toggleHotel(int $hotelId): void
    {
        $existing = collect($this->stays)->search(fn ($s) => (int) $s['hotel_id'] === $hotelId);

        if ($existing !== false) {
            unset($this->stays[$existing]);
            $this->stays = array_values($this->stays);
        } else {
            $hotel = $this->hotels->firstWhere('id', $hotelId);
            if ($hotel) {
                $raw = strtolower($this->session->recommendation_raw ?? '');
                $this->stays[] = $this->makeStay($hotel, empty($this->stays) ? $this->guessNights($raw) : 1);
            }
        }

        $this->persistSelection();
    }

    public function clearStays(): void
    {
        $this->stays = [];
        $this->persistSelection();
    }

    public function toggleTour(int $tourId): void
    {
        if (in_array($tourId, $this->selectedTourIds)) {
            $this->selectedTourIds = array_values(array_filter($this->selectedTourIds, fn ($id) => $id !== $tourId));
        } else {
            $this->selectedTourIds[] = $tourId;
        }
        $this->syncTourVariants();
        $this->persistSelection();
    }

    /** Pilih varian harga sebuah paket tour secara eksplisit. */
    public function selectTourVariant(int $tourId, int $variantId): void
    {
        $tour = $this->tourPackages->firstWhere('id', $tourId);
        $variant = $tour?->variants->firstWhere('id', $variantId);

        if (!$tour || !$variant || !in_array($tourId, $this->selectedTourIds)
            || !$variant->fitsPax(max((int) $this->pax, 1))) {
            return;
        }

        $this->tourVariants[$tourId] = $variant->id;
        $this->persistSelection();
    }

    /**
     * Pastikan tiap tour terpilih punya varian valid untuk jumlah rombongan;
     * yang belum punya (atau variannya tak lagi muat) diisi varian termurah.
     */
    private function syncTourVariants(): void
    {
        $pax = max((int) $this->pax, 1);
        $map = [];
        foreach ($this->selectedTourIds as $id) {
            $tour = $this->tourPackages->firstWhere('id', $id);
            if (!$tour) {
                continue;
            }

            $current = $tour->variants->firstWhere('id', $this->tourVariants[$id] ?? 0);
            if (!$current || !$current->fitsPax($pax)) {
                $current = $tour->bestVariantFor($pax);
            }
            if ($current) {
                $map[$id] = $current->id;
            }
        }

        $this->tourVariants = $map;
    }

    public function toggleDestination(int $destId): void
    {
        if (in_array($destId, $this->selectedDestinationIds)) {
            $this->selectedDestinationIds = array_values(array_filter($this->selectedDestinationIds, fn ($id) => $id !== $destId));
        } else {
            $this->selectedDestinationIds[] = $destId;
        }
        $this->persistSelection();
    }

    /** Perubahan lewat wire:model (tanggal, pax, kamar/malam per stay, catatan) ikut dipersist. */
    public function updated($name): void
    {
        if ($name === 'pax') {
            // Jumlah orang berubah → komposisi kamar & varian tour disarankan ulang.
            foreach ($this->stays as $i => $stay) {
                $hotel = $this->hotels->firstWhere('id', $stay['hotel_id']);
                if ($hotel) {
                    $this->stays[$i]['rooms'] = $this->suggestedComposition($hotel);
                }
            }
            $this->tourVariants = [];
            $this->syncTourVariants();
        }

        if (in_array($name, ['pax', 'startDate', 'customNotes'], true) || str_starts_with($name, 'stays.')) {
            $this->persistSelection();
        }
    }

    /**
     * Komposisi kamar campuran yang muat semua orang, greedy dari kapasitas
     * terbesar ke terkecil (mis. 5 org → 1 family + 1 single) — meniru cara
     * Ara menghitung. Hasil: [tipe => jumlah kamar] untuk tipe yang tersedia.
     */
    public function suggestedComposition(Hotel $hotel): array
    {
        $available = $this->roomTypesFor($hotel);
        $byCapacity = collect(self::ROOM_CAPACITY)
            ->only(array_keys($available))
            ->sortDesc();

        $remaining = max((int) $this->pax, 1);
        $rooms = [];
        foreach ($byCapacity as $key => $cap) {
            $rooms[$key] = intdiv($remaining, $cap);
            $remaining  -= $rooms[$key] * $cap;
        }

        if ($remaining > 0 && $byCapacity->isNotEmpty()) {
            $smallest = $byCapacity->keys()->last();
            $rooms[$smallest] = ($rooms[$smallest] ?? 0) + 1;
        }

        return $rooms;
    }

    /** Harga per malam sebuah stay = jumlah (kamar × harga tipe). */
    public function stayNightPrice(Hotel $hotel, array $stay): float
    {
        return (float) collect($stay['rooms'] ?? [])
            ->map(fn ($n, $key) => max((int) $n, 0) * $this->hotelRoomPrice($hotel, $key))
            ->sum();
    }

    /** Total kapasitas orang dari komposisi kamar sebuah stay. */
    public function stayCapacity(array $stay): int
    {
        return (int) collect($stay['rooms'] ?? [])
            ->map(fn ($n, $key) => max((int) $n, 0) * (self::ROOM_CAPACITY[$key] ?? 0))
            ->sum();
    }

    /** Stay baru untuk sebuah hotel dengan komposisi kamar yang disarankan. */
    private function makeStay(Hotel $hotel, int $nights = 1): array
    {
        return [
            'hotel_id' => (int) $hotel->id,
            'rooms'    => $this->suggestedComposition($hotel),
            'nights'   => max($nights, 1),
        ];
    }

    /** Bersihkan daftar stays; format lama (satu tipe kamar) dikonversi ke komposisi. */
    private function sanitizeStays(array $stays): array
    {
        $clean = [];
        foreach ($stays as $stay) {
            $hotel = $this->hotels->firstWhere('id', (int) ($stay['hotel_id'] ?? 0));
            if (!$hotel) {
                continue;
            }

            $available = $this->roomTypesFor($hotel);
            $rooms = [];

            if (is_array($stay['rooms'] ?? null)) {
                foreach (array_keys($available) as $key) {
                    $rooms[$key] = min(max((int) ($stay['rooms'][$key] ?? 0), 0), 10);
                }
            } elseif (!empty($stay['room_type'])) {
                // Format lama: satu tipe kamar + jumlah (integer).
                $rooms = array_fill_keys(array_keys($available), 0);
                $type = isset($available[$stay['room_type']]) ? $stay['room_type'] : array_key_first($available);
                if ($type !== null) {
                    $rooms[$type] = min(max((int) ($stay['rooms'] ?? 1), 1), 10);
                }
            }

            if (array_sum($rooms) < 1) {
                $rooms = $this->suggestedComposition($hotel);
            }

            $nights = (int) ($stay['nights'] ?? 0);

            $clean[] = [
                'hotel_id' => (int) $hotel->id,
                'rooms'    => $rooms,
                'nights'   => $nights > 0 ? min($nights, 30) : 1,
            ];
        }

        return $clean;
    }

    /** [checkIn, checkOut] untuk stay ke-$index — berantai berurutan dari startDate. */
    public function stayDates(int $index): array
    {
        $start = Carbon::parse($this->startDate ?: Carbon::tomorrow()->toDateString());

        $offset = 0;
        foreach ($this->stays as $i => $stay) {
            $nights = max((int) ($stay['nights'] ?? 1), 1);
            if ($i === $index) {
                return [$start->copy()->addDays($offset), $start->copy()->addDays($offset + $nights)];
            }
            $offset += $nights;
        }

        return [$start->copy(), $start->copy()->addDay()];
    }

    // ── Simpan / keranjang ────────────────────────────────────────
    public function saveSelection(): void
    {
        $this->persistSelection();
        $this->showSuccess = true;
    }

    /**
     * Sinkronkan semua item terpilih ke keranjang lalu menuju halaman keranjang
     * untuk checkout & pembayaran. Item yang cocok dihapus dulu agar klik
     * berulang TIDAK menumpuk duplikat.
     */
    public function addToCart(CartService $cart)
    {
        if (!auth()->check()) {
            // Simpan dulu supaya pilihan tidak hilang setelah login.
            $this->persistSelection();

            return redirect()->to(route('login', ['redirect' => url()->current()]));
        }

        $this->validate([
            'startDate' => 'required|date|after_or_equal:today',
            'pax'       => 'required|integer|min:1|max:30',
        ], [], [
            'startDate' => 'tanggal mulai',
            'pax'       => 'jumlah orang',
        ]);

        $this->cartError = null;
        $this->stays = $this->sanitizeStays($this->stays);
        $start = Carbon::parse($this->startDate);
        $pax   = max((int) $this->pax, 1);

        // Kunci item terpilih (type:id).
        $selectedKeys = [];
        foreach ($this->stays as $stay) {
            $selectedKeys[] = Hotel::class . ':' . (int) $stay['hotel_id'];
        }
        foreach ($this->selectedTourIds as $id) {
            $selectedKeys[] = TourPackage::class . ':' . (int) $id;
        }
        foreach ($this->selectedDestinationIds as $id) {
            $selectedKeys[] = Destination::class . ':' . (int) $id;
        }

        if (empty($selectedKeys)) {
            $this->cartError = 'Pilih minimal satu item (hotel, paket tour, atau destinasi) terlebih dahulu.';
            return;
        }

        // Hapus dulu item lama yang cocok → mencegah duplikat saat klik berulang.
        foreach ($cart->items() as $existing) {
            if (in_array($existing->itemable_type . ':' . $existing->itemable_id, $selectedKeys, true)) {
                $cart->remove($existing->id);
            }
        }

        $added   = 0;
        $skipped = [];

        // 1) Hotel — tanggal berantai per menginap; tiap tipe kamar dalam satu
        //    menginap jadi booking sendiri dengan rentang tanggal yang sama.
        $offset = 0;
        foreach ($this->stays as $stay) {
            $hotel = $this->hotels->firstWhere('id', $stay['hotel_id']);
            if (!$hotel) {
                continue;
            }

            $nights   = max((int) $stay['nights'], 1);
            $checkIn  = $start->copy()->addDays($offset);
            $checkOut = $checkIn->copy()->addDays($nights);
            $offset  += $nights;

            foreach ($stay['rooms'] as $type => $rooms) {
                $rooms = (int) $rooms;
                if ($rooms < 1) {
                    continue;
                }

                if ($hotel->availableRooms($type, $checkIn, $checkOut) >= $rooms) {
                    $cart->add(Hotel::class, $hotel->id, [
                        'label'          => $hotel->name,
                        'room_type'      => $type,
                        'rooms'          => $rooms,
                        'check_in_date'  => $checkIn->toDateString(),
                        'check_out_date' => $checkOut->toDateString(),
                        'quantity'       => $nights,
                        'unit_price'     => $this->hotelRoomPrice($hotel, $type),
                    ]);
                    $added++;
                } else {
                    $skipped[] = $hotel->name . ' (kamar ' . $type . ' tidak cukup tersedia)';
                }
            }
        }

        // 2) Paket tour — hormati varian harga (flat = 1 paket, per orang = pax tiket).
        foreach ($this->selectedTourIds as $id) {
            $t = $this->tourPackages->firstWhere('id', $id);
            if (!$t) {
                continue;
            }
            $variant = $this->tourVariantFor($t);
            $isFlat  = $variant && $variant->price_type === 'flat';

            $cart->add(TourPackage::class, $t->id, [
                'label'         => $t->name . ($variant ? ' — ' . $variant->name : ''),
                'booking_date'  => $start->toDateString(),
                'quantity'      => $isFlat ? 1 : $pax,
                'unit_price'    => $variant ? (float) $variant->price : (float) $t->price,
                'variant_id'    => $variant?->id,
                'variant_label' => $variant?->name,
                'price_type'    => $variant?->price_type ?? 'per_person',
                'pax'           => $pax,
            ]);
            $added++;
        }

        // 3) Destinasi
        foreach ($this->selectedDestinationIds as $id) {
            $d = $this->destinations->firstWhere('id', $id);
            if (!$d) {
                continue;
            }
            $cart->add(Destination::class, $d->id, [
                'label'        => $d->name,
                'booking_date' => $start->toDateString(),
                'quantity'     => $pax,
                'unit_price'   => $this->destUnitPrice($d),
            ]);
            $added++;
        }

        if ($added === 0) {
            $this->cartError = 'Tidak ada item yang bisa ditambahkan.';
            return;
        }

        $this->persistSelection();

        $msg = "{$added} item dari rekomendasi Ara siap di keranjang.";
        if ($skipped) {
            $msg .= ' Dilewati: ' . implode(', ', $skipped) . '.';
        }
        session()->flash('success', $msg);

        return redirect()->route('cart.index');
    }

    public function dismissNotification(): void
    {
        $this->showSuccess = false;
    }

    // ══════════════════════════════════════════════════════════════
    // Helper (dipakai juga di Blade)
    // ══════════════════════════════════════════════════════════════

    /** Tipe kamar yang punya harga (>0) untuk sebuah hotel, beserta harga (flash-aware). */
    public function roomTypesFor(?Hotel $hotel): array
    {
        if (!$hotel) {
            return [];
        }

        $types = [];
        foreach (['single' => 'Single', 'double' => 'Double', 'family' => 'Family'] as $key => $label) {
            if ((float) ($hotel->{$key . '_room_price'} ?? 0) > 0) {
                $types[$key] = ['label' => $label, 'price' => $this->hotelRoomPrice($hotel, $key)];
            }
        }

        return $types;
    }

    public function hotelRoomPrice(Hotel $hotel, string $type): float
    {
        $base = (float) ($hotel->{$type . '_room_price'} ?? 0);

        return (float) ($hotel->flashSalePrice($base) ?? $base);
    }

    /** Varian harga terpilih untuk sebuah paket tour (null = harga dasar per orang). */
    public function tourVariantFor(TourPackage $tour): ?TourPackageVariant
    {
        return $tour->variants->firstWhere('id', $this->tourVariants[$tour->id] ?? 0);
    }

    /** Total harga sebuah paket tour sesuai varian terpilih & jumlah orang. */
    public function tourTotal(TourPackage $tour): float
    {
        $pax = max((int) $this->pax, 1);
        $variant = $this->tourVariantFor($tour);

        return $variant ? $variant->totalFor($pax) : (float) $tour->price * $pax;
    }

    public function destUnitPrice(Destination $d): float
    {
        return (float) ($d->isOnFlashSale() ? $d->flash_sale_price : $d->price);
    }

    public function getHotelSubtotalProperty(): float
    {
        return collect($this->stays)->sum(function ($stay) {
            $hotel = $this->hotels->firstWhere('id', $stay['hotel_id'] ?? 0);
            if (!$hotel) {
                return 0;
            }

            return $this->stayNightPrice($hotel, $stay) * max((int) ($stay['nights'] ?? 1), 1);
        });
    }

    public function getToursSubtotalProperty(): float
    {
        return $this->tourPackages
            ->whereIn('id', $this->selectedTourIds)
            ->sum(fn ($t) => $this->tourTotal($t));
    }

    public function getDestinationsSubtotalProperty(): float
    {
        $pax = max((int) $this->pax, 1);

        return $this->destinations
            ->whereIn('id', $this->selectedDestinationIds)
            ->sum(fn ($d) => $this->destUnitPrice($d) * $pax);
    }

    public function getGrandTotalProperty(): float
    {
        return $this->hotelSubtotal + $this->toursSubtotal + $this->destinationsSubtotal;
    }

    private function guessNights(string $raw): int
    {
        // "hotel ... 2 malam" lebih akurat daripada total hari (itinerary multi-wilayah
        // bisa saja hanya butuh hotel di sebagian trip, mis. sisanya include di paket).
        if (preg_match('/(\d+)\s*malam/u', $raw, $m)) {
            return min(max((int) $m[1], 1), 30);
        }

        if (preg_match('/(\d+)\s*hari/u', $raw, $m)) {
            return max((int) $m[1] - 1, 1);
        }

        return 2;
    }

    private function guessPax(string $raw): int
    {
        if (preg_match('/peserta:?\**\s*(\d+)\s*orang/iu', $raw, $m)
            || preg_match('/(\d+)\s*orang/u', $raw, $m)) {
            return min(max((int) $m[1], 1), 30);
        }

        return 2;
    }

    /** Itinerary Ara dirender sebagai markdown penuh (tabel, garis pemisah, list). */
    public function getRecommendationHtmlProperty(): string
    {
        $raw = trim($this->session->recommendation_raw ?? '');
        if ($raw === '') {
            return '';
        }

        return Str::markdown($raw, [
            'html_input'         => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }

    private function persistSelection(): void
    {
        $this->session->update([
            'recommendation_edited' => [
                'stays'                    => $this->sanitizeStays($this->stays),
                'selected_tour_ids'        => $this->selectedTourIds,
                'tour_variants'            => $this->tourVariants,
                'selected_destination_ids' => $this->selectedDestinationIds,
                'pax'                      => (int) $this->pax,
                'custom_notes'             => $this->customNotes,
                'start_date'               => $this->startDate,
            ],
        ]);
    }

    public function render()
    {
        $raw = strtolower($this->session->recommendation_raw ?? '');

        $selectedTours        = $this->tourPackages->whereIn('id', $this->selectedTourIds);
        $selectedDestinations = $this->destinations->whereIn('id', $this->selectedDestinationIds);

        // Grid destinasi: 8 teratas + yang sedang terpilih; bisa dibuka semua.
        $displayDestinations = $this->showAllDestinations
            ? $this->destinations
            : $this->destinations->take(8)
                ->concat($this->destinations->slice(8)->whereIn('id', $this->selectedDestinationIds))
                ->values();

        return view('livewire.travel-recommendation', compact(
            'selectedTours', 'selectedDestinations', 'raw', 'displayDestinations'
        ));
    }
}
