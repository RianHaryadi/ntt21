@extends('layouts.app')

@section('title', 'All Hotels')

@push('styles')
<style>
    .page-hero {
        background: url('https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat fixed;
        position: relative;
    }
    .page-hero::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(to bottom, rgba(0,26,51,0.6) 0%, rgba(0,26,51,0.95) 100%);
    }

    /* Used for filter bar select elements */
    .ocean-select {
        width: 100%; padding: 0.75rem 1rem; border-radius: 9999px;
        border: 1px solid #e5e7eb; color: #001a33; background-color: #ffffff;
        font-size: 0.875rem; transition: all 0.3s ease; appearance: none;
        background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="%236b7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"%3e%3cpath d="M6 8l4 4 4-4"/%3e%3c/svg%3e');
        background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.2em 1.2em;
    }
    .ocean-select:focus {
        box-shadow: 0 0 0 2px #ff6b35; border-color: #ff6b35; outline: none;
    }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="page-hero min-h-[500px] flex items-center justify-center text-white pt-24 pb-12">
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto py-10 reveal">
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-sunset-500 text-white text-sm font-bold mb-6 shadow-lg">
            <i class="fas fa-hotel"></i> Premium Accommodations
        </div>
        <h1 class="text-5xl md:text-7xl font-black mb-6 font-montserrat tracking-tight leading-tight" style="text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Find Your <span class="text-sunset-500">Dream Stay</span>
        </h1>
        <p class="text-xl text-white/80 max-w-2xl mx-auto mb-10 font-medium tracking-wide">
            Explore handpicked accommodations across East Nusa Tenggara, from beachfront villas to mountain retreats.
        </p>

        {{-- SEARCH FORM INTEGRATED INTO HERO --}}
        <form method="GET" action="{{ route('hotels.index') }}" id="search-form" class="bg-white/10 backdrop-blur-md p-3 rounded-full shadow-2xl border border-white/20 mt-8 max-w-4xl mx-auto flex flex-col md:flex-row gap-3">
            <div class="flex-1 flex items-center pl-6 pr-2 py-1 border-r border-white/20">
                <i class="fas fa-map-marker-alt text-sunset-500"></i>
                <select id="location" name="location" class="w-full bg-transparent text-white font-medium focus:outline-none appearance-none ml-2 border-0 focus:ring-0">
                    <option value="" class="text-ocean-900">Anywhere in NTT</option>
                    @foreach($locations as $loc)
                        @if($loc)
                        <option value="{{ $loc }}" class="text-ocean-900" {{ request('location')==$loc?'selected':'' }}>{{ $loc }}</option>
                        @endif
                    @endforeach
                </select>
                <i class="fas fa-chevron-down text-white/50 text-xs ml-auto"></i>
            </div>
            
            <div class="flex-1 flex items-center pl-6 pr-2 py-1">
                <i class="fas fa-sort text-sunset-500"></i>
                <select id="sort_main" name="sort" class="w-full bg-transparent text-white font-medium focus:outline-none appearance-none ml-2 border-0 focus:ring-0">
                    <option value="recommended" class="text-ocean-900" {{ !request('sort')||request('sort')=='recommended'?'selected':'' }}>Recommended</option>
                    <option value="price_asc" class="text-ocean-900" {{ request('sort')=='price_asc'?'selected':'' }}>Price: Low to High</option>
                    <option value="price_desc" class="text-ocean-900" {{ request('sort')=='price_desc'?'selected':'' }}>Price: High to Low</option>
                    <option value="rating_desc" class="text-ocean-900" {{ request('sort')=='rating_desc'?'selected':'' }}>Highest Rated</option>
                </select>
                <i class="fas fa-chevron-down text-white/50 text-xs ml-auto"></i>
            </div>
            
            <button type="submit" class="btn-primary w-full md:w-auto px-8 py-3 rounded-full text-sm">
                Search
            </button>
        </form>
    </div>
</section>

{{-- ── HOTEL GRID ── --}}
<main class="bg-light min-h-screen py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Results header --}}
        <div class="flex flex-col md:flex-row items-center justify-between mb-12 gap-6 reveal">
            <div>
                <h2 class="text-3xl font-black text-ocean-900 font-montserrat tracking-tight">
                    {{ $hotels->total() }} Accommodations Found
                </h2>
                <div class="h-1 w-16 bg-sunset-500 rounded-full mt-2"></div>
                @if(request('location'))
                    <p class="text-gray-500 font-medium mt-3">Showing results for <span class="font-bold text-ocean-900">{{ request('location') }}</span></p>
                @endif
            </div>
        </div>

        @if($hotels->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($hotels as $idx => $hotel)
            @php
                $prices  = [$hotel->single_room_price??999999999, $hotel->double_room_price??999999999, $hotel->family_room_price??999999999];
                $prices_filtered = array_filter($prices, fn($v)=>$v!=999999999);
                $minPrice = count($prices_filtered) > 0 ? min($prices_filtered) : 0;
                $rating  = rand(35,50)/10;
            @endphp
            <div class="cinematic-card flex flex-col h-full group reveal">
                <a href="{{ route('hotels.show', $hotel->id) }}" class="card-img-wrap h-64 block relative">
                    <img src="{{ $hotel->image ? asset('storage/'.$hotel->image) : asset('images/hotel-fallback.jpg') }}"
                         alt="{{ $hotel->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-ocean-900/20 group-hover:bg-transparent transition-colors"></div>

                    {{-- Price Tag --}}
                    @if($minPrice > 0)
                    <div class="absolute top-4 left-4 bg-sunset-500 text-white font-bold text-xs px-3 py-1.5 rounded-full shadow-lg">
                        Rp {{ number_format($minPrice,0,',','.') }} <span class="font-medium opacity-80">/night</span>
                    </div>
                    @endif
                    
                    {{-- Rating --}}
                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full flex items-center gap-1 text-xs font-bold text-ocean-900 shadow-md">
                        <i class="fas fa-star text-yellow-400"></i> {{ number_format($rating, 1) }}
                    </div>
                </a>

                <div class="p-6 flex flex-col flex-1">
                    <h3 class="font-black text-xl text-ocean-900 mb-2 font-montserrat tracking-tight">
                        <a href="{{ route('hotels.show', $hotel->id) }}">{{ $hotel->name }}</a>
                    </h3>
                    <p class="text-sunset-500 text-sm font-medium flex items-center gap-1.5 mb-4 border-b border-gray-100 pb-4">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $hotel->location }}
                    </p>
                    
                    <p class="text-gray-500 text-sm line-clamp-2 mb-4 flex-1">{{ strip_tags($hotel->description) }}</p>

                    @if($hotel->facilities)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach(array_slice(explode(',',$hotel->facilities),0,3) as $fac)
                        <span class="text-xs px-3 py-1 rounded-full bg-ocean-900/10 text-ocean-900 font-medium">{{ trim($fac) }}</span>
                        @endforeach
                        @if(count(explode(',',$hotel->facilities))>3)
                        <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-500 font-medium">+{{ count(explode(',',$hotel->facilities))-3 }}</span>
                        @endif
                    </div>
                    @endif

                    <div class="flex gap-2 mt-auto">
                        <a href="{{ route('hotels.show', $hotel->id) }}" class="flex-1 text-center btn-outline !border-ocean-900 !text-ocean-900 py-3 hover:!bg-ocean-900 hover:!text-white rounded-full text-sm">
                            Details
                        </a>
                        <a href="{{ route('hotels.book', $hotel->id) }}" class="flex-1 text-center btn-primary py-3 px-0 shadow-sm rounded-full text-sm">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($hotels->hasPages())
        <div class="mt-16 flex flex-col items-center gap-6 reveal">
            <nav class="flex items-center gap-2">
                <a href="{{ $hotels->previousPageUrl() }}"
                   class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-colors {{ $hotels->onFirstPage() ? 'bg-gray-100 text-gray-400 pointer-events-none' : 'bg-white border border-gray-200 text-ocean-900 hover:bg-sunset-500 hover:text-white hover:border-sunset-500' }}">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                
                @php $start=max(1,$hotels->currentPage()-2); $end=min($hotels->lastPage(),$hotels->currentPage()+2); @endphp
                @foreach(range($start,$end) as $page)
                <a href="{{ $hotels->url($page) }}"
                   class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-colors {{ $page==$hotels->currentPage() ? 'bg-ocean-900 text-white' : 'bg-white border border-gray-200 text-ocean-900 hover:bg-sunset-500 hover:text-white hover:border-sunset-500' }}">
                    {{ $page }}
                </a>
                @endforeach
                
                <a href="{{ $hotels->nextPageUrl() }}"
                   class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition-colors {{ !$hotels->hasMorePages() ? 'bg-gray-100 text-gray-400 pointer-events-none' : 'bg-white border border-gray-200 text-ocean-900 hover:bg-sunset-500 hover:text-white hover:border-sunset-500' }}">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </nav>
        </div>
        @endif

        @else
        <div class="flex flex-col items-center justify-center py-24 text-center bg-white rounded-2xl border border-dashed border-gray-300">
            <div class="w-20 h-20 rounded-full bg-light flex items-center justify-center mb-6 shadow-soft">
                <i class="fas fa-hotel text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-2xl font-black text-ocean-900 font-montserrat mb-2">No hotels found</h3>
            <p class="text-gray-500 mb-6">Try adjusting your filters or search destination.</p>
            <a href="{{ route('hotels.index') }}" class="btn-primary py-3 px-8">Clear Filters</a>
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