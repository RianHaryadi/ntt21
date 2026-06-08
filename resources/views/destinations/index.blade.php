@extends('layouts.app')

@section('title', 'Destinations')

@push('styles')
<style>
    .page-hero {
        background: url('https://images.unsplash.com/photo-1505228395891-9a51e7e86bf6?auto=format&fit=crop&w=2070&q=80') center/cover no-repeat fixed;
        position: relative;
    }
    .page-hero::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(to bottom, rgba(0,26,51,0.6) 0%, rgba(0,26,51,0.9) 100%);
    }

    .filter-pill {
        transition: all 0.3s ease;
    }
    .filter-pill:hover, .filter-pill.active {
        background-color: #ff6b35;
        color: white;
        border-color: #ff6b35;
    }

    /* cinematic-card is already in app.blade.php */
</style>
@endpush

@section('content')

{{-- ── PAGE HERO ── --}}
<section class="page-hero min-h-[450px] flex items-center justify-center text-white pt-24 pb-12">
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto reveal">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-sm font-medium mb-6">
            <i class="fas fa-map-marker-alt text-sunset-500"></i>
            East Nusa Tenggara, Indonesia
        </div>
        <h1 class="text-5xl md:text-6xl font-black mb-6 font-montserrat tracking-tight leading-tight" style="text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Discover Hidden Gems
        </h1>
        <p class="text-xl text-white/80 max-w-2xl mx-auto mb-10 font-medium tracking-wide">
            Explore breathtaking landscapes, pristine beaches, and the rich cultural heritage of East Nusa Tenggara.
        </p>

        {{-- Search Box --}}
        <form action="{{ route('destinations.index') }}" method="GET"
              class="mx-auto flex flex-col md:flex-row items-center gap-3 max-w-2xl bg-white/10 backdrop-blur-md p-3 rounded-full shadow-2xl border border-white/20">
            <div class="flex items-center flex-1 w-full pl-6">
                <i class="fas fa-search text-white/60 text-lg"></i>
                <input type="text" name="search" placeholder="Search destinations..."
                       class="w-full bg-transparent text-white placeholder-white/70 outline-none text-base px-4 font-medium"
                       value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn-primary w-full md:w-auto px-8 py-3 rounded-full text-sm">
                Search
            </button>
        </form>
    </div>
</section>

{{-- ── MAIN ── --}}
<main class="bg-light py-20 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $currentCategory = request('category', 'All');
            $categories = ['All', 'Beach', 'Mountain', 'Culture', 'Nature'];
            $icons = ['All'=>'fa-border-all','Beach'=>'fa-umbrella-beach','Mountain'=>'fa-mountain','Culture'=>'fa-landmark','Nature'=>'fa-tree'];
        @endphp

        {{-- Filter Header --}}
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-12 reveal">
            <div>
                <h2 class="text-3xl font-black text-ocean-900 font-montserrat tracking-tight">Browse Locations</h2>
                <p class="text-gray-500 mt-2 font-medium">Found <span class="text-sunset-500 font-bold">{{ $destinations->total() }}</span> destinations matching your criteria.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @foreach($categories as $cat)
                @php
                    $isActive = ($cat === 'All' && !$currentCategory) || ($currentCategory === $cat);
                    $params = array_filter(['search'=>request('search'),'category'=>$cat!=='All'?$cat:null]);
                @endphp
                <a href="{{ route('destinations.index', $params) }}"
                   class="filter-pill {{ $isActive ? 'active' : 'bg-white text-gray-600 border border-gray-200' }} inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-bold shadow-sm">
                    <i class="fas {{ $icons[$cat] }}"></i> {{ $cat }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($destinations as $idx => $destination)
            <div class="cinematic-card flex flex-col h-full group reveal">
                <a href="{{ route('destinations.show', $destination->id) }}" class="block card-img-wrap h-56">
                    <img src="{{ $destination->image ? asset('storage/'.ltrim($destination->image,'/')) : asset('images/fallback.jpg') }}"
                         class="w-full h-full object-cover"
                         alt="{{ $destination->name ?? 'Destination' }}"
                         loading="lazy">
                    <div class="absolute inset-0 bg-ocean-900/30 group-hover:bg-transparent transition-colors"></div>

                    @if($destination->is_popular)
                    <div class="absolute top-4 left-4 bg-sunset-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                        <i class="fas fa-fire mr-1"></i> Popular
                    </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-ocean-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                        {{ $destination->category ?? 'Destination' }}
                    </div>
                </a>

                <div class="p-6 flex flex-col flex-1">
                    <h3 class="font-black text-xl text-ocean-900 mb-2 font-montserrat tracking-tight">{{ $destination->name ?? 'Unknown' }}</h3>
                    <p class="text-sunset-500 text-sm font-medium mb-3 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i> {{ $destination->location ?? 'Unknown Location' }}
                    </p>
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 mb-6 flex-1">
                        {{ $destination->description ?? 'No description available.' }}
                    </p>
                    
                    <div class="flex items-center justify-between border-t border-gray-100 pt-4 mb-4">
                        <div class="flex items-center gap-1">
                            @if($destination->rating)
                                <div class="text-yellow-400 text-xs">
                                    @for($s=1;$s<=5;$s++)
                                        <i class="fas fa-star {{ $s<=round($destination->rating)?'':'opacity-30' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-400 ml-1 font-medium">({{ $destination->rating_count ?? 0 }})</span>
                            @else
                                <span class="text-xs text-gray-400">No ratings</span>
                            @endif
                        </div>
                        @if($destination->price)
                        <span class="text-ocean-900 font-bold">Rp{{ number_format($destination->price,0,',','.') }}</span>
                        @endif
                    </div>
                    
                    <a href="{{ route('destinations.show', $destination->id) }}" class="btn-outline !border-ocean-900 !text-ocean-900 hover:!bg-ocean-900 hover:!text-white text-center w-full py-2.5 text-sm">
                        Explore Destination
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-dashed border-gray-300">
                <div class="w-20 h-20 rounded-full bg-light flex items-center justify-center mb-6 shadow-soft">
                    <i class="fas fa-map-marked-alt text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-black text-ocean-900 font-montserrat mb-2">No destinations found</h3>
                <p class="text-gray-500 mb-6">Try adjusting your search or filter to find what you're looking for.</p>
                <a href="{{ route('destinations.index') }}" class="btn-primary py-3 px-8">Reset Filters</a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($destinations->hasPages())
        @php
            $current = $destinations->currentPage();
            $last    = $destinations->lastPage();
            $query   = http_build_query(array_filter(request()->except('page')));
            $q       = $query ? '&'.$query : '';
        @endphp
        <div class="mt-16 flex flex-col items-center gap-6 reveal">
            <p class="text-sm font-medium text-gray-400">
                Showing <span class="font-bold text-ocean-900">{{ $destinations->firstItem() }}</span>–<span class="font-bold text-ocean-900">{{ $destinations->lastItem() }}</span> of <span class="font-bold text-ocean-900">{{ $destinations->total() }}</span> destinations
            </p>
            <nav class="flex items-center gap-2">
                @if(!$destinations->onFirstPage())
                <a href="{{ $destinations->previousPageUrl().$q }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-ocean-900 hover:bg-sunset-500 hover:text-white hover:border-sunset-500 transition-colors shadow-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                @endif
                @foreach($destinations->getUrlRange(max(1,$current-2),min($last,$current+2)) as $page=>$url)
                <a href="{{ $url.$q }}"
                   class="w-10 h-10 rounded-full text-sm font-bold flex items-center justify-center transition-colors shadow-sm {{ $page==$current ? 'bg-ocean-900 text-white border-ocean-900' : 'bg-white text-ocean-900 border border-gray-200 hover:bg-sunset-500 hover:text-white hover:border-sunset-500' }}">
                    {{ $page }}
                </a>
                @endforeach
                @if($destinations->hasMorePages())
                <a href="{{ $destinations->nextPageUrl().$q }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-ocean-900 hover:bg-sunset-500 hover:text-white hover:border-sunset-500 transition-colors shadow-sm">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
                @endif
            </nav>
        </div>
        @endif
    </div>
</main>

@endsection