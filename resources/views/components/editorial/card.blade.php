@props(['image', 'title', 'subtitle' => null, 'price' => null, 'href' => '#'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group flex flex-col bg-paper rounded-2xl overflow-hidden border border-line shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 h-full']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-surface">
        <img src="{{ $image }}" alt="{{ $title }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=800&auto=format&fit=crop';" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
        @if($price)
            <div class="absolute top-4 right-4 bg-paper/90 backdrop-blur px-3 py-1.5 rounded-full text-sm font-bold text-ink shadow-sm">
                {{ $price }}
            </div>
        @endif
    </div>
    
    <div class="p-6 flex flex-col flex-grow justify-between">
        <div>
            @if($subtitle)
                <div class="text-xs font-bold tracking-widest uppercase text-clay mb-2 flex items-center gap-1.5">
                    <i class="fas fa-map-marker-alt"></i> {{ $subtitle }}
                </div>
            @endif
            <h3 class="font-serif text-xl text-ink leading-snug group-hover:text-clay transition-colors line-clamp-2">{{ $title }}</h3>
        </div>
        <div class="mt-4 pt-4 border-t border-line flex items-center justify-between text-sm font-medium text-muted">
            <span>Explore details</span>
            <i class="fas fa-arrow-right text-clay transform group-hover:translate-x-1 transition-transform"></i>
        </div>
    </div>
</a>
