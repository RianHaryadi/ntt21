@extends('layouts.app')

@section('title', 'All Hotels')

@push('styles')
<style>
    /* Used for filter bar select elements */
    .ocean-select {
        width: 100%; padding: 0.75rem 1rem; border-radius: 9999px;
        border: 1px solid #DCDED5; color: #16201E; background-color: #ffffff;
        font-size: 0.875rem; transition: all 0.3s ease; appearance: none;
        background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="%2369736E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"%3e%3cpath d="M6 8l4 4 4-4"/%3e%3c/svg%3e');
        background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.2em 1.2em;
    }
    .ocean-select:focus {
        box-shadow: 0 0 0 2px #0F6E63; border-color: #0F6E63; outline: none;
    }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="relative min-h-[520px] md:min-h-[600px] bg-ink overflow-hidden flex items-center justify-center pt-28 pb-16">
    <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1920&q=80" class="absolute inset-0 w-full h-full object-cover opacity-70" alt="Hero background">
    <div class="absolute inset-0 bg-ink/40"></div>
    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-ink/80 to-transparent"></div>
    
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto py-10 text-paper">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-clay/10 border border-clay/20 text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-md">
            <i class="fas fa-hotel text-clay"></i> {{ __('site.hotel_hero_badge') }}
        </div>
        <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 tracking-tight leading-tight">
            {{ __('site.hotel_hero_title') }}
        </h1>
        <p class="text-lg md:text-xl text-paper/80 max-w-2xl mx-auto mb-10 font-medium tracking-wide leading-relaxed">
            {{ __('site.hotel_hero_subtitle') }}
        </p>

        {{-- SEARCH FORM INTEGRATED INTO HERO --}}
        <form method="GET" action="{{ route('hotels.index') }}" id="search-form" class="bg-surface p-2 rounded-2xl md:rounded-full shadow-lg border border-line mt-8 max-w-4xl mx-auto flex flex-col md:flex-row gap-3">
            <div class="flex-1 flex items-center pl-6 pr-2 py-1 md:border-r border-line">
                <i class="fas fa-map-marker-alt text-clay"></i>
                <select id="location" name="location" class="w-full bg-transparent text-ink font-semibold focus:outline-none appearance-none ml-2 border-0 focus:ring-0 text-sm">
                    <option value="" class="text-ink bg-surface">{{ __('site.hotel_anywhere') }}</option>
                    @foreach($locations as $loc)
                        @if($loc)
                        <option value="{{ $loc }}" class="text-ink bg-surface" {{ request('location')==$loc?'selected':'' }}>{{ $loc }}</option>
                        @endif
                    @endforeach
                </select>
                <i class="fas fa-chevron-down text-muted text-xs ml-auto pr-2"></i>
            </div>

            <div class="flex-1 flex items-center pl-6 pr-2 py-1">
                <i class="fas fa-sort text-clay"></i>
                <select id="sort_main" name="sort" class="w-full bg-transparent text-ink font-semibold focus:outline-none appearance-none ml-2 border-0 focus:ring-0 text-sm">
                    <option value="recommended" class="text-ink bg-surface" {{ !request('sort')||request('sort')=='recommended'?'selected':'' }}>{{ __('site.hotel_sort_recommended') }}</option>
                    <option value="price_asc" class="text-ink bg-surface" {{ request('sort')=='price_asc'?'selected':'' }}>{{ __('site.hotel_sort_price_asc') }}</option>
                    <option value="price_desc" class="text-ink bg-surface" {{ request('sort')=='price_desc'?'selected':'' }}>{{ __('site.hotel_sort_price_desc') }}</option>
                    <option value="rating_desc" class="text-ink bg-surface" {{ request('sort')=='rating_desc'?'selected':'' }}>{{ __('site.hotel_sort_rating') }}</option>
                    <option value="most_booked" class="text-ink bg-surface" {{ request('sort')=='most_booked'?'selected':'' }}>{{ __('site.hotel_sort_most_booked') }}</option>
                </select>
                <i class="fas fa-chevron-down text-muted text-xs ml-auto pr-2"></i>
            </div>

            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-clay text-paper font-semibold text-sm w-full md:w-auto px-8 py-3.5 rounded-xl md:rounded-full hover:bg-clay/90 active:scale-[0.98] transition-all">
                {{ __('site.hotel_search_button') }}
            </button>
        </form>
    </div>
</section>

{{-- ── HOTEL GRID ── --}}
<main class="bg-paper min-h-screen py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Results header --}}
        <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-6">
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-serif font-bold text-ink tracking-tight">
                    {{ __('site.hotel_found', ['count' => $hotels->total()]) }}
                </h2>
                @if(request('location'))
                    <p class="text-muted font-medium mt-3">{{ __('site.hotel_showing_for') }} <span class="font-bold text-clay">{{ request('location') }}</span></p>
                @endif
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('hotels.index') }}" id="filter-form" class="flex flex-wrap justify-center md:justify-end items-center gap-3 mb-12">
            @if(request('location')) <input type="hidden" name="location" value="{{ request('location') }}"> @endif
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

            <div class="flex items-center gap-2 bg-surface border border-line rounded-full px-4 py-2">
                <i class="fas fa-tag text-clay text-xs"></i>
                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="{{ __('site.dest_price_min') }}"
                       class="w-24 bg-transparent text-sm font-semibold text-ink placeholder:text-muted placeholder:font-medium focus:outline-none">
                <span class="text-muted text-xs">–</span>
                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="{{ __('site.dest_price_max') }}"
                       class="w-24 bg-transparent text-sm font-semibold text-ink placeholder:text-muted placeholder:font-medium focus:outline-none">
            </div>

            <div class="flex items-center gap-2 bg-surface border border-line rounded-full px-4 py-2">
                <i class="fas fa-star text-clay text-xs"></i>
                <select name="min_rating" onchange="document.getElementById('filter-form').submit()"
                        class="bg-transparent text-sm font-semibold text-ink focus:outline-none appearance-none cursor-pointer">
                    <option value="" {{ !request('min_rating') ? 'selected' : '' }}>{{ __('site.dest_rating_all') }}</option>
                    <option value="3" {{ request('min_rating')=='3' ? 'selected' : '' }}>3.0+</option>
                    <option value="4" {{ request('min_rating')=='4' ? 'selected' : '' }}>4.0+</option>
                    <option value="4.5" {{ request('min_rating')=='4.5' ? 'selected' : '' }}>4.5+</option>
                </select>
            </div>

            <button type="submit" class="inline-flex items-center gap-2 bg-ink text-paper text-sm font-semibold px-5 py-2 rounded-full hover:bg-ink/90 transition-colors">
                <i class="fas fa-filter text-xs"></i> {{ __('site.dest_apply_filter') }}
            </button>

            @if(request('min_price') || request('max_price') || request('min_rating'))
            <a href="{{ route('hotels.index', array_filter(['location'=>request('location'),'sort'=>request('sort')])) }}"
               class="text-xs font-semibold text-muted hover:text-clay transition-colors">{{ __('site.dest_reset_filter') }}</a>
            @endif
        </form>

        @if($hotels->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 reveal-group">
            @foreach($hotels as $idx => $hotel)
            @php
                $prices  = [$hotel->single_room_price??999999999, $hotel->double_room_price??999999999, $hotel->family_room_price??999999999];
                $prices_filtered = array_filter($prices, fn($v)=>$v!=999999999);
                $minPrice = count($prices_filtered) > 0 ? min($prices_filtered) : 0;
                $rating  = $hotel->reviews_avg_rating ? round($hotel->reviews_avg_rating, 1) : null;
                $flashPrice = $hotel->flashSalePrice($minPrice);
            @endphp
            <div class="bg-paper border border-line rounded-2xl overflow-hidden card-lift group flex flex-col h-full">
                <a href="{{ route('hotels.show', $hotel->id) }}" class="aspect-[4/3] block relative overflow-hidden img-zoom">
                    <img src="{{ $hotel->image ? asset('storage/'.$hotel->image) : asset('images/hotel-fallback.jpg') }}"
                         alt="{{ $hotel->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-ink/10 group-hover:bg-transparent transition-colors"></div>

                    @if($hotel->isOnFlashSale())
                    <div class="absolute top-4 left-4">
                        @include('partials.flash-sale-badge', ['endsAt' => $hotel->flash_sale_ends_at])
                    </div>
                    @endif

                    {{-- Price Tag --}}
                    @if($flashPrice)
                    <div class="absolute bottom-4 right-4 bg-coral text-paper font-bold text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                        <span class="line-through opacity-70 font-medium">{{ format_price($minPrice) }}</span>
                        {{ format_price($flashPrice) }} <span class="font-medium opacity-80">{{ __('site.hotel_per_night') }}</span>
                    </div>
                    @elseif($minPrice > 0)
                    <div class="absolute top-4 left-4 bg-clay text-paper font-bold text-xs px-3 py-1.5 rounded-full shadow-lg">
                        {{ format_price($minPrice) }} <span class="font-medium opacity-80">{{ __('site.hotel_per_night') }}</span>
                    </div>
                    @endif

                    {{-- Rating --}}
                    @if($rating)
                    <div class="absolute bottom-4 left-4 bg-paper/95 border border-line px-3 py-1.5 rounded-full flex items-center gap-1 text-xs font-bold text-ink shadow-md">
                        <i class="fas fa-star text-clay"></i> {{ number_format($rating, 1) }}
                    </div>
                    @endif
                </a>

                <div class="p-6 flex flex-col flex-1">
                    <h3 class="font-serif font-bold text-xl text-ink mb-2 tracking-tight group-hover:text-clay transition-colors">
                        <a href="{{ route('hotels.show', $hotel->id) }}">{{ $hotel->name }}</a>
                    </h3>
                    <p class="text-clay text-sm font-semibold flex items-center gap-1.5 mb-4 border-b border-line pb-4">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $hotel->location }}
                    </p>
                    
                    <p class="text-muted text-sm line-clamp-2 mb-4 flex-1">{{ strip_tags($hotel->description) }}</p>

                    @if($hotel->facilities)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach(array_slice(explode(',',$hotel->facilities),0,3) as $fac)
                        <span class="text-xs px-3 py-1 rounded-full bg-surface text-muted font-medium">{{ trim($fac) }}</span>
                        @endforeach
                        @if(count(explode(',',$hotel->facilities))>3)
                        <span class="text-xs px-3 py-1 rounded-full bg-surface text-muted font-medium">+{{ count(explode(',',$hotel->facilities))-3 }}</span>
                        @endif
                    </div>
                    @endif

                    <div class="flex gap-2 mt-auto">
                        <a href="{{ route('hotels.show', $hotel->id) }}" class="flex-1 text-center inline-flex items-center justify-center gap-2 bg-surface text-ink font-semibold text-sm py-3 rounded-full border border-line hover:border-clay hover:text-clay transition-all">
                            {{ __('site.hotel_details') }}
                        </a>
                        @auth
                        <button type="button"
                                onclick="openQuickAdd({type: 'hotel', id: {{ $hotel->id }}, name: '{{ addslashes($hotel->name) }}'})"
                                class="w-12 flex-shrink-0 inline-flex items-center justify-center rounded-full bg-ink text-paper hover:bg-ink/90 transition-all"
                                title="Tambah ke Keranjang">
                            <i class="fas fa-shopping-bag text-xs"></i>
                        </button>
                        @endauth
                        <a href="{{ route('hotels.book', $hotel->id) }}" class="flex-1 text-center inline-flex items-center justify-center gap-2 bg-clay text-paper font-semibold text-sm py-3 rounded-full hover:bg-clay/90 active:scale-[0.98] transition-all">
                            {{ __('site.hotel_book_now') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($hotels->hasPages())
        <div class="mt-16 flex flex-col items-center gap-6">
            <nav class="flex items-center gap-2">
                <a href="{{ $hotels->previousPageUrl() }}"
                   class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-colors border {{ $hotels->onFirstPage() ? 'bg-surface text-muted border-line pointer-events-none' : 'bg-surface border-line text-ink hover:border-clay hover:text-clay' }}">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                
                @php $start=max(1,$hotels->currentPage()-2); $end=min($hotels->lastPage(),$hotels->currentPage()+2); @endphp
                @foreach(range($start,$end) as $page)
                <a href="{{ $hotels->url($page) }}"
                   class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-colors border {{ $page==$hotels->currentPage() ? 'bg-clay text-paper border-clay' : 'bg-surface text-ink border-line hover:border-clay hover:text-clay' }}">
                    {{ $page }}
                </a>
                @endforeach
                
                <a href="{{ $hotels->nextPageUrl() }}"
                   class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-colors border {{ !$hotels->hasMorePages() ? 'bg-surface text-muted border-line pointer-events-none' : 'bg-surface border-line text-ink hover:border-clay hover:text-clay' }}">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </nav>
        </div>
        @endif

        @else
        <div class="flex flex-col items-center justify-center py-24 text-center bg-surface rounded-3xl border border-line">
            <div class="w-16 h-16 rounded-full bg-paper flex items-center justify-center mb-6 shadow-sm border border-line">
                <i class="fas fa-hotel text-2xl text-muted"></i>
            </div>
            <h3 class="text-xl font-serif font-bold text-ink mb-2">{{ __('site.hotel_not_found_title') }}</h3>
            <p class="text-muted mb-6 text-sm">{{ __('site.hotel_not_found_desc') }}</p>
            <a href="{{ route('hotels.index') }}" class="inline-flex items-center justify-center gap-2 bg-clay text-paper font-semibold text-sm px-8 py-3 rounded-full hover:bg-clay/90 transition-all">{{ __('site.hotel_clear_filters') }}</a>
        </div>
        @endif
    </div>
</main>

@push('scripts')
<script>
document.getElementById('location')?.addEventListener('change', function(){
    document.getElementById('search-form').submit();
});
document.getElementById('sort_main')?.addEventListener('change', function(){
    document.getElementById('search-form').submit();
});
</script>
@endpush

@endsection