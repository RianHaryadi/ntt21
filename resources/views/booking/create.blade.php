@extends('layouts.app')

@section('title', 'Book Your Stay — ' . ($hotel->name ?? 'Hotel'))

@push('styles')
<style>
    /* ── Page Base ── */
    .booking-page { background: #fafaf9; }

    /* ── Sticky Sidebar ── */
    .sidebar-sticky { position: sticky; top: 100px; }

    /* ── Step Indicator ── */
    .step-item { position: relative; padding-left: 3.5rem; }
    .step-item::before {
        content: ''; position: absolute; left: 1.1rem; top: 2.8rem;
        width: 2px; height: calc(100% - 0.5rem);
        background: rgba(15,23,42,0.12);
    }
    .step-item:last-child::before { display: none; }
    .step-num {
        position: absolute; left: 0; top: 0;
        width: 2.25rem; height: 2.25rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.8rem;
        border: 2px solid rgba(15,23,42,0.25);
        background: rgba(15,23,42,0.05);
        color: #475569;
        transition: all 0.4s ease;
    }
    .step-item.done .step-num { background: #0F6E63; border-color: #0F6E63; color: white; }
    .step-item.active .step-num { background: #1e293b; color: white; border-color: #1e293b; box-shadow: 0 0 0 5px rgba(15,23,42,0.08); }

    /* ── Room Cards ── */
    .room-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.25rem;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .room-card::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(15,110,99,0.03) 0%, rgba(15,23,42,0.02) 100%);
        opacity: 0; transition: opacity 0.3s;
    }
    .room-card:hover { border-color: #0F6E63; transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(15,110,99,0.08); }
    .room-card:hover::before { opacity: 1; }
    .room-card.selected {
        border-color: #0F6E63; background: #ffffff;
        box-shadow: 0 0 0 4px rgba(15,110,99,0.12), 0 20px 40px -12px rgba(15,110,99,0.15);
        transform: translateY(-4px);
    }
    .room-card .check-badge {
        position: absolute; top: 0.75rem; right: 0.75rem;
        width: 1.75rem; height: 1.75rem; border-radius: 50%;
        background: #0F6E63; color: white;
        display: none; align-items: center; justify-content: center;
        font-size: 0.65rem; box-shadow: 0 4px 8px rgba(15,110,99,0.3);
    }
    .room-card.selected .check-badge { display: flex; }

    /* ── Input Fields ── */
    .form-input {
        width: 100%; background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.15); border-radius: 0.875rem;
        padding: 0.875rem 1rem 0.875rem 2.75rem;
        font-size: 0.875rem; font-weight: 600; color: #1e293b;
        transition: all 0.25s ease;
        outline: none;
    }
    .form-input:focus { border-color: #0F6E63; background: #ffffff; box-shadow: 0 0 0 4px rgba(15,110,99,0.08); }
    .form-input::placeholder { color: #94a3b8; font-weight: 400; }

    /* ── Section Divider ── */
    .section-label {
        display: flex; align-items: center; gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .section-label .icon-wrap {
        width: 2.5rem; height: 2.5rem; border-radius: 0.75rem;
        background: linear-gradient(135deg, #0F6E63, #1C4750);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 0.9rem; flex-shrink: 0;
        box-shadow: 0 6px 12px rgba(15,110,99,0.25);
    }

    /* ── Payment Cards ── */
    .payment-card {
        border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 1rem;
        padding: 1.1rem 1.25rem; cursor: pointer;
        transition: all 0.3s ease; background: #ffffff;
        display: flex; align-items: center; gap: 1rem;
        color: #1e293b;
    }
    .payment-card:hover { border-color: #0F6E63; background: #ffffff; }
    .payment-card.selected { border-color: #0F6E63; background: #ffffff; box-shadow: 0 0 0 3px rgba(15,110,99,0.12); }
    .payment-card .radio-ring {
        width: 1.25rem; height: 1.25rem; border-radius: 50%;
        border: 2px solid #cbd5e1; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.25s;
    }
    .payment-card.selected .radio-ring { border-color: #0F6E63; }
    .payment-card.selected .radio-ring::after {
        content: ''; width: 0.5rem; height: 0.5rem;
        border-radius: 50%; background: #0F6E63;
    }

    /* ── Promo Badge ── */
    .promo-success { background: rgba(16, 185, 129, 0.1); border: 1.5px solid rgba(16, 185, 129, 0.2); border-radius: 0.875rem; color: #10b981; }
    .promo-error { background: rgba(239, 68, 68, 0.1); border: 1.5px solid rgba(239, 68, 68, 0.2); border-radius: 0.875rem; color: #ef4444; }

    /* ── Price Summary Card ── */
    .price-row { display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; }
    .price-row + .price-row { border-top: 1px solid rgba(15,23,42,0.05); }

    /* ── Glow Button ── */
    .btn-glow {
        background: linear-gradient(135deg, #0F6E63, #1C4750);
        color: white; font-weight: 800; border-radius: 0.875rem;
        padding: 1rem 1.5rem; width: 100%;
        display: flex; align-items: center; justify-content: center; gap: 0.6rem;
        transition: all 0.3s ease; font-size: 1rem;
        box-shadow: 0 8px 24px rgba(15,110,99,0.25);
        border: none; cursor: pointer;
    }
    .btn-glow:hover { transform: translateY(-3px); box-shadow: 0 14px 32px rgba(15,110,99,0.35); }
    .btn-glow:active { transform: translateY(0); }

    /* ── Trust Badges ── */
    .trust-badge { display: flex; align-items: center; gap: 0.4rem; font-size: 0.7rem; font-weight: 700; color: #64748b; }

    /* ── Amenity Tag ── */
    .amenity-tag {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.35rem 0.8rem; border-radius: 2rem;
        background: #f8fafc; color: #475569;
        font-size: 0.72rem; font-weight: 700;
        border: 1px solid rgba(15, 23, 42, 0.06);
    }
    .amenity-tag i { color: #0F6E63; }

    /* ── Animate In ── */
    @keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
    .fade-up { animation: fadeUp 0.5s ease-out both; }
    .fade-up-1 { animation-delay: 0.05s; }
    .fade-up-2 { animation-delay: 0.12s; }
    .fade-up-3 { animation-delay: 0.18s; }
    .fade-up-4 { animation-delay: 0.25s; }
    .fade-up-5 { animation-delay: 0.32s; }
    .fade-up-6 { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="booking-page min-h-screen pt-28 pb-20">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ── BREADCRUMB ── --}}
    <nav class="flex items-center gap-2 text-xs font-bold text-muted mb-8 fade-up">
        <a href="{{ route('home') }}" class="hover:text-clay transition-colors">Home</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <a href="{{ route('hotels.index') }}" class="hover:text-clay transition-colors">Hotels</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <a href="{{ route('hotels.show', $hotel->id) }}" class="hover:text-clay transition-colors truncate max-w-[160px]">{{ $hotel->name }}</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        <span class="text-clay">Book</span>
    </nav>

    <div class="flex flex-col xl:flex-row gap-8">

        {{-- ═══════════════════════════════════════
             LEFT COLUMN — Sidebar
        ═══════════════════════════════════════ --}}
        <div class="xl:w-72 flex-shrink-0">
            <div class="sidebar-sticky space-y-5">

                {{-- Hotel Info Card --}}
                <div class="bg-paper rounded-3xl overflow-hidden shadow-sm border border-line/60 fade-up fade-up-1">
                    <div class="relative h-44">
                        <img src="{{ $hotel->image ? asset('storage/' . ltrim($hotel->image, '/')) : asset('images/fallback-hotel.jpg') }}"
                             alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-white via-white/20 to-transparent"></div>
                        <div class="absolute top-3 left-3">
                            <div class="flex gap-0.5">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star text-[10px] {{ $i < ($hotel->stars ?? 5) ? 'text-yellow-500' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="p-5 bg-paper">
                        @if($hotel->isOnFlashSale())
                        <div class="mb-2">
                            @include('partials.flash-sale-badge', ['endsAt' => $hotel->flash_sale_ends_at])
                        </div>
                        @endif
                        <h2 class="text-ink font-black text-lg font-serif tracking-tight leading-tight mb-1">{{ $hotel->name }}</h2>
                        <div class="flex items-center gap-1.5 text-muted text-xs font-semibold mb-4">
                            <i class="fas fa-map-marker-alt text-clay text-[10px]"></i>
                            {{ $hotel->location ?? 'NTT, Indonesia' }}
                        </div>
                        @if($hotel->description)
                        <p class="text-muted text-xs leading-relaxed line-clamp-3">{{ $hotel->description }}</p>
                        @endif

                        {{-- Facilities --}}
                        @php
                            $facilities = is_array($hotel->facilities) ? $hotel->facilities : ($hotel->facilities ? explode(',', $hotel->facilities) : []);
                        @endphp
                        @if(count($facilities) > 0)
                        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-1.5">
                            @foreach(array_slice($facilities, 0, 6) as $facility)
                            @php $f = strtolower(trim($facility)); @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface text-slate-600 text-[10px] font-bold border border-line/60">
                                @if(str_contains($f,'wifi')) <i class="fas fa-wifi text-clay"></i>
                                @elseif(str_contains($f,'pool')) <i class="fas fa-swimming-pool text-clay"></i>
                                @elseif(str_contains($f,'restaurant')) <i class="fas fa-utensils text-clay"></i>
                                @elseif(str_contains($f,'parking')) <i class="fas fa-parking text-clay"></i>
                                @elseif(str_contains($f,'spa')) <i class="fas fa-spa text-clay"></i>
                                @elseif(str_contains($f,'gym')) <i class="fas fa-dumbbell text-clay"></i>
                                @else <i class="fas fa-check text-clay"></i>
                                @endif
                                {{ ucfirst(trim($facility)) }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Booking Steps --}}
                <div class="bg-paper rounded-3xl p-6 shadow-sm border border-line/60 fade-up fade-up-2">
                    <h3 class="text-ink font-black text-sm uppercase tracking-widest mb-6 font-serif tracking-tight">Booking Steps</h3>
                    <div class="space-y-6">
                        @php
                            $steps = [
                                ['label' => 'Room Type', 'desc' => 'Choose single, double or family', 'icon' => 'fa-bed'],
                                ['label' => 'Dates & Promo', 'desc' => 'Set dates, apply discount code', 'icon' => 'fa-calendar-alt'],
                                ['label' => 'Guest Info', 'desc' => 'Name, email and phone number', 'icon' => 'fa-user-circle'],
                                ['label' => 'Payment', 'desc' => 'Transfer, QRIS or cash', 'icon' => 'fa-credit-card'],
                                ['label' => 'Confirm', 'desc' => 'Review and complete booking', 'icon' => 'fa-check-circle'],
                            ];
                        @endphp
                        @foreach($steps as $idx => $step)
                        <div class="step-item" id="step-{{ $idx + 1 }}" data-step="{{ $idx + 1 }}">
                            <div class="step-num text-slate-700">{{ $idx + 1 }}</div>
                            <div class="min-h-[2.5rem]">
                                <p class="text-ink font-bold text-sm leading-none mb-1">{{ $step['label'] }}</p>
                                <p class="text-muted text-[11px] leading-tight">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <p class="text-muted text-[10px] uppercase tracking-widest font-bold mb-3">Need Assistance?</p>
                        <a href="tel:+6281234567890" class="flex items-center gap-2 text-slate-600 hover:text-ink text-xs font-semibold mb-2 transition-colors">
                            <i class="fas fa-phone-alt text-clay text-[10px]"></i> +62 812 3456 7890
                        </a>
                        <a href="mailto:explore@pesonantt.id" class="flex items-center gap-2 text-slate-600 hover:text-ink text-xs font-semibold transition-colors">
                            <i class="fas fa-envelope text-clay text-[10px]"></i> explore@pesonantt.id
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════
             RIGHT COLUMN — Main Form
        ═══════════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- Error Banner --}}
            @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/25 rounded-2xl p-5 mb-6 flex gap-4 fade-up">
                <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-400 text-sm mb-1">Please fix {{ $errors->count() }} error(s)</h4>
                    <ul class="text-red-400 text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-1.5"><i class="fas fa-circle text-[4px]"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form id="bookingForm" method="POST" action="{{ route('booking.hotel.store') }}">
                @csrf
                {{-- Hidden Inputs --}}
                <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                <input type="hidden" id="hRoomType"      name="room_type"       value="{{ old('room_type','single') }}">
                @php
                    $effectivePrice = fn($base) => $hotel->flashSalePrice($base) ?? $base;
                @endphp
                <input type="hidden" id="hRoomPrice"     name="room_price"      value="{{ old('room_price', $effectivePrice($hotel->single_room_price ?? 0)) }}">
                <input type="hidden" id="hNightCount"    name="night_count"     value="{{ old('night_count', 1) }}">
                <input type="hidden" id="hTax"           name="tax"             value="{{ old('tax', 0) }}">
                <input type="hidden" id="hService"       name="service_charge"  value="{{ old('service_charge', 0) }}">
                <input type="hidden" id="hDiscount"      name="discount_amount" value="{{ old('discount_amount', 0) }}">
                <input type="hidden" id="hPromoCodeId"   name="promo_code_id"   value="{{ old('promo_code_id', '') }}">
                <input type="hidden" id="hTotal"         name="total_price"     value="{{ old('total_price', 0) }}">
                <input type="hidden" name="status"       value="pending">

                {{-- Room prices data --}}
                <div id="roomPriceData"
                     data-single="{{ $effectivePrice($hotel->single_room_price ?? 0) }}"
                     data-double="{{ $effectivePrice($hotel->double_room_price ?? 0) }}"
                     data-family="{{ $effectivePrice($hotel->family_room_price ?? 0) }}"
                     class="hidden"></div>

                {{-- Promo codes data --}}
                <div id="promoData" class="hidden"
                     data-promos="{{ json_encode($promos->mapWithKeys(fn($p) => [strtoupper($p->code) => ['id'=>$p->id,'amount'=>$p->discount_amount??null,'percent'=>$p->discount_percent??null,'valid_from'=>$p->valid_from?$p->valid_from->toDateString():null,'valid_until'=>$p->valid_until?$p->valid_until->toDateString():null,'active'=>$p->active]])->toArray()) }}">
                </div>

                <div class="space-y-6">

                    {{-- ── SECTION 1: Room Selection ── --}}
                    <div class="bg-paper rounded-3xl p-7 shadow-sm border border-line/60 fade-up fade-up-1">
                        <div class="section-label">
                            <div class="icon-wrap"><i class="fas fa-bed"></i></div>
                            <div>
                                <h2 class="text-ink font-black text-lg font-serif tracking-tight leading-none">Choose Your Room</h2>
                                <p class="text-muted text-xs font-medium mt-0.5">Select the room category that suits you</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="roomCards">
                            @php
                                $roomDefs = [
                                    ['type'=>'single','label'=>'Single Room','price'=>$effectivePrice($hotel->single_room_price??0),'originalPrice'=>$hotel->single_room_price??0,'capacity'=>1,'icon'=>'fa-user','features'=>['1 King Bed','Free Breakfast','City View','24h Room Service']],
                                    ['type'=>'double','label'=>'Double Room','price'=>$effectivePrice($hotel->double_room_price??0),'originalPrice'=>$hotel->double_room_price??0,'capacity'=>2,'icon'=>'fa-user-friends','features'=>['1 Queen Bed','Free Breakfast','Garden View','Minibar']],
                                    ['type'=>'family','label'=>'Family Suite','price'=>$effectivePrice($hotel->family_room_price??0),'originalPrice'=>$hotel->family_room_price??0,'capacity'=>4,'icon'=>'fa-users','features'=>['2 Queen Beds','Free Breakfast','Pool View','Kitchenette']],
                                ];
                            @endphp
                            @foreach($roomDefs as $rd)
                            <div class="room-card p-5 {{ old('room_type','single') === $rd['type'] ? 'selected' : '' }}"
                                 data-type="{{ $rd['type'] }}" data-price="{{ $rd['price'] }}">
                                <div class="check-badge"><i class="fas fa-check"></i></div>

                                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4
                                    {{ old('room_type','single') === $rd['type'] ? 'bg-clay' : 'bg-surface border border-line/60 shadow-sm' }}
                                    room-icon-bg transition-colors duration-300">
                                    <i class="fas {{ $rd['icon'] }} text-sm
                                        {{ old('room_type','single') === $rd['type'] ? 'text-white' : 'text-muted' }}
                                        room-icon-color transition-colors duration-300"></i>
                                </div>

                                <h3 class="font-black text-ink text-sm font-serif tracking-tight mb-0.5">{{ $rd['label'] }}</h3>
                                <p class="text-muted text-[10px] font-medium mb-3">Up to {{ $rd['capacity'] }} guest{{ $rd['capacity'] > 1 ? 's' : '' }}</p>

                                <ul class="space-y-1.5 mb-4">
                                    @foreach($rd['features'] as $feat)
                                    <li class="flex items-center gap-1.5 text-[11px] font-semibold text-muted">
                                        <i class="fas fa-check text-clay text-[8px] flex-shrink-0"></i>
                                        {{ $feat }}
                                    </li>
                                    @endforeach
                                </ul>

                                <div class="pt-3 border-t border-slate-100">
                                    @if($hotel->isOnFlashSale())
                                    <p class="text-xs text-muted line-through">Rp{{ number_format($rd['originalPrice'], 0, ',', '.') }}</p>
                                    <p class="text-coral font-black text-xl font-serif tracking-tight">
                                        Rp{{ number_format($rd['price'], 0, ',', '.') }}
                                        <span class="text-xs text-muted font-medium">/night</span>
                                    </p>
                                    @else
                                    <p class="text-clay font-black text-xl font-serif tracking-tight">
                                        Rp{{ number_format($rd['price'], 0, ',', '.') }}
                                        <span class="text-xs text-muted font-medium">/night</span>
                                    </p>
                                    @endif
                                </div>

                                <input type="radio" name="_room_type_hidden" value="{{ $rd['type'] }}" class="hidden"
                                    {{ old('room_type','single') === $rd['type'] ? 'checked' : '' }}>
                            </div>
                            @endforeach
                        </div>
                        @error('room_type')
                            <p class="text-red-500 text-xs font-bold mt-3 flex items-center gap-1.5"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ── SECTION 2: Dates ── --}}
                    <div class="bg-paper rounded-3xl p-7 shadow-sm border border-line/60 fade-up fade-up-2">
                        <div class="section-label">
                            <div class="icon-wrap"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <h2 class="text-ink font-black text-lg font-serif tracking-tight leading-none">Select Your Dates</h2>
                                <p class="text-muted text-xs font-medium mt-0.5">When would you like to stay?</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-black text-muted mb-2">Check-In Date</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-clay pointer-events-none w-5 text-center">
                                        <i class="fas fa-calendar-day text-sm"></i>
                                    </span>
                                    <input type="date" id="checkIn" name="check_in_date"
                                           value="{{ old('check_in_date', date('Y-m-d')) }}"
                                           min="{{ date('Y-m-d') }}"
                                           class="form-input" required>
                                </div>
                                @error('check_in_date')
                                    <p class="text-red-500 text-xs font-bold mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-black text-muted mb-2">Check-Out Date</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-clay pointer-events-none w-5 text-center">
                                        <i class="fas fa-calendar-check text-sm"></i>
                                    </span>
                                    <input type="date" id="checkOut" name="check_out_date"
                                           value="{{ old('check_out_date', date('Y-m-d', strtotime('+1 day'))) }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="form-input" required>
                                </div>
                                @error('check_out_date')
                                    <p class="text-red-500 text-xs font-bold mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="availabilityStatus" class="hidden mt-3 text-xs font-bold rounded-xl px-4 py-3 flex items-center gap-2"></div>

                        {{-- Duration pill --}}
                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <div class="flex items-center gap-2 px-4 py-2 bg-surface rounded-full border border-line/60">
                                <i class="fas fa-moon text-clay text-xs"></i>
                                <span class="text-slate-700 font-black text-xs"><span id="nightLabel">1</span> night stay</span>
                            </div>
                            <div class="flex items-center gap-2 px-4 py-2 bg-green-500/10 rounded-full border border-green-500/20">
                                <i class="fas fa-shield-alt text-green-600 text-xs"></i>
                                <span class="text-green-600 font-bold text-xs">Free cancellation 48h before</span>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 3: Promo Code ── --}}
                    <div class="bg-paper rounded-3xl p-7 shadow-sm border border-line/60 fade-up fade-up-3">
                        <div class="section-label">
                            <div class="icon-wrap"><i class="fas fa-tag"></i></div>
                            <div>
                                <h2 class="text-ink font-black text-lg font-serif tracking-tight leading-none">Promo Code</h2>
                                <p class="text-muted text-xs font-medium mt-0.5">Enter your discount code for savings</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex-1 relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-clay pointer-events-none w-5 text-center">
                                    <i class="fas fa-ticket-alt text-sm"></i>
                                </span>
                                <input type="text" id="promoCodeInput" name="promo_code"
                                       value="{{ old('promo_code') }}"
                                       placeholder="Enter promo code e.g. NTT25"
                                       class="form-input uppercase tracking-widest">
                            </div>
                            <button type="button" id="applyPromoBtn"
                                    class="px-5 py-3 bg-clay text-white font-black text-sm rounded-xl hover:bg-clay/90 transition-all active:scale-95 whitespace-nowrap flex items-center gap-2 shadow-md">
                                <i class="fas fa-bolt text-white text-xs"></i> Apply
                            </button>
                        </div>

                        <div id="promoMsg" class="hidden mt-3 px-4 py-3 text-xs font-bold rounded-xl flex items-center gap-2"></div>
                    </div>

                    {{-- ── SECTION 4: Guest Information ── --}}
                    <div class="bg-paper rounded-3xl p-7 shadow-sm border border-line/60 fade-up fade-up-4">
                        <div class="section-label">
                            <div class="icon-wrap"><i class="fas fa-user-circle"></i></div>
                            <div>
                                <h2 class="text-ink font-black text-lg font-serif tracking-tight leading-none">Guest Information</h2>
                                <p class="text-muted text-xs font-medium mt-0.5">Details for your reservation record</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-black text-muted mb-2">Full Name <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none w-5 text-center"><i class="fas fa-user text-sm"></i></span>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                           placeholder="Your full name" class="form-input" required>
                                </div>
                                @error('customer_name')<p class="text-red-500 text-xs font-bold mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-black text-muted mb-2">Email Address <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none w-5 text-center"><i class="fas fa-envelope text-sm"></i></span>
                                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                           placeholder="your@email.com" class="form-input" required>
                                </div>
                                @error('customer_email')<p class="text-red-500 text-xs font-bold mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-black text-muted mb-2">Phone Number <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none w-5 text-center"><i class="fas fa-phone text-sm"></i></span>
                                    <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}"
                                           placeholder="08xxxxxxxxxx" class="form-input" required>
                                </div>
                                @error('customer_phone')<p class="text-red-500 text-xs font-bold mt-1 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase tracking-widest font-black text-muted mb-2">Special Requests <span class="text-muted font-normal normal-case tracking-normal">(optional)</span></label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted pointer-events-none w-5 text-center"><i class="fas fa-comment-dots text-sm"></i></span>
                                    <input type="text" name="special_requests" value="{{ old('special_requests') }}"
                                           placeholder="e.g. high floor, extra pillow…" class="form-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 5: Payment Method ── --}}
                    <div class="bg-paper rounded-3xl p-7 shadow-sm border border-line/60 fade-up fade-up-5">
                        <div class="section-label">
                            <div class="icon-wrap"><i class="fas fa-credit-card"></i></div>
                            <div>
                                <h2 class="text-ink font-black text-lg font-serif tracking-tight leading-none">Payment Method</h2>
                                <p class="text-muted text-xs font-medium mt-0.5">How would you like to pay?</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="paymentCards">
                            @php
                                $methods = [
                                    ['value'=>'transfer','label'=>'Bank Transfer','desc'=>'Via ATM or internet banking','icon'=>'fa-university','extra'=>'BCA · BNI · Mandiri · BRI'],
                                    ['value'=>'qris','label'=>'QRIS','desc'=>'Scan QR code to pay instantly','icon'=>'fa-qrcode','extra'=>'GoPay · OVO · Dana · LinkAja'],
                                    ['value'=>'cash','label'=>'Pay on Arrival','desc'=>'Pay cash at check-in','icon'=>'fa-money-bill-wave','extra'=>'No upfront payment needed'],
                                ];
                            @endphp
                            @foreach($methods as $pm)
                            <div class="payment-card {{ old('payment_method') === $pm['value'] ? 'selected' : '' }}"
                                 data-value="{{ $pm['value'] }}" style="cursor:pointer; flex-direction:column; align-items:flex-start; gap:0.75rem;">
                                <div class="flex items-center gap-3 w-full">
                                    <div class="radio-ring flex-shrink-0 border-slate-300 bg-surface"></div>
                                    <div class="flex-1">
                                        <p class="font-black text-ink text-sm leading-none">{{ $pm['label'] }}</p>
                                        <p class="text-muted text-[11px] font-medium mt-0.5">{{ $pm['desc'] }}</p>
                                    </div>
                                    <div class="w-9 h-9 rounded-xl bg-surface border border-line/60 shadow-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fas {{ $pm['icon'] }} text-slate-600 text-sm"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-muted font-bold pl-7">{{ $pm['extra'] }}</p>
                                <input type="radio" name="payment_method" value="{{ $pm['value'] }}" class="hidden"
                                    {{ old('payment_method') === $pm['value'] ? 'checked' : '' }} required>
                            </div>
                            @endforeach
                        </div>
                        @error('payment_method')
                            <p class="text-red-500 text-xs font-bold mt-3 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ── Travel Insurance Add-on ── --}}
                    <div class="bg-paper rounded-3xl p-7 shadow-sm border border-line/60 fade-up fade-up-5">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="has_insurance" id="hasInsurance" value="1"
                                   {{ old('has_insurance') ? 'checked' : '' }}
                                   class="mt-1 w-5 h-5 rounded border-line text-clay focus:ring-clay">
                            <span class="flex-1">
                                <span class="flex items-center gap-2 font-black text-ink text-sm">
                                    <i class="fas fa-shield-alt text-clay"></i> Tambahkan Asuransi Perjalanan
                                </span>
                                <span class="block text-muted text-xs font-medium mt-1">
                                    Perlindungan kecelakaan &amp; pembatalan menginap — Rp{{ number_format(config('services.insurance.price_per_booking'), 0, ',', '.') }} / booking
                                </span>
                            </span>
                        </label>
                        <input type="hidden" id="insurancePricePerBooking" value="{{ config('services.insurance.price_per_booking') }}">
                    </div>

                    {{-- ── SECTION 6: Price Summary + Submit ── --}}
                    <div class="bg-paper rounded-3xl p-7 shadow-sm border border-line/60 fade-up fade-up-6">
                        <div class="section-label">
                            <div class="icon-wrap"><i class="fas fa-receipt"></i></div>
                            <div>
                                <h2 class="text-ink font-black text-lg font-serif tracking-tight leading-none">Price Breakdown</h2>
                                <p class="text-muted text-xs font-medium mt-0.5">Full cost summary before you confirm</p>
                            </div>
                        </div>

                        <div class="bg-surface rounded-2xl p-5 border border-line/60 mb-6">
                            {{-- Selected room info --}}
                            <div class="flex items-center gap-3 pb-4 mb-2 border-b border-line">
                                <div class="w-8 h-8 rounded-lg bg-clay/10 flex items-center justify-center flex-shrink-0">
                                    <i id="summaryRoomIcon" class="fas fa-user text-clay text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest font-black text-muted">Room Type</p>
                                    <p id="summaryRoomName" class="text-ink font-black text-sm">Single Room</p>
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-[10px] uppercase tracking-widest font-black text-muted">Per Night</p>
                                    <p id="summaryPerNight" class="text-ink font-black text-sm">Rp0</p>
                                </div>
                            </div>

                            <div class="space-y-0.5">
                                <div class="price-row">
                                    <span class="text-muted text-sm font-medium">Room × <span id="summaryNights">1</span> night(s)</span>
                                    <span id="summarySubtotal" class="font-bold text-ink text-sm">Rp0</span>
                                </div>
                                <div class="price-row">
                                    <span class="text-muted text-sm font-medium">Tax (10%)</span>
                                    <span id="summaryTax" class="font-bold text-ink text-sm">Rp0</span>
                                </div>
                                <div class="price-row">
                                    <span class="text-muted text-sm font-medium">Service Fee (5%)</span>
                                    <span id="summaryService" class="font-bold text-ink text-sm">Rp0</span>
                                </div>
                                <div class="price-row" id="discountRow" style="display:none;">
                                    <span class="text-emerald-600 text-sm font-bold flex items-center gap-1.5"><i class="fas fa-tag text-xs"></i> Promo Discount</span>
                                    <span id="summaryDiscount" class="font-bold text-emerald-600 text-sm">-Rp0</span>
                                </div>
                                <div class="price-row" id="insuranceRow" style="display:none;">
                                    <span class="text-muted text-sm font-medium flex items-center gap-1.5"><i class="fas fa-shield-alt text-xs text-clay"></i> Travel Insurance</span>
                                    <span id="summaryInsurance" class="font-bold text-ink text-sm">Rp0</span>
                                </div>
                                <div class="pt-4 mt-1 border-t border-dashed border-line flex justify-between items-center">
                                    <div>
                                        <p class="font-black text-ink text-base">Total Amount</p>
                                        <p class="text-muted text-[10px] font-medium">Amount to be charged</p>
                                    </div>
                                    <p id="summaryTotal" class="font-black text-clay text-2xl font-serif tracking-tight">Rp0</p>
                                </div>
                            </div>
                        </div>

                        {{-- Terms --}}
                        <label class="flex items-start gap-3 mb-6 cursor-pointer group">
                            <div class="relative flex-shrink-0 mt-0.5">
                                <input type="checkbox" name="agree_terms" id="agreeTerms" value="1"
                                       {{ old('agree_terms') ? 'checked' : '' }}
                                       required class="sr-only peer">
                                <div class="w-5 h-5 rounded-md border-2 border-slate-300 bg-surface peer-checked:border-clay peer-checked:bg-clay transition-all flex items-center justify-center">
                                    <i class="fas fa-check text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity scale-0 peer-checked:scale-100"></i>
                                </div>
                            </div>
                            <span class="text-muted text-xs leading-relaxed font-medium">
                                I agree to the <a href="#" class="text-clay font-bold hover:text-ink underline transition-colors">Terms of Service</a>
                                and <a href="#" class="text-clay font-bold hover:text-ink underline transition-colors">Privacy Policy</a>
                                of Pesona NTT. I understand that my booking is subject to cancellation policies.
                            </span>
                        </label>
                        @error('agree_terms')
                            <p class="text-red-500 text-xs font-bold mb-4 flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror

                        <button type="submit" class="btn-glow">
                            <i class="fas fa-lock text-sm"></i>
                            <span>Confirm Reservation — <span id="btnTotal">Rp0</span></span>
                        </button>
                        <button type="button" id="addToCartBtn" class="w-full mt-3 py-3.5 rounded-xl flex items-center justify-center gap-2 bg-white border border-slate-200 text-slate-700 font-bold text-sm hover:border-laut hover:text-laut transition-all">
                            <i class="fas fa-shopping-bag"></i> Tambah ke Keranjang
                        </button>

                        {{-- Trust Badges --}}
                        <div class="flex flex-wrap justify-center gap-5 mt-5 pt-4 border-t border-white/5">
                            <div class="trust-badge"><i class="fas fa-shield-alt text-green-500"></i> SSL Secured</div>
                            <div class="trust-badge"><i class="fas fa-lock text-blue-500"></i> Encrypted</div>
                            <div class="trust-badge"><i class="fas fa-headset text-clay"></i> 24/7 Support</div>
                            <div class="trust-badge"><i class="fas fa-undo text-purple-500"></i> Free Cancellation</div>
                        </div>
                    </div>

                </div>{{-- end space-y-6 --}}
            </form>
        </div>{{-- end right col --}}

    </div>{{-- end main flex --}}
</div>{{-- end container --}}
</div>{{-- end booking-page --}}

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── State ──
    const state = {
        roomType:  '{{ old("room_type","single") }}',
        roomPrice: parseFloat(document.getElementById('hRoomPrice').value) || 0,
        nights:    parseInt(document.getElementById('hNightCount').value) || 1,
        discount:  parseFloat(document.getElementById('hDiscount').value) || 0,
        promoId:   document.getElementById('hPromoCodeId').value || '',
    };

    // ── Room info map ──
    const roomMap = {
        single: { label: 'Single Room',  icon: 'fa-user',         price: parseFloat(document.getElementById('roomPriceData').dataset.single) || 0 },
        double: { label: 'Double Room',  icon: 'fa-user-friends',  price: parseFloat(document.getElementById('roomPriceData').dataset.double) || 0 },
        family: { label: 'Family Suite', icon: 'fa-users',         price: parseFloat(document.getElementById('roomPriceData').dataset.family) || 0 },
    };

    // ── Promos ──
    let promos = {};
    try { promos = JSON.parse(document.getElementById('promoData').dataset.promos || '{}'); } catch(e) {}

    // ── DOM refs ──
    const checkIn        = document.getElementById('checkIn');
    const checkOut       = document.getElementById('checkOut');
    const availabilityBox = document.getElementById('availabilityStatus');
    const submitBtn       = document.getElementById('bookingForm')?.querySelector('button[type="submit"]');
    const promoInput     = document.getElementById('promoCodeInput');
    const applyBtn       = document.getElementById('applyPromoBtn');
    const promoMsg       = document.getElementById('promoMsg');
    const hRoomType      = document.getElementById('hRoomType');
    const hRoomPrice     = document.getElementById('hRoomPrice');
    const hNightCount    = document.getElementById('hNightCount');
    const hTax           = document.getElementById('hTax');
    const hService       = document.getElementById('hService');
    const hDiscount      = document.getElementById('hDiscount');
    const hPromoCodeId   = document.getElementById('hPromoCodeId');
    const hTotal         = document.getElementById('hTotal');
    const roomCards      = document.querySelectorAll('#roomCards .room-card');
    const paymentCards   = document.querySelectorAll('#paymentCards .payment-card');

    // ── Summary DOM refs ──
    const elNightLabel   = document.getElementById('nightLabel');
    const elRoomIcon     = document.getElementById('summaryRoomIcon');
    const elRoomName     = document.getElementById('summaryRoomName');
    const elPerNight     = document.getElementById('summaryPerNight');
    const elNights       = document.getElementById('summaryNights');
    const elSubtotal     = document.getElementById('summarySubtotal');
    const elTax          = document.getElementById('summaryTax');
    const elService      = document.getElementById('summaryService');
    const elDiscount     = document.getElementById('summaryDiscount');
    const discountRow    = document.getElementById('discountRow');
    const elTotal        = document.getElementById('summaryTotal');
    const btnTotal       = document.getElementById('btnTotal');
    const hasInsurance   = document.getElementById('hasInsurance');
    const insuranceRow   = document.getElementById('insuranceRow');
    const elInsurance    = document.getElementById('summaryInsurance');
    const insurancePricePerBooking = parseFloat(document.getElementById('insurancePricePerBooking').value) || 0;

    // ── Formatter ──
    const fmt = n => 'Rp' + Math.round(n).toLocaleString('id-ID');

    // ── Calculate ──
    function calc() {
        const sub       = state.roomPrice * state.nights;
        const tax       = sub * 0.10;
        const service   = sub * 0.05;
        const insurance = hasInsurance.checked ? insurancePricePerBooking : 0;
        const total     = Math.max(sub + tax + service - state.discount, 0) + insurance;

        hTax.value     = tax.toFixed(2);
        hService.value = service.toFixed(2);
        hDiscount.value= state.discount.toFixed(2);
        hTotal.value   = total.toFixed(2);
        hNightCount.value = state.nights;

        elInsurance.textContent = fmt(insurance);
        insuranceRow.style.display = insurance > 0 ? 'flex' : 'none';

        // Summary
        const rm = roomMap[state.roomType];
        elRoomIcon.className = `fas ${rm.icon} text-clay text-xs`;
        elRoomName.textContent = rm.label;
        elPerNight.textContent  = fmt(state.roomPrice);
        elNightLabel.textContent = state.nights;
        elNights.textContent    = state.nights;
        elSubtotal.textContent  = fmt(sub);
        elTax.textContent       = fmt(tax);
        elService.textContent   = fmt(service);
        elTotal.textContent     = fmt(total);
        btnTotal.textContent    = fmt(total);

        if (state.discount > 0) {
            elDiscount.textContent = '-' + fmt(state.discount);
            discountRow.style.display = 'flex';
        } else {
            discountRow.style.display = 'none';
        }

        updateSteps();
    }

    // ── Night count ──
    function getNights() {
        if (!checkIn.value || !checkOut.value) return 1;
        const d = Math.ceil((new Date(checkOut.value) - new Date(checkIn.value)) / 86400000);
        return Math.max(d, 1);
    }

    // ── Update step sidebar ──
    function updateSteps() {
        const steps = document.querySelectorAll('.step-item');
        const hasRoom     = !!hRoomType.value;
        const hasDates    = !!checkIn.value && !!checkOut.value;
        const hasGuest    = document.querySelector('[name="customer_name"]')?.value.trim().length > 0;
        const hasPayment  = !!document.querySelector('[name="payment_method"]:checked');

        const done = [hasRoom ? 0 : -1, hasDates ? 1 : -1, hasGuest ? 2 : -1, hasPayment ? 3 : -1];
        steps.forEach((s, i) => {
            s.classList.remove('done','active');
            if (done.includes(i)) s.classList.add('done');
            else { if (!done.includes(i-1) && i === done.filter(x=>x>=0).length) s.classList.add('active'); }
        });
        // Smarter: mark done steps
        const allDone = [hasRoom, hasDates, hasGuest, hasPayment];
        steps.forEach((s, i) => {
            s.classList.remove('done','active');
            if (i < 4) {
                const prevDone = allDone.slice(0, i).every(Boolean);
                if (allDone[i]) s.classList.add('done');
                else if (prevDone) s.classList.add('active');
            } else {
                if (allDone.every(Boolean)) s.classList.add('active');
            }
        });
    }

    // ── Ketersediaan kamar (real-time) ──
    let availabilityTimer = null;
    function checkAvailability() {
        if (!checkIn.value || !checkOut.value || !state.roomType) return;

        clearTimeout(availabilityTimer);
        availabilityTimer = setTimeout(() => {
            const url = new URL('{{ route('hotels.availability', $hotel->id) }}', window.location.origin);
            url.searchParams.set('room_type', state.roomType);
            url.searchParams.set('check_in_date', checkIn.value);
            url.searchParams.set('check_out_date', checkOut.value);

            availabilityBox.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-700');
            availabilityBox.classList.add('bg-surface', 'text-muted');
            availabilityBox.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengecek ketersediaan kamar...';

            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    availabilityBox.classList.remove('bg-surface', 'text-muted');
                    if (data.available) {
                        availabilityBox.classList.add('bg-emerald-50', 'text-emerald-700');
                        availabilityBox.innerHTML = `<i class="fas fa-check-circle"></i> Tersedia — ${data.remaining} kamar tersisa untuk tanggal ini.`;
                        if (submitBtn) submitBtn.disabled = false;
                    } else {
                        availabilityBox.classList.add('bg-red-50', 'text-red-700');
                        availabilityBox.innerHTML = '<i class="fas fa-times-circle"></i> Maaf, kamar tipe ini sudah penuh untuk tanggal yang dipilih. Silakan pilih tanggal atau tipe kamar lain.';
                        if (submitBtn) submitBtn.disabled = true;
                    }
                })
                .catch(() => {
                    availabilityBox.classList.add('hidden');
                });
        }, 350);
    }

    // ── Room selection ──
    roomCards.forEach(card => {
        card.addEventListener('click', () => {
            roomCards.forEach(c => {
                c.classList.remove('selected');
                c.querySelector('.room-icon-bg')?.classList.replace('bg-clay','bg-surface');
                c.querySelector('.room-icon-color')?.classList.replace('text-white','text-muted');
            });
            card.classList.add('selected');
            card.querySelector('.room-icon-bg')?.classList.replace('bg-surface','bg-clay');
            card.querySelector('.room-icon-color')?.classList.replace('text-muted','text-white');
            card.querySelector('input[type=radio]').checked = true;

            state.roomType  = card.dataset.type;
            state.roomPrice = parseFloat(card.dataset.price) || 0;
            hRoomType.value  = state.roomType;
            hRoomPrice.value = state.roomPrice;

            // Reset promo when room changes
            clearPromo();
            checkAvailability();
        });
    });

    // ── Payment selection ──
    paymentCards.forEach(card => {
        card.addEventListener('click', () => {
            paymentCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            card.querySelector('input[type=radio]').checked = true;
            updateSteps();
        });
    });

    // ── Dates ──
    checkIn.addEventListener('change', () => {
        const nextDay = new Date(checkIn.value);
        nextDay.setDate(nextDay.getDate() + 1);
        checkOut.min = nextDay.toISOString().split('T')[0];
        if (checkOut.value && new Date(checkOut.value) <= new Date(checkIn.value)) {
            checkOut.value = checkOut.min;
        }
        state.nights = getNights();
        calc();
        checkAvailability();
    });
    checkOut.addEventListener('change', () => {
        state.nights = getNights();
        calc();
        checkAvailability();
    });

    // Guest fields → update steps on blur
    document.querySelectorAll('[name="customer_name"],[name="customer_email"],[name="customer_phone"]').forEach(el => {
        el.addEventListener('blur', updateSteps);
    });

    // ── Promo ──
    function clearPromo(keepInput = false) {
        state.discount  = 0;
        state.promoId   = '';
        hPromoCodeId.value = '';
        hDiscount.value = '0';
        if (!keepInput) promoInput.value = '';
        promoMsg.className = 'hidden';
        promoMsg.textContent = '';
        calc();
    }

    function showPromoMsg(text, ok) {
        promoMsg.className = `mt-3 p-3.5 px-4 text-sm font-bold flex items-center gap-2.5 ${ok ? 'promo-success text-green-700' : 'promo-error text-red-700'}`;
        promoMsg.innerHTML = `<i class="fas ${ok ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500'}"></i><span>${text}</span>`;
    }

    let applyLock = false;
    applyBtn.addEventListener('click', () => {
        if (applyLock) return;
        applyLock = true; setTimeout(() => applyLock = false, 600);

        const code = promoInput.value.trim().toUpperCase();
        if (!code) { showPromoMsg('Please enter a promo code first.', false); return; }

        const promo = promos[code];
        if (!promo || !promo.active) {
            state.discount = 0; hDiscount.value = '0'; hPromoCodeId.value = '';
            showPromoMsg(`Promo code "${code}" is invalid or inactive.`, false);
            calc(); return;
        }

        const today = new Date().toISOString().split('T')[0];
        if (promo.valid_from && promo.valid_from > today) { showPromoMsg(`Promo starts on ${promo.valid_from}.`, false); calc(); return; }
        if (promo.valid_until && promo.valid_until < today) { showPromoMsg(`Promo expired on ${promo.valid_until}.`, false); calc(); return; }
        if (state.roomPrice <= 0) { showPromoMsg('Please select a room type first.', false); return; }

        const sub = state.roomPrice * state.nights;
        let disc = 0, discLabel = '';
        if (promo.percent && parseFloat(promo.percent) > 0) {
            disc = sub * (parseFloat(promo.percent) / 100);
            discLabel = `${parseFloat(promo.percent)}% off`;
        } else if (promo.amount && parseFloat(promo.amount) > 0) {
            disc = parseFloat(promo.amount);
            discLabel = fmt(disc) + ' off';
        } else { showPromoMsg('This promo has no valid discount.', false); calc(); return; }

        state.discount = disc;
        state.promoId  = promo.id;
        hPromoCodeId.value = promo.id;
        hDiscount.value = disc.toFixed(2);
        promoInput.value = code;
        showPromoMsg(`🎉 Promo applied! Saving ${discLabel} (${fmt(disc)})`, true);
        calc();
    });

    promoInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); applyBtn.click(); } });

    // ── Submit validation ──
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        if (!document.querySelector('input[name="payment_method"]:checked')) {
            e.preventDefault();
            document.querySelector('#paymentCards').scrollIntoView({ behavior:'smooth', block:'center' });
            return;
        }
        if (!document.getElementById('agreeTerms').checked) {
            e.preventDefault();
            document.getElementById('agreeTerms').scrollIntoView({ behavior:'smooth', block:'center' });
            return;
        }
        state.nights = getNights();
        const sub = state.roomPrice * state.nights;
        const tax = sub * 0.10;
        const svc = sub * 0.05;
        const total = Math.max(sub + tax + svc - state.discount, 0);
        hTotal.value = total.toFixed(2);
        hTax.value   = tax.toFixed(2);
        hService.value = svc.toFixed(2);
        hNightCount.value = state.nights;
    });

    // ── Init ──
    state.nights = getNights();
    // Set initial checkout min
    if (checkIn.value) {
        const minOut = new Date(checkIn.value);
        minOut.setDate(minOut.getDate() + 1);
        checkOut.min = minOut.toISOString().split('T')[0];
    }

    // Mark initial selected room card icon state
    roomCards.forEach(c => {
        if (c.classList.contains('selected')) {
            c.querySelector('.room-icon-bg')?.classList.replace('bg-surface','bg-clay');
            c.querySelector('.room-icon-color')?.classList.replace('text-muted','text-white');
        }
    });

    hasInsurance.addEventListener('change', calc);

    const addToCartBtn = document.getElementById('addToCartBtn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            if (!checkIn.value || !checkOut.value) {
                alert('Pilih tanggal check-in dan check-out terlebih dahulu.');
                return;
            }

            addToCartBtn.disabled = true;
            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    itemable_type: 'hotel',
                    itemable_id: {{ $hotel->id }},
                    room_type: state.roomType,
                    check_in_date: checkIn.value,
                    check_out_date: checkOut.value,
                }),
            }).then((response) => {
                if (response.ok || response.redirected) {
                    window.location.href = '{{ route('cart.index') }}';
                } else {
                    addToCartBtn.disabled = false;
                    alert('Gagal menambahkan ke keranjang. Kamar mungkin sudah tidak tersedia.');
                }
            }).catch(() => {
                addToCartBtn.disabled = false;
                alert('Gagal menambahkan ke keranjang. Coba lagi.');
            });
        });
    }

    calc();
    checkAvailability();
});
</script>
@endsection
