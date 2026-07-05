@extends('layouts.app')

@section('title', 'Tour Packages')

@push('styles')
<style>
    /* Used for filter bar select elements */
    .ocean-select {
        width: 100%; padding: 0.75rem 1.25rem; border-radius: 9999px;
        border: 1px solid #DCDED5; color: #16201E; background-color: #ffffff;
        font-size: 0.875rem; transition: all 0.3s ease; appearance: none;
        background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="%2369736E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"%3e%3cpath d="M6 8l4 4 4-4"/%3e%3c/svg%3e');
        background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.2em 1.2em;
    }
    .ocean-select:focus {
        box-shadow: 0 0 0 2px rgba(15,110,99,0.15); border-color: #0F6E63; outline: none;
    }

    .marquee-wrapper { overflow: hidden; white-space: nowrap; position: relative; }
    .marquee-track { display: inline-block; white-space: nowrap; animation: marquee 30s linear infinite; }
    .marquee-wrapper:hover .marquee-track { animation-play-state: paused; }
    @keyframes marquee { 0% { transform: translateX(0%); } 100% { transform: translateX(-50%); } }

</style>
@endpush

@section('content')

{{-- ── PAGE HERO ── --}}
<section class="relative min-h-[520px] md:min-h-[600px] bg-ink overflow-hidden flex items-center justify-center pt-28 pb-16">
    <img src="https://images.unsplash.com/photo-1469774749834-38e30d2b7f6c?auto=format&fit=crop&w=2070&q=80" class="absolute inset-0 w-full h-full object-cover opacity-70" alt="Hero background">
    <div class="absolute inset-0 bg-ink/40"></div>
    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-ink/80 to-transparent"></div>
    
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto text-paper">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-clay/10 border border-clay/20 text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-md">
            <i class="fas fa-suitcase-rolling text-clay"></i> Curated Journeys
        </div>
        <h1 class="text-5xl md:text-7xl font-bold font-serif mb-6 tracking-tight leading-tight">
            Unforgettable Tours
        </h1>
        <p class="text-lg md:text-xl text-paper/80 max-w-2xl mx-auto mb-10 font-medium tracking-wide leading-relaxed">
            Exclusive access to breathtaking destinations with premium accommodations and bespoke experiences.
        </p>
    </div>
</section>

{{-- ── MARQUEE ── --}}
<section class="bg-ink border-b border-petrol/60 py-8 overflow-hidden relative">
    <div class="marquee-wrapper mx-auto">
        <div class="marquee-track">
            @foreach(['Komodo Island', 'Labuan Bajo', 'Flores Highlands', 'Sumba', 'Alor Archipelago', 'Pink Beach', 'Kelimutu Lakes', 'Padar'] as $dest)
                <span class="text-2xl md:text-3xl font-bold text-paper/30 font-serif mx-10 inline-block uppercase tracking-widest hover:text-paper transition-colors cursor-default">
                    {{ $dest }}
                </span>
                @if (!$loop->last)
                    <span class="text-2xl text-clay mx-5">•</span>
                @endif
            @endforeach
            <!-- Duplicate for seamless loop -->
            @foreach(['Komodo Island', 'Labuan Bajo', 'Flores Highlands', 'Sumba', 'Alor Archipelago', 'Pink Beach', 'Kelimutu Lakes', 'Padar'] as $dest)
                <span class="text-2xl md:text-3xl font-bold text-paper/30 font-serif mx-10 inline-block uppercase tracking-widest hover:text-paper transition-colors cursor-default">
                    {{ $dest }}
                </span>
                @if (!$loop->last)
                    <span class="text-2xl text-clay mx-5">•</span>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- ── FILTER BARS ── --}}
<section class="bg-surface/95 backdrop-blur-md border-b border-line py-6 sticky top-14 z-20 shadow-sm transition-all" id="filters">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('paket-tours.index') }}" method="GET" class="w-full" id="tour-filter-form">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div class="relative">
                    <label class="block text-xs font-bold text-muted uppercase ml-3 mb-1">Destination</label>
                    <select name="destination" class="ocean-select">
                        <option value="">All Destinations</option>
                        @foreach($destinations ?? [] as $destination)
                            <option value="{{ $destination }}" {{ request('destination') == $destination ? 'selected' : '' }}>{{ $destination }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="relative">
                    <label class="block text-xs font-bold text-muted uppercase ml-3 mb-1">Duration</label>
                    <select name="duration" class="ocean-select">
                        <option value="" class="text-ink">Any Duration</option>
                        <option value="1-3" class="text-ink" {{ request('duration') == '1-3' ? 'selected' : '' }}>1-3 Days</option>
                        <option value="4-7" class="text-ink" {{ request('duration') == '4-7' ? 'selected' : '' }}>4-7 Days</option>
                        <option value="8+" class="text-ink" {{ request('duration') == '8+' ? 'selected' : '' }}>8+ Days</option>
                    </select>
                </div>
                
                <div class="relative">
                    <label class="block text-xs font-bold text-muted uppercase ml-3 mb-1">Price</label>
                    <select name="price" class="ocean-select">
                        <option value="" class="text-ink">Any Price</option>
                        <option value="under-1000000" class="text-ink" {{ request('price') == 'under-1000000' ? 'selected' : '' }}>Under 1M</option>
                        <option value="1-3" class="text-ink" {{ request('price') == '1-3' ? 'selected' : '' }}>1M - 3M</option>
                        <option value="3-5" class="text-ink" {{ request('price') == '3-5' ? 'selected' : '' }}>3M - 5M</option>
                    </select>
                </div>
                
                <div class="relative">
                    <label class="block text-xs font-bold text-muted uppercase ml-3 mb-1">Category</label>
                    <select name="category" class="ocean-select">
                        <option value="" class="text-ink">All Categories</option>
                        <option value="adventure" class="text-ink" {{ request('category') == 'adventure' ? 'selected' : '' }}>Adventure</option>
                        <option value="luxury" class="text-ink" {{ request('category') == 'luxury' ? 'selected' : '' }}>Luxury</option>
                        <option value="family" class="text-ink" {{ request('category') == 'family' ? 'selected' : '' }}>Family</option>
                    </select>
                </div>
                
                <button type="submit" class="inline-flex items-center justify-center gap-2 bg-clay text-paper font-semibold text-sm w-full h-[46px] rounded-full hover:bg-clay/90 transition-all">
                    Search Tours
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ── MAIN PACKAGES ── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-paper" id="tours">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
        <div>
            <h2 class="text-3xl font-bold text-ink font-serif tracking-tight mb-2">
                {{ $paketTours->total() }} Tours Available
            </h2>
            <div class="h-1 w-16 bg-clay rounded-full"></div>
        </div>
        
        <div class="flex items-center w-full md:w-auto">
            <span class="text-xs font-bold text-muted uppercase mr-3">Sort:</span>
            <select class="ocean-select !py-2 !pl-4 !pr-10 w-48 text-sm bg-surface text-ink border border-line" onchange="window.location.href = this.value">
                <option value="{{ route('paket-tours.index', array_merge(request()->query(), ['sort' => 'popular'])) }}" class="text-ink bg-surface" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                <option value="{{ route('paket-tours.index', array_merge(request()->query(), ['sort' => 'price-asc'])) }}" class="text-ink bg-surface" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Price: Low</option>
                <option value="{{ route('paket-tours.index', array_merge(request()->query(), ['sort' => 'price-desc'])) }}" class="text-ink bg-surface" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Price: High</option>
            </select>
        </div>
    </div>

    <div class="space-y-8 reveal-group">
        @forelse ($paketTours as $paket)
            <div class="bg-surface border-line shadow-sm flex flex-col md:flex-row overflow-hidden rounded-3xl border card-lift">
                <!-- Image -->
                <a href="{{ route('paket-tours.show', $paket->id) }}" class="md:w-2/5 relative h-64 md:h-auto block bg-paper img-zoom">
                    <img src="{{ $paket->thumbnail ? asset('storage/' . $paket->thumbnail) : asset('images/tour-fallback.jpg') }}"
                         alt="{{ $paket->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-ink/10 group-hover:bg-transparent transition-colors"></div>
                    @if($paket->is_featured)
                        <div class="absolute top-4 left-4 bg-clay text-paper text-[10px] font-bold px-3 py-1.5 rounded-full shadow-md uppercase tracking-wider">
                            Featured
                        </div>
                    @endif
                    @if($paket->category)
                        <div class="absolute top-4 right-4 bg-surface border border-line text-ink text-[10px] font-bold uppercase px-3.5 py-1.5 rounded-full shadow-sm tracking-wide">
                            {{ $paket->category }}
                        </div>
                    @endif
                </a>

                <!-- Content -->
                <div class="md:w-3/5 p-6 md:p-8 flex flex-col justify-center bg-surface">
                    <div class="flex flex-col md:flex-row justify-between items-start mb-4 gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-ink font-serif tracking-tight mb-2">
                                <a href="{{ route('paket-tours.show', $paket->id) }}" class="hover:text-clay transition-colors">{{ $paket->name }}</a>
                            </h3>
                            <div class="flex flex-wrap gap-4 text-sm font-medium text-muted">
                                <span class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-clay"></i> {{ $paket->location }}</span>
                                <span class="flex items-center gap-1.5"><i class="far fa-clock text-clay"></i> {{ $paket->days }} {{ Str::plural('day', $paket->days) }}</span>
                                @if($paket->includes_hotel)
                                    <span class="flex items-center gap-1.5"><i class="fas fa-hotel text-clay"></i> Hotel Included</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-left md:text-right bg-paper p-3 rounded-xl border border-line">
                            <span class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1">Price per person</span>
                            @if($paket->price)
                                <span class="text-xl font-bold text-clay font-serif">{{ format_price($paket->price) }}</span>
                            @else
                                <span class="text-lg font-bold text-ink">Contact Us</span>
                            @endif
                        </div>
                    </div>

                    <p class="text-muted mb-6 line-clamp-2 leading-relaxed text-sm">
                        {{ $paket->description }}
                    </p>

                    <div class="mt-auto flex items-center justify-between border-t border-line pt-5">
                        <div class="flex items-center gap-2">
                            <div class="flex text-clay text-xs gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= floor($paket->rating ?? 0) ? '' : 'opacity-30' }}"></i>
                                @endfor
                            </div>
                            <span class="text-xs font-medium text-muted">({{ $paket->rating_count ?? 12 }} Reviews)</span>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('paket-tours.show', $paket->id) }}" class="inline-flex items-center justify-center gap-2 bg-paper text-ink font-semibold text-sm px-6 py-2 rounded-full border border-line hover:border-clay hover:text-clay transition-all">
                                Details
                            </a>
                            <a href="{{ route('paket-tour.create', $paket) }}" class="inline-flex items-center justify-center gap-2 bg-clay text-paper font-semibold text-sm px-6 py-2 rounded-full hover:bg-clay/90 transition-all">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-surface rounded-3xl border border-line shadow-sm">
                <i class="fas fa-search text-4xl text-muted mb-4"></i>
                <h3 class="text-xl font-bold text-ink font-serif mb-2">No matching tours found</h3>
                <p class="text-muted mb-6">Try adjusting your filters or destination.</p>
                <a href="{{ route('paket-tours.index') }}" class="inline-flex items-center justify-center gap-2 bg-clay text-paper font-semibold text-sm px-8 py-3 rounded-full hover:bg-clay/90 transition-all">Reset Filters</a>
            </div>
        @endforelse
    </div>
    
    @if($paketTours->hasPages())
        <div class="mt-16 text-center">
            {{ $paketTours->onEachSide(1)->links('vendor.pagination.tailwind') }}
        </div>
    @endif
</section>

@endsection