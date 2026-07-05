@extends('layouts.app')

@section('title', 'Check Your Booking')

@push('styles')
<style>
    .input-modern {
        width: 100%;
        padding: 16px 48px 16px 20px;
        border: 1px solid rgba(15, 23, 42, 0.12);
        border-radius: 16px;
        font-size: 1rem;
        color: #1e293b;
        background: #ffffff;
        transition: all 0.3s ease;
        outline: none;
        font-weight: 500;
    }
    .input-modern:focus {
        border-color: #0F6E63;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(15, 110, 99, 0.1);
    }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="relative pt-32 pb-20 text-white overflow-hidden bg-[#1e293b] border-b border-slate-700/60 reveal">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent pointer-events-none z-0"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-laut rounded-full mix-blend-multiply filter blur-[100px] opacity-20 z-0"></div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold uppercase tracking-widest text-laut mb-6 backdrop-blur-md">
            <i class="fas fa-ticket-alt"></i> Reservation Management
        </div>
        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-6 font-serif tracking-tight tracking-tight drop-shadow-2xl">
            Check Your <span class="text-laut">Booking</span>
        </h1>
        <p class="text-white/80 text-lg md:text-xl max-w-2xl mx-auto font-inter">Enter your booking reference number to retrieve your reservation status and details.</p>
    </div>
</section>

{{-- ── CONTENT ── --}}
<section class="py-20 bg-transparent min-h-[50vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Search Form Card --}}
        <div class="cinematic-card shadow-2xl border border-slate-200/60 p-8 md:p-10 mb-12 bg-white reveal">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center flex-shrink-0 border border-slate-200/60 shadow-sm">
                    <i class="fas fa-search text-slate-700 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-800 font-serif tracking-tight tracking-tight">Find My Booking</h2>
                    <p class="text-slate-500 text-sm font-medium">Enter your reference to securely access details</p>
                </div>
            </div>

            <form action="{{ route('booking.check') }}" method="POST" id="bookingForm" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-slate-500 mb-2">
                        Booking Reference Number
                    </label>
                    <div class="relative">
                        <input type="text" id="booking_number" name="booking_number"
                               placeholder="e.g. BOOK-20250618-1234" required
                               value="{{ old('booking_number') }}"
                               class="input-modern pr-12">
                        <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none">
                            <i class="fas fa-barcode text-slate-400"></i>
                        </div>
                    </div>
                    @error('booking_number')
                    <p class="mt-3 text-sm text-red-500 flex items-center gap-1 font-bold">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                    @enderror
                    <p class="mt-3 text-xs text-slate-500 font-medium">Found in your confirmation email or SMS receipt.</p>
                </div>

                <button type="submit" id="searchBtn"
                        class="btn-primary w-full py-4 text-lg rounded-xl flex items-center justify-center gap-2 shadow-sm shadow-laut/10">
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
            <div class="cinematic-card p-0 shadow-2xl overflow-hidden border border-slate-200/60 bg-white rounded-3xl">
                {{-- Hotel Image Header --}}
                <div class="relative h-56 bg-slate-950 overflow-hidden">
                    @if(isset($data->hotel) && $data->hotel->image)
                    <img src="{{ asset('storage/'.$data->hotel->image) }}" alt="{{ $data->hotel->name }}" class="w-full h-full object-cover opacity-60 mix-blend-overlay">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-white to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 w-full flex items-end justify-between">
                        <div>
                            <h2 class="text-3xl lg:text-4xl font-black text-slate-800 font-serif tracking-tight tracking-tight mb-2">{{ $data->hotel->name ?? '-' }}</h2>
                            <p class="text-laut text-sm font-bold flex items-center gap-2 uppercase tracking-widest">
                                <i class="fas fa-map-marker-alt"></i> {{ $data->hotel->location ?? '' }}
                            </p>
                        </div>
                        <span class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-slate-200/60 backdrop-blur-md shadow-sm"
                               style="background:{{ match(strtolower($data->status)) {'confirmed'=>'rgba(16, 185, 129, 0.2)', 'pending'=>'rgba(245, 158, 11, 0.2)', default=>'rgba(239, 68, 68, 0.2)'} }}; color:{{ match(strtolower($data->status)) {'confirmed'=>'#10b981', 'pending'=>'#f59e0b', default=>'#ef4444'} }}">
                            <i class="fas fa-circle text-[8px] mr-1"></i> {{ strtoupper($data->status) }}
                        </span>
                    </div>
                </div>

                <div class="p-8 space-y-8 bg-transparent">
                    {{-- Grid Info --}}
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/60 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200/60 text-laut">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <h3 class="font-black text-slate-800 font-serif tracking-tight uppercase tracking-wider text-sm">Guest Details</h3>
                            </div>
                            <div class="space-y-1">
                                <p class="text-slate-800 font-bold text-lg">{{ $data->customer_name }}</p>
                                <p class="text-slate-500 text-sm font-medium">{{ $data->customer_email }}</p>
                                <p class="text-slate-500 text-sm font-medium">{{ $data->customer_phone }}</p>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/60 hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200/60 text-laut">
                                    <i class="far fa-calendar-alt text-sm"></i>
                                </div>
                                <h3 class="font-black text-slate-800 font-serif tracking-tight uppercase tracking-wider text-sm">Stay Info</h3>
                            </div>
                            <div class="space-y-1">
                                <p class="font-mono text-slate-800 font-bold text-sm bg-white px-2 py-1 rounded border border-slate-200/60 w-fit mb-2">{{ $data->booking_number }}</p>
                                <p class="text-slate-500 text-sm font-medium">{{ ucfirst($data->room_type) }} Room · {{ $data->guests }} Guest{{ $data->guests > 1 ? 's' : '' }}</p>
                                <p class="text-slate-800 text-sm font-bold">{{ $data->check_in_date->format('M d, Y') }} &rarr; {{ $data->check_out_date->format('M d, Y') }}</p>
                                <p class="text-laut text-xs font-bold uppercase tracking-widest">{{ $data->check_in_date->diffInDays($data->check_out_date) }} night{{ $data->check_in_date->diffInDays($data->check_out_date) > 1 ? 's' : '' }} total stay</p>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Summary --}}
                    <div class="bg-slate-50 rounded-2xl p-6 md:p-8 border border-slate-200/60 text-slate-700 relative overflow-hidden shadow-sm">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-laut rounded-full mix-blend-multiply filter blur-3xl opacity-10 pointer-events-none"></div>
                        
                        <h3 class="font-black text-slate-800 mb-6 flex items-center gap-3 font-serif tracking-tight uppercase tracking-wider text-sm relative z-10">
                            <i class="fas fa-file-invoice-dollar text-laut"></i> Overview Keuangan
                        </h3>
                        <div class="space-y-4 relative z-10 font-medium">
                            <div class="flex justify-between text-sm text-slate-500">
                                <span>Base Room Rate</span>
                                <span class="text-slate-800 font-bold">Rp {{ number_format($data->room_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-500">
                                <span>Government Taxes</span>
                                <span class="text-slate-800 font-bold">Rp {{ number_format($data->tax, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-500">
                                <span>Service & Resort Fees</span>
                                <span class="text-slate-800 font-bold">Rp {{ number_format($data->service_charge, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-slate-200 pt-4 mt-2 flex justify-between items-center">
                                <span>Total Tagihan</span>
                                <span class="font-black text-laut text-2xl font-serif tracking-tight tracking-tight">Rp {{ number_format($data->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-4 pt-2 font-black">
                        <a href="{{ route('booking.voucher', $data->id) }}" class="flex-1 flex items-center justify-center gap-2 btn-outline py-4 rounded-xl text-sm font-bold transition">
                            <i class="fas fa-file-pdf text-slate-500"></i> Download Voucher PDF
                        </a>
                        <form action="{{ route('booking.resendEmail', $data->booking_number) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl text-sm font-bold border border-slate-950 transition">
                                <i class="fas fa-paper-plane text-laut"></i> Kirim Ulang Email
                            </button>
                        </form>
                    </div>

                    @if(session('success'))
                    <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-bold flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                    @endif

                    {{-- Cancellation --}}
                    <div class="pt-6 border-t border-slate-100">
                        @if($data->cancellation_status)
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-sm font-bold flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            Status pembatalan: <span class="uppercase">{{ $data->cancellation_status }}</span>
                        </div>
                        @elseif($data->isCancellable())
                        <details class="group">
                            <summary class="cursor-pointer select-none list-none text-sm font-bold text-red-500 hover:text-red-600 flex items-center gap-2">
                                <i class="fas fa-ban"></i> Ajukan Pembatalan Booking
                            </summary>
                            <form action="{{ route('booking.requestCancellation', $data->booking_number) }}" method="POST" class="mt-4 space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs uppercase tracking-widest font-bold text-slate-500 mb-2">Konfirmasi Email Pemesan</label>
                                    <input type="email" name="customer_email" required placeholder="Email yang digunakan saat booking"
                                           class="input-modern">
                                    @error('customer_email')
                                    <p class="mt-2 text-sm text-red-500 font-bold"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-widest font-bold text-slate-500 mb-2">Alasan Pembatalan</label>
                                    <textarea name="reason" required rows="3" placeholder="Ceritakan alasan Anda membatalkan booking ini..."
                                              class="input-modern"></textarea>
                                </div>
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-3 px-6 rounded-xl text-sm font-bold transition">
                                    Kirim Permintaan Pembatalan
                                </button>
                            </form>
                        </details>
                        @endif
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