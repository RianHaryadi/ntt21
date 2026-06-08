@extends('layouts.app')

@section('title', 'Tour Packages')

@push('styles')
<style>
    .page-hero {
        background: url('https://images.unsplash.com/photo-1469774749834-38e30d2b7f6c?auto=format&fit=crop&w=2070&q=80') center/cover no-repeat fixed;
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

    .marquee-wrapper { overflow: hidden; white-space: nowrap; position: relative; }
    .marquee-track { display: inline-block; white-space: nowrap; animation: marquee 30s linear infinite; }
    .marquee-wrapper:hover .marquee-track { animation-play-state: paused; }
    @keyframes marquee { 0% { transform: translateX(0%); } 100% { transform: translateX(-50%); } }

</style>
@endpush

@section('content')

{{-- ── PAGE HERO ── --}}
<section class="page-hero min-h-[500px] flex items-center justify-center text-white pt-24 pb-12">
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto reveal">
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-sunset-500 text-white text-sm font-bold mb-6 shadow-lg">
            <i class="fas fa-suitcase-rolling"></i> Curated Journeys
        </div>
        <h1 class="text-5xl md:text-7xl font-black mb-6 font-montserrat tracking-tight leading-tight" style="text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Unforgettable <span class="text-sunset-500">Tours</span>
        </h1>
        <p class="text-xl text-white/80 max-w-2xl mx-auto mb-10 font-medium tracking-wide">
            Exclusive access to breathtaking destinations with premium accommodations and bespoke experiences.
        </p>
    </div>
</section>

{{-- ── MARQUEE ── --}}
<section class="bg-ocean-900 border-b border-white/10 py-8 overflow-hidden relative">
    <div class="marquee-wrapper mx-auto">
        <div class="marquee-track">
            @foreach(['Komodo Island', 'Labuan Bajo', 'Flores Highlands', 'Sumba', 'Alor Archipelago', 'Pink Beach', 'Kelimutu Lakes', 'Padar'] as $dest)
                <span class="text-2xl md:text-3xl font-black text-white/40 font-montserrat mx-10 inline-block uppercase tracking-widest hover:text-white transition-colors cursor-default">
                    {{ $dest }}
                </span>
                @if (!$loop->last)
                    <span class="text-2xl text-sunset-500 mx-5">•</span>
                @endif
            @endforeach
            <!-- Duplicate for seamless loop -->
            @foreach(['Komodo Island', 'Labuan Bajo', 'Flores Highlands', 'Sumba', 'Alor Archipelago', 'Pink Beach', 'Kelimutu Lakes', 'Padar'] as $dest)
                <span class="text-2xl md:text-3xl font-black text-white/40 font-montserrat mx-10 inline-block uppercase tracking-widest hover:text-white transition-colors cursor-default">
                    {{ $dest }}
                </span>
                @if (!$loop->last)
                    <span class="text-2xl text-sunset-500 mx-5">•</span>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- ── FILTER BARS ── --}}
<section class="bg-white py-6 sticky top-14 z-20 shadow-sm transition-all" id="filters">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('paket-tours.index') }}" method="GET" class="w-full" id="tour-filter-form">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase ml-3 mb-1">Destination</label>
                    <select name="destination" class="ocean-select">
                        <option value="">All Destinations</option>
                        @foreach($destinations ?? [] as $destination)
                            <option value="{{ $destination }}" {{ request('destination') == $destination ? 'selected' : '' }}>{{ $destination }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase ml-3 mb-1">Duration</label>
                    <select name="duration" class="ocean-select">
                        <option value="">Any Duration</option>
                        <option value="1-3" {{ request('duration') == '1-3' ? 'selected' : '' }}>1-3 Days</option>
                        <option value="4-7" {{ request('duration') == '4-7' ? 'selected' : '' }}>4-7 Days</option>
                        <option value="8+" {{ request('duration') == '8+' ? 'selected' : '' }}>8+ Days</option>
                    </select>
                </div>
                
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase ml-3 mb-1">Price</label>
                    <select name="price" class="ocean-select">
                        <option value="">Any Price</option>
                        <option value="under-1000000" {{ request('price') == 'under-1000000' ? 'selected' : '' }}>Under 1M</option>
                        <option value="1-3" {{ request('price') == '1-3' ? 'selected' : '' }}>1M - 3M</option>
                        <option value="3-5" {{ request('price') == '3-5' ? 'selected' : '' }}>3M - 5M</option>
                    </select>
                </div>
                
                <div class="relative">
                    <label class="block text-xs font-bold text-gray-500 uppercase ml-3 mb-1">Category</label>
                    <select name="category" class="ocean-select">
                        <option value="">All Categories</option>
                        <option value="adventure" {{ request('category') == 'adventure' ? 'selected' : '' }}>Adventure</option>
                        <option value="luxury" {{ request('category') == 'luxury' ? 'selected' : '' }}>Luxury</option>
                        <option value="family" {{ request('category') == 'family' ? 'selected' : '' }}>Family</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary w-full h-[46px] rounded-full text-sm">
                    Search Tours
                </button>
            </div>
        </form>
    </div>
</section>

{{-- ── MAIN PACKAGES ── --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-light" id="tours">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 reveal">
        <div>
            <h2 class="text-3xl font-black text-ocean-900 font-montserrat tracking-tight mb-2">
                {{ $paketTours->total() }} Tours Available
            </h2>
            <div class="h-1 w-16 bg-sunset-500 rounded-full"></div>
        </div>
        
        <div class="flex items-center w-full md:w-auto">
            <span class="text-xs font-bold text-gray-400 uppercase mr-3">Sort:</span>
            <select class="ocean-select !py-2 !pl-4 !pr-10 w-48 text-sm bg-white" onchange="window.location.href = this.value">
                <option value="{{ route('paket-tours.index', array_merge(request()->query(), ['sort' => 'popular'])) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                <option value="{{ route('paket-tours.index', array_merge(request()->query(), ['sort' => 'price-asc'])) }}" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Price: Low</option>
                <option value="{{ route('paket-tours.index', array_merge(request()->query(), ['sort' => 'price-desc'])) }}" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Price: High</option>
            </select>
        </div>
    </div>

    <div class="space-y-8">
        @forelse ($paketTours as $paket)
            <div class="cinematic-package-card reveal flex flex-col md:flex-row">
                <!-- Image -->
                <a href="{{ route('paket-tours.show', $paket->id) }}" class="md:w-2/5 relative h-64 md:h-auto card-img-wrap block">
                    <img src="{{ $paket->thumbnail ? asset('storage/' . $paket->thumbnail) : asset('images/tour-fallback.jpg') }}" 
                         alt="{{ $paket->name }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-ocean-900/10 hover:bg-transparent transition-colors"></div>
                    @if($paket->is_featured)
                        <div class="absolute top-4 left-4 bg-sunset-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md">
                            Featured
                        </div>
                    @endif
                    @if($paket->category)
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-ocean-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm uppercase tracking-wide">
                            {{ $paket->category }}
                        </div>
                    @endif
                </a>

                <!-- Content -->
                <div class="md:w-3/5 p-6 md:p-8 flex flex-col justify-center">
                    <div class="flex flex-col md:flex-row justify-between items-start mb-4 gap-4">
                        <div>
                            <h3 class="text-2xl font-black text-ocean-900 font-montserrat tracking-tight mb-2">
                                <a href="{{ route('paket-tours.show', $paket->id) }}">{{ $paket->name }}</a>
                            </h3>
                            <div class="flex flex-wrap gap-4 text-sm font-medium text-gray-500">
                                <span class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-sunset-500"></i> {{ $paket->location }}</span>
                                <span class="flex items-center gap-1.5"><i class="far fa-clock text-sunset-500"></i> {{ $paket->days }} {{ Str::plural('day', $paket->days) }}</span>
                                @if($paket->includes_hotel)
                                    <span class="flex items-center gap-1.5"><i class="fas fa-hotel text-sunset-500"></i> Hotel Included</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-left md:text-right bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Price per person</span>
                            @if($paket->price)
                                <span class="text-xl font-black text-ocean-900">Rp {{ number_format($paket->price, 0, ',', '.') }}</span>
                            @else
                                <span class="text-lg font-bold text-ocean-900">Contact Us</span>
                            @endif
                        </div>
                    </div>

                    <p class="text-gray-600 mb-6 line-clamp-2">
                        {{ $paket->description }}
                    </p>

                    <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-5">
                        <div class="flex items-center gap-2">
                            <div class="flex text-sunset-500 text-xs gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= floor($paket->rating ?? 0) ? '' : 'opacity-30' }}"></i>
                                @endfor
                            </div>
                            <span class="text-xs font-medium text-gray-500">({{ $paket->rating_count ?? 12 }} Reviews)</span>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('paket-tours.show', $paket->id) }}" class="btn-outline !text-ocean-900 !border-ocean-900 py-2 hover:!bg-ocean-900 hover:!text-white">
                                Details
                            </a>
                            <a href="{{ route('paket-tour.create', $paket) }}" class="btn-primary py-2 px-6 shadow-sm">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-ocean-900 mb-2">No matching tours found</h3>
                <p class="text-gray-500 mb-6">Try adjusting your filters or destination.</p>
                <a href="{{ route('paket-tours.index') }}" class="btn-primary px-6 py-2">Reset Filters</a>
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