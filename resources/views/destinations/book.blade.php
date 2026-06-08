@extends('layouts.app')

@section('title', 'Pesan Destinasi - ' . ($destination->name ?? 'Destination'))

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
<div class="antialiased bg-light min-h-screen py-20 reveal">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-4xl md:text-5xl font-black text-ocean-900 font-montserrat tracking-tight mb-4">Secure Your Adventure</h1>
            <p class="text-gray-500 text-lg">Complete the form below to book your spot at {{ $destination->name ?? 'this destination' }}.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12">
            {{-- Left Column: Destination Details --}}
            <div class="lg:col-span-3 space-y-8">
                {{-- Destination Card --}}
                <div class="cinematic-card p-0 overflow-hidden group border-0 shadow-2xl rounded-3xl">
                    <div class="relative w-full aspect-[16/9]">
                        <img src="{{ isset($destination) && $destination->image ? asset('storage/' . ltrim($destination->image, '/')) : asset('images/fallback.jpg') }}"
                             class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                             alt="{{ $destination->name ?? 'Destination' }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-ocean-900/80 via-transparent to-transparent"></div>
                        
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-map-marker-alt text-sunset-500 mr-2"></i>
                                        <span class="font-bold text-white uppercase tracking-widest text-sm">{{ $destination->location ?? 'Unknown Location' }}</span>
                                    </div>
                                    <h2 class="text-3xl font-black text-white font-montserrat drop-shadow-lg">{{ $destination->name ?? 'Destination' }}</h2>
                                </div>
                                <div class="bg-sunset-500/20 backdrop-blur-md border border-sunset-500/30 text-sunset-100 px-4 py-2 rounded-full text-sm font-bold flex items-center">
                                    <i class="fas fa-check-circle mr-2 text-sunset-500"></i>
                                    Available
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8 bg-white">
                        <h3 class="text-xl font-black text-ocean-900 mb-4 font-montserrat tracking-tight flex items-center">
                            <i class="fas fa-info-circle text-sunset-500 mr-3"></i> About Destination
                        </h3>
                        <div class="prose max-w-none text-gray-600 leading-relaxed">
                            {!! $destination->description ?? '<p>No description available.</p>' !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Booking Form --}}
            <div class="lg:col-span-2">
                <div id="bookingCard" 
                     class="cinematic-card p-0 border-0 shadow-2xl rounded-3xl overflow-hidden lg:sticky lg:top-24"
                     data-price-per-ticket="{{ $destination->price ?? 0 }}">
                    
                    {{-- Price Header --}}
                    <div class="bg-ocean-900 px-8 py-6 relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-sunset-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
                        <div class="flex justify-between items-center relative z-10">
                            <span class="text-white/70 font-bold uppercase tracking-widest text-xs">Starting from</span>
                            <span class="text-3xl font-black text-sunset-500 font-montserrat">
                                Rp {{ number_format($destination->price ?? 0, 0, ',', '.') }}
                                <span class="text-base font-medium text-white/70">/ pax</span>
                            </span>
                        </div>
                    </div>

                    <div class="p-8 bg-white">
                        <h2 class="text-2xl font-black text-ocean-900 mb-1 font-montserrat tracking-tight">Booking Details</h2>
                        <p class="text-gray-500 text-sm mb-6">Fill in your information to secure tickets.</p>

                        @if ($errors->any())
                            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl my-6" role="alert">
                                <div class="flex">
                                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i></div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-bold text-red-800">You have {{ $errors->count() }} validation errors</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form id="bookingForm" action="{{ route('destinations.store') }}" method="POST" class="space-y-5">
                            @csrf
                            <input type="hidden" name="destination_id" value="{{ $destination->id }}">
                            <input type="hidden" name="promo_code_id" id="promoCodeId" value="{{ old('promo_code_id') }}">
                            <input type="hidden" name="discount_amount" id="discountAmount" value="{{ old('discount_amount', 0) }}">

                            {{-- Customer Information --}}
                            <div class="space-y-4">
                                <div>
                                    <label for="customer_name" class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1.5">Full Name</label>
                                    <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required 
                                           class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-ocean-900 focus:border-transparent transition-all font-medium text-ocean-900">
                                </div>
                                <div>
                                    <label for="customer_email" class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1.5">Email Address</label>
                                    <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}" required 
                                           class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-ocean-900 focus:border-transparent transition-all font-medium text-ocean-900">
                                </div>
                                <div>
                                    <label for="customer_phone" class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1.5">Phone Number</label>
                                    <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required 
                                           class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-ocean-900 focus:border-transparent transition-all font-medium text-ocean-900">
                                </div>
                            </div>
                            
                            <hr class="border-gray-100 my-6">
                            
                            {{-- Booking Details --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="booking_date" class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1.5">Visit Date</label>
                                    <input type="date" name="booking_date" id="booking_date" value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}" required 
                                           class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-ocean-900 focus:border-transparent transition-all font-medium text-ocean-900">
                                </div>
                                <div>
                                    <label for="number_of_tickets" class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1.5">Passengers</label>
                                    <input type="number" name="number_of_tickets" id="number_of_tickets" value="{{ old('number_of_tickets', 1) }}" min="1" max="50" required 
                                           class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-ocean-900 focus:border-transparent transition-all font-medium text-ocean-900">
                                </div>
                            </div>
                            
                            <hr class="border-gray-100 my-6">
                            
                            {{-- Promo Code --}}
                            <div>
                                <label for="promoCode" class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-1.5">Promo Vouchers</label>
                                <div class="flex gap-2">
                                    <input type="text" id="promoCode" name="promo_code" value="{{ old('promo_code') }}" 
                                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-ocean-900 focus:border-transparent transition-all font-medium text-ocean-900" placeholder="Enter code">
                                    <button type="button" id="applyPromoBtn" class="px-6 py-3.5 bg-ocean-100 text-ocean-900 rounded-xl hover:bg-ocean-200 transition-colors duration-200 font-bold whitespace-nowrap">Apply</button>
                                </div>
                                <div id="promoMessage" class="hidden mt-3 text-sm p-3 rounded-lg font-medium"></div>
                            </div>

                            {{-- Price Summary --}}
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mt-6">
                                <h3 class="text-sm font-black text-ocean-900 mb-4 uppercase tracking-widest">Payment Summary</h3>
                                <div class="space-y-3 font-medium text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Ticket Price</span>
                                        <span class="text-ocean-900" id="subtotal-display">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Discount</span>
                                        <span id="discount-display" class="text-sunset-500 font-bold">- Rp 0</span>
                                    </div>
                                    <div class="flex justify-between font-black text-lg pt-4 border-t border-gray-200 mt-2">
                                        <span class="text-ocean-900">Total Price</span>
                                        <span id="total-price-display" class="text-sunset-500">Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="mt-8">
                                <button type="submit" class="btn-primary w-full py-4 rounded-xl flex items-center justify-center gap-x-2 shadow-xl shadow-sunset-500/20 text-lg">
                                    <i class="fas fa-lock"></i>
                                    Proceed to Payment
                                </button>
                            </div>

                            <p class="text-xs text-gray-400 font-medium text-center mt-4 border-t border-gray-100 pt-4">
                                By continuing, you agree to our <a href="#" class="text-ocean-900 hover:text-sunset-500 underline transition-colors">Terms & Conditions</a>.
                            </p>
                        </form>

                        {{-- Hidden Promo Data --}}
                        <div id="promoData" class="hidden" data-promos="{{ $promos ? json_encode($promos->mapWithKeys(fn($promo) => [strtoupper($promo->code) => [
                            'id' => $promo->id,
                            'amount' => $promo->discount_amount ?? null,
                            'percent' => $promo->discount_percent ?? null,
                            'active' => $promo->active,
                            'valid_from' => $promo->valid_from,
                            'valid_until' => $promo->valid_until
                        ]])) : '{}' }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const state = {
        pricePerTicket: 0,
        ticketCount: 1,
        promo: {
            id: null,
            code: '',
            discountValue: 0,
            applied: false
        }
    };

    const ui = {
        bookingCard: document.getElementById('bookingCard'),
        ticketInput: document.getElementById('number_of_tickets'),
        promoInput: document.getElementById('promoCode'),
        applyPromoBtn: document.getElementById('applyPromoBtn'),
        promoMessage: document.getElementById('promoMessage'),
        promoCodeIdInput: document.getElementById('promoCodeId'),
        discountAmountInput: document.getElementById('discountAmount'),
        subtotalDisplay: document.getElementById('subtotal-display'),
        discountDisplay: document.getElementById('discount-display'),
        totalPriceDisplay: document.getElementById('total-price-display'),
        form: document.getElementById('bookingForm'),
    };
    
    const promoDataEl = ui.bookingCard.querySelector('#promoData');
    const allPromos = promoDataEl ? JSON.parse(promoDataEl.dataset.promos || '{}') : {};

    const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
    
    const showPromoMessage = (message, isSuccess = true) => {
        ui.promoMessage.textContent = message;
        ui.promoMessage.className = 'mt-3 text-sm p-3 rounded-lg font-medium';
        ui.promoMessage.classList.add(isSuccess ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200');
        ui.promoMessage.classList.remove('hidden');
    };

    const resetPromoState = () => {
        state.promo.id = null;
        state.promo.code = '';
        state.promo.discountValue = 0;
        state.promo.applied = false;
        
        ui.promoCodeIdInput.value = '';
        ui.discountAmountInput.value = '0';
        ui.promoMessage.classList.add('hidden');
    };

    const calculateTotals = () => {
        const subtotal = state.pricePerTicket * state.ticketCount;
        const total = Math.max(0, subtotal - state.promo.discountValue);

        ui.subtotalDisplay.textContent = `${formatter.format(state.pricePerTicket)} x ${state.ticketCount}`;
        ui.discountDisplay.textContent = `- ${formatter.format(state.promo.discountValue)}`;
        ui.totalPriceDisplay.textContent = formatter.format(total);
        ui.discountAmountInput.value = state.promo.discountValue.toFixed(0);
    };

    const applyPromoCode = () => {
        const code = ui.promoInput.value.trim().toUpperCase();
        if (!code) {
            showPromoMessage('Enter promo code.', false);
            return;
        }

        resetPromoState();
        const promo = allPromos[code];

        if (!promo || !promo.active) {
            showPromoMessage('Invalid or inactive promo code.', false);
            calculateTotals();
            return;
        }

        const today = new Date().toISOString().split('T')[0];
        if (promo.valid_from && promo.valid_from > today) {
            showPromoMessage(`Promo is valid starting ${promo.valid_from}.`, false);
            calculateTotals();
            return;
        }
        if (promo.valid_until && promo.valid_until < today) {
            showPromoMessage(`Promo expired on ${promo.valid_until}.`, false);
            calculateTotals();
            return;
        }

        let discount = 0;
        const subtotal = state.pricePerTicket * state.ticketCount;
        if (promo.percent > 0) {
            discount = subtotal * (promo.percent / 100);
        } else if (promo.amount > 0) {
            discount = promo.amount;
        }

        if (discount > 0) {
            state.promo = { id: promo.id, code: code, discountValue: discount, applied: true };
            ui.promoCodeIdInput.value = promo.id;
            showPromoMessage(`Promo "${code}" applied successfully!`, true);
        } else {
            showPromoMessage('Promo is valid but provides zero discount here.', false);
        }
        calculateTotals();
    };

    const initializeEventListeners = () => {
        ui.ticketInput.addEventListener('input', (e) => {
            state.ticketCount = Math.max(1, parseInt(e.target.value, 10) || 1);
            ui.ticketInput.value = state.ticketCount;
            if (state.promo.applied) {
                applyPromoCode();
            } else {
                calculateTotals();
            }
        });

        ui.applyPromoBtn.addEventListener('click', applyPromoCode);

        ui.form.addEventListener('submit', (e) => {
            if (ui.promoInput.value && !state.promo.applied) {
                e.preventDefault();
                showPromoMessage('Promo code not applied. Click "Apply" first.', false);
            }
        });
    };

    const init = () => {
        state.pricePerTicket = parseFloat(ui.bookingCard.dataset.pricePerTicket) || 0;
        
        if (state.pricePerTicket <= 0) {
            ui.bookingCard.innerHTML = '<div class="p-8 text-center text-red-500 font-bold">Failed to load price details.</div>';
            return;
        }
        
        state.ticketCount = Math.max(1, parseInt(ui.ticketInput.value, 10) || 1);
        ui.ticketInput.value = state.ticketCount;
        
        initializeEventListeners();
        calculateTotals();
    };

    init();
});
</script>
@endsection