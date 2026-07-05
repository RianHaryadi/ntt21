@extends('layouts.app')

@section('title', 'Pesona NTT | Jelajahi Keindahan Nusa Tenggara Timur')
@section('meta_description', 'Temukan destinasi tersembunyi, hotel terbaik, dan paket tour eksklusif di Nusa Tenggara Timur — Komodo, Labuan Bajo, Flores, Sumba, dan lainnya. Rencanakan perjalanan Anda bersama Ara, AI Guide kami.')

@push('styles')
<style>
    /* Custom utility for the Bento grid */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1.5rem;
    }
    @media (min-width: 768px) {
        .bento-grid {
            grid-template-columns: repeat(4, 1fr);
            grid-auto-rows: 300px;
        }
        .bento-main {
            grid-column: span 2;
            grid-row: span 2;
        }
        .bento-sub {
            grid-column: span 2;
            grid-row: span 1;
        }
    }
    @media (min-width: 1024px) {
        .bento-grid {
            grid-template-columns: repeat(6, 1fr);
        }
        .bento-main {
            grid-column: span 4;
            grid-row: span 2;
        }
        .bento-sub {
            grid-column: span 2;
            grid-row: span 1;
        }
    }

    /* Magazine overlapping style */
    .magazine-card {
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .magazine-card:hover {
        transform: translateY(-8px);
    }
    .magazine-img-wrapper {
        transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .magazine-card:hover .magazine-img-wrapper img {
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')

    {{-- ── 1. IMMERSIVE HERO SECTION ── --}}
    <section class="relative h-screen min-h-[700px] flex items-center justify-center overflow-hidden bg-ink">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1577717903315-1691ae25ab3f?q=80&w=2560&auto=format&fit=crop" 
                 alt="Komodo Island" 
                 class="w-full h-full object-cover object-center scale-105 transform transition-transform duration-[20s] hover:scale-110 opacity-80 mix-blend-luminosity">
            <div class="absolute inset-0 bg-gradient-to-b from-ink/60 via-ink/20 to-ink"></div>
        </div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-6 pt-20 flex flex-col items-center text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-paper/20 bg-paper/5 backdrop-blur-md mb-8 fade-in-up">
                <span class="w-2 h-2 rounded-full bg-clay animate-pulse"></span>
                <span class="text-paper text-xs font-bold uppercase tracking-widest">{{ __('site.home_badge') }}</span>
            </div>

            <h1 class="font-serif font-black text-5xl md:text-7xl lg:text-8xl tracking-tighter text-paper leading-[0.9] mb-8 drop-shadow-2xl fade-in-up" style="animation-delay: 0.1s;">
                {{ __('site.home_hero_title_1') }} <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-paper to-paper/50 italic font-light">{{ __('site.home_hero_title_2') }}</span>
            </h1>

            <p class="text-paper/80 text-lg md:text-xl font-medium max-w-2xl mb-12 fade-in-up" style="animation-delay: 0.2s;">
                {{ __('site.home_hero_subtitle') }}
            </p>

            <div class="flex flex-col sm:flex-row gap-5 fade-in-up" style="animation-delay: 0.3s;">
                <x-editorial.btn as="a" href="{{ route('destinations.index') }}" class="!px-8 !py-4 !text-base group">
                    {{ __('site.home_start_exploring') }} <i class="fas fa-arrow-right -rotate-45 group-hover:rotate-0 transition-transform"></i>
                </x-editorial.btn>
                <a href="#ai-guide" class="px-8 py-4 bg-paper/10 border border-paper/20 text-paper rounded-full font-bold text-sm backdrop-blur-md hover:bg-paper/20 transition-all">
                    {{ __('site.home_ask_ai_guide') }}
                </a>
            </div>
        </div>

        {{-- Floating Scroll Indicator --}}
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-60">
            <span class="text-paper text-[10px] uppercase tracking-widest font-bold">{{ __('site.home_scroll') }}</span>
            <div class="w-[1px] h-12 bg-gradient-to-b from-paper to-transparent"></div>
        </div>
    </section>

    @include('partials.flash-sale-section')

    {{-- ── 2. BENTO GRID DESTINATIONS ── --}}
    <section class="py-32 bg-paper relative z-20 -mt-8 rounded-t-[3rem]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6 reveal">
                <div class="max-w-2xl">
                    <span class="text-clay text-xs font-bold uppercase tracking-widest mb-3 block">{{ __('site.home_curated_locations') }}</span>
                    <h2 class="font-serif font-black text-4xl md:text-5xl lg:text-6xl tracking-tight text-ink">
                        {{ __('site.home_iconic_destinations') }}
                    </h2>
                </div>
                <a href="{{ route('destinations.index') }}" class="inline-flex items-center gap-2 text-ink hover:text-clay font-bold text-sm uppercase tracking-widest transition-colors pb-1 border-b-2 border-ink hover:border-clay">
                    {{ __('site.home_view_all_places') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="bento-grid reveal-group">
                @php $destCount = count($destinations); @endphp
                @foreach($destinations->take(3) as $index => $destination)
                    @php
                        $isMain = $index === 0;
                        $class = $isMain ? 'bento-main' : 'bento-sub';
                    @endphp
                    <a href="{{ route('destinations.show', $destination->id) }}" class="{{ $class }} relative group rounded-[2rem] overflow-hidden bg-ink shadow-sm">
                        <img src="{{ $destination->image ? asset('storage/' . $destination->image) : 'https://images.unsplash.com/photo-1512100356356-de1b84283e18?q=80&w=1200&auto=format&fit=crop' }}"
                             alt="{{ $destination->name }}"
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1512100356356-de1b84283e18?q=80&w=1200&auto=format&fit=crop';"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105 opacity-90 group-hover:opacity-100">
                        <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/20 to-transparent opacity-80 group-hover:opacity-60 transition-opacity duration-500"></div>
                        
                        <div class="absolute bottom-0 left-0 w-full p-8 md:p-10 flex flex-col justify-end h-full">
                            <div class="transform transition-transform duration-500 group-hover:-translate-y-2">
                                <span class="inline-block px-3 py-1 bg-paper/20 backdrop-blur-md rounded-full text-paper text-[10px] font-bold uppercase tracking-widest mb-4 border border-paper/10">
                                    {{ $destination->location ?? 'NTT' }}
                                </span>
                                <h3 class="font-serif font-black text-paper {{ $isMain ? 'text-4xl md:text-5xl' : 'text-2xl md:text-3xl' }} tracking-tight mb-2">
                                    {{ $destination->name }}
                                </h3>
                                @if($isMain)
                                    <p class="text-paper/80 text-sm md:text-base max-w-md line-clamp-2 mt-4 hidden md:block">
                                        {{ $destination->description ?? 'Experience the pristine beauty and rich culture of this incredible destination in East Nusa Tenggara.' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="absolute top-8 right-8 w-12 h-12 bg-paper/10 backdrop-blur-md rounded-full flex items-center justify-center border border-paper/20 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500">
                            <i class="fas fa-arrow-right text-paper -rotate-45"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>


    {{-- ── 3. MAGAZINE STYLE TOUR PACKAGES ── --}}
    <section class="py-32 bg-surface">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20 reveal">
                <i class="fas fa-compass text-clay text-3xl mb-6"></i>
                <h2 class="font-serif font-black text-4xl md:text-5xl tracking-tight text-ink mb-6">
                    {{ __('site.home_crafted_journeys') }}
                </h2>
                <p class="text-muted text-lg">
                    {{ __('site.home_crafted_journeys_desc') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-16 reveal-group">
                @foreach($TourPackage->take(3) as $index => $package)
                    <a href="{{ route('paket-tours.show', $package->id) }}" class="magazine-card group block {{ $index === 1 ? 'md:mt-16' : '' }}">
                        <div class="relative rounded-[2rem] overflow-hidden aspect-[4/5] mb-8 magazine-img-wrapper shadow-lg">
                            <img src="{{ $package->thumbnail ? asset('storage/' . $package->thumbnail) : 'https://images.unsplash.com/photo-1542401886-65d6c61db217?q=80&w=800&auto=format&fit=crop' }}"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1542401886-65d6c61db217?q=80&w=800&auto=format&fit=crop';"
                                 alt="{{ $package->name }}"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <div class="absolute top-4 left-4">
                                <span class="bg-paper text-ink text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full shadow-sm">
                                    {{ $package->duration ?? 'Multi-day' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-2xl text-ink tracking-tight mb-3 group-hover:text-clay transition-colors">
                                {{ $package->name }}
                            </h3>
                            <div class="flex items-center justify-between border-t border-line pt-4">
                                <p class="text-muted text-sm font-medium">{{ __('site.home_starting_from') }}</p>
                                <p class="font-bold text-ink text-lg">{{ format_price($package->price) }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <div class="mt-16 text-center">
                <a href="{{ route('paket-tours.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-full border border-line bg-paper text-ink font-bold text-sm hover:border-clay hover:text-clay transition-all hover:shadow-sm">
                    {{ __('site.home_browse_packages') }}
                </a>
            </div>
        </div>
    </section>


    {{-- ── 4. PREMIUM DARK AI SECTION ── --}}
    <section id="ai-guide" class="py-32 bg-ink relative overflow-hidden">
        {{-- Decorative glowing orbs --}}
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-clay/20 rounded-full mix-blend-screen filter blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-ink/40 rounded-full mix-blend-screen filter blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                
                {{-- Text Content --}}
                <div class="reveal">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-clay/30 bg-clay/10 mb-8">
                        <i class="fas fa-sparkles text-clay text-[10px]"></i>
                        <span class="text-clay text-[10px] font-bold uppercase tracking-widest">{{ __('site.home_ai_badge') }}</span>
                    </div>

                    <h2 class="font-serif font-black text-4xl md:text-5xl lg:text-6xl tracking-tight text-paper mb-6 leading-tight">
                        {{ __('site.home_meet_ara') }} <br>
                        <span class="text-clay italic font-light tracking-normal">{{ __('site.home_meet_ara_2') }}</span>
                    </h2>

                    <p class="text-paper/60 text-lg leading-relaxed mb-10 max-w-xl">
                        {{ __('site.home_ara_desc') }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button onclick="document.dispatchEvent(new CustomEvent('open-chat'))" class="group relative px-8 py-4 bg-clay text-paper rounded-full font-bold text-sm overflow-hidden transition-all shadow-sm shadow-clay/20 hover:shadow-md shadow-clay/30">
                            <span class="relative z-10 flex items-center gap-2 justify-center">
                                <i class="fas fa-comment-dots"></i> {{ __('site.home_chat_with_ara') }}
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Visual Element --}}
                <div class="relative reveal" style="transition-delay: 0.15s;">
                    <div class="absolute inset-0 bg-gradient-to-tr from-clay/20 to-ink/20 rounded-3xl transform rotate-3 scale-105 border border-white/5 backdrop-blur-sm"></div>
                    <div class="relative bg-ink/80 backdrop-blur-xl border border-white/10 rounded-3xl p-8 shadow-2xl">
                        {{-- Mock Chat UI --}}
                        <div class="flex items-center gap-4 mb-8 border-b border-white/10 pb-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-clay to-ink flex items-center justify-center shadow-lg">
                                <i class="fas fa-robot text-paper"></i>
                            </div>
                            <div>
                                <h4 class="text-paper font-bold text-sm">Ara AI</h4>
                                <p class="text-paper/50 text-[10px] uppercase tracking-widest">Online</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex justify-end">
                                <div class="bg-paper text-ink px-5 py-3 rounded-2xl rounded-tr-sm text-sm font-medium max-w-[80%] shadow-sm">
                                    I want a 3-day relaxing trip in Labuan Bajo for my honeymoon.
                                </div>
                            </div>
                            <div class="flex justify-start">
                                <div class="bg-paper/5 border border-white/10 text-paper px-5 py-4 rounded-2xl rounded-tl-sm text-sm leading-relaxed max-w-[90%] shadow-sm">
                                    <p class="mb-3">Congratulations! Labuan Bajo is perfect. Here's a curated 3-day luxury itinerary:</p>
                                    <ul class="space-y-2 text-paper/80">
                                        <li><strong class="text-clay">Day 1:</strong> Check-in at Ayana Resort & sunset cruise.</li>
                                        <li><strong class="text-clay">Day 2:</strong> Private speed boat to Padar Island & Pink Beach.</li>
                                        <li><strong class="text-clay">Day 3:</strong> Spa morning & seafood dining at Ujung.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── 5. LUXURY STAYS (Refined Cards) ── --}}
    <section class="py-32 bg-paper">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6 reveal">
                <div class="max-w-2xl">
                    <span class="text-clay text-xs font-bold uppercase tracking-widest mb-3 block">{{ __('site.home_accommodations') }}</span>
                    <h2 class="font-serif font-black text-4xl md:text-5xl tracking-tight text-ink">
                        {{ __('site.home_luxury_stays') }}
                    </h2>
                </div>
                <a href="{{ route('hotels.index') }}" class="inline-flex items-center gap-2 text-ink hover:text-clay font-bold text-sm uppercase tracking-widest transition-colors pb-1 border-b-2 border-ink hover:border-clay">
                    {{ __('site.home_view_all_hotels') }} <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-group">
                @foreach($hotels->take(4) as $hotel)
                    @php
                        $hotelPrices = array_filter([$hotel->single_room_price, $hotel->double_room_price, $hotel->family_room_price], fn($v) => $v > 0);
                        $hotelFromPrice = !empty($hotelPrices) ? min($hotelPrices) : 0;
                    @endphp
                    <a href="{{ route('hotels.show', $hotel->id) }}" class="group block">
                        <div class="relative rounded-3xl overflow-hidden aspect-square mb-5 border border-line shadow-sm">
                            <img src="{{ $hotel->image ? asset('storage/' . $hotel->image) : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800&auto=format&fit=crop' }}"
                                 alt="{{ $hotel->name }}"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800&auto=format&fit=crop';"
                                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500">
                                <span class="bg-paper/20 backdrop-blur-md text-paper border border-paper/20 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-widest">
                                    View Details
                                </span>
                                <div class="w-8 h-8 rounded-full bg-clay text-paper flex items-center justify-center">
                                    <i class="fas fa-arrow-right -rotate-45 text-xs"></i>
                                </div>
                            </div>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-ink tracking-tight mb-1 group-hover:text-clay transition-colors truncate">
                            {{ $hotel->name }}
                        </h3>
                        <p class="text-muted text-sm font-medium mb-2 truncate"><i class="fas fa-map-marker-alt text-clay/70 mr-1 text-[10px]"></i> {{ $hotel->location }}</p>
                        <p class="font-bold text-ink">{{ format_price($hotelFromPrice) }} <span class="text-xs text-muted font-medium">/ night</span></p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection