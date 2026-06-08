@extends('layouts.app')

@section('title', 'Check Your Booking')

@push('styles')
<style>
    .input-modern {
        width: 100%;
        padding: 16px 48px 16px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-size: 1rem;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.3s ease;
        outline: none;
        font-weight: 500;
    }
    .input-modern:focus {
        border-color: #0f172a; /* ocean-900 */
        background: #fff;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.1);
    }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="relative pt-32 pb-20 text-white overflow-hidden bg-ocean-900 border-b border-white/10 reveal">
    <div class="absolute inset-0 bg-gradient-to-t from-ocean-900 via-ocean-900/40 to-transparent pointer-events-none z-0"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-sunset-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-40 z-0"></div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 border border-white/20 text-xs tracking-widest uppercase font-bold text-sunset-500 mb-6 backdrop-blur-md">
            <i class="fas fa-ticket-alt"></i> Reservation Management
        </div>
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-6 font-montserrat tracking-tight drop-shadow-2xl">
            Check Your <span class="text-sunset-500">Booking</span>
        </h1>
        <p class="text-white/70 text-lg md:text-xl max-w-2xl mx-auto font-inter">Enter your booking reference number to retrieve your reservation status and details.</p>
    </div>
</section>

{{-- ── CONTENT ── --}}
<section class="py-20 bg-light min-h-[50vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Search Form Card --}}
        <div class="cinematic-card shadow-2xl border-0 p-8 md:p-10 mb-12 reveal">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-ocean-50 flex items-center justify-center flex-shrink-0 border border-ocean-100 shadow-sm">
                    <i class="fas fa-search text-ocean-900 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-ocean-900 font-montserrat tracking-tight">Find My Booking</h2>
                    <p class="text-gray-500 text-sm font-medium">Enter your reference to securely access details</p>
                </div>
            </div>

            <form action="{{ route('booking.check') }}" method="POST" id="bookingForm" class="space-y-6">
                @csrf
                <div>
                    <label for="booking_number" class="block text-xs uppercase tracking-widest font-bold text-gray-400 mb-2">
                        Booking Reference Number
                    </label>
                    <div class="relative">
                        <input type="text" id="booking_number" name="booking_number"
                               placeholder="e.g. BOOK-20250618-1234" required
                               value="{{ old('booking_number') }}"
                               class="input-modern pr-12">
                        <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none">
                            <i class="fas fa-barcode text-gray-300"></i>
                        </div>
                    </div>
                    @error('booking_number')
                    <p class="mt-3 text-sm text-red-500 flex items-center gap-1 font-bold">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                    @enderror
                    <p class="mt-3 text-xs text-gray-400 font-medium">Found in your confirmation email or SMS receipt.</p>
                </div>

                <button type="submit" id="searchBtn"
                        class="btn-primary w-full py-4 text-lg rounded-xl flex items-center justify-center gap-2 shadow-xl shadow-sunset-500/20">
                    <span id="searchText" class="flex items-center gap-2 font-bold tracking-wide">
                        <i class="fas fa-search" id="searchIcon"></i> Retrieve Booking
                    </span>
                    <svg id="spinnerIcon" class="hidden animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Result --}}
        @isset($bookingType)
        <div id="resultContainer" class="opacity-0 translate-y-8 transition-all duration-700 ease-out">
            @if($bookingType === 'hotel')
            <div class="cinematic-card p-0 shadow-2xl overflow-hidden border-0 rounded-3xl">
                {{-- Hotel Image Header --}}
                <div class="relative h-56 bg-ocean-900 overflow-hidden">
                    @if(isset($data->hotel) && $data->hotel->image)
                    <img src="{{ asset('storage/'.$data->hotel->image) }}" alt="{{ $data->hotel->name }}" class="w-full h-full object-cover opacity-60 mix-blend-overlay">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-ocean-900 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 w-full flex items-end justify-between">
                        <div>
                            <h2 class="text-3xl lg:text-4xl font-black text-white font-montserrat tracking-tight mb-2">{{ $data->hotel->name ?? '-' }}</h2>
                            <p class="text-sunset-500 text-sm font-bold flex items-center gap-2 uppercase tracking-widest">
                                <i class="fas fa-map-marker-alt"></i> {{ $data->hotel->location ?? '' }}
                            </p>
                        </div>
                        <span class="px-4 py-2 rounded-full text-xs font-bold text-white uppercase tracking-widest border border-white/20 backdrop-blur-md shadow-lg"
                              style="background:{{ match(strtolower($data->status)) {'confirmed'=>'rgba(16, 185, 129, 0.2)', 'pending'=>'rgba(245, 158, 11, 0.2)', default=>'rgba(239, 68, 68, 0.2)'} }}; color:{{ match(strtolower($data->status)) {'confirmed'=>'#10b981', 'pending'=>'#f59e0b', default=>'#ef4444'} }}">
                            <i class="fas fa-circle text-[8px] mr-1"></i> {{ strtoupper($data->status) }}
                        </span>
                    </div>
                </div>

                <div class="p-8 space-y-8 bg-white">
                    {{-- Grid Info --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 text-sunset-500">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <h3 class="font-black text-ocean-900 font-montserrat uppercase tracking-wider text-sm">Guest Details</h3>
                            </div>
                            <div class="space-y-1">
                                <p class="text-ocean-900 font-bold text-lg">{{ $data->customer_name }}</p>
                                <p class="text-gray-500 text-sm font-medium">{{ $data->customer_email }}</p>
                                <p class="text-gray-500 text-sm font-medium">{{ $data->customer_phone }}</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 text-sunset-500">
                                    <i class="far fa-calendar-alt text-sm"></i>
                                </div>
                                <h3 class="font-black text-ocean-900 font-montserrat uppercase tracking-wider text-sm">Stay Info</h3>
                            </div>
                            <div class="space-y-1">
                                <p class="font-mono text-ocean-900 font-bold text-sm bg-white px-2 py-1 rounded border border-gray-200 w-fit mb-2">{{ $data->booking_number }}</p>
                                <p class="text-gray-500 text-sm font-medium">{{ ucfirst($data->room_type) }} Room · {{ $data->guests }} Guest{{ $data->guests > 1 ? 's' : '' }}</p>
                                <p class="text-ocean-900 text-sm font-bold">{{ $data->check_in_date->format('M d, Y') }} &rarr; {{ $data->check_out_date->format('M d, Y') }}</p>
                                <p class="text-sunset-500 text-xs font-bold uppercase tracking-widest">{{ $data->check_in_date->diffInDays($data->check_out_date) }} night{{ $data->check_in_date->diffInDays($data->check_out_date) > 1 ? 's' : '' }} total stay</p>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Summary --}}
                    <div class="bg-ocean-900 rounded-2xl p-6 md:p-8 border border-white/10 text-white relative overflow-hidden shadow-xl">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sunset-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none"></div>
                        
                        <h3 class="font-black text-white mb-6 flex items-center gap-3 font-montserrat uppercase tracking-wider text-sm relative z-10">
                            <i class="fas fa-file-invoice-dollar text-sunset-500"></i> Financial Overview
                        </h3>
                        <div class="space-y-4 relative z-10 font-medium">
                            <div class="flex justify-between text-sm text-white/70">
                                <span>Base Room Rate</span>
                                <span class="text-white">Rp {{ number_format($data->room_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-white/70">
                                <span>Government Taxes</span>
                                <span class="text-white">Rp {{ number_format($data->tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-white/70">
                                <span>Service & Resort Fees</span>
                                <span class="text-white">Rp {{ number_format($data->service_charge, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-white/20 pt-4 mt-2 flex justify-between items-center">
                                <span class="text-xs uppercase tracking-widest text-sunset-500 font-bold">Total Processed</span>
                                <span class="font-black text-white text-2xl font-montserrat tracking-tight">Rp {{ number_format($data->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <button onclick="window.print()" class="flex-1 flex items-center justify-center gap-2 btn-outline text-ocean-900 border-gray-200 py-4 rounded-xl text-sm font-bold transition">
                            <i class="fas fa-print text-gray-400"></i> Download / Print
                        </button>
                        <a href="#" class="flex-1 flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-ocean-900 py-4 rounded-xl text-sm font-bold transition">
                            <i class="fas fa-headset text-sunset-500"></i> Get Support
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endisset

    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bookingForm');
    const btn = document.getElementById('searchBtn');
    const searchIcon = document.getElementById('searchIcon');
    const spinnerIcon = document.getElementById('spinnerIcon');
    const searchText = document.getElementById('searchText');

    if(form) {
        form.addEventListener('submit', function () {
            searchIcon.classList.add('hidden');
            spinnerIcon.classList.remove('hidden');
            searchText.innerHTML = 'Retrieving...';
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        });
    }

    const result = document.getElementById('resultContainer');
    if (result) {
        setTimeout(() => {
            result.classList.remove('opacity-0', 'translate-y-8');
            result.classList.add('opacity-100', 'translate-y-0');
        }, 300);
    }

    const input = document.getElementById('booking_number');
    if(input) { input.focus(); }
});
</script>
@endpush

@endsection