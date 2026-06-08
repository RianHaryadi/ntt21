@extends('layouts.app')

@section('title', $destination->name . ' — Wonderful NTT')

@push('styles')
<style>
    /* ── Cinematic Hero ── */
    .dest-hero {
        position: relative;
        height: 92vh;
        min-height: 600px;
        overflow: hidden;
    }
    .dest-hero-bg {
        position: absolute; inset: 0;
        background-size: cover;
        background-position: center;
        transform: scale(1.08);
        transition: transform 8s ease-out;
    }
    .dest-hero-bg.loaded { transform: scale(1.0); }
    .dest-hero-vignette {
        position: absolute; inset: 0;
        background:
            linear-gradient(to top,    rgba(0,26,51,0.96) 0%, rgba(0,26,51,0.35) 40%, transparent 70%),
            linear-gradient(to right,  rgba(0,26,51,0.45) 0%, transparent 60%),
            linear-gradient(to bottom, rgba(0,26,51,0.3) 0%, transparent 40%);
    }

    /* ── HUD elements ── */
    .hud-line {
        position: absolute; background: rgba(255,255,255,0.12);
    }
    .hud-corner {
        position: absolute; width: 20px; height: 20px;
        border-color: rgba(255,107,53,0.6); border-style: solid;
    }

    /* ── Scroll Indicator ── */
    @keyframes bounce-scroll { 0%,100% { transform: translateY(0); } 50% { transform: translateY(8px); } }
    .scroll-ind { animation: bounce-scroll 2s ease-in-out infinite; }

    /* ── Pill Tag ── */
    .pill-tag {
        display: inline-flex; align-items: center; gap: 0.45rem;
        padding: 0.4rem 1rem; border-radius: 99px;
        font-size: 0.7rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase;
        backdrop-filter: blur(12px);
    }

    /* ── Info Grid Cards ── */
    .info-glass {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.13);
        border-radius: 1.25rem;
        backdrop-filter: blur(16px);
        transition: all 0.3s ease;
    }
    .info-glass:hover { background: rgba(255,255,255,0.12); border-color: rgba(255,107,53,0.4); }

    /* ── Content Section ── */
    .content-wrap { background: linear-gradient(170deg, #f0f4f8 0%, #e8edf5 60%, #f0f4f8 100%); }

    /* ── Sticky Booking Card ── */
    .booking-card {
        position: sticky; top: 100px;
        background: white;
        border-radius: 2rem;
        box-shadow: 0 30px 80px -20px rgba(0,26,51,0.18);
        overflow: hidden;
    }
    .booking-card-header {
        background: linear-gradient(135deg, #001a33, #002b5e);
        padding: 1.75rem;
    }

    /* ── Ticket Counter ── */
    .qty-btn {
        width: 2.25rem; height: 2.25rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 1.1rem;
        transition: all 0.2s;
        border: none; cursor: pointer;
    }
    .qty-btn.minus { background: #f1f5f9; color: #001a33; }
    .qty-btn.minus:hover { background: #e2e8f0; }
    .qty-btn.plus { background: #ff6b35; color: white; box-shadow: 0 4px 12px rgba(255,107,53,0.35); }
    .qty-btn.plus:hover { background: #e55a2b; transform: scale(1.1); }

    /* ── Description Prose ── */
    .dest-prose { font-size: 1.0625rem; line-height: 1.85; color: #374151; }
    .dest-prose p { margin-bottom: 1.25rem; }

    /* ── Feature Chip ── */
    .feat-chip {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: white; border-radius: 1rem;
        border: 1.5px solid #e5e7eb;
        transition: all 0.3s;
    }
    .feat-chip:hover { border-color: #ff8559; transform: translateY(-3px); box-shadow: 0 12px 28px -8px rgba(255,107,53,0.15); }
    .feat-chip .icon-wrap {
        width: 2.5rem; height: 2.5rem; border-radius: 0.75rem;
        background: linear-gradient(135deg, #ff6b35, #e55a2b);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 0.8rem; flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(255,107,53,0.3);
    }

    /* ── Tour Package Card ── */
    .tour-card {
        background: white; border-radius: 1.5rem;
        overflow: hidden; border: 1.5px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 24px -8px rgba(0,0,0,0.06);
    }
    .tour-card:hover { transform: translateY(-10px); box-shadow: 0 28px 48px -12px rgba(0,26,51,0.18); border-color: transparent; }
    .tour-card-img { overflow: hidden; position: relative; }
    .tour-card-img img { transition: transform 0.7s ease; }
    .tour-card:hover .tour-card-img img { transform: scale(1.08); }

    /* ── Map Container ── */
    .map-wrap {
        border-radius: 2rem; overflow: hidden;
        box-shadow: 0 40px 80px -20px rgba(0,26,51,0.15);
        border: 3px solid white;
    }

    /* ── Section Header ── */
    .section-head { position: relative; display: inline-block; }
    .section-head::after {
        content: ''; position: absolute;
        bottom: -8px; left: 0; width: 40px; height: 4px;
        background: linear-gradient(90deg, #ff6b35, #e55a2b);
        border-radius: 99px;
    }

    /* ── Floating Category Badge ── */
    @keyframes float-badge { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
    .float-badge { animation: float-badge 4s ease-in-out infinite; }

    /* ── Rating Stars ── */
    .star-filled { color: #fbbf24; }
    .star-empty  { color: #e5e7eb; }

    /* ── Scroll Reveal ── */
    @keyframes fadeSlideUp { from { opacity:0; transform:translateY(32px); } to { opacity:1; transform:translateY(0); } }
    .anim-up { animation: fadeSlideUp 0.7s ease-out both; }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.45s; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════
     CINEMATIC HERO
═══════════════════════════════════════════════ --}}
<section class="dest-hero" id="destHero">

    {{-- Background Image --}}
    <div class="dest-hero-bg" id="heroBg"
         style="background-image: url('{{ $destination->image ? asset('storage/' . ltrim($destination->image, '/')) : asset('images/fallback.jpg') }}')">
    </div>

    {{-- Layered Vignette --}}
    <div class="dest-hero-vignette"></div>

    {{-- Subtle HUD Lines --}}
    <div class="hud-line" style="top:30%; left:0; width:100%; height:1px; opacity:0.08;"></div>
    <div class="hud-line" style="top:0; left:25%; width:1px; height:100%; opacity:0.06;"></div>
    <div class="hud-line" style="top:0; left:75%; width:1px; height:100%; opacity:0.06;"></div>
    <div class="hud-corner" style="top:20px; left:20px; border-width: 2px 0 0 2px;"></div>
    <div class="hud-corner" style="top:20px; right:20px; border-width: 2px 2px 0 0;"></div>
    <div class="hud-corner" style="bottom:80px; left:20px; border-width: 0 0 2px 2px;"></div>
    <div class="hud-corner" style="bottom:80px; right:20px; border-width: 0 2px 2px 0;"></div>

    {{-- Hero Content --}}
    <div class="absolute inset-0 flex flex-col justify-end pb-20 px-6 sm:px-10 lg:px-20 z-10 max-w-7xl mx-auto w-full left-0 right-0">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-white/40 mb-8 anim-up">
            <a href="{{ route('home') }}" class="hover:text-white/70 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[8px] text-white/25"></i>
            <a href="{{ route('destinations.index') }}" class="hover:text-white/70 transition-colors">Destinations</a>
            <i class="fas fa-chevron-right text-[8px] text-white/25"></i>
            <span class="text-sunset-500">{{ $destination->name }}</span>
        </nav>

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div class="flex-1">
                {{-- Category & Popular Badge --}}
                <div class="flex items-center gap-3 mb-5 anim-up delay-1">
                    <span class="pill-tag bg-sunset-500/20 border border-sunset-500/40 text-sunset-500 float-badge">
                        <i class="fas fa-{{ strtolower($destination->category) === 'beach' ? 'umbrella-beach' : (strtolower($destination->category) === 'mountain' ? 'mountain' : (strtolower($destination->category) === 'culture' ? 'landmark' : 'leaf')) }}"></i>
                        {{ $destination->category }}
                    </span>
                    @if($destination->is_popular)
                    <span class="pill-tag bg-yellow-400/15 border border-yellow-400/35 text-yellow-300">
                        <i class="fas fa-fire"></i> Popular
                    </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-white font-montserrat leading-none tracking-tight drop-shadow-2xl mb-5 anim-up delay-2">
                    {{ $destination->name }}
                </h1>

                {{-- Location + Rating --}}
                <div class="flex flex-wrap items-center gap-4 anim-up delay-3">
                    <div class="flex items-center gap-2 text-white/70 text-sm font-bold">
                        <i class="fas fa-map-marker-alt text-sunset-500"></i>
                        {{ $destination->location }}
                    </div>

                    @if($destination->rating)
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/15">
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= round($destination->rating) ? 'star-filled' : 'star-empty' }}"></i>
                            @endfor
                        </div>
                        <span class="text-white font-black text-sm">{{ number_format($destination->rating, 1) }}</span>
                        @if($destination->rating_count)
                        <span class="text-white/50 text-xs font-medium">({{ number_format($destination->rating_count) }} reviews)</span>
                        @endif
                    </div>
                    @endif

                    @if($destination->price)
                    <div class="flex items-center gap-2 bg-sunset-500/20 backdrop-blur-md px-4 py-2 rounded-full border border-sunset-500/30">
                        <i class="fas fa-ticket-alt text-sunset-500 text-xs"></i>
                        <span class="text-white font-black text-sm">Rp{{ number_format($destination->price, 0, ',', '.') }}/person</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quick Info Cards --}}
            <div class="grid grid-cols-3 gap-3 md:w-80 anim-up delay-4">
                <div class="info-glass p-3.5 text-center">
                    <i class="fas fa-calendar-check text-sunset-500 text-lg mb-1.5 block"></i>
                    <p class="text-[10px] uppercase tracking-widest text-white/40 font-bold mb-0.5">Best Time</p>
                    <p class="text-white font-black text-xs leading-tight">Year-round</p>
                </div>
                <div class="info-glass p-3.5 text-center">
                    <i class="fas fa-shield-alt text-sunset-500 text-lg mb-1.5 block"></i>
                    <p class="text-[10px] uppercase tracking-widest text-white/40 font-bold mb-0.5">Safety</p>
                    <p class="text-white font-black text-xs leading-tight">Very High</p>
                </div>
                <div class="info-glass p-3.5 text-center">
                    <i class="fas fa-clock text-sunset-500 text-lg mb-1.5 block"></i>
                    <p class="text-[10px] uppercase tracking-widest text-white/40 font-bold mb-0.5">Duration</p>
                    <p class="text-white font-black text-xs leading-tight">Full Day</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 z-10">
        <span class="text-white/30 text-[10px] uppercase tracking-widest font-bold">Scroll</span>
        <div class="scroll-ind w-6 h-9 rounded-full border-2 border-white/20 flex items-start justify-center pt-1.5">
            <div class="w-1 h-2 bg-white/50 rounded-full"></div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════ --}}
<div class="content-wrap">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex flex-col xl:flex-row gap-12">

        {{-- ── LEFT: Main Content ── --}}
        <div class="flex-1 min-w-0 space-y-16">

            {{-- ABOUT SECTION --}}
            <section class="reveal">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sunset-500 to-sunset-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-compass text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="section-head text-2xl font-black text-ocean-900 font-montserrat">About {{ $destination->name }}</h2>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-8 shadow-soft border border-gray-100/60">
                    {{-- Main Image --}}
                    <div class="relative rounded-2xl overflow-hidden mb-8 h-80 sm:h-96 group">
                        <img src="{{ $destination->image ? asset('storage/' . ltrim($destination->image, '/')) : asset('images/fallback.jpg') }}"
                             alt="{{ $destination->name }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                             onerror="this.src='/images/fallback.jpg'">
                        <div class="absolute inset-0 bg-gradient-to-t from-ocean-900/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        {{-- Image Caption bar --}}
                        <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-500 bg-gradient-to-t from-ocean-900/80 to-transparent">
                            <p class="text-white font-bold text-sm flex items-center gap-2">
                                <i class="fas fa-image text-sunset-500"></i>
                                {{ $destination->name }} — {{ $destination->location }}
                            </p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="dest-prose">
                        @if($destination->description)
                            {!! Str::markdown($destination->description) !!}
                        @else
                            <p>Temukan keindahan {{ $destination->name }} yang menakjubkan di {{ $destination->location }}. Destinasi ini menawarkan pengalaman wisata yang tak terlupakan dengan pemandangan alam yang luar biasa indah.</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- HIGHLIGHT FEATURES --}}
            <section class="reveal">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-ocean-700 to-ocean-900 flex items-center justify-center shadow-lg">
                        <i class="fas fa-star text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="section-head text-2xl font-black text-ocean-900 font-montserrat">Highlights</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @php
                        $catLower = strtolower($destination->category ?? '');
                        $highlights = [];
                        if ($catLower === 'beach') {
                            $highlights = [
                                ['icon'=>'fa-water',        'title'=>'Crystal Waters',      'desc'=>'Exceptionally clear water visibility up to 30m'],
                                ['icon'=>'fa-sun',          'title'=>'Sunny Weather',        'desc'=>'Enjoy 280+ sunny days each year'],
                                ['icon'=>'fa-fish',         'title'=>'Marine Life',          'desc'=>'Vibrant coral reef ecosystem to explore'],
                                ['icon'=>'fa-camera',       'title'=>'Scenic Views',         'desc'=>'Stunning panoramic vistas at every angle'],
                                ['icon'=>'fa-anchor',       'title'=>'Water Sports',         'desc'=>'Snorkeling, diving & boat trips available'],
                                ['icon'=>'fa-concierge-bell','title'=>'Local Cuisine',       'desc'=>'Fresh seafood & traditional NTT dishes'],
                            ];
                        } elseif ($catLower === 'mountain') {
                            $highlights = [
                                ['icon'=>'fa-mountain',     'title'=>'Scenic Trekking',      'desc'=>'Well-maintained trails for all skill levels'],
                                ['icon'=>'fa-cloud',        'title'=>'Cloud Sea',            'desc'=>'Breathtaking cloud views from the summit'],
                                ['icon'=>'fa-leaf',         'title'=>'Rich Flora',           'desc'=>'Diverse endemic plant & wildlife species'],
                                ['icon'=>'fa-binoculars',   'title'=>'Panoramic Views',      'desc'=>'360° views of NTT\'s stunning landscape'],
                                ['icon'=>'fa-fire',         'title'=>'Campfire Nights',      'desc'=>'Camping facilities with stunning stargazing'],
                                ['icon'=>'fa-camera',       'title'=>'Photography Spots',    'desc'=>'Perfect golden hour & misty morning shots'],
                            ];
                        } elseif ($catLower === 'culture') {
                            $highlights = [
                                ['icon'=>'fa-landmark',     'title'=>'Heritage Sites',       'desc'=>'Ancient temples & traditional architecture'],
                                ['icon'=>'fa-masks-theater','title'=>'Cultural Shows',       'desc'=>'Traditional dances & ritual performances'],
                                ['icon'=>'fa-paint-brush',  'title'=>'Local Crafts',         'desc'=>'Handwoven ikat fabric & traditional art'],
                                ['icon'=>'fa-utensils',     'title'=>'Traditional Food',     'desc'=>'Authentic NTT cuisine & local delicacies'],
                                ['icon'=>'fa-users',        'title'=>'Community Tours',      'desc'=>'Engage with local tribes & communities'],
                                ['icon'=>'fa-camera',       'title'=>'Photo Moments',        'desc'=>'Colorful traditional attire & ceremonies'],
                            ];
                        } else {
                            $highlights = [
                                ['icon'=>'fa-tree',         'title'=>'Natural Beauty',       'desc'=>'Untouched natural landscapes & ecosystems'],
                                ['icon'=>'fa-binoculars',   'title'=>'Wildlife Spotting',    'desc'=>'Diverse endemic flora & fauna species'],
                                ['icon'=>'fa-hiking',       'title'=>'Adventure Trails',     'desc'=>'Scenic treks through stunning terrain'],
                                ['icon'=>'fa-camera',       'title'=>'Photography',          'desc'=>'Incredible shots at sunrise & sunset'],
                                ['icon'=>'fa-leaf',         'title'=>'Eco-Tourism',          'desc'=>'Sustainable eco-friendly experiences'],
                                ['icon'=>'fa-map-marked-alt','title'=>'Guided Tours',        'desc'=>'Expert local guides available daily'],
                            ];
                        }
                    @endphp
                    @foreach($highlights as $h)
                    <div class="feat-chip">
                        <div class="icon-wrap"><i class="fas {{ $h['icon'] }}"></i></div>
                        <div>
                            <p class="font-black text-ocean-900 text-sm font-montserrat">{{ $h['title'] }}</p>
                            <p class="text-gray-400 text-xs font-medium leading-tight mt-0.5">{{ $h['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            {{-- TOUR PACKAGES --}}
            @php
                $tourPackages = $destination->tourPackages()->get();
            @endphp
            @if($tourPackages->count() > 0)
            <section class="reveal">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center shadow-lg">
                        <i class="fas fa-suitcase-rolling text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="section-head text-2xl font-black text-ocean-900 font-montserrat">Tour Packages</h2>
                        <p class="text-gray-400 text-sm font-medium mt-1">Ready-made tours departing to {{ $destination->name }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($tourPackages as $tour)
                    <div class="tour-card">
                        <div class="tour-card-img h-52">
                            <img src="{{ $tour->thumbnail ? asset('storage/' . ltrim($tour->thumbnail, '/')) : asset('images/fallback.jpg') }}"
                                 alt="{{ $tour->name }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.src='/images/fallback.jpg'">
                            <div class="absolute inset-0 bg-gradient-to-t from-ocean-900/60 to-transparent"></div>
                            {{-- Badges --}}
                            <div class="absolute top-4 left-4 flex gap-2">
                                @if($tour->days)
                                <span class="bg-ocean-900/80 backdrop-blur text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest border border-white/10">
                                    {{ $tour->days }}D
                                </span>
                                @endif
                            </div>
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1.5 rounded-full flex items-center gap-1.5">
                                <i class="fas fa-star text-yellow-400 text-[10px]"></i>
                                <span class="text-ocean-900 font-black text-xs">{{ $tour->rating ?? '4.8' }}</span>
                            </div>
                            {{-- Hover CTA --}}
                            <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0">
                                <a href="{{ route('paket-tours.show', $tour->id) }}"
                                   class="block text-center bg-sunset-500 text-white font-black text-sm py-2.5 rounded-xl shadow-lg">
                                    View Details
                                </a>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-black text-ocean-900 text-base font-montserrat mb-1 hover:text-sunset-500 transition-colors">
                                <a href="{{ route('paket-tours.show', $tour->id) }}">{{ $tour->name }}</a>
                            </h3>
                            <div class="flex items-center gap-1.5 text-sunset-500 text-xs font-bold mb-4">
                                <i class="fas fa-map-marker-alt text-[10px]"></i> {{ $tour->location }}
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-0.5">Price / Person</p>
                                    <p class="font-black text-ocean-900 text-lg font-montserrat">
                                        Rp{{ number_format($tour->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                <a href="{{ route('paket-tours.show', $tour->id) }}"
                                   class="flex items-center gap-2 bg-ocean-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-ocean-800 transition-all hover:-translate-y-0.5 shadow-md">
                                    Book Tour <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- LOCATION / MAP --}}
            @if($destination->latitude && $destination->longitude)
            <section class="reveal">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-lg">
                        <i class="fas fa-map-marked-alt text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="section-head text-2xl font-black text-ocean-900 font-montserrat">Location</h2>
                        <p class="text-gray-400 text-sm font-medium mt-1">
                            {{ number_format($destination->latitude, 4) }}°, {{ number_format($destination->longitude, 4) }}°
                            @if($destination->maps_url)
                            — <a href="{{ $destination->maps_url }}" target="_blank" class="text-sunset-500 hover:underline font-bold">Open in Google Maps</a>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="map-wrap">
                    <iframe
                        class="w-full h-[400px] sm:h-[480px]"
                        frameborder="0" scrolling="no"
                        src="https://maps.google.com/maps?q={{ $destination->latitude }},{{ $destination->longitude }}&z=14&output=embed"
                        allowfullscreen
                        aria-label="Map of {{ $destination->name }}">
                    </iframe>
                </div>
            </section>
            @endif

        </div>

        {{-- ── RIGHT: Booking Card ── --}}
        <div class="xl:w-96 flex-shrink-0">
            <div class="booking-card">
                {{-- Card Header --}}
                <div class="booking-card-header">
                    <p class="text-white/50 text-[10px] uppercase tracking-widest font-black mb-3">Book Your Visit</p>
                    <h3 class="text-white font-black text-2xl font-montserrat leading-tight mb-3">{{ $destination->name }}</h3>
                    <div class="flex items-baseline gap-1.5">
                        @if($destination->price)
                        <span class="text-sunset-500 font-black text-3xl font-montserrat">Rp{{ number_format($destination->price, 0, ',', '.') }}</span>
                        <span class="text-white/40 text-sm font-medium">per person</span>
                        @else
                        <span class="text-white/50 text-base font-medium">Contact for pricing</span>
                        @endif
                    </div>
                </div>

                <form id="quickBookForm" action="{{ route('destinations.book', $destination->id) }}" method="GET">
                    <div class="p-6 space-y-5">

                        {{-- Visit Date --}}
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-2">Visit Date</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sunset-500 pointer-events-none w-5 text-center">
                                    <i class="fas fa-calendar-day text-sm"></i>
                                </span>
                                <input type="date" id="visitDate" name="date"
                                       value="{{ date('Y-m-d') }}"
                                       min="{{ date('Y-m-d') }}"
                                       style="width:100%; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:0.875rem; padding:0.875rem 1rem 0.875rem 2.75rem; font-size:0.875rem; font-weight:700; color:#001a33; outline:none; transition:all 0.25s;">
                            </div>
                        </div>

                        {{-- Ticket Quantity --}}
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-2">Number of Tickets</label>
                            <div class="flex items-center gap-4 bg-gray-50 rounded-2xl p-3 border border-gray-100">
                                <button type="button" class="qty-btn minus" id="qtyMinus">−</button>
                                <div class="flex-1 text-center">
                                    <span id="qtyDisplay" class="text-2xl font-black text-ocean-900 font-montserrat">1</span>
                                    <span class="text-gray-400 text-xs font-medium block -mt-0.5">ticket(s)</span>
                                </div>
                                <button type="button" class="qty-btn plus" id="qtyPlus">+</button>
                                <input type="hidden" id="qtyInput" name="qty" value="1">
                            </div>
                        </div>

                        {{-- Price Preview --}}
                        @if($destination->price)
                        <div class="bg-gradient-to-r from-ocean-900/5 to-sunset-500/5 rounded-2xl p-4 border border-ocean-900/8">
                            <div class="flex justify-between text-sm text-gray-500 font-medium mb-2">
                                <span>Rp{{ number_format($destination->price, 0, ',', '.') }} × <span id="previewQty">1</span> person</span>
                                <span id="previewSubtotal">Rp{{ number_format($destination->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between font-black text-ocean-900 text-base font-montserrat border-t border-gray-200 pt-2 mt-2">
                                <span>Estimated Total</span>
                                <span id="previewTotal" class="text-sunset-500">Rp{{ number_format($destination->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endif

                        {{-- Book Now Button --}}
                        @auth
                        <a href="{{ route('destinations.book', $destination->id) }}"
                           id="bookNowBtn"
                           class="flex items-center justify-center gap-2.5 w-full py-4 rounded-2xl font-black text-base text-white transition-all duration-300 hover:-translate-y-1"
                           style="background: linear-gradient(135deg, #ff6b35, #e55a2b); box-shadow: 0 10px 28px rgba(255,107,53,0.4);">
                            <i class="fas fa-ticket-alt text-sm"></i>
                            Book Tickets Now
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                           class="flex items-center justify-center gap-2.5 w-full py-4 rounded-2xl font-black text-base text-white transition-all duration-300 hover:-translate-y-1"
                           style="background: linear-gradient(135deg, #374151, #1f2937); box-shadow: 0 10px 28px rgba(0,0,0,0.25);">
                            <i class="fas fa-lock text-sm text-sunset-400"></i>
                            Login untuk Booking
                        </a>
                        <p class="text-xs text-gray-400 text-center">Daftar gratis, booking mudah & aman</p>
                        @endauth

                        {{-- Trust Points --}}
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-shield-alt text-green-500 text-xs"></i>
                                <span class="text-[11px] text-gray-400 font-bold">Secure Booking</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-undo text-blue-500 text-xs"></i>
                                <span class="text-[11px] text-gray-400 font-bold">Free Cancel</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-headset text-sunset-500 text-xs"></i>
                                <span class="text-[11px] text-gray-400 font-bold">24/7 Support</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                <span class="text-[11px] text-gray-400 font-bold">Top Rated</span>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Share Section --}}
                <div class="px-6 pb-6">
                    <div class="border-t border-gray-100 pt-5">
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-300 mb-3">Share This Place</p>
                        <div class="flex gap-2.5">
                            @php $shareUrl = urlencode(url()->current()); $shareName = urlencode($destination->name); @endphp
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                               class="flex-1 py-2.5 rounded-xl bg-[#1877f2]/10 text-[#1877f2] text-xs font-black flex items-center justify-center gap-1.5 hover:bg-[#1877f2] hover:text-white transition-all">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://wa.me/?text={{ $shareName }}%20{{ $shareUrl }}" target="_blank"
                               class="flex-1 py-2.5 rounded-xl bg-[#25d366]/10 text-[#25d366] text-xs font-black flex items-center justify-center gap-1.5 hover:bg-[#25d366] hover:text-white transition-all">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>{this.innerHTML='<i class=\'fas fa-check\'></i>';setTimeout(()=>{this.innerHTML='<i class=\'fas fa-link\'></i>'},2000)})"
                                    class="w-10 h-10 rounded-xl bg-gray-100 text-gray-500 text-xs flex items-center justify-center hover:bg-ocean-900 hover:text-white transition-all">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end flex --}}
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── Parallax hero background ──
    const heroBg = document.getElementById('heroBg');
    if (heroBg) {
        heroBg.classList.add('loaded');
        window.addEventListener('scroll', () => {
            const scrollY = window.pageYOffset;
            const hero = document.getElementById('destHero');
            if (hero && scrollY < hero.offsetHeight * 1.5) {
                heroBg.style.transform = `scale(1.0) translateY(${scrollY * 0.3}px)`;
            }
        }, { passive: true });
    }

    // ── Ticket Quantity ──
    const qtyMinus   = document.getElementById('qtyMinus');
    const qtyPlus    = document.getElementById('qtyPlus');
    const qtyDisplay = document.getElementById('qtyDisplay');
    const qtyInput   = document.getElementById('qtyInput');
    const previewQty     = document.getElementById('previewQty');
    const previewSubtotal= document.getElementById('previewSubtotal');
    const previewTotal   = document.getElementById('previewTotal');
    const pricePerPerson = {{ $destination->price ?? 0 }};

    let qty = 1;
    const fmt = n => 'Rp' + Math.round(n).toLocaleString('id-ID');
    const updateQty = () => {
        qtyDisplay.textContent = qty;
        qtyInput.value = qty;
        if (previewQty) previewQty.textContent = qty;
        if (previewSubtotal && pricePerPerson) previewSubtotal.textContent = fmt(pricePerPerson * qty);
        if (previewTotal && pricePerPerson) previewTotal.textContent = fmt(pricePerPerson * qty);
        qtyMinus.style.opacity = qty <= 1 ? '0.4' : '1';
        qtyMinus.disabled = qty <= 1;
    };
    if (qtyMinus) qtyMinus.addEventListener('click', () => { if (qty > 1) { qty--; updateQty(); } });
    if (qtyPlus)  qtyPlus.addEventListener('click',  () => { if (qty < 20) { qty++; updateQty(); } });
    updateQty();
});
</script>
@endpush

@endsection