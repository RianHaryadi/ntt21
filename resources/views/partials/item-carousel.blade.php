{{--
    Partial: Horizontal item carousel (used for "Rekomendasi Serupa" & "Baru Dilihat")
    Variabel yang dibutuhkan:
    - $carouselTitle : judul section
    - $carouselIcon   : fontawesome icon class
    - $carouselItems  : collection berisi ['type' => 'destination'|'hotel', 'model' => Model]
--}}
@if($carouselItems->count() > 0)
<section class="py-12 bg-paper border-t border-line">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-xl font-black font-serif tracking-tight text-ink mb-6 flex items-center gap-2">
            <i class="{{ $carouselIcon }} text-clay text-lg"></i> {{ $carouselTitle }}
        </h2>
        <div class="flex gap-4 overflow-x-auto pb-2 -mx-4 px-4 snap-x snap-mandatory">
            @foreach($carouselItems as $entry)
            @php
                $item = $entry['model'];
                $isHotel = $entry['type'] === 'hotel';
                $url = $isHotel ? route('hotels.show', $item->id) : route('destinations.show', $item->id);
                $img = $isHotel
                    ? ($item->image ? asset('storage/'.$item->image) : asset('images/hotel-fallback.jpg'))
                    : ($item->image ? asset('storage/'.ltrim($item->image, '/')) : asset('images/fallback.jpg'));
                $rating = $isHotel ? ($item->reviews_avg_rating ? round($item->reviews_avg_rating, 1) : null) : ($item->rating ?? null);
                $subtitle = $isHotel ? $item->location : ($item->category ?? 'Destination');
            @endphp
            <a href="{{ $url }}" class="flex-shrink-0 w-56 snap-start bg-surface border border-line rounded-2xl overflow-hidden card-lift group">
                <div class="aspect-[4/3] overflow-hidden relative img-zoom">
                    <img src="{{ $img }}" alt="{{ $item->name }}" class="w-full h-full object-cover" loading="lazy">
                    @if($rating)
                    <div class="absolute bottom-2 left-2 bg-paper/95 border border-line px-2 py-1 rounded-full flex items-center gap-1 text-[11px] font-bold text-ink shadow-sm">
                        <i class="fas fa-star text-clay"></i> {{ number_format($rating, 1) }}
                    </div>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-[10px] text-muted font-semibold uppercase tracking-wider mb-1">{{ $subtitle }}</p>
                    <h3 class="font-serif font-bold text-sm text-ink leading-snug line-clamp-2 group-hover:text-clay transition-colors">
                        {{ $item->name }}
                    </h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
