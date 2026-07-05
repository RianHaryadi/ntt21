@extends('layouts.app')

@section('title', 'Destinations')

@section('content')

{{-- ── PAGE HERO ── --}}
<section class="relative min-h-[520px] md:min-h-[600px] bg-ink overflow-hidden flex items-center justify-center pt-28 pb-16">
    <img src="https://images.unsplash.com/photo-1505228395891-9a51e7e86bf6?auto=format&fit=crop&w=2070&q=80" class="absolute inset-0 w-full h-full object-cover opacity-70" alt="Hero background">
    <div class="absolute inset-0 bg-ink/40"></div>
    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-ink/80 to-transparent"></div>
    <div class="relative z-10 text-center px-6 max-w-4xl mx-auto text-paper">
        <span class="inline-flex items-center gap-1.5 bg-clay/10 text-clay text-[11px] font-bold uppercase tracking-[0.1em] px-2.5 py-1 rounded-full mb-6">
            <i class="fas fa-map-marker-alt"></i> {{ __('site.dest_hero_badge') }}
        </span>
        <h1 class="font-serif font-bold text-5xl md:text-7xl tracking-tight leading-[1.05] mb-5">
            {{ __('site.dest_hero_title') }}
        </h1>
        <p class="text-paper/80 text-base md:text-lg max-w-2xl mx-auto mb-10">
            {{ __('site.dest_hero_subtitle') }}
        </p>

        {{-- Search Box --}}
        <form action="{{ route('destinations.index') }}" method="GET" autocomplete="off"
              class="relative mx-auto flex flex-col md:flex-row items-center gap-2 max-w-2xl bg-surface p-2 rounded-2xl md:rounded-full shadow-lg shadow-ink/5 border border-line">
            <div class="flex items-center flex-1 w-full pl-4 md:pl-5 py-2 md:py-0">
                <i class="fas fa-search text-muted text-base"></i>
                <input type="text" name="search" placeholder="{{ __('site.dest_search_placeholder') }}" data-search-autocomplete
                       class="w-full bg-transparent text-ink placeholder:text-muted outline-none text-sm px-4 focus:ring-0"
                       value="{{ request('search') }}">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 bg-clay text-paper font-semibold text-sm w-full md:w-auto px-8 py-3.5 rounded-xl md:rounded-full hover:bg-clay/90 active:scale-[0.98] transition-all">
                {{ __('site.dest_search_button') }}
            </button>
            <div data-search-autocomplete-results
                 class="hidden absolute left-0 right-0 top-full mt-2 bg-paper border border-line rounded-2xl shadow-xl overflow-hidden z-30 text-left"></div>
        </form>
    </div>
</section>

{{-- ── MAIN ── --}}
<main class="bg-paper py-20 min-h-screen">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        @php
            $currentCategory = request('category', 'All');
            $categories = ['All', 'Beach', 'Mountain', 'Culture', 'Nature'];
            $icons = ['All'=>'fa-border-all','Beach'=>'fa-umbrella-beach','Mountain'=>'fa-mountain','Culture'=>'fa-landmark','Nature'=>'fa-tree'];
        @endphp

        {{-- Filter Header --}}
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-16">
            <div class="text-center lg:text-left">
                <h2 class="font-serif font-bold text-3xl md:text-4xl tracking-tight text-ink mb-2">{{ __('site.dest_browse_locations') }}</h2>
                <p class="text-muted text-sm">{{ __('site.dest_found_prefix') }} <span class="text-clay font-bold">{{ $destinations->total() }}</span> {{ __('site.dest_found_suffix') }}</p>
            </div>
            <div class="flex flex-wrap justify-center items-center gap-3">
                @foreach($categories as $cat)
                @php
                    $isActive = ($cat === 'All' && !$currentCategory) || ($currentCategory === $cat);
                    $params = array_filter(['search'=>request('search'),'category'=>$cat!=='All'?$cat:null]);
                @endphp
                <a href="{{ route('destinations.index', $params) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-semibold border transition-all {{ $isActive ? 'bg-clay text-paper border-clay' : 'bg-surface text-ink border-line hover:border-clay hover:text-clay' }}">
                    <i class="fas {{ $icons[$cat] }}"></i> {{ $cat }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Filter & Sort --}}
        <form method="GET" action="{{ route('destinations.index') }}" id="sort-form" class="flex flex-wrap justify-end items-center gap-3 mb-8 -mt-10">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif

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
                <select name="min_rating" onchange="document.getElementById('sort-form').submit()"
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
            <a href="{{ route('destinations.index', array_filter(['search'=>request('search'),'category'=>request('category')])) }}"
               class="text-xs font-semibold text-muted hover:text-clay transition-colors">{{ __('site.dest_reset_filter') }}</a>
            @endif

            <div class="flex items-center gap-2 bg-surface border border-line rounded-full px-4 py-2">
                <i class="fas fa-sort text-clay text-xs"></i>
                <select name="sort" onchange="document.getElementById('sort-form').submit()"
                        class="bg-transparent text-sm font-semibold text-ink focus:outline-none appearance-none cursor-pointer">
                    <option value="" {{ !request('sort') ? 'selected' : '' }}>{{ __('site.dest_sort_latest') }}</option>
                    <option value="popular" {{ request('sort')=='popular' ? 'selected' : '' }}>{{ __('site.dest_sort_popular') }}</option>
                    <option value="rating" {{ request('sort')=='rating' ? 'selected' : '' }}>{{ __('site.dest_sort_rating') }}</option>
                    <option value="price-asc" {{ request('sort')=='price-asc' ? 'selected' : '' }}>{{ __('site.dest_sort_price_asc') }}</option>
                    <option value="price-desc" {{ request('sort')=='price-desc' ? 'selected' : '' }}>{{ __('site.dest_sort_price_desc') }}</option>
                </select>
            </div>
        </form>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 reveal-group">
            @forelse($destinations as $idx => $destination)
            <div class="bg-paper border border-line rounded-2xl overflow-hidden card-lift group flex flex-col h-full">
                <a href="{{ route('destinations.show', $destination->id) }}" class="aspect-[4/3] overflow-hidden block relative img-zoom">
                    <img src="{{ $destination->image ? asset('storage/'.ltrim($destination->image,'/')) : asset('images/fallback.jpg') }}"
                         class="w-full h-full object-cover"
                         alt="{{ $destination->name ?? 'Destination' }}"
                         loading="lazy">
                    <div class="absolute inset-0 bg-ink/10 group-hover:bg-transparent transition-colors"></div>

                    @if($destination->isOnFlashSale())
                    <div class="absolute top-4 left-4">
                        @include('partials.flash-sale-badge', ['endsAt' => $destination->flash_sale_ends_at])
                    </div>
                    @elseif($destination->is_popular)
                    <div class="absolute top-4 left-4 bg-clay text-paper text-[10px] font-bold px-2.5 py-1 rounded-full shadow-lg uppercase tracking-wider">
                        <i class="fas fa-fire mr-1"></i> Popular
                    </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-paper/95 border border-line text-ink text-[10px] font-bold uppercase tracking-[0.1em] px-2.5 py-1 rounded-full shadow-sm">
                        {{ $destination->category ?? 'Destination' }}
                    </div>
                </a>

                <div class="p-5 flex flex-col flex-1">
                    <span class="text-[11px] text-muted font-semibold uppercase tracking-[0.12em] flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-map-marker-alt text-clay"></i> {{ $destination->location ?? 'Unknown Location' }}
                    </span>
                    <h3 class="font-serif font-bold text-lg text-ink tracking-tight mb-2 group-hover:text-clay transition-colors">
                        <a href="{{ route('destinations.show', $destination->id) }}">{{ $destination->name ?? 'Unknown' }}</a>
                    </h3>
                    <p class="text-muted text-sm line-clamp-2 leading-relaxed mb-4 flex-1">
                        {{ $destination->description ?? 'No description available.' }}
                    </p>
                    
                    <div class="flex items-center justify-between border-t border-line pt-4 mb-4">
                        <div class="flex items-center gap-1.5">
                            @if($destination->rating)
                                <div class="text-clay text-[10px]">
                                    @for($s=1;$s<=5;$s++)
                                        <i class="fas fa-star {{ $s<=round($destination->rating)?'':'opacity-30' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-muted font-medium">({{ $destination->rating_count ?? 0 }})</span>
                            @else
                                <span class="text-xs text-muted">{{ __('site.dest_no_ratings') }}</span>
                            @endif
                        </div>
                        @if($destination->isOnFlashSale())
                        <div class="flex flex-col items-end">
                            <span class="text-muted text-xs line-through">{{ format_price($destination->price) }}</span>
                            <span class="text-coral font-bold text-sm">{{ format_price($destination->flash_sale_price) }}</span>
                        </div>
                        @elseif($destination->price)
                        <span class="text-clay font-bold text-sm">{{ format_price($destination->price) }}</span>
                        @endif
                    </div>
                    
                    <a href="{{ route('destinations.show', $destination->id) }}" class="inline-flex items-center justify-center gap-2 bg-surface text-ink font-semibold text-sm w-full py-3 rounded-xl border border-line hover:border-clay hover:text-clay transition-all">
                        {{ __('site.dest_explore') }}
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center bg-surface rounded-2xl border border-line">
                <div class="w-16 h-16 rounded-full bg-paper flex items-center justify-center mb-6 shadow-sm border border-line">
                    <i class="fas fa-map-marked-alt text-2xl text-muted"></i>
                </div>
                <h3 class="font-serif font-bold text-xl text-ink mb-2">{{ __('site.dest_not_found_title') }}</h3>
                <p class="text-muted text-sm mb-6">{{ __('site.dest_not_found_desc') }}</p>
                <a href="{{ route('destinations.index') }}" class="inline-flex items-center gap-2 bg-clay text-paper font-semibold text-sm px-6 py-3 rounded-full hover:bg-clay/90 transition-all">
                    {{ __('site.dest_reset_filters') }}
                </a>
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
        <div class="mt-16 flex flex-col items-center gap-6">
            <p class="text-sm font-medium text-muted">
                {{ __('site.dest_showing') }} <span class="font-bold text-ink">{{ $destinations->firstItem() }}</span>–<span class="font-bold text-ink">{{ $destinations->lastItem() }}</span> {{ __('site.dest_showing_of') }} <span class="font-bold text-ink">{{ $destinations->total() }}</span> {{ __('site.dest_showing_suffix') }}
            </p>
            <nav class="flex items-center gap-2">
                @if(!$destinations->onFirstPage())
                <a href="{{ $destinations->previousPageUrl().$q }}" class="w-10 h-10 rounded-full bg-surface border border-line flex items-center justify-center text-ink hover:border-clay hover:text-clay transition-colors">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                @endif
                
                @foreach($destinations->getUrlRange(max(1,$current-2),min($last,$current+2)) as $page=>$url)
                <a href="{{ $url.$q }}"
                   class="w-10 h-10 rounded-full text-sm font-semibold flex items-center justify-center transition-colors border {{ $page==$current ? 'bg-clay text-paper border-clay' : 'bg-surface text-ink border-line hover:border-clay hover:text-clay' }}">
                    {{ $page }}
                </a>
                @endforeach
                
                @if($destinations->hasMorePages())
                <a href="{{ $destinations->nextPageUrl().$q }}" class="w-10 h-10 rounded-full bg-surface border border-line flex items-center justify-center text-ink hover:border-clay hover:text-clay transition-colors">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
                @endif
            </nav>
        </div>
        @endif
    </div>
</main>

@endsection