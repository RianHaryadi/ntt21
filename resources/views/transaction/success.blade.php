@extends('layouts.app')

@section('title', 'Pemesanan Berhasil')

@push('styles')
<style>
    @keyframes checkPop {
        0%{ transform: scale(0); opacity: 0; }
        60%{ transform: scale(1.2); }
        100%{ transform: scale(1); opacity: 1; }
    }
    .check-anim { animation: checkPop 0.6s cubic-bezier(0.34,1.56,0.64,1) 0.2s both; }
    
    .ticket-divider {
        background-image: radial-gradient(circle at 10px 10px, transparent 10px, white 10px);
        background-position: -10px 0;
        background-size: 20px 20px;
        background-repeat: repeat-x;
        height: 20px;
        width: 100%;
    }
</style>
@endpush

@section('content')

{{-- ── HERO BANNER ── --}}
<section class="relative pt-32 pb-20 text-white overflow-hidden bg-ocean-900 border-b border-white/10 reveal">
    <div class="absolute inset-0 bg-gradient-to-t from-ocean-900 via-ocean-900/40 to-transparent pointer-events-none z-0"></div>
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-sunset-500 rounded-full mix-blend-multiply filter blur-[100px] opacity-40 z-0"></div>
    
    <div class="max-w-3xl mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 backdrop-blur-md rounded-full mb-8 border border-white/20 check-anim shadow-2xl">
            <i class="fas fa-check text-4xl text-sunset-500"></i>
        </div>
        <h1 class="text-5xl md:text-6xl font-black mb-6 font-montserrat tracking-tight drop-shadow-2xl">
            Payment <span class="text-sunset-500">Successful!</span>
        </h1>
        <p class="text-white/70 text-lg md:text-xl max-w-2xl mx-auto font-inter">Thank you for choosing Wonderful NTT. Exploring the beauty of East Nusa Tenggara starts now.</p>
    </div>
</section>

{{-- ── CONTENT ── --}}
<section class="py-20 bg-light min-h-[50vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Booking Card --}}
        <div class="cinematic-card p-0 shadow-2xl overflow-hidden border-0 rounded-3xl reveal">
            {{-- Header --}}
            <div class="bg-ocean-900 p-8 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-sunset-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-white/50 text-xs font-bold uppercase tracking-widest mb-1">Booking Reference</p>
                        <p class="font-mono font-black text-white text-2xl tracking-widest">{{ $transaction->booking_code }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fas fa-ticket-alt text-sunset-500 text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-white">
                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Date</p>
                        <p class="font-bold text-ocean-900 text-sm">{{ now()->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Guest</p>
                        <p class="font-bold text-ocean-900 text-sm truncate px-1">{{ $transaction->customer_name }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Total</p>
                        <p class="font-bold text-sunset-500 text-sm">{{ $transaction->total_price_formatted }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center">
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Status</p>
                        <p class="font-bold text-green-600 text-sm uppercase tracking-tighter">Paid</p>
                    </div>
                </div>

                {{-- Tickets Section --}}
                <div class="mb-10">
                    <h2 class="font-black text-ocean-900 mb-6 flex items-center gap-3 font-montserrat uppercase tracking-wider text-sm">
                        <i class="fas fa-qrcode text-sunset-500"></i>
                        Official Digital Tickets
                        <span class="bg-ocean-900 text-white text-[10px] px-2 py-0.5 rounded-full font-black ml-auto">{{ $transaction->tickets->count() }} Issued</span>
                    </h2>

                    <div class="space-y-4">
                        @foreach($transaction->tickets as $ticket)
                        <div class="bg-white border-2 border-gray-50 rounded-2xl p-6 transition-all hover:border-sunset-500/30 group">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h3 class="font-black text-ocean-900 text-lg font-montserrat tracking-tight mb-1">
                                        {{ $transaction->destination?->name ?? $transaction->tourPackage?->name }}
                                    </h3>
                                    <div class="flex items-center text-gray-400 text-sm font-medium">
                                        <i class="fas fa-user-circle mr-2 text-sunset-500"></i> {{ $ticket->visitor_name }}
                                    </div>
                                </div>
                                <div class="bg-ocean-900 text-white px-5 py-3 rounded-xl font-mono font-black text-sm tracking-widest flex items-center gap-3 group-hover:bg-sunset-500 transition-colors">
                                    <i class="fas fa-barcode"></i> {{ $ticket->ticket_code }}
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-dashed border-gray-100 flex items-center gap-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                <span class="flex items-center gap-1.5"><i class="far fa-calendar-check text-sunset-500"></i> {{ $transaction->booking_date->translatedFormat('d F Y') }}</span>
                                <span class="flex items-center gap-1.5 ml-auto text-ocean-900"><i class="fas fa-check-double text-green-500"></i> Valid Document</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t border-gray-100">
                    <a href="{{ url('/') }}" class="flex-1 btn-primary text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 shadow-xl shadow-sunset-500/20">
                        <i class="fas fa-home"></i> Back to Explore
                    </a>
                    <button onclick="window.print()" class="flex-1 btn-outline text-ocean-900 border-gray-200 py-4 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition">
                        <i class="fas fa-file-download text-gray-400"></i> Save as PDF
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Bantuan --}}
        <p class="text-center mt-10 text-gray-400 text-sm font-medium">
            Need help? Contact our support at <a href="#" class="text-ocean-900 hover:text-sunset-500 transition-colors underline font-bold">support@wonderfulntt.com</a>
        </p>

    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    confetti({ particleCount: 150, spread: 80, origin: { y: 0.5 }, colors: ['#0f172a','#ff6b35','#ffffff','#f8fafc'] });
});
</script>
@endpush

@endsection