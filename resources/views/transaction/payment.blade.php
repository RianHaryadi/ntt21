@extends('layouts.app')

@section('title', 'Pembayaran - ' . ($transaction->booking_code ?? 'Payment'))

@section('content')
<div class="antialiased bg-light min-h-screen py-24 px-4 reveal">
    <div class="container mx-auto max-w-4xl">
        
        <!-- Success Alert for Initial Booking Creation -->
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-6 rounded-2xl mb-10 shadow-xl flex items-center justify-between reveal">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <div>
                        <p class="font-black text-sm uppercase tracking-widest">{{ session('success_title') ?? 'Booking Created Successfully!' }}</p>
                        <p class="text-xs font-bold opacity-70">{{ session('success') }}</p>
                    </div>
                </div>
                <button class="text-green-500 hover:text-green-700" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
            
            {{-- Order Summary --}}
            <div class="lg:col-span-2">
                <div class="cinematic-card p-0 shadow-2xl border-0 overflow-hidden rounded-3xl lg:sticky lg:top-24">
                    <div class="bg-ocean-900 p-8 relative overflow-hidden text-white">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-sunset-500 rounded-full filter blur-3xl opacity-30"></div>
                        <p class="text-[10px] uppercase font-black text-sunset-500 tracking-widest mb-1 relative z-10">Booking Invoice</p>
                        <h2 class="text-2xl font-black font-montserrat tracking-tight relative z-10">#{{ $transaction->booking_code }}</h2>
                    </div>

                    <div class="p-8 bg-white space-y-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-1">Guest Name</p>
                                <p class="text-ocean-900 font-bold text-sm">{{ $transaction->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-1">Destination / Package</p>
                                <p class="text-ocean-900 font-bold text-sm leading-snug">
                                    {{ $transaction->destination?->name ?? $transaction->tourPackage?->name ?? 'Special Package' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest mb-1">Visit Date</p>
                                <p class="text-ocean-900 font-bold text-sm">
                                    {{ $transaction->booking_date->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>

                        <hr class="border-gray-50">

                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 flex flex-col gap-2">
                            <span class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Total Amount Payable</span>
                            <span class="text-3xl font-black text-sunset-500 font-montserrat tracking-tight">{{ $transaction->total_price_formatted }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Selection --}}
            <div class="lg:col-span-3 space-y-8">
                <div class="cinematic-card p-8 shadow-2xl border-0 rounded-3xl bg-white">
                    <h3 class="text-2xl font-black text-ocean-900 font-montserrat tracking-tight mb-2">Secure Payment</h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Choose your preferred transaction method</p>

                    <form action="{{ route('transactions.pay', $transaction->id) }}" method="POST" id="paymentForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="booking_code" value="{{ $transaction->booking_code }}">
                        <input type="hidden" name="payment_method" id="payment_method_input">

                        <div class="grid grid-cols-1 gap-4">
                            <!-- Bank Transfer -->
                            <div class="payment-option relative group">
                                <input type="radio" name="payment_choice" id="method_bank" value="bank_transfer" class="peer hidden">
                                <label for="method_bank" class="flex items-start p-6 border-2 border-gray-100 rounded-2xl cursor-pointer transition-all hover:border-sunset-500/30 peer-checked:border-sunset-500 peer-checked:bg-orange-50/30 peer-checked:shadow-lg">
                                    <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-sunset-500 shadow-sm mr-4">
                                        <i class="fas fa-university text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-black text-ocean-900 uppercase tracking-wide">Bank Transfer</span>
                                            <span class="text-[10px] font-black text-sunset-500 uppercase tracking-widest px-2 py-0.5 bg-orange-100 rounded">Instant</span>
                                        </div>
                                        <p class="text-xs text-gray-400 font-medium mb-4">Manual or Virtual Account Transfer</p>
                                        <div class="flex gap-3 grayscale group-hover:grayscale-0 transition-all opacity-40 group-hover:opacity-100">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" class="h-4 object-contain" alt="BCA">
                                            <img src="https://upload.wikimedia.org/wikipedia/id/f/fa/Bank_Mandiri_logo.svg" class="h-4 object-contain" alt="Mandiri">
                                            <img src="https://upload.wikimedia.org/wikipedia/id/5/55/BNI_logo.svg" class="h-4 object-contain" alt="BNI">
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 border-2 border-gray-200 rounded-full flex items-center justify-center peer-checked:border-sunset-500 peer-checked:bg-sunset-500 ml-4 mt-1 transition-all">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                </label>
                            </div>

                            <!-- QRIS -->
                            <div class="payment-option relative group">
                                <input type="radio" name="payment_choice" id="method_qris" value="qris" class="peer hidden">
                                <label for="method_qris" class="flex items-start p-6 border-2 border-gray-100 rounded-2xl cursor-pointer transition-all hover:border-sunset-500/30 peer-checked:border-sunset-500 peer-checked:bg-orange-50/30 peer-checked:shadow-lg">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 shadow-sm mr-4">
                                        <i class="fas fa-qrcode text-lg"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-black text-ocean-900 uppercase tracking-wide">QRIS / E-Wallet</span>
                                        </div>
                                        <p class="text-xs text-gray-400 font-medium mb-4">Gopay, OVO, ShopeePay, Dana</p>
                                        <div class="bg-white p-2 border border-gray-50 rounded-lg inline-flex items-center gap-4">
                                            <img src="https://images.seeklogo.com/logo-png/39/2/quick-response-code-indonesia-standard-qris-logo-png_seeklogo-391791.png" class="h-5 grayscale group-hover:grayscale-0 opacity-50 group-hover:opacity-100 transition-all" alt="QRIS">
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 border-2 border-gray-200 rounded-full flex items-center justify-center peer-checked:border-sunset-500 peer-checked:bg-sunset-500 ml-4 mt-1 transition-all">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-8 space-y-4">
                            <button type="submit" id="payButton" disabled class="btn-primary w-full py-5 text-lg rounded-2xl font-black shadow-xl shadow-sunset-500/20 flex items-center justify-center gap-3 transition-all active:scale-95 disabled:opacity-30 disabled:grayscale disabled:cursor-not-allowed">
                                <i class="fas fa-lock text-sm"></i>
                                Proceed To Secure Checkout
                            </button>
                            <p class="text-[10px] text-gray-400 text-center font-bold tracking-widest uppercase">
                                <i class="fas fa-clock mr-1"></i> Invoice expires in 24 hours
                            </p>
                        </div>
                    </form>
                </div>
                
                {{-- Trusted Badges --}}
                <div class="flex justify-center gap-8 opacity-40">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-shield-alt text-2xl text-ocean-900"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest">Safe & Secure</span>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-paper-plane text-2xl text-ocean-900"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest">Instant Receipt</span>
                    </div>
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-headset text-2xl text-ocean-900"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest">24/7 Concierge</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="payment_choice"]');
        const payBtn = document.getElementById('payButton');
        const methodInput = document.getElementById('payment_method_input');

        radios.forEach(r => {
            r.addEventListener('change', function() {
                if(this.checked) {
                    methodInput.value = this.value;
                    payBtn.disabled = false;
                }
            });
        });
    });
</script>
@endsection