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
</style>
@endpush

@section('content')

{{-- ── HERO BANNER ── --}}
<section class="relative pt-32 pb-20 text-paper overflow-hidden bg-ink border-b border-ink/20 reveal">
    <div class="absolute inset-0 bg-gradient-to-t from-ink/40 via-transparent to-transparent pointer-events-none z-0"></div>
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-clay rounded-full mix-blend-multiply filter blur-[100px] opacity-20 z-0"></div>

    <div class="max-w-3xl mx-auto px-4 text-center relative z-10">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-paper/10 backdrop-blur-md rounded-full mb-8 border border-paper/20 check-anim shadow-2xl">
            <i class="fas fa-check text-4xl text-clay"></i>
        </div>
        <h1 class="text-5xl md:text-6xl font-bold mb-6 font-serif tracking-tight drop-shadow-2xl">
            Payment <span class="text-clay">Successful!</span>
        </h1>
        <p class="text-paper/80 text-lg md:text-xl max-w-2xl mx-auto">Terima kasih telah memilih Pesona NTT. {{ $order->itemCount() }} item dalam pesanan Anda sudah dikonfirmasi.</p>
    </div>
</section>

{{-- ── CONTENT ── --}}
<section class="py-20 bg-paper min-h-[50vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <div class="bg-surface border border-line rounded-3xl overflow-hidden shadow-sm reveal">
            <div class="bg-ink p-8 border-b border-ink/20 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-clay rounded-full mix-blend-multiply filter blur-3xl opacity-20 pointer-events-none"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-paper/60 text-xs font-bold uppercase tracking-widest mb-1">Kode Order</p>
                        <p class="font-mono font-bold text-paper text-2xl tracking-widest">{{ $order->order_code }}</p>
                    </div>
                    <div class="w-16 h-16 bg-paper/10 backdrop-blur-md border border-paper/20 rounded-2xl flex items-center justify-center shadow-xl">
                        <i class="fas fa-shopping-bag text-clay text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-surface">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                    <div class="bg-paper rounded-2xl p-4 border border-line text-center">
                        <p class="text-[10px] uppercase tracking-widest font-bold text-muted mb-1">Tanggal</p>
                        <p class="font-bold text-ink text-sm">{{ now()->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="bg-paper rounded-2xl p-4 border border-line text-center">
                        <p class="text-[10px] uppercase tracking-widest font-bold text-muted mb-1">Pemesan</p>
                        <p class="font-bold text-ink text-sm truncate px-1">{{ $order->customer_name }}</p>
                    </div>
                    <div class="bg-paper rounded-2xl p-4 border border-line text-center">
                        <p class="text-[10px] uppercase tracking-widest font-bold text-muted mb-1">Total</p>
                        <p class="font-bold text-clay text-sm">{{ $order->total_price_formatted }}</p>
                    </div>
                    <div class="bg-paper rounded-2xl p-4 border border-line text-center">
                        <p class="text-[10px] uppercase tracking-widest font-bold text-muted mb-1">Status</p>
                        <p class="font-bold text-emerald-600 text-sm uppercase tracking-tighter">{{ $order->status }}</p>
                    </div>
                </div>

                @if($order->has_insurance)
                <div class="flex items-center gap-2 bg-laut/5 border border-laut/20 rounded-xl px-4 py-3 mb-8 text-sm font-bold text-ink">
                    <i class="fas fa-shield-alt text-laut"></i>
                    Asuransi perjalanan disertakan
                    <span class="ml-auto text-muted font-medium">Rp{{ number_format($order->insurance_amount, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="mb-10">
                    <h2 class="font-bold text-ink mb-6 flex items-center gap-3 font-serif uppercase tracking-wider text-sm">
                        <i class="fas fa-list-ul text-clay"></i>
                        Item Pesanan
                        <span class="bg-clay text-paper text-[10px] px-2 py-0.5 rounded-full font-bold ml-auto">{{ $order->itemCount() }} item</span>
                    </h2>

                    <div class="space-y-4">
                        @foreach($order->transactions as $transaction)
                        <div class="bg-paper border border-line rounded-2xl p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-ink text-lg font-serif tracking-tight mb-1">
                                        {{ $transaction->destination?->name ?? $transaction->tourPackage?->name }}
                                    </h3>
                                    <div class="flex items-center text-muted text-sm font-medium">
                                        <i class="fas fa-ticket-alt mr-2 text-clay"></i> {{ $transaction->number_of_tickets }} tiket
                                    </div>
                                </div>
                                <div class="bg-surface border border-line text-ink px-5 py-3 rounded-xl font-mono font-bold text-sm tracking-widest">
                                    {{ $transaction->booking_code }}
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @foreach($order->bookingHotels as $booking)
                        <div class="bg-paper border border-line rounded-2xl p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-ink text-lg font-serif tracking-tight mb-1">
                                        {{ $booking->hotel?->name }}
                                    </h3>
                                    <div class="flex items-center text-muted text-sm font-medium">
                                        <i class="fas fa-bed mr-2 text-clay"></i> {{ ucfirst($booking->room_type) }} Room · {{ $booking->night_count }} malam
                                    </div>
                                </div>
                                <div class="bg-surface border border-line text-ink px-5 py-3 rounded-xl font-mono font-bold text-sm tracking-widest">
                                    {{ $booking->booking_number }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t border-line font-bold">
                    <a href="{{ url('/') }}" class="flex-1 inline-flex items-center justify-center gap-2 bg-clay text-paper font-bold py-4 rounded-xl hover:bg-clay/90 transition-all">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <p class="text-center mt-10 text-muted text-sm font-medium">
            Butuh bantuan? Hubungi kami di <a href="mailto:support@pesonantt.com" class="text-clay hover:text-clay/80 transition-colors underline font-bold">support@pesonantt.com</a>
        </p>

    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    confetti({ particleCount: 150, spread: 80, origin: { y: 0.5 }, colors: ['#1C4750','#0F6E63','#F7F6F2','#EBEDE5'] });
});
</script>
@endpush

@endsection
