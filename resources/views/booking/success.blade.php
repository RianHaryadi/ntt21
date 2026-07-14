@extends('layouts.app')

@section('title', 'Booking Confirmed! - ' . ($booking->hotel->name ?? 'Hotel'))

@push('styles')
<style>
    @keyframes checkPop {
        0%{ transform: scale(0); opacity: 0; }
        60%{ transform: scale(1.2); opacity: 1; }
        100%{ transform: scale(1); opacity: 1; }
    }
    .check-anim { animation: checkPop 0.6s cubic-bezier(0.34,1.56,0.64,1) 0.3s both; }
    
    .timeline-line { background: linear-gradient(to bottom, #0F6E63, rgba(15,23,42,0.05)); }
    .step-dot-active {
        background: radial-gradient(circle at center, #0F6E63, #1C4750);
        box-shadow: 0 0 0 4px rgba(15, 110, 99, 0.15);
    }
</style>
@endpush

@section('content')

{{-- ── HERO BANNER ── --}}
<section class="relative pt-32 pb-20 text-white overflow-hidden bg-[#1e293b] border-b border-slate-700/60 reveal">
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent pointer-events-none z-0"></div>
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-laut rounded-full mix-blend-multiply filter blur-[100px] opacity-20 z-0"></div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-white/10 backdrop-blur-md rounded-full mb-8 border border-white/20 check-anim shadow-2xl">
            <i class="fas fa-check text-4xl text-laut"></i>
        </div>
        <h1 class="text-5xl md:text-6xl font-black mb-6 font-serif tracking-tight tracking-tight drop-shadow-2xl">
            Stay <span class="text-laut">Confirmed!</span>
        </h1>
        <p class="text-white/80 text-lg md:text-xl max-w-2xl mx-auto font-inter">Your reservation at <span class="text-white font-bold">{{ $booking->hotel->name }}</span> has been successfully processed.</p>
        
        <div class="mt-10 flex flex-col items-center">
            <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-xl">
               <span class="text-[10px] uppercase tracking-widest font-bold text-laut">Ref Number</span>
               <span class="font-mono font-black text-white text-lg tracking-widest">{{ $booking->booking_number }}</span>
               <button onclick="navigator.clipboard.writeText('{{ $booking->booking_number }}')" class="text-white hover:text-laut transition-colors ml-2" title="Copy Number">
                 <i class="fas fa-copy text-sm"></i>
               </button>
            </div>
        </div>
    </div>
</section>

{{-- ── CONTENT ── --}}
<section class="py-20 bg-transparent min-h-[50vh]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-3 gap-10">

            {{-- ── LEFT: MAIN CARD ── --}}
            <div class="md:col-span-2 space-y-10">

                {{-- Hotel Ticket Card --}}
                <div class="cinematic-card p-0 shadow-2xl overflow-hidden border border-slate-200/60 bg-white rounded-3xl reveal">
                    <div class="flex flex-col sm:flex-row h-full">
                        <div class="sm:w-2/5 relative h-56 sm:h-auto bg-slate-950">
                            <img src="{{ $booking->hotel->image ? asset('storage/'.$booking->hotel->image) : asset('images/hotel-fallback.jpg') }}"
                                 alt="{{ $booking->hotel->name }}"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/60 to-transparent sm:from-transparent"></div>
                        </div>
                        <div class="flex-1 p-8 bg-white">
                            <div class="flex items-center gap-2 text-yellow-500 mb-2">
                                @for($i = 0; $i < ($booking->hotel->stars ?? 5); $i++)
                                    <i class="fas fa-star text-[10px]"></i>
                                @endfor
                            </div>
                            <h2 class="text-3xl font-black text-slate-800 font-serif tracking-tight tracking-tight mb-4">{{ $booking->hotel->name }}</h2>
                            
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                                    <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-1">Room Type</p>
                                    <p class="text-slate-800 font-bold text-sm">{{ ucfirst($booking->room_type) }} Room</p>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/60">
                                    <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-1">Payment</p>
                                    <p class="text-slate-800 font-bold text-sm">{{ strtoupper($booking->payment_method) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-6 border-t border-dashed border-slate-100">
                                <div class="flex flex-col">
                                    <p class="text-[10px] uppercase font-black text-laut tracking-widest">Check-In</p>
                                    <p class="text-slate-800 font-black text-sm">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</p>
                                </div>
                                <div class="text-slate-400">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="flex flex-col text-right">
                                    <p class="text-[10px] uppercase font-black text-laut tracking-widest">Check-Out</p>
                                    <p class="text-slate-800 font-black text-sm">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Price & Breakdown --}}
                <div class="bg-white rounded-3xl p-8 shadow-2xl border border-slate-200/60 reveal">
                   <h3 class="font-black text-slate-800 mb-6 flex items-center gap-3 font-serif tracking-tight uppercase tracking-wider text-sm">
                        <i class="fas fa-receipt text-laut"></i> Detailed Price Breakdown
                    </h3>
                    <div class="space-y-4 text-sm font-bold">
                        <div class="flex justify-between text-slate-500">
                            <span>Base Rate ({{ $booking->night_count }} nights)</span>
                            <span class="text-slate-800">Rp{{ number_format($booking->room_price * $booking->night_count, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Government Taxes (10%)</span>
                            <span class="text-slate-800">Rp{{ number_format($booking->tax, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Resort & Service Fee (5%)</span>
                            <span class="text-slate-800">Rp{{ number_format($booking->service_charge, 0, ',', '.') }}</span>
                        </div>
                        @if($booking->promo_code)
                        <div class="flex justify-between text-laut">
                            <span>Voucher applied</span>
                            <span>-Rp{{ number_format($booking->discount_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        @if($booking->has_insurance)
                        <div class="flex justify-between text-slate-500">
                            <span class="flex items-center gap-1.5"><i class="fas fa-shield-alt text-laut text-xs"></i> Travel Insurance</span>
                            <span class="text-slate-800">Rp{{ number_format($booking->insurance_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                            <span class="text-xl font-black text-slate-800">Total Charged</span>
                            <span class="text-3xl font-black text-laut">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── RIGHT: SIDEBAR ── --}}
            <div class="space-y-8">

                {{-- Timeline --}}
                <div class="bg-white rounded-3xl p-8 shadow-2xl border border-slate-200/60 reveal">
                    <h3 class="font-black text-slate-800 mb-8 font-serif tracking-tight uppercase tracking-wider text-xs">Stay Progress</h3>
                    <div class="relative pl-8">
                        <div class="timeline-line absolute left-3 top-2 w-0.5 h-[90%] rounded-full"></div>
                        <div class="space-y-10">
                            <div class="relative flex gap-4 items-start">
                                <div class="absolute -left-8 w-6 h-6 rounded-full step-dot-active flex items-center justify-center border-4 border-white">
                                    <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800 uppercase tracking-widest">Reserved</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase">{{ now()->format('D, d M - g:i A') }}</p>
                                </div>
                            </div>
                            <div class="relative flex gap-4 items-start">
                                <div class="absolute -left-8 w-6 h-6 rounded-full bg-slate-50 border-2 border-slate-200 flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 bg-slate-300 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest">Check-In</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('D, d M Y') }} · 14:00</p>
                                </div>
                            </div>
                            <div class="relative flex gap-4 items-start">
                                <div class="absolute -left-8 w-6 h-6 rounded-full bg-slate-50 border-2 border-slate-200 flex items-center justify-center"></div>
                                <div>
                                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest">Check-Out</p>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('D, d M Y') }} · 12:00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Helper --}}
                <div class="bg-slate-50 rounded-3xl p-8 text-slate-700 border border-slate-200/60 relative overflow-hidden shadow-sm">
                   <div class="absolute -right-8 -top-8 w-32 h-32 bg-laut rounded-full filter blur-3xl opacity-10"></div>
                   <h3 class="font-black text-slate-800 mb-4 text-xs uppercase tracking-widest relative z-10">What's Next?</h3>
                   <div class="space-y-4 text-xs font-bold leading-relaxed relative z-10 text-slate-600">
                       <p class="flex items-start gap-2"><i class="fas fa-paper-plane text-laut mt-0.5"></i> Digital receipt has been sent to your email.</p>
                       <p class="flex items-start gap-2"><i class="fas fa-id-card text-laut mt-0.5"></i> Present your ID and booking reference at reception.</p>
                       <p class="flex items-start gap-2"><i class="fas fa-shield-alt text-laut mt-0.5"></i> Cancellation is free until 48h before arrival.</p>
                   </div>
                </div>

                {{-- Actions --}}
                <div class="space-y-3 no-print reveal">
                    <a href="{{ route('home') }}" class="w-full btn-primary font-black py-4 rounded-2xl flex items-center justify-center gap-2 text-sm shadow-sm shadow-laut/10">
                        <i class="fas fa-home text-xs"></i> Back to Explore
                    </a>
                    <a href="{{ route('booking.voucher', $booking->id) }}" class="w-full bg-slate-900 border border-slate-950 text-white hover:bg-slate-800 font-black py-4 rounded-2xl flex items-center justify-center gap-2 text-sm transition-all active:scale-95">
                        <i class="fas fa-file-pdf text-xs text-laut"></i> Download Voucher PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    confetti({ particleCount: 150, spread: 80, origin: { y: 0.5 }, colors: ['#1e293b','#0F6E63','#ffffff'] });
});
</script>
@endpush

@endsection