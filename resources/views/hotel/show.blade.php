@extends('layouts.app')

@section('title', $hotel->name . ' — Pesona NTT Hotels')
@section('meta_description', Str::limit(strip_tags($hotel->description ?? "Pesan {$hotel->name} di {$hotel->location}, salah satu hotel terbaik di Nusa Tenggara Timur."), 160))
@section('og_title', $hotel->name . ' — Pesona NTT')
@section('og_image', $hotel->image ? asset('storage/' . $hotel->image) : asset('images/hotel-fallback.jpg'))

@php
    $prices = array_filter([$hotel->single_room_price, $hotel->double_room_price, $hotel->family_room_price], fn($v) => $v > 0);
    $minPrice = !empty($prices) ? min($prices) : 0;
    $maxPrice = !empty($prices) ? max($prices) : 0;
    $facilities = is_array($hotel->facilities) ? $hotel->facilities : ($hotel->facilities ? explode(',', $hotel->facilities) : []);
    $facilityIconMap = [
        'wifi'         => 'fa-wifi',
        'pool'         => 'fa-swimming-pool',
        'restaurant'   => 'fa-utensils',
        'parking'      => 'fa-parking',
        'spa'          => 'fa-spa',
        'gym'          => 'fa-dumbbell',
        'bar'          => 'fa-cocktail',
        'laundry'      => 'fa-tshirt',
        'ac'           => 'fa-snowflake',
        'tv'           => 'fa-tv',
        'room service' => 'fa-bell',
        'breakfast'    => 'fa-coffee',
        'airport'      => 'fa-plane',
        'beach'        => 'fa-umbrella-beach',
        'meeting'      => 'fa-users',
        'lift'         => 'fa-arrows-alt-v',
        'elevator'     => 'fa-arrows-alt-v',
    ];
@endphp

@push('styles')
<style>
    /* ── Hero ── */
    .hotel-hero { position:relative; height:88vh; min-height:580px; overflow:hidden; }
    .hotel-hero-img {
        position:absolute; inset:0; width:100%; height:100%;
        object-fit:cover; transform:scale(1.06);
        transition: transform 8s ease-out;
        will-change: transform;
    }
    .hotel-hero-img.loaded { transform:scale(1.0); }
    .hotel-hero-overlay {
        position:absolute; inset:0;
        background:
            linear-gradient(to top,    rgba(250,250,249,1) 0%,   rgba(22,32,30,0.3)  60%, transparent 100%),
            linear-gradient(to right,  rgba(22,32,30,0.4)  0%,   transparent 55%),
            linear-gradient(165deg,    rgba(22,32,30,0.2) 0%,   transparent 50%);
    }

    /* ── HUD decoration ── */
    .hud-corner { position:absolute; width:24px; height:24px; border-color:rgba(15,110,99,0.5); border-style:solid; }

    /* ── Floating Price Badge ── */
    @keyframes price-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(15,110,99,0.3)} 50%{box-shadow:0 0 0 16px rgba(15,110,99,0)} }
    .price-pulse { animation: price-pulse 3s ease-in-out infinite; }

    /* ── Stats bar ── */
    .stat-glass {
        backdrop-filter: blur(12px);
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.15);
    }

    /* ── Page body ── */
    .hotel-body { background: #fafaf9; }

    /* ── Section heading ── */
    .sec-head {
        display:flex; align-items:center; gap:1rem; margin-bottom:2rem;
    }
    .sec-icon {
        width:3rem; height:3rem; border-radius:1rem; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-size:1.1rem; color:#475569;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        box-shadow: 0 4px 10px rgba(15,23,42,0.04);
        border: 1px solid rgba(15,23,42,0.05);
    }
    .sec-title { font-size:1.5rem; font-weight:900; color:#1e293b; font-family:'Satoshi',sans-serif; letter-spacing:-0.025em; position:relative; display:inline-block; }
    .sec-title::after { content:''; position:absolute; bottom:-6px; left:0; width:36px; height:3px; background:linear-gradient(90deg,#0F6E63,#1C4750); border-radius:99px; }

    /* ── Content card ── */
    .content-card { background: #ffffff; color: #475569; border-radius:1.75rem; padding:2.25rem; box-shadow:0 8px 32px -8px rgba(15,23,42,0.04); border:1px solid rgba(15, 23, 42, 0.05); }

    /* ── Facility chip ── */
    .fac-chip {
        display:flex; align-items:center; gap:0.875rem;
        padding:0.875rem 1.1rem;
        background: #ffffff; border-radius:1rem;
        border:1px solid rgba(15, 23, 42, 0.06);
        color: #1e293b;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .fac-chip:hover { border-color:#0F6E63; transform:translateY(-4px); box-shadow:0 14px 28px -8px rgba(15,110,99,0.08); }
    .fac-chip .chip-icon {
        width:2.5rem; height:2.5rem; border-radius:0.75rem; flex-shrink:0;
        background:linear-gradient(135deg,#0F6E63,#1C4750);
        display:flex; align-items:center; justify-content:center;
        color:white; font-size:0.85rem;
        box-shadow:0 4px 10px rgba(15,110,99,0.2);
    }

    /* ── Room card ── */
    .room-card {
        background: #ffffff; border-radius:1.75rem; overflow:hidden;
        border:1px solid rgba(15, 23, 42, 0.05);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow:0 6px 24px -8px rgba(15,23,42,0.03);
        position:relative;
        color: #1e293b;
    }
    .room-card:hover { border-color:#0F6E63; transform:translateY(-6px); box-shadow:0 24px 48px -12px rgba(15,110,99,0.12); }
    .room-card-img { position:relative; height:220px; overflow:hidden; }
    .room-card-img img { width:100%; height:100%; object-fit:cover; transition:transform 0.7s ease; }
    .room-card:hover .room-card-img img { transform:scale(1.07); }
    .room-card-img .img-overlay { position:absolute;inset:0;background:linear-gradient(to top,rgba(15,23,42,0.3),transparent); }
    .room-badge {
        position:absolute; top:1rem; left:1rem;
        background:rgba(255,255,255,0.95); backdrop-filter:blur(8px);
        color:#1e293b; font-size:0.65rem; font-weight:900;
        padding:0.35rem 0.85rem; border-radius:99px;
        text-transform:uppercase; letter-spacing:0.08em;
        border:1px solid rgba(15,23,42,0.1);
    }
    .room-popular-badge {
        position:absolute; top:1rem; right:1rem;
        background:linear-gradient(135deg,#0F6E63,#1C4750);
        color:white; font-size:0.6rem; font-weight:900;
        padding:0.3rem 0.75rem; border-radius:99px;
        text-transform:uppercase; letter-spacing:0.08em;
    }
    .room-feature { display:flex; align-items:center; gap:0.5rem; font-size:0.75rem; font-weight:700; color:#475569; }
    .room-feature i { color:#0F6E63; font-size:0.65rem; }

    /* ── Room price tag ── */
    .room-price-tag { font-size:1.875rem; font-weight:900; color:#0F6E63; font-family:'Satoshi',sans-serif; line-height:1; }

    /* ── Reserve button ── */
    .reserve-btn {
        display:flex; align-items:center; justify-content:center; gap:0.5rem;
        padding:0.875rem 1.5rem; border-radius:0.875rem;
        background:linear-gradient(135deg,#0F6E63,#1C4750);
        color:white; font-weight:900; font-size:0.875rem;
        transition:all 0.3s; border:none; cursor:pointer;
        box-shadow:0 8px 20px rgba(15,110,99,0.25);
        width:100%; text-decoration:none;
    }
    .reserve-btn:hover { transform:translateY(-2px); box-shadow:0 14px 28px rgba(15,110,99,0.35); }

    /* ── Sidebar card ── */
    .sidebar-card {
        background: #ffffff; border-radius: 1.75rem;
        border: 1px solid rgba(15, 23, 42, 0.06);
        backdrop-filter: blur(16px);
        box-shadow: 0 20px 50px -15px rgba(15, 23, 42, 0.06);
        overflow:hidden;
        position:sticky; top:100px;
        color: #1e293b;
    }
    .sidebar-header {
        background: #ffffff;
        padding:1.75rem;
        border-bottom: 1px solid rgba(15, 23, 42, 0.05);
    }

    /* ── Contact row ── */
    .contact-row { display:flex; align-items:flex-start; gap:1rem; padding:1rem 0; }
    .contact-row + .contact-row { border-top:1px solid rgba(15,23,42,0.05); }
    .contact-icon {
        width:2.5rem; height:2.5rem; border-radius:0.75rem; flex-shrink:0;
        background: rgba(15, 23, 42, 0.03); display:flex; align-items:center; justify-content:center;
        color: #475569; font-size:0.85rem;
        border:1px solid rgba(15,23,42,0.05);
    }

    /* ── Star display ── */
    .star-on { color:#D2674A; }
    .star-off { color:rgba(255,255,255,0.25); }

    /* ── Availability badge ── */
    .avail-badge {
        display:inline-flex; align-items:center; gap:0.35rem;
        padding:0.3rem 0.75rem; border-radius:99px;
        font-size:0.65rem; font-weight:900; text-transform:uppercase; letter-spacing:0.08em;
    }
    .avail-badge.available { background:#dcfce7; color:#15803d; }
    .avail-badge.limited   { background:#fef9c3; color:#854d0e; }
    .avail-badge.full      { background:#fee2e2; color:#b91c1c; }
    .avail-badge .dot { width:6px; height:6px; border-radius:50%; }
    .available .dot { background:#22c55e; }
    .limited .dot   { background:#eab308; }
    .full .dot      { background:#ef4444; }

    /* ── Parallax ── */
    @keyframes fadeSlideUp { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
    .anim-up { animation:fadeSlideUp 0.6s ease-out both; }
    .d1{animation-delay:.08s} .d2{animation-delay:.16s} .d3{animation-delay:.25s} .d4{animation-delay:.35s}

    /* ── Scroll cue ── */
    @keyframes bounce-y { 0%,100%{transform:translateY(0)} 50%{transform:translateY(8px)} }
    .bounce-y { animation:bounce-y 2.5s ease-in-out infinite; }

    /* ── Gallery dots ── */
    .gallery-img:not(:first-child) { display:none; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════
     CINEMATIC HERO
═══════════════════════════════════════════ --}}
<section class="hotel-hero" id="hotelHero">

    {{-- Background Image --}}
    <img id="heroImg"
         src="{{ $hotel->image ? asset('storage/' . ltrim($hotel->image, '/')) : asset('images/hotel-fallback.jpg') }}"
         alt="{{ $hotel->name }}"
         class="hotel-hero-img"
         onerror="this.src='/images/hotel-fallback.jpg'">

    {{-- Overlays --}}
    <div class="hotel-hero-overlay"></div>

    {{-- HUD Corners --}}
    <div class="hud-corner" style="top:18px;left:18px;border-width:2px 0 0 2px;"></div>
    <div class="hud-corner" style="top:18px;right:18px;border-width:2px 2px 0 0;"></div>
    <div class="hud-corner" style="bottom:90px;left:18px;border-width:0 0 2px 2px;"></div>
    <div class="hud-corner" style="bottom:90px;right:18px;border-width:0 2px 2px 0;"></div>
    <div class="absolute" style="top:30%;left:0;right:0;height:1px;background:rgba(255,255,255,0.05);"></div>

    {{-- Hero Content --}}
    <div class="absolute inset-0 flex flex-col justify-end z-10 pb-20 max-w-7xl mx-auto w-full px-6 sm:px-10 lg:px-8 left-0 right-0">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-white/35 mb-7 anim-up">
            <a href="{{ route('home') }}" class="hover:text-white/60 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[8px] text-white/20"></i>
            <a href="{{ route('hotels.index') }}" class="hover:text-white/60 transition-colors">Hotels</a>
            <i class="fas fa-chevron-right text-[8px] text-white/20"></i>
            <span class="text-clay">{{ $hotel->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="flex-1">
                {{-- Star Rating --}}
                <div class="flex items-center justify-between gap-3 mb-5 anim-up d1">
                    <div class="flex items-center gap-3">
                        <div class="flex gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-sm {{ $hotel->reviews_avg_rating && $i <= round($hotel->reviews_avg_rating) ? 'star-on' : 'star-off' }}"></i>
                            @endfor
                        </div>
                        @if($hotel->reviews_avg_rating)
                        <span class="text-white/70 text-xs font-bold">{{ number_format($hotel->reviews_avg_rating,1) }} / 5.0</span>
                        @endif
                        <span class="w-1 h-1 rounded-full bg-paper/20"></span>
                        <span class="text-white/50 text-xs font-medium">{{ $hotel->reviews_count }} reviews</span>
                    </div>
                    @php $wishlistType = 'hotel'; $wishlistId = $hotel->id; @endphp
                    @include('partials.wishlist-btn')
                </div>

                {{-- Hotel Name --}}
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-[5.5rem] font-bold text-white font-serif leading-none tracking-tight drop-shadow-2xl mb-5 anim-up d2">
                    {{ $hotel->name }}
                </h1>

                {{-- Location + type chips --}}
                <div class="flex flex-wrap items-center gap-3 anim-up d3">
                    <div class="flex items-center gap-2 text-white/80 text-sm font-bold">
                        <i class="fas fa-map-marker-alt text-clay"></i>
                        {{ $hotel->location }}
                    </div>
                    @if($hotel->address && $hotel->address !== $hotel->location)
                    <span class="w-1 h-1 rounded-full bg-paper/20"></span>
                    <span class="text-white/50 text-xs font-medium">{{ $hotel->address }}</span>
                    @endif
                    <div class="flex items-center gap-1.5 bg-paper/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                        <i class="fas fa-hotel text-clay text-xs"></i>
                        <span class="text-white text-xs font-bold">Hotel</span>
                    </div>
                </div>
            </div>

            {{-- Price Badge --}}
            @if($minPrice > 0)
            <div class="price-pulse lg:flex-shrink-0 bg-paper/10 backdrop-blur-md border border-white/20 rounded-3xl p-6 text-center shadow-2xl anim-up d4" style="min-width:200px;">
                <p class="text-[10px] uppercase tracking-widest text-white/60 font-black mb-1.5">Starting from</p>
                <p class="text-4xl font-bold text-clay font-serif leading-none">
                    Rp{{ number_format($minPrice, 0, ',', '.') }}
                </p>
                <p class="text-white/60 text-xs font-medium mt-1.5">per night</p>
                @if($maxPrice > $minPrice)
                <p class="text-white/40 text-[10px] font-medium mt-1">up to Rp{{ number_format($maxPrice, 0, ',', '.') }}</p>
                @endif
                @auth
                <a href="{{ route('hotels.book', $hotel->id) }}"
                   class="mt-4 block bg-clay hover:bg-clay/90 text-white font-bold text-xs py-2.5 rounded-xl transition-all hover:-translate-y-0.5 shadow-lg">
                    Book Now →
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="mt-4 block bg-paper/15 hover:bg-paper/25 text-white font-bold text-xs py-2.5 rounded-xl transition-all hover:-translate-y-0.5 border border-white/30 text-center">
                    <i class="fas fa-lock mr-1 text-clay"></i> Login untuk Book
                </a>
                @endauth
            </div>
            @endif
        </div>
    </div>

    {{-- Stats Bar (bottom overlay) --}}
    <div class="absolute bottom-0 left-0 right-0 z-20">
        <div class="stat-glass border-t border-white/15">
            <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-8">
                <div class="flex overflow-x-auto gap-0 divide-x divide-white/15">
                    @php
                        $stats = [
                            ['icon'=>'fa-bed',          'val'=> ($hotel->room_count_single??0)+($hotel->room_count_double??0)+($hotel->room_count_family??0) . ' Rooms', 'label'=>'Total Capacity'],
                            ['icon'=>'fa-check-circle', 'val'=>'Free Cancel',      'label'=>'No Hidden Fees'],
                            ['icon'=>'fa-concierge-bell','val'=>'24/7 Service',   'label'=>'Around the Clock'],
                            ['icon'=>'fa-shield-alt',   'val'=>'Secure Stay',     'label'=>'Verified Hotel'],
                            ['icon'=>'fa-star',         'val'=>number_format($hotel->rating??4.8,1). ' Rating', 'label'=>'Guest Satisfaction'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                    <div class="flex items-center gap-3 py-4 px-6 flex-shrink-0">
                        <i class="fas {{ $stat['icon'] }} text-clay text-sm flex-shrink-0"></i>
                        <div>
                            <p class="text-white font-bold text-xs leading-none">{{ $stat['val'] }}</p>
                            <p class="text-white/35 text-[10px] font-medium mt-0.5">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════ --}}
<div class="hotel-body bg-paper">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex flex-col xl:flex-row gap-12">

        {{-- LEFT: Content --}}
        <div class="flex-1 min-w-0 space-y-12">

            {{-- ── ABOUT ── --}}
            <section class="reveal">
                <div class="sec-head">
                    <div class="sec-icon">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">About This Hotel</h2>
                        <p class="text-muted text-sm font-medium mt-2">Everything you need to know</p>
                    </div>
                </div>
                <div class="content-card bg-surface">
                    <div class="text-ink text-base leading-relaxed" style="line-height:1.9;">
                        @if($hotel->description)
                            {!! nl2br(e($hotel->description)) !!}
                        @else
                            <p>{{ $hotel->name }} adalah hotel pilihan terbaik di {{ $hotel->location }}, menawarkan kenyamanan dan kemewahan bagi setiap tamu yang menginap. Dengan fasilitas lengkap dan layanan prima, hotel ini siap menjadikan pengalaman perjalanan Anda tak terlupakan.</p>
                        @endif
                    </div>

                    {{-- Quick facts grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-8 border-t border-line">
                        @php
                            $facts = [
                                ['icon'=>'fa-map-marker-alt','label'=>'Location', 'val'=>$hotel->location ?? '-'],
                                ['icon'=>'fa-phone-alt',     'label'=>'Phone',    'val'=>$hotel->phone ?? 'On request'],
                                ['icon'=>'fa-clock',         'label'=>'Check-In', 'val'=>'14:00'],
                                ['icon'=>'fa-sign-out-alt',  'label'=>'Check-Out','val'=>'12:00'],
                            ];
                        @endphp
                        @foreach($facts as $fact)
                        <div class="text-center p-3 rounded-xl bg-paper border border-line">
                            <i class="fas {{ $fact['icon'] }} text-clay text-lg mb-2 block"></i>
                            <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-0.5">{{ $fact['label'] }}</p>
                            <p class="font-bold text-ink text-xs leading-tight">{{ Str::limit($fact['val'],22) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ── FACILITIES ── --}}
            @if(count($facilities) > 0)
            <section class="reveal">
                <div class="sec-head">
                    <div class="sec-icon">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">Premium Facilities</h2>
                        <p class="text-muted text-sm font-medium mt-2">{{ count($facilities) }} amenities included</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($facilities as $fac)
                    @php
                        $f = strtolower(trim($fac));
                        $icon = 'fa-check-circle';
                        foreach($facilityIconMap as $key => $ico) {
                            if(str_contains($f, $key)) { $icon = $ico; break; }
                        }
                    @endphp
                    <div class="fac-chip bg-surface border border-line">
                        <div class="chip-icon"><i class="fas {{ $icon }}"></i></div>
                        <span class="font-bold text-ink text-sm">{{ ucfirst(trim($fac)) }}</span>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- ── ROOMS ── --}}
            <section class="reveal">
                <div class="sec-head">
                    <div class="sec-icon">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">Available Rooms</h2>
                        <p class="text-muted text-sm font-medium mt-2">Select the perfect room for your stay</p>
                    </div>
                </div>

                <div class="space-y-6">
                    @php
                        $roomDefs = [
                            [
                                'type'    => 'single',
                                'price'   => $hotel->single_room_price,
                                'count'   => $hotel->room_count_single ?? 0,
                                'label'   => 'Superior Single',
                                'badge'   => 'Single',
                                'tagline' => 'Perfect for solo travelers',
                                'popular' => false,
                                'features' => ['1 King/Queen Bed','Free Wi-Fi Included','City / Garden View','24-Hour Room Service','Air Conditioning','Flat-Screen TV'],
                                'capacity' => '1 guest',
                                'size'    => '28 m²',
                            ],
                            [
                                'type'    => 'double',
                                'price'   => $hotel->double_room_price,
                                'count'   => $hotel->room_count_double ?? 0,
                                'label'   => 'Deluxe Double',
                                'badge'   => 'Double',
                                'tagline' => 'Ideal for couples & partners',
                                'popular' => true,
                                'features' => ['1 Large Double Bed','Free Wi-Fi Included','Pool / Oceanview','24-Hour Room Service','Minibar','Bathtub & Shower'],
                                'capacity' => '2 guests',
                                'size'    => '38 m²',
                            ],
                            [
                                'type'    => 'family',
                                'price'   => $hotel->family_room_price,
                                'count'   => $hotel->room_count_family ?? 0,
                                'label'   => 'Executive Family Suite',
                                'badge'   => 'Family',
                                'tagline' => 'Spacious suite for the whole family',
                                'popular' => false,
                                'features' => ['2 Queen Beds','Free Wi-Fi Included','Ocean-Facing View','Kitchenette','Living Area','Butler Service'],
                                'capacity' => 'Up to 4 guests',
                                'size'    => '60 m²',
                            ],
                        ];
                    @endphp

                    @foreach($roomDefs as $room)
                        @if($room['price'] > 0)
                        @php
                            $avail = $room['count'];
                            $availClass = $avail > 5 ? 'available' : ($avail > 0 ? 'limited' : 'full');
                            $availText  = $avail > 5 ? 'Available' : ($avail > 0 ? "Only $avail left" : 'Fully Booked');
                        @endphp
                        <div class="room-card bg-surface border border-line">
                            <div class="flex flex-col lg:flex-row bg-surface">
                                {{-- Room Image --}}
                                <div class="room-card-img lg:w-72 lg:h-auto lg:flex-shrink-0 bg-paper">
                                    {{-- Use hotel image as fallback if no specific room image --}}
                                    <img src="{{ asset('images/room-' . $room['type'] . '.jpg') }}"
                                         alt="{{ $room['label'] }}"
                                         onerror="this.src='{{ $hotel->image ? asset('storage/' . ltrim($hotel->image, '/')) : asset('images/hotel-fallback.jpg') }}'">
                                    <div class="img-overlay"></div>
                                    <span class="room-badge">{{ $room['badge'] }}</span>
                                    @if($room['popular'])
                                    <span class="room-popular-badge">⭐ Most Popular</span>
                                    @endif
                                    {{-- Available count overlay --}}
                                    <div class="absolute bottom-3 left-3">
                                        <span class="avail-badge {{ $availClass }}">
                                            <span class="dot"></span> {{ $availText }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Room Details --}}
                                <div class="flex-1 p-6 lg:p-7 flex flex-col">
                                    <div class="flex items-start justify-between gap-4 mb-3">
                                        <div>
                                            <h3 class="font-bold text-ink text-xl font-serif leading-tight mb-0.5">{{ $room['label'] }}</h3>
                                            <p class="text-muted text-sm font-medium">{{ $room['tagline'] }}</p>
                                        </div>
                                        {{-- Size + Capacity pills --}}
                                        <div class="flex gap-2 flex-shrink-0">
                                            <div class="text-center px-3 py-1.5 rounded-xl bg-paper border border-line">
                                                <p class="text-[9px] uppercase tracking-widest text-muted font-bold">Size</p>
                                                <p class="text-ink font-bold text-xs">{{ $room['size'] }}</p>
                                            </div>
                                            <div class="text-center px-3 py-1.5 rounded-xl bg-paper border border-line">
                                                <p class="text-[9px] uppercase tracking-widest text-muted font-bold">Guests</p>
                                                <p class="text-ink font-bold text-xs">{{ $room['capacity'] }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Features --}}
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 mb-5">
                                        @foreach($room['features'] as $feat)
                                        <div class="room-feature">
                                            <i class="fas fa-check-circle text-clay"></i>
                                            <span class="text-ink">{{ $feat }}</span>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Price + Action --}}
                                    <div class="mt-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-line">
                                        <div>
                                            <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-0.5">Price / Night</p>
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="room-price-tag text-clay font-serif">Rp{{ number_format($room['price'], 0, ',', '.') }}</span>
                                                <span class="text-muted text-xs font-medium">incl. taxes</span>
                                            </div>
                                        </div>
                                        @if($availClass !== 'full')
                                            @auth
                                            <a href="{{ route('hotels.book', $hotel->id) }}" class="reserve-btn sm:w-auto">
                                                <i class="fas fa-lock text-xs opacity-70"></i>
                                                Reserve This Room
                                            </a>
                                            @else
                                            <a href="{{ route('login') }}" class="reserve-btn sm:w-auto text-center" style="background:#1C4750; box-shadow:none;">
                                                <i class="fas fa-lock text-xs"></i>
                                                Login untuk Booking
                                            </a>
                                            @endauth
                                        @else
                                        <button disabled class="reserve-btn sm:w-auto opacity-40 cursor-not-allowed" style="background:#9ca3af;box-shadow:none;">
                                            <i class="fas fa-times-circle text-xs"></i>
                                            Fully Booked
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </section>

            {{-- ── POLICIES ── --}}
            <section class="reveal">
                <div class="sec-head">
                    <div class="sec-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">Hotel Policies</h2>
                        <p class="text-muted text-sm font-medium mt-2">Please read before your stay</p>
                    </div>
                </div>
                <div class="content-card bg-surface border border-line">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @php
                            $policies = [
                                ['icon'=>'fa-sign-in-alt',    'color'=>'text-blue-600',   'title'=>'Check-In',       'val'=>'From 14:00 (2:00 PM)'],
                                ['icon'=>'fa-sign-out-alt',   'color'=>'text-red-500',    'title'=>'Check-Out',      'val'=>'Until 12:00 (Noon)'],
                                ['icon'=>'fa-smoking-ban',    'color'=>'text-muted',      'title'=>'Smoking',        'val'=>'Smoke-free property'],
                                ['icon'=>'fa-paw',            'color'=>'text-amber-600',  'title'=>'Pets',           'val'=>'Pets not allowed'],
                                ['icon'=>'fa-undo',           'color'=>'text-green-600',  'title'=>'Cancellation',   'val'=>'Free within 48h'],
                                ['icon'=>'fa-id-card',        'color'=>'text-purple-600', 'title'=>'ID Required',    'val'=>'KTP / Passport'],
                            ];
                        @endphp
                        @foreach($policies as $policy)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-paper border border-line">
                            <div class="w-9 h-9 rounded-xl bg-surface flex items-center justify-center flex-shrink-0 shadow-sm border border-line">
                                <i class="fas {{ $policy['icon'] }} {{ $policy['color'] }} text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-0.5">{{ $policy['title'] }}</p>
                                <p class="font-bold text-ink text-sm">{{ $policy['val'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="xl:w-88 flex-shrink-0" style="width:22rem;">
            <div class="sidebar-card bg-surface border-line">

                {{-- Sidebar Header --}}
                <div class="sidebar-header bg-surface border-line">
                    <p class="text-muted text-[10px] uppercase tracking-widest font-bold mb-2.5">Reserve Your Stay</p>
                    <h3 class="text-ink font-bold text-xl font-serif leading-tight mb-3">{{ $hotel->name }}</h3>
                    @if($minPrice > 0)
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-clay font-bold text-3xl font-serif">Rp{{ number_format($minPrice, 0, ',', '.') }}</span>
                        <span class="text-muted text-sm font-medium">/ night</span>
                    </div>
                    @endif

                    {{-- Mini Star Rating --}}
                    <div class="flex items-center gap-2 mt-3">
                        <div class="flex gap-0.5">
                            @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star text-xs {{ $i<=($hotel->rating??5)?'star-on':'star-off' }}"></i>
                            @endfor
                        </div>
                        <span class="text-muted text-xs font-medium">{{ number_format($hotel->rating??4.8,1) }} / 5.0</span>
                    </div>
                </div>

                {{-- Quick Book CTA --}}
                <div class="p-6">
                    @auth
                    <a href="{{ route('hotels.book', $hotel->id) }}"
                       class="reserve-btn mb-5">
                        <i class="fas fa-calendar-check text-sm"></i>
                        Check Availability & Book
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                       class="reserve-btn mb-5 text-center" style="background:#1C4750; box-shadow:none;">
                        <i class="fas fa-lock text-sm"></i>
                        Login untuk Booking
                    </a>
                    <p class="text-xs text-muted text-center -mt-3 mb-5">Daftar gratis dan mulai booking sekarang</p>
                    @endauth

                    {{-- Contact Info --}}
                    <div class="space-y-0">
                        @if($hotel->phone)
                        <div class="contact-row">
                            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-0.5">Call Us</p>
                                <a href="tel:{{ $hotel->phone }}" class="font-bold text-ink hover:text-clay transition-colors text-sm">{{ $hotel->phone }}</a>
                            </div>
                        </div>
                        @endif

                        @if($hotel->email)
                        <div class="contact-row">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-0.5">Email</p>
                                <a href="mailto:{{ $hotel->email }}" class="font-bold text-ink hover:text-clay transition-colors text-sm break-all">{{ $hotel->email }}</a>
                            </div>
                        </div>
                        @endif

                        <div class="contact-row">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-muted font-bold mb-0.5">Address</p>
                                <p class="font-bold text-ink text-sm">{{ $hotel->address ?? $hotel->location }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Call to action secondary --}}
                    @if($hotel->phone)
                    <a href="tel:{{ $hotel->phone }}"
                       class="flex items-center justify-center gap-2 w-full py-3 mt-5 rounded-2xl border border-line text-ink font-bold text-sm hover:bg-paper transition-all duration-300 shadow-sm">
                        <i class="fas fa-phone-alt text-xs text-clay"></i> Call Concierge
                    </a>
                    @endif
                </div>

                {{-- Trust Badges --}}
                <div class="px-6 pb-6 pt-2 border-t border-line">
                    <div class="grid grid-cols-2 gap-2.5">
                        @php
                            $trust = [
                                ['icon'=>'fa-shield-alt',    'color'=>'text-green-600',  'text'=>'Secure Booking'],
                                ['icon'=>'fa-undo',          'color'=>'text-blue-600',   'text'=>'Free Cancellation'],
                                ['icon'=>'fa-headset',       'color'=>'text-clay',       'text'=>'24/7 Support'],
                                ['icon'=>'fa-star',          'color'=>'text-clay',      'text'=>'Top Rated'],
                            ];
                        @endphp
                        @foreach($trust as $t)
                        <div class="flex items-center gap-2 py-2">
                            <i class="fas {{ $t['icon'] }} {{ $t['color'] }} text-xs"></i>
                            <span class="text-[11px] text-muted font-bold">{{ $t['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Share --}}
                <div class="px-6 pb-6 border-t border-line pt-5">
                    <p class="text-[10px] uppercase tracking-widest text-muted mb-3 font-bold">Share This Hotel</p>
                    <div class="flex gap-2">
                        @php $shareUrl = urlencode(url()->current()); $shareName = urlencode($hotel->name); @endphp
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                           class="flex-1 py-2.5 rounded-xl bg-[#1877f2]/10 text-[#1877f2] text-xs font-bold flex items-center justify-center gap-1.5 hover:bg-[#1877f2] hover:text-white transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://wa.me/?text={{ $shareName }}%20{{ $shareUrl }}" target="_blank"
                           class="flex-1 py-2.5 rounded-xl bg-[#25d366]/10 text-[#25d366] text-xs font-bold flex items-center justify-center gap-1.5 hover:bg-[#25d366] hover:text-white transition-all">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>{this.innerHTML='<i class=\'fas fa-check\'></i>';setTimeout(()=>{this.innerHTML='<i class=\'fas fa-link\'></i>'},2000)})"
                                class="w-10 rounded-xl bg-paper border border-line text-muted text-xs flex items-center justify-center hover:bg-clay hover:text-white transition-all">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end flex --}}
</div>
</div>

{{-- ── REVIEW SECTION ── --}}
@php
    $reviews        = $hotel->reviews()->with('user', 'helpfulVotes')->withCount('helpfulVotes')->latest()->get();
    $reviewableType = 'hotel';
    $reviewableId   = $hotel->id;
    $hasCompletedBooking = auth()->check() && \App\Models\BookingHotel::where('user_id', auth()->id())
        ->where('hotel_id', $hotel->id)
        ->whereIn('status', ['checked-in', 'checked-out'])
        ->exists();
@endphp
@include('partials.ai-review-summary')
@include('partials.reviews')

{{-- ── Q&A SECTION ── --}}
@php
    $questions = $hotel->questions;
    $questionableType = 'hotel';
    $questionableId = $hotel->id;
@endphp
@include('partials.qa-section')

{{-- ── SIMILAR & RECENTLY VIEWED ── --}}
@php
    $carouselTitle = 'Hotel Serupa';
    $carouselIcon = 'fas fa-hotel';
    $carouselItems = $similarHotels->map(fn($h) => ['type' => 'hotel', 'model' => $h]);
@endphp
@include('partials.item-carousel')

@php
    $carouselTitle = 'Baru Dilihat';
    $carouselIcon = 'fas fa-clock-rotate-left';
    $carouselItems = $recentlyViewedItems;
@endphp
@include('partials.item-carousel')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Parallax hero
    const heroImg = document.getElementById('heroImg');
    if (heroImg) {
        heroImg.classList.add('loaded');
        window.addEventListener('scroll', () => {
            const y = window.pageYOffset;
            const hero = document.getElementById('hotelHero');
            if (hero && y < hero.offsetHeight * 1.5) {
                heroImg.style.transform = `scale(1.0) translateY(${y * 0.28}px)`;
            }
        }, { passive: true });
    }
});
</script>
@endpush

@endsection