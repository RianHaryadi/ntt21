@extends('layouts.app')

@section('title', 'Book Tour Package')

@section('content')
<section class="py-12 bg-gradient-to-b from-surface to-paper">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-paper p-8 rounded-3xl shadow-xl border border-laut/20">
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-between relative">
                    <div class="absolute top-1/2 left-0 right-0 h-1 bg-line -translate-y-1/2 z-0"></div>
                    <div class="flex items-center justify-between w-full relative z-10">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-laut text-white flex items-center justify-center font-bold mb-2">
                                1
                            </div>
                            <span class="text-sm font-medium text-laut">Booking Details</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-line text-muted flex items-center justify-center font-bold mb-2">
                                2
                            </div>
                            <span class="text-sm font-medium text-muted">Payment</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-line text-muted flex items-center justify-center font-bold mb-2">
                                3
                            </div>
                            <span class="text-sm font-medium text-muted">Confirmation</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-ink">Book Your Adventure</h2>
                <div class="bg-laut/10 text-laut px-4 py-2 rounded-full text-sm font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Instant Confirmation
                </div>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 text-red-600 bg-red-50 rounded-lg border border-red-200 font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('paket-tour.store') }}" method="POST" class="space-y-8">
                @csrf

                <input type="hidden" name="tour_package_id" value="{{ $tourPackage->id }}">
                <input type="hidden" name="promo_code_id" id="promoCodeId">
                <input type="hidden" name="discount_amount" id="discountInput">

                <!-- Tour Package Card -->
                <div class="bg-laut/5 rounded-xl p-6 border border-laut/20 transition-all duration-300 hover:shadow-md">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="md:w-1/3 relative">
                            <div class="aspect-w-16 aspect-h-9 bg-line rounded-xl overflow-hidden shadow-md">
                                <img src="{{ asset('storage/' . $tourPackage->thumbnail) }}" 
                                    alt="{{ $tourPackage->name }} tour"
                                    class="w-full h-full object-cover transition-transform duration-700 hover:scale-105"
                                    loading="eager"
                                    width="400"
                                    height="288">
                            </div>
                            <div class="absolute top-3 left-3 bg-laut text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ $tourPackage->days }} Days
                            </div>
                        </div>
                        <div class="md:w-2/3">
                            <h3 class="text-2xl font-bold text-ink mb-2">{{ $tourPackage->name }}</h3>
                            <div class="flex items-center text-muted mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-laut mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $tourPackage->location }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-muted">Difficulty</p>
                                    <p class="font-medium flex items-center">
                                        @for($i = 0; $i < $tourPackage->difficulty; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-muted">Price</p>
                                    <p class="font-bold text-laut text-lg" id="pricePerTicketDisplay">
                                        Rp {{ number_format($tourPackage->price, 0, ',', '.') }} <span class="text-sm font-normal">/ person</span>
                                    </p>
                                    <input type="hidden" id="pricePerTicket" value="{{ $tourPackage->price }}">
                                </div>
                            </div>
                            <p class="text-muted">{{ Str::limit($tourPackage->description, 150) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-ink border-b pb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-laut" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Your Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-ink mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required readonly
                                    class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 bg-line/30 text-muted cursor-not-allowed focus:outline-none">
                            </div>
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-ink mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', auth()->user()->email) }}" required readonly
                                    class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 bg-line/30 text-muted cursor-not-allowed focus:outline-none">
                            </div>
                            @error('customer_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-ink mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', auth()->user()->phone) }}" required
                                    class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 focus:ring-laut focus:border-laut hover:border-laut/60 transition duration-200">
                            </div>
                            @error('customer_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-ink border-b pb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-laut" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Booking Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="destination_id" class="block text-sm font-medium text-ink mb-1">
                                Destination <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <select name="destination_id" id="destination_id" required
                                        class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 focus:ring-laut focus:border-laut hover:border-laut/60 transition duration-200 appearance-none bg-paper">
                                    @foreach ($destinations as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="number_of_tickets" class="block text-sm font-medium text-ink mb-1">
                                Number of Tickets <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                </div>
                                <input type="number" name="number_of_tickets" id="number_of_tickets" min="1" max="10" value="1" required
                                       class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 focus:ring-laut focus:border-laut hover:border-laut/60 transition duration-200">
                            </div>
                        </div>
                        <div>
                            <label for="booking_date" class="block text-sm font-medium text-ink mb-1">
                                Booking Date <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" name="booking_date" id="booking_date" required min="{{ date('Y-m-d') }}"
                                       class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 focus:ring-laut focus:border-laut hover:border-laut/60 transition duration-200">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="special_request" class="block text-sm font-medium text-ink mb-1">
                            Special Requests
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 pt-3 flex items-start pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                            </div>
                            <textarea name="special_request" id="special_request" rows="3"
                                      class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 focus:ring-laut focus:border-laut hover:border-laut/60 transition duration-200"
                                      placeholder="Any special requirements or notes..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Promo Code Section -->
                <div class="bg-laut/5 rounded-xl p-6 border border-laut/20 transition-all duration-300 hover:shadow-md">
                    <h3 class="text-xl font-bold text-ink mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-laut mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Apply Promo Code
                    </h3>
                    <p class="text-muted mb-4">Enter your promo code below to get discounts</p>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-grow relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            </div>
                            <input type="text" id="promoInput" name="promo_code" 
                                   class="pl-10 block w-full border border-line rounded-lg shadow-sm py-3 px-4 focus:ring-laut focus:border-laut hover:border-laut/60 transition duration-200" 
                                   placeholder="SUMMER2023">
                        </div>
                        <button type="button" id="applyPromoBtn" 
                                class="px-6 py-3 bg-laut hover:bg-laut/90 text-white font-medium rounded-lg shadow-md transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Apply Code
                        </button>
                    </div>
                    
                    <div id="promoMessage" class="hidden mt-3 p-3 rounded-lg border text-sm"></div>
                    <div id="discountDisplay" class="mt-3 text-green-600 font-medium"></div>
                </div>

                <!-- Travel Insurance Add-on -->
                <div class="bg-paper rounded-xl p-6 border border-line shadow-sm">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="has_insurance" id="hasInsurance" value="1"
                               class="mt-1 w-5 h-5 rounded border-line text-laut focus:ring-laut">
                        <span class="flex-1">
                            <span class="flex items-center gap-2 font-bold text-ink">
                                <i class="fas fa-shield-alt text-laut"></i> Tambahkan Asuransi Perjalanan
                            </span>
                            <span class="block text-muted text-sm mt-1">
                                Perlindungan kecelakaan &amp; pembatalan perjalanan — Rp {{ number_format(config('services.insurance.price_per_ticket'), 0, ',', '.') }} / tiket
                            </span>
                        </span>
                    </label>
                    <input type="hidden" id="insurancePricePerTicket" value="{{ config('services.insurance.price_per_ticket') }}">
                </div>

                <!-- Price Summary -->
                <div class="bg-paper rounded-xl p-6 border border-line shadow-sm">
                    <h3 class="text-xl font-bold text-ink mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-laut" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Price Summary
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-muted">Base Price</span>
                            <span id="basePriceDisplay" class="text-ink">Rp {{ number_format($tourPackage->price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Number of Tickets</span>
                            <span id="ticketCountDisplay" class="text-ink">1</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted">Subtotal</span>
                            <span id="subtotalDisplay" class="text-ink">Rp {{ number_format($tourPackage->price, 0, ',', '.') }}</span>
                        </div>
                        @if($bundleHotel && $bundleHotelPricePerNight > 0)
                        @php $bundleHotelTotal = $bundleHotelPricePerNight * $bundleNights * (1 - $bundleDiscountPercent / 100); @endphp
                        <div class="flex justify-between items-start bg-laut/5 border border-laut/20 rounded-lg px-3 py-2.5 -mx-1">
                            <span class="text-laut text-xs font-semibold leading-relaxed">
                                <i class="fas fa-hotel mr-1"></i> Bundle: {{ $bundleHotel->name }}
                                ({{ $bundleNights }} malam) — hemat {{ $bundleDiscountPercent }}% dari harga hotel
                            </span>
                            <span class="text-laut text-xs font-bold whitespace-nowrap ml-3">+Rp {{ number_format($bundleHotelTotal, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-muted">Discount</span>
                            <span id="discountAmountDisplay" class="text-green-600">- Rp 0</span>
                        </div>
                        <div class="flex justify-between" id="insuranceRow" style="display:none">
                            <span class="text-muted">Asuransi Perjalanan</span>
                            <span id="insuranceAmountDisplay" class="text-ink">Rp 0</span>
                        </div>
                        <div class="border-t border-line pt-3 mt-3">
                            <div class="flex justify-between font-bold">
                                <span class="text-ink">Total Payment</span>
                                <span id="totalPriceDisplay" class="text-laut text-xl">Rp {{ number_format($tourPackage->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 space-y-3">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-laut to-petrol hover:from-laut/90 hover:to-petrol/90 text-white text-lg font-bold rounded-lg shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-laut">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Confirm Booking
                    </button>
                    @if(!$bundleHotel)
                    <button type="button" id="addToCartBtn"
                            class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-white border border-line text-ink font-bold rounded-lg hover:border-laut hover:text-laut transition-all">
                        <i class="fas fa-shopping-bag mr-2"></i> Tambah ke Keranjang
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Promo JSON -->
<div id="promoData" data-promos='@json($promoJson)'></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum booking date
        document.getElementById('booking_date').min = new Date().toISOString().split('T')[0];

        // Get elements
        const promoData = document.getElementById('promoData');
        const promoCodes = JSON.parse(promoData.dataset.promos);
        const promoInput = document.getElementById('promoInput');
        const promoCodeIdInput = document.getElementById('promoCodeId');
        const discountInput = document.getElementById('discountInput');
        const applyPromoBtn = document.getElementById('applyPromoBtn');
        const promoMessage = document.getElementById('promoMessage');
        const ticketInput = document.getElementById('number_of_tickets');
        const discountDisplay = document.getElementById('discountDisplay');
        const pricePerTicket = parseFloat(document.getElementById('pricePerTicket').value);
        const bundleHotelPerTicket = {{ $bundleHotel ? ($bundleHotelPricePerNight * $bundleNights * (1 - $bundleDiscountPercent / 100)) : 0 }};
        const insurancePricePerTicket = parseFloat(document.getElementById('insurancePricePerTicket').value);
        const hasInsuranceInput = document.getElementById('hasInsurance');
        const insuranceRow = document.getElementById('insuranceRow');
        const insuranceAmountDisplay = document.getElementById('insuranceAmountDisplay');
        const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' });

        // Price summary elements
        const basePriceDisplay = document.getElementById('basePriceDisplay');
        const ticketCountDisplay = document.getElementById('ticketCountDisplay');
        const subtotalDisplay = document.getElementById('subtotalDisplay');
        const discountAmountDisplay = document.getElementById('discountAmountDisplay');
        const totalPriceDisplay = document.getElementById('totalPriceDisplay');

        // Initialize price display
        function updatePriceSummary() {
            const ticketCount = parseInt(ticketInput.value) || 1;
            const tourSubtotal = ticketCount * pricePerTicket;
            const hotelBundleTotal = ticketCount * bundleHotelPerTicket;
            const subtotal = tourSubtotal + hotelBundleTotal;
            const discount = parseFloat(discountInput.value) || 0;
            const insurance = hasInsuranceInput.checked ? ticketCount * insurancePricePerTicket : 0;
            const total = subtotal - discount + insurance;

            ticketCountDisplay.textContent = ticketCount;
            subtotalDisplay.textContent = formatter.format(tourSubtotal);
            discountAmountDisplay.textContent = discount > 0 ? `- ${formatter.format(discount)}` : '- Rp 0';
            insuranceRow.style.display = insurance > 0 ? 'flex' : 'none';
            insuranceAmountDisplay.textContent = formatter.format(insurance);
            totalPriceDisplay.textContent = formatter.format(total);
        }

        // Clear promo code
        function clearPromo() {
            promoMessage.classList.add('hidden');
            promoMessage.textContent = '';
            promoMessage.className = 'hidden mt-3 p-3 rounded-lg border text-sm';
            discountInput.value = '0';
            promoCodeIdInput.value = '';
            discountDisplay.textContent = '';
            updatePriceSummary();
        }

        // Apply promo code
        applyPromoBtn.addEventListener('click', () => {
            const code = promoInput.value.trim().toUpperCase();
            clearPromo();

            const promo = promoCodes[code];
            const today = new Date().toISOString().split('T')[0];
            const ticketCount = parseInt(ticketInput.value) || 1;
            const subtotal = (ticketCount * pricePerTicket) + (ticketCount * bundleHotelPerTicket);

            if (!promo || !promo.active) {
                showPromoMessage(`Promo code "${code}" is invalid`, 'error');
                return;
            }

            if (promo.valid_from && promo.valid_from > today) {
                showPromoMessage(`Promo ${code} is valid from ${promo.valid_from}`, 'error');
                return;
            }

            if (promo.valid_until && promo.valid_until < today) {
                showPromoMessage(`Promo ${code} expired on ${promo.valid_until}`, 'error');
                return;
            }

            let discount = 0;
            let discountType = '';

            if (promo.percent && parseFloat(promo.percent) > 0) {
                discount = subtotal * (parseFloat(promo.percent) / 100);
                discountType = `${parseFloat(promo.percent)}%`;
            } else if (promo.amount && parseFloat(promo.amount) > 0) {
                discount = parseFloat(promo.amount);
                discountType = formatter.format(discount);
            }

            if (discount <= 0) {
                showPromoMessage(`Promo ${code} has no valid discount`, 'error');
                return;
            }

            promoCodeIdInput.value = promo.id;
            discountInput.value = discount.toFixed(2);
            showPromoMessage(`Promo ${code} applied! You saved ${discountType} (${formatter.format(discount)})`, 'success');
            discountDisplay.textContent = `Discount applied: ${formatter.format(discount)}`;
            updatePriceSummary();
        });

        // Show promo message
        function showPromoMessage(message, type) {
            promoMessage.textContent = message;
            promoMessage.className = 'mt-3 p-3 rounded-lg border text-sm';
            
            if (type === 'success') {
                promoMessage.classList.add('text-green-600', 'border-green-200', 'bg-green-50');
            } else {
                promoMessage.classList.add('text-red-600', 'border-red-200', 'bg-red-50');
            }
            
            promoMessage.classList.remove('hidden');
        }

        // Ticket input change handler
        ticketInput.addEventListener('change', function() {
            clearPromo();
            updatePriceSummary();
        });

        // Insurance checkbox handler
        hasInsuranceInput.addEventListener('change', updatePriceSummary);

        // Initialize price summary
        updatePriceSummary();

        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                const bookingDate = document.getElementById('booking_date').value;
                if (!bookingDate) {
                    showPromoMessage('Pilih tanggal kunjungan terlebih dahulu.', 'error');
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
                        itemable_type: 'tour',
                        itemable_id: {{ $tourPackage->id }},
                        booking_date: bookingDate,
                        number_of_tickets: parseInt(ticketInput.value, 10) || 1,
                    }),
                }).then((response) => {
                    if (response.ok || response.redirected) {
                        window.location.href = '{{ route('cart.index') }}';
                    } else {
                        addToCartBtn.disabled = false;
                        showPromoMessage('Gagal menambahkan ke keranjang. Coba lagi.', 'error');
                    }
                }).catch(() => {
                    addToCartBtn.disabled = false;
                    showPromoMessage('Gagal menambahkan ke keranjang. Coba lagi.', 'error');
                });
            });
        }
    });
</script>
@endsection