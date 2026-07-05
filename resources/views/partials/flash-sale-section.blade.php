{{--
    Partial: Flash Sale section (home page)
    Variabel: $flashSaleDestinations, $flashSaleHotels
--}}
@php
    $flashItems = collect()
        ->merge($flashSaleDestinations->map(fn($d) => ['type' => 'destination', 'model' => $d]))
        ->merge($flashSaleHotels->map(fn($h) => ['type' => 'hotel', 'model' => $h]));
@endphp
@if($flashItems->count() > 0)
<section class="py-20 bg-ink relative overflow-hidden reveal">
    <div class="absolute -top-20 -right-20 w-96 h-96 bg-coral rounded-full mix-blend-multiply filter blur-[120px] opacity-30 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
            <div>
                <span class="inline-flex items-center gap-2 bg-coral/15 text-coral text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full mb-4">
                    <i class="fas fa-bolt"></i> Flash Sale
                </span>
                <h2 class="font-serif font-black text-3xl md:text-4xl tracking-tight text-paper">
                    Promo Bertenggat Waktu
                </h2>
                <p class="text-paper/60 text-sm mt-2">Diskon terbatas — pesan sebelum waktu habis.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($flashItems as $entry)
            @php
                $item = $entry['model'];
                $isHotel = $entry['type'] === 'hotel';
                $url = $isHotel ? route('hotels.show', $item->id) : route('destinations.show', $item->id);
                $img = $isHotel
                    ? ($item->image ? asset('storage/'.$item->image) : asset('images/hotel-fallback.jpg'))
                    : ($item->image ? asset('storage/'.ltrim($item->image, '/')) : asset('images/fallback.jpg'));
                $originalPrice = $isHotel ? ($item->single_room_price ?? 0) : ($item->price ?? 0);
                $discountedPrice = $isHotel ? $item->flashSalePrice($originalPrice) : $item->flash_sale_price;
            @endphp
            <a href="{{ $url }}" class="bg-paper/5 border border-paper/10 rounded-2xl overflow-hidden card-lift group backdrop-blur-sm">
                <div class="aspect-[4/3] overflow-hidden relative img-zoom">
                    <img src="{{ $img }}" alt="{{ $item->name }}" class="w-full h-full object-cover" loading="lazy">
                    <div class="absolute top-3 left-3 bg-coral text-paper text-[10px] font-bold px-2.5 py-1 rounded-full">
                        -{{ $item->flash_sale_discount_percent }}%
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-[10px] text-paper/50 font-semibold uppercase tracking-wider mb-1">{{ $isHotel ? $item->location : ($item->category ?? 'Destination') }}</p>
                    <h3 class="font-serif font-bold text-sm text-paper leading-snug line-clamp-2 mb-2 group-hover:text-coral transition-colors">
                        {{ $item->name }}
                    </h3>
                    <div class="flex items-baseline gap-2 mb-3">
                        <span class="text-paper/40 text-xs line-through">{{ format_price($originalPrice) }}</span>
                        <span class="text-coral font-bold text-sm">{{ format_price($discountedPrice) }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-paper/70 font-mono tabular-nums bg-paper/5 rounded-lg px-2 py-1.5">
                        <i class="far fa-clock"></i>
                        <span data-flash-sale-ends="{{ $item->flash_sale_ends_at->toIso8601String() }}">--:--:--</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
