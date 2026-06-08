@extends('layouts.app')

@section('title', $hotel->name . ' — Wonderful NTT Hotels')

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
            linear-gradient(to top,    rgba(0,26,51,0.97) 0%,   rgba(0,26,51,0.5)  35%, transparent 65%),
            linear-gradient(to right,  rgba(0,26,51,0.5)  0%,   transparent 55%),
            linear-gradient(165deg,    rgba(0,26,51,0.25) 0%,   transparent 50%);
    }

    /* ── HUD decoration ── */
    .hud-corner { position:absolute; width:24px; height:24px; border-color:rgba(255,107,53,0.5); border-style:solid; }

    /* ── Floating Price Badge ── */
    @keyframes price-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(255,107,53,0.3)} 50%{box-shadow:0 0 0 16px rgba(255,107,53,0)} }
    .price-pulse { animation: price-pulse 3s ease-in-out infinite; }

    /* ── Stats bar ── */
    .stat-glass {
        backdrop-filter: blur(20px);
        background: rgba(0,26,51,0.75);
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* ── Page body ── */
    .hotel-body { background: linear-gradient(170deg, #f0f4f8 0%, #e8edf5 60%, #f0f4f8 100%); }

    /* ── Section heading ── */
    .sec-head {
        display:flex; align-items:center; gap:1rem; margin-bottom:2rem;
    }
    .sec-icon {
        width:3rem; height:3rem; border-radius:1rem; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        font-size:1.1rem; color:white;
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    .sec-title { font-size:1.5rem; font-weight:900; color:#001a33; font-family:'Montserrat',sans-serif; position:relative; display:inline-block; }
    .sec-title::after { content:''; position:absolute; bottom:-6px; left:0; width:36px; height:3px; background:linear-gradient(90deg,#ff6b35,#e55a2b); border-radius:99px; }

    /* ── Content card ── */
    .content-card { background:white; border-radius:1.75rem; padding:2.25rem; box-shadow:0 8px 32px -8px rgba(0,26,51,0.08); border:1px solid rgba(229,231,235,0.6); }

    /* ── Facility chip ── */
    .fac-chip {
        display:flex; align-items:center; gap:0.875rem;
        padding:0.875rem 1.1rem;
        background:white; border-radius:1rem;
        border:1.5px solid #e5e7eb;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .fac-chip:hover { border-color:#ff8559; transform:translateY(-4px); box-shadow:0 14px 28px -8px rgba(255,107,53,0.18); }
    .fac-chip .chip-icon {
        width:2.5rem; height:2.5rem; border-radius:0.75rem; flex-shrink:0;
        background:linear-gradient(135deg,#ff6b35,#e55a2b);
        display:flex; align-items:center; justify-content:center;
        color:white; font-size:0.85rem;
        box-shadow:0 4px 10px rgba(255,107,53,0.3);
    }

    /* ── Room card ── */
    .room-card {
        background:white; border-radius:1.75rem; overflow:hidden;
        border:2px solid #e5e7eb;
        transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
        box-shadow:0 6px 24px -8px rgba(0,0,0,0.07);
        position:relative;
    }
    .room-card:hover { border-color:#ff6b35; transform:translateY(-6px); box-shadow:0 24px 48px -12px rgba(255,107,53,0.2); }
    .room-card-img { position:relative; height:220px; overflow:hidden; }
    .room-card-img img { width:100%; height:100%; object-fit:cover; transition:transform 0.7s ease; }
    .room-card:hover .room-card-img img { transform:scale(1.07); }
    .room-card-img .img-overlay { position:absolute;inset:0;background:linear-gradient(to top,rgba(0,26,51,0.5),transparent); }
    .room-badge {
        position:absolute; top:1rem; left:1rem;
        background:rgba(0,26,51,0.85); backdrop-filter:blur(8px);
        color:white; font-size:0.65rem; font-weight:900;
        padding:0.35rem 0.85rem; border-radius:99px;
        text-transform:uppercase; letter-spacing:0.08em;
        border:1px solid rgba(255,255,255,0.12);
    }
    .room-popular-badge {
        position:absolute; top:1rem; right:1rem;
        background:linear-gradient(135deg,#ff6b35,#e55a2b);
        color:white; font-size:0.6rem; font-weight:900;
        padding:0.3rem 0.75rem; border-radius:99px;
        text-transform:uppercase; letter-spacing:0.08em;
    }
    .room-feature { display:flex; align-items:center; gap:0.5rem; font-size:0.75rem; font-weight:700; color:#64748b; }
    .room-feature i { color:#ff6b35; font-size:0.65rem; }

    /* ── Room price tag ── */
    .room-price-tag { font-size:1.875rem; font-weight:900; color:#ff6b35; font-family:'Montserrat',sans-serif; line-height:1; }

    /* ── Reserve button ── */
    .reserve-btn {
        display:flex; align-items:center; justify-content:center; gap:0.5rem;
        padding:0.875rem 1.5rem; border-radius:0.875rem;
        background:linear-gradient(135deg,#ff6b35,#e55a2b);
        color:white; font-weight:900; font-size:0.875rem;
        transition:all 0.3s; border:none; cursor:pointer;
        box-shadow:0 8px 20px rgba(255,107,53,0.35);
        width:100%; text-decoration:none;
    }
    .reserve-btn:hover { transform:translateY(-2px); box-shadow:0 14px 28px rgba(255,107,53,0.45); }

    /* ── Sidebar card ── */
    .sidebar-card {
        background:white; border-radius:1.75rem;
        box-shadow:0 24px 60px -16px rgba(0,26,51,0.14);
        overflow:hidden;
        position:sticky; top:100px;
    }
    .sidebar-header {
        background:linear-gradient(135deg,#001a33,#002b5e);
        padding:1.75rem;
    }

    /* ── Contact row ── */
    .contact-row { display:flex; align-items:flex-start; gap:1rem; padding:1rem 0; }
    .contact-row + .contact-row { border-top:1px solid #f1f5f9; }
    .contact-icon {
        width:2.5rem; height:2.5rem; border-radius:0.75rem; flex-shrink:0;
        background:#f8fafc; display:flex; align-items:center; justify-content:center;
        color:#001a33; font-size:0.85rem;
        border:1px solid #e2e8f0;
    }

    /* ── Star display ── */
    .star-on { color:#fbbf24; }
    .star-off { color:#e5e7eb; }

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
        <nav class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-white/35 mb-7 anim-up">
            <a href="{{ route('home') }}" class="hover:text-white/60 transition-colors">Home</a>
            <i class="fas fa-chevron-right text-[8px] text-white/20"></i>
            <a href="{{ route('hotels.index') }}" class="hover:text-white/60 transition-colors">Hotels</a>
            <i class="fas fa-chevron-right text-[8px] text-white/20"></i>
            <span class="text-sunset-500">{{ $hotel->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="flex-1">
                {{-- Star Rating --}}
                <div class="flex items-center gap-3 mb-5 anim-up d1">
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= ($hotel->rating ?? 5) ? 'star-on' : 'star-off' }}"></i>
                        @endfor
                    </div>
                    @if($hotel->rating)
                    <span class="text-white/70 text-xs font-bold">{{ number_format($hotel->rating,1) }} / 5.0</span>
                    @endif
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="text-white/50 text-xs font-medium">{{ $hotel->reviews_count ?? rand(80,350) }} reviews</span>
                </div>

                {{-- Hotel Name --}}
                <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-[5.5rem] font-black text-white font-montserrat leading-none tracking-tight drop-shadow-2xl mb-5 anim-up d2">
                    {{ $hotel->name }}
                </h1>

                {{-- Location + type chips --}}
                <div class="flex flex-wrap items-center gap-3 anim-up d3">
                    <div class="flex items-center gap-2 text-white/80 text-sm font-bold">
                        <i class="fas fa-map-marker-alt text-sunset-500"></i>
                        {{ $hotel->location }}
                    </div>
                    @if($hotel->address && $hotel->address !== $hotel->location)
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="text-white/50 text-xs font-medium">{{ $hotel->address }}</span>
                    @endif
                    <div class="flex items-center gap-1.5 bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                        <i class="fas fa-hotel text-sunset-500 text-xs"></i>
                        <span class="text-white text-xs font-bold">Hotel</span>
                    </div>
                </div>
            </div>

            {{-- Price Badge --}}
            @if($minPrice > 0)
            <div class="price-pulse lg:flex-shrink-0 bg-ocean-900/80 backdrop-blur-xl border border-white/15 rounded-3xl p-6 text-center shadow-2xl anim-up d4" style="min-width:200px;">
                <p class="text-[10px] uppercase tracking-widest text-white/40 font-black mb-1.5">Starting from</p>
                <p class="text-4xl font-black text-sunset-500 font-montserrat leading-none">
                    Rp{{ number_format($minPrice, 0, ',', '.') }}
                </p>
                <p class="text-white/50 text-xs font-medium mt-1.5">per night</p>
                @if($maxPrice > $minPrice)
                <p class="text-white/30 text-[10px] font-medium mt-1">up to Rp{{ number_format($maxPrice, 0, ',', '.') }}</p>
                @endif
                @auth
                <a href="{{ route('hotels.book', $hotel->id) }}"
                   class="mt-4 block bg-sunset-500 hover:bg-sunset-600 text-white font-black text-xs py-2.5 rounded-xl transition-all hover:-translate-y-0.5 shadow-lg">
                    Book Now →
                </a>
                @else
                <a href="{{ route('login') }}"
                   class="mt-4 block bg-white/10 hover:bg-white/20 text-white font-black text-xs py-2.5 rounded-xl transition-all hover:-translate-y-0.5 border border-white/20 text-center">
                    <i class="fas fa-lock mr-1 text-sunset-400"></i> Login untuk Book
                </a>
                @endauth
            </div>
            @endif
        </div>
    </div>

    {{-- Stats Bar (bottom overlay) --}}
    <div class="absolute bottom-0 left-0 right-0 z-20">
        <div class="stat-glass border-t border-white/10">
            <div class="max-w-7xl mx-auto px-6 sm:px-10 lg:px-8">
                <div class="flex overflow-x-auto gap-0 divide-x divide-white/10">
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
                        <i class="fas {{ $stat['icon'] }} text-sunset-500 text-sm flex-shrink-0"></i>
                        <div>
                            <p class="text-white font-black text-xs leading-none">{{ $stat['val'] }}</p>
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
<div class="hotel-body">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex flex-col xl:flex-row gap-12">

        {{-- LEFT: Content --}}
        <div class="flex-1 min-w-0 space-y-12">

            {{-- ── ABOUT ── --}}
            <section class="reveal">
                <div class="sec-head">
                    <div class="sec-icon" style="background:linear-gradient(135deg,#ff6b35,#e55a2b);">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">About This Hotel</h2>
                        <p class="text-gray-400 text-sm font-medium mt-2">Everything you need to know</p>
                    </div>
                </div>
                <div class="content-card">
                    <div class="text-gray-600 text-base leading-relaxed" style="line-height:1.9;">
                        @if($hotel->description)
                            {!! nl2br(e($hotel->description)) !!}
                        @else
                            <p>{{ $hotel->name }} adalah hotel pilihan terbaik di {{ $hotel->location }}, menawarkan kenyamanan dan kemewahan bagi setiap tamu yang menginap. Dengan fasilitas lengkap dan layanan prima, hotel ini siap menjadikan pengalaman perjalanan Anda tak terlupakan.</p>
                        @endif
                    </div>

                    {{-- Quick facts grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-8 border-t border-gray-100">
                        @php
                            $facts = [
                                ['icon'=>'fa-map-marker-alt','label'=>'Location', 'val'=>$hotel->location ?? '-'],
                                ['icon'=>'fa-phone-alt',     'label'=>'Phone',    'val'=>$hotel->phone ?? 'On request'],
                                ['icon'=>'fa-clock',         'label'=>'Check-In', 'val'=>'14:00'],
                                ['icon'=>'fa-sign-out-alt',  'label'=>'Check-Out','val'=>'12:00'],
                            ];
                        @endphp
                        @foreach($facts as $fact)
                        <div class="text-center p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <i class="fas {{ $fact['icon'] }} text-sunset-500 text-lg mb-2 block"></i>
                            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-0.5">{{ $fact['label'] }}</p>
                            <p class="font-black text-ocean-900 text-xs leading-tight">{{ Str::limit($fact['val'],22) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ── FACILITIES ── --}}
            @if(count($facilities) > 0)
            <section class="reveal">
                <div class="sec-head">
                    <div class="sec-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">Premium Facilities</h2>
                        <p class="text-gray-400 text-sm font-medium mt-2">{{ count($facilities) }} amenities included</p>
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
                    <div class="fac-chip">
                        <div class="chip-icon"><i class="fas {{ $icon }}"></i></div>
                        <span class="font-black text-ocean-900 text-sm">{{ ucfirst(trim($fac)) }}</span>
                    </div>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- ── ROOMS ── --}}
            <section class="reveal">
                <div class="sec-head">
                    <div class="sec-icon" style="background:linear-gradient(135deg,#059669,#047857);">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">Available Rooms</h2>
                        <p class="text-gray-400 text-sm font-medium mt-2">Select the perfect room for your stay</p>
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
                        <div class="room-card">
                            <div class="flex flex-col lg:flex-row">
                                {{-- Room Image --}}
                                <div class="room-card-img lg:w-72 lg:h-auto lg:flex-shrink-0">
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
                                            <h3 class="font-black text-ocean-900 text-xl font-montserrat leading-tight mb-0.5">{{ $room['label'] }}</h3>
                                            <p class="text-gray-400 text-sm font-medium">{{ $room['tagline'] }}</p>
                                        </div>
                                        {{-- Size + Capacity pills --}}
                                        <div class="flex gap-2 flex-shrink-0">
                                            <div class="text-center px-3 py-1.5 rounded-xl bg-ocean-900/5 border border-ocean-900/8">
                                                <p class="text-[9px] uppercase tracking-widest text-gray-400 font-black">Size</p>
                                                <p class="text-ocean-900 font-black text-xs">{{ $room['size'] }}</p>
                                            </div>
                                            <div class="text-center px-3 py-1.5 rounded-xl bg-ocean-900/5 border border-ocean-900/8">
                                                <p class="text-[9px] uppercase tracking-widest text-gray-400 font-black">Guests</p>
                                                <p class="text-ocean-900 font-black text-xs">{{ $room['capacity'] }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Features --}}
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 mb-5">
                                        @foreach($room['features'] as $feat)
                                        <div class="room-feature">
                                            <i class="fas fa-check-circle"></i>
                                            <span>{{ $feat }}</span>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- Price + Action --}}
                                    <div class="mt-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-100">
                                        <div>
                                            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-0.5">Price / Night</p>
                                            <div class="flex items-baseline gap-1.5">
                                                <span class="room-price-tag">Rp{{ number_format($room['price'], 0, ',', '.') }}</span>
                                                <span class="text-gray-400 text-xs font-medium">incl. taxes</span>
                                            </div>
                                        </div>
                                        @if($availClass !== 'full')
                                            @auth
                                            <a href="{{ route('hotels.book', $hotel->id) }}" class="reserve-btn sm:w-auto">
                                                <i class="fas fa-lock text-xs opacity-70"></i>
                                                Reserve This Room
                                            </a>
                                            @else
                                            <a href="{{ route('login') }}" class="reserve-btn sm:w-auto" style="background:linear-gradient(135deg,#374151,#1f2937);">
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
                    <div class="sec-icon" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <h2 class="sec-title">Hotel Policies</h2>
                        <p class="text-gray-400 text-sm font-medium mt-2">Please read before your stay</p>
                    </div>
                </div>
                <div class="content-card">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @php
                            $policies = [
                                ['icon'=>'fa-sign-in-alt',    'color'=>'text-blue-500',   'title'=>'Check-In',       'val'=>'From 14:00 (2:00 PM)'],
                                ['icon'=>'fa-sign-out-alt',   'color'=>'text-red-400',    'title'=>'Check-Out',      'val'=>'Until 12:00 (Noon)'],
                                ['icon'=>'fa-smoking-ban',    'color'=>'text-gray-500',   'title'=>'Smoking',        'val'=>'Smoke-free property'],
                                ['icon'=>'fa-paw',            'color'=>'text-amber-500',  'title'=>'Pets',           'val'=>'Pets not allowed'],
                                ['icon'=>'fa-undo',           'color'=>'text-green-500',  'title'=>'Cancellation',   'val'=>'Free within 48h'],
                                ['icon'=>'fa-id-card',        'color'=>'text-purple-500', 'title'=>'ID Required',    'val'=>'KTP / Passport'],
                            ];
                        @endphp
                        @foreach($policies as $policy)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm border border-gray-100">
                                <i class="fas {{ $policy['icon'] }} {{ $policy['color'] }} text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-0.5">{{ $policy['title'] }}</p>
                                <p class="font-black text-ocean-900 text-sm">{{ $policy['val'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="xl:w-88 flex-shrink-0" style="width:22rem;">
            <div class="sidebar-card">

                {{-- Sidebar Header --}}
                <div class="sidebar-header">
                    <p class="text-white/40 text-[10px] uppercase tracking-widest font-black mb-2.5">Reserve Your Stay</p>
                    <h3 class="text-white font-black text-xl font-montserrat leading-tight mb-3">{{ $hotel->name }}</h3>
                    @if($minPrice > 0)
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-sunset-500 font-black text-3xl font-montserrat">Rp{{ number_format($minPrice, 0, ',', '.') }}</span>
                        <span class="text-white/40 text-sm font-medium">/ night</span>
                    </div>
                    @endif

                    {{-- Mini Star Rating --}}
                    <div class="flex items-center gap-2 mt-3">
                        <div class="flex gap-0.5">
                            @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star text-xs {{ $i<=($hotel->rating??5)?'star-on':'star-off' }}"></i>
                            @endfor
                        </div>
                        <span class="text-white/50 text-xs font-medium">{{ number_format($hotel->rating??4.8,1) }} / 5.0</span>
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
                       class="reserve-btn mb-5" style="background:linear-gradient(135deg,#374151,#1f2937);">
                        <i class="fas fa-lock text-sm"></i>
                        Login untuk Booking
                    </a>
                    <p class="text-xs text-gray-400 text-center -mt-3 mb-5">Daftar gratis dan mulai booking sekarang</p>
                    @endauth

                    {{-- Contact Info --}}
                    <div class="space-y-0">
                        @if($hotel->phone)
                        <div class="contact-row">
                            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-0.5">Call Us</p>
                                <a href="tel:{{ $hotel->phone }}" class="font-black text-ocean-900 text-sm hover:text-sunset-500 transition-colors">{{ $hotel->phone }}</a>
                            </div>
                        </div>
                        @endif

                        @if($hotel->email)
                        <div class="contact-row">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-0.5">Email</p>
                                <a href="mailto:{{ $hotel->email }}" class="font-black text-ocean-900 text-sm hover:text-sunset-500 transition-colors break-all">{{ $hotel->email }}</a>
                            </div>
                        </div>
                        @endif

                        <div class="contact-row">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-black mb-0.5">Address</p>
                                <p class="font-bold text-ocean-900 text-sm">{{ $hotel->address ?? $hotel->location }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Call to action secondary --}}
                    @if($hotel->phone)
                    <a href="tel:{{ $hotel->phone }}"
                       class="flex items-center justify-center gap-2 w-full py-3 mt-5 rounded-2xl border-2 border-ocean-900 text-ocean-900 font-black text-sm hover:bg-ocean-900 hover:text-white transition-all duration-300">
                        <i class="fas fa-phone-alt text-xs"></i> Call Concierge
                    </a>
                    @endif
                </div>

                {{-- Trust Badges --}}
                <div class="px-6 pb-6 pt-2 border-t border-gray-100">
                    <div class="grid grid-cols-2 gap-2.5">
                        @php
                            $trust = [
                                ['icon'=>'fa-shield-alt',    'color'=>'text-green-500',  'text'=>'Secure Booking'],
                                ['icon'=>'fa-undo',          'color'=>'text-blue-500',   'text'=>'Free Cancellation'],
                                ['icon'=>'fa-headset',       'color'=>'text-sunset-500', 'text'=>'24/7 Support'],
                                ['icon'=>'fa-star',          'color'=>'text-yellow-400', 'text'=>'Top Rated'],
                            ];
                        @endphp
                        @foreach($trust as $t)
                        <div class="flex items-center gap-2 py-2">
                            <i class="fas {{ $t['icon'] }} {{ $t['color'] }} text-xs"></i>
                            <span class="text-[11px] text-gray-400 font-bold">{{ $t['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Share --}}
                <div class="px-6 pb-6 border-t border-gray-100 pt-5">
                    <p class="text-[10px] uppercase tracking-widest text-gray-300 font-black mb-3">Share This Hotel</p>
                    <div class="flex gap-2">
                        @php $shareUrl = urlencode(url()->current()); $shareName = urlencode($hotel->name); @endphp
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                           class="flex-1 py-2.5 rounded-xl bg-[#1877f2]/10 text-[#1877f2] text-xs font-black flex items-center justify-center gap-1.5 hover:bg-[#1877f2] hover:text-white transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://wa.me/?text={{ $shareName }}%20{{ $shareUrl }}" target="_blank"
                           class="flex-1 py-2.5 rounded-xl bg-[#25d366]/10 text-[#25d366] text-xs font-black flex items-center justify-center gap-1.5 hover:bg-[#25d366] hover:text-white transition-all">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>{this.innerHTML='<i class=\'fas fa-check\'></i>';setTimeout(()=>{this.innerHTML='<i class=\'fas fa-link\'></i>'},2000)})"
                                class="w-10 rounded-xl bg-gray-100 text-gray-400 text-xs flex items-center justify-center hover:bg-ocean-900 hover:text-white transition-all">
                            <i class="fas fa-link"></i>
                        </button>
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