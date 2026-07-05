@extends('layouts.app')

@section('title', 'Pembayaran - ' . ($order->order_code ?? 'Payment'))

@section('content')
<div class="antialiased bg-transparent min-h-screen py-24 px-4 reveal">
    <div class="container mx-auto max-w-4xl">

        @if (session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-6 rounded-2xl mb-10 shadow-sm flex items-center justify-between reveal">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-500/10 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <p class="text-xs font-bold opacity-75">{{ session('success') }}</p>
                </div>
                <button class="text-emerald-600 hover:text-emerald-500" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

            {{-- Order Summary --}}
            <div class="lg:col-span-2">
                <div class="cinematic-card p-0 shadow-sm border border-slate-200/60 bg-white overflow-hidden rounded-3xl lg:sticky lg:top-24">
                    <div class="bg-slate-900 p-8 relative overflow-hidden text-white">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-laut rounded-full filter blur-3xl opacity-20"></div>
                        <p class="text-[10px] uppercase font-bold text-laut tracking-widest mb-1 relative z-10">Order Gabungan</p>
                        <h2 class="text-2xl font-black font-serif tracking-tight relative z-10">#{{ $order->order_code }}</h2>
                        <p class="text-white/50 text-xs font-bold mt-1 relative z-10">{{ $order->itemCount() }} item dalam pesanan ini</p>
                    </div>

                    <div class="p-8 bg-white space-y-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] uppercase font-black text-slate-500 tracking-widest mb-1">Nama Pemesan</p>
                                <p class="text-slate-800 font-bold text-sm">{{ $order->customer_name }}</p>
                            </div>

                            <div class="space-y-3">
                                @foreach($order->transactions as $transaction)
                                <div class="flex items-center justify-between gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-200/60">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $transaction->destination?->name ?? $transaction->tourPackage?->name ?? 'Item' }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $transaction->number_of_tickets }} tiket</p>
                                    </div>
                                    <span class="text-xs font-bold text-laut whitespace-nowrap">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                                </div>
                                @endforeach

                                @foreach($order->bookingHotels as $booking)
                                <div class="flex items-center justify-between gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-200/60">
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $booking->hotel?->name ?? 'Hotel' }}</p>
                                        <p class="text-[10px] text-slate-500">{{ ucfirst($booking->room_type) }} · {{ $booking->night_count }} malam</p>
                                    </div>
                                    <span class="text-xs font-bold text-laut whitespace-nowrap">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/60 flex flex-col gap-2">
                            <span class="text-[10px] uppercase font-black text-slate-500 tracking-widest">Total Tagihan</span>
                            <span class="text-3xl font-black text-laut font-serif tracking-tight">{{ $order->total_price_formatted }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Selection --}}
            <div class="lg:col-span-3 space-y-8">
                <div class="cinematic-card p-8 shadow-sm border border-slate-200/60 rounded-3xl bg-white">
                    <h3 class="text-2xl font-black text-slate-800 font-serif tracking-tight mb-2">Pembayaran Aman</h3>
                    <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-8">Powered by Midtrans — Transfer Bank, QRIS, E-Wallet, Kartu Kredit</p>

                    @if($order->status !== 'pending')
                        <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-sm flex items-center gap-3">
                            <i class="fas fa-check-circle text-lg"></i>
                            Pembayaran untuk pesanan ini sudah berstatus <span class="uppercase">{{ $order->status }}</span>.
                        </div>
                        <a href="{{ route('orders.success', $order->order_code) }}" class="btn-primary w-full py-5 text-lg rounded-2xl font-black mt-6 flex items-center justify-center gap-3">
                            Lihat Detail Pesanan
                        </a>
                    @elseif(!$midtransConfigured || !$snapToken)
                        <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 font-bold text-sm flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle text-lg"></i>
                            Payment gateway sedang tidak dapat diakses. Silakan coba beberapa saat lagi atau hubungi kami.
                        </div>
                    @else
                        <button type="button" id="payButton" class="btn-primary w-full py-5 text-lg rounded-2xl font-black shadow-sm shadow-laut/10 flex items-center justify-center gap-3 transition-all active:scale-95">
                            <i class="fas fa-lock text-sm"></i>
                            Bayar Sekarang
                        </button>
                        <p class="text-[10px] text-slate-500 text-center font-bold tracking-widest uppercase mt-4">
                            <i class="fas fa-clock mr-1"></i> Selesaikan pembayaran melalui popup Midtrans
                        </p>
                    @endif
                </div>

                <div class="flex justify-center gap-8 opacity-70">
                    <div class="flex flex-col items-center gap-2 text-slate-700">
                        <i class="fas fa-shield-alt text-2xl text-slate-700"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Safe & Secure</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 text-slate-700">
                        <i class="fas fa-paper-plane text-2xl text-slate-700"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Instant Receipt</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 text-slate-700">
                        <i class="fas fa-headset text-2xl text-slate-700"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">24/7 Concierge</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if($order->status === 'pending' && $midtransConfigured && $snapToken)
<script src="{{ $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ $clientKey }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const payBtn = document.getElementById('payButton');
        if (!payBtn) return;

        payBtn.addEventListener('click', function () {
            payBtn.disabled = true;
            snap.pay('{{ $snapToken }}', {
                onSuccess: function () {
                    window.location.href = "{{ route('orders.success', $order->order_code) }}";
                },
                onPending: function () {
                    window.location.href = "{{ route('orders.success', $order->order_code) }}";
                },
                onError: function () {
                    payBtn.disabled = false;
                    alert('Pembayaran gagal diproses. Silakan coba lagi.');
                },
                onClose: function () {
                    payBtn.disabled = false;
                }
            });
        });
    });
</script>
@endif
@endsection
