@extends('layouts.app')

@section('title', 'Keranjang Saya')

@section('content')

<section class="bg-ink pt-32 pb-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center">
        <span class="inline-flex items-center gap-1.5 bg-clay/10 text-clay text-[11px] font-bold uppercase tracking-[0.1em] px-2.5 py-1 rounded-full mb-6">
            <i class="fas fa-shopping-bag"></i> Keranjang
        </span>
        <h1 class="font-serif font-bold text-4xl md:text-5xl text-paper tracking-tight">Keranjang Saya</h1>
        <p class="text-paper/70 text-sm mt-3">Gabungkan destinasi, hotel, dan paket tour dalam satu pembayaran.</p>
    </div>
</section>

<main class="bg-paper py-16 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm font-medium rounded-2xl p-4 mb-8">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif
        @error('cart')
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-medium rounded-2xl p-4 mb-8">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ $message }}
        </div>
        @enderror

        @if($items->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center bg-surface rounded-3xl border border-line">
            <div class="w-16 h-16 rounded-full bg-paper flex items-center justify-center mb-6 shadow-sm border border-line">
                <i class="fas fa-shopping-bag text-2xl text-muted"></i>
            </div>
            <h3 class="font-serif font-bold text-xl text-ink mb-2">Keranjang Anda kosong</h3>
            <p class="text-muted text-sm mb-6">Jelajahi destinasi, hotel, atau paket tour dan tambahkan ke keranjang.</p>
            <a href="{{ route('destinations.index') }}" class="inline-flex items-center gap-2 bg-clay text-paper font-semibold text-sm px-6 py-3 rounded-full hover:bg-clay/90 transition-all">
                Jelajahi Destinasi
            </a>
        </div>
        @else
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm font-semibold text-ink">{{ $items->count() }} item di keranjang</p>
            <form method="POST" action="{{ route('cart.clear') }}"
                  onsubmit="return confirm('Hapus semua item dari keranjang?');">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 text-xs font-bold text-red-500 hover:text-red-700 border border-red-200 hover:border-red-300 bg-red-50 px-4 py-2 rounded-full transition">
                    <i class="fas fa-trash-alt"></i> Kosongkan Keranjang
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

            {{-- Item List --}}
            <div class="lg:col-span-3 space-y-4">
                @foreach($items as $item)
                @php
                    $type = $item->itemType();
                    $icon = ['destination' => 'fa-map-marker-alt', 'hotel' => 'fa-hotel', 'tour' => 'fa-suitcase-rolling'][$type] ?? 'fa-tag';
                    $typeLabel = ['destination' => 'Destinasi', 'hotel' => 'Hotel', 'tour' => 'Paket Tour'][$type] ?? 'Item';
                @endphp
                <div class="bg-surface border border-line rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-paper border border-line flex items-center justify-center text-clay flex-shrink-0">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-widest font-bold text-muted mb-0.5">{{ $typeLabel }}</p>
                        <h3 class="font-serif font-bold text-ink truncate">{{ $item->details['label'] ?? $item->itemable?->name }}</h3>
                        <p class="text-xs text-muted mt-1">
                            @if($type === 'hotel')
                                {{ ucfirst($item->details['room_type']) }} Room · {{ \Carbon\Carbon::parse($item->details['check_in_date'])->format('d M') }} &rarr; {{ \Carbon\Carbon::parse($item->details['check_out_date'])->format('d M Y') }} ({{ $item->quantity() }} malam · {{ $item->rooms() }} kamar)
                            @elseif(($item->details['price_type'] ?? null) === 'flat')
                                {{ \Carbon\Carbon::parse($item->details['booking_date'])->format('d M Y') }} · 1 paket ({{ $item->details['pax'] ?? '-' }} orang)
                            @else
                                {{ \Carbon\Carbon::parse($item->details['booking_date'])->format('d M Y') }} · {{ $item->quantity() }} tiket
                            @endif
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-bold text-clay text-sm">{{ format_price($item->subtotal()) }}</p>
                        @if($type === 'hotel')
                        <p class="text-[10px] text-muted">+pajak &amp; layanan</p>
                        @endif
                        <form method="POST" action="{{ route('cart.remove', $item->id) }}" class="mt-2">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition font-semibold">
                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Checkout --}}
            <div class="lg:col-span-2">
                <div class="bg-surface border border-line rounded-2xl p-6 sticky top-24">
                    <h3 class="font-bold text-ink mb-5">Selesaikan Pesanan</h3>

                    <form method="POST" action="{{ route('cart.checkout') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Nama Lengkap</label>
                            <input type="text" name="customer_name" required readonly value="{{ old('customer_name', auth()->user()->name) }}"
                                   class="w-full border border-line rounded-xl px-4 py-3 text-sm bg-line/30 text-muted cursor-not-allowed focus:outline-none">
                            <p class="text-[11px] text-muted mt-1">Sesuai akun Anda. Ubah di <a href="{{ route('profile.edit') }}" class="text-laut font-semibold hover:underline">halaman profil</a>.</p>
                            @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Email</label>
                            <input type="email" name="customer_email" required readonly value="{{ old('customer_email', auth()->user()->email) }}"
                                   class="w-full border border-line rounded-xl px-4 py-3 text-sm bg-line/30 text-muted cursor-not-allowed focus:outline-none">
                            <p class="text-[11px] text-muted mt-1">Sesuai akun Anda. Ubah di <a href="{{ route('profile.edit') }}" class="text-laut font-semibold hover:underline">halaman profil</a>.</p>
                            @error('customer_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Nomor Telepon</label>
                            <input type="text" name="customer_phone" required value="{{ old('customer_phone', auth()->user()->phone) }}"
                                   class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut">
                            @if(!auth()->user()->phone)
                            <p class="text-[11px] text-muted mt-1">Belum ada nomor tersimpan — akan disimpan ke akun setelah checkout.</p>
                            @endif
                            @error('customer_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-ink mb-1.5">Kode Promo <span class="font-normal text-muted">(opsional)</span></label>
                            <div class="flex gap-2">
                                <input type="text" id="promo_code_input" name="promo_code" value="{{ old('promo_code') }}" placeholder="Masukkan kode"
                                       class="flex-1 border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut uppercase tracking-widest"
                                       autocomplete="off" oninput="this.value = this.value.toUpperCase()">
                                <button type="button" id="apply_promo_btn"
                                        class="px-5 py-3 bg-clay text-paper text-xs font-bold rounded-xl hover:bg-clay/90 active:scale-95 transition-all whitespace-nowrap flex-shrink-0">
                                    Terapkan
                                </button>
                            </div>
                            <div id="promo_feedback" class="mt-2 hidden text-xs font-semibold rounded-lg px-3 py-2"></div>
                        </div>

                        <label class="flex items-start gap-3 cursor-pointer bg-paper rounded-xl p-4 border border-line">
                            <input type="checkbox" name="has_insurance" value="1" class="mt-1 w-5 h-5 rounded border-line text-laut focus:ring-laut">
                            <span class="flex-1">
                                <span class="flex items-center gap-2 font-bold text-ink text-sm">
                                    <i class="fas fa-shield-alt text-laut"></i> Tambahkan Asuransi Perjalanan
                                </span>
                                <span class="block text-muted text-xs mt-1">
                                    Berlaku untuk semua item — Rp{{ number_format(config('services.insurance.price_per_ticket'), 0, ',', '.') }}/tiket destinasi/tour, Rp{{ number_format(config('services.insurance.price_per_booking'), 0, ',', '.') }}/booking hotel
                                </span>
                            </span>
                        </label>

                        <div class="border-t border-line pt-4 mt-4 space-y-2">
                            <div class="flex justify-between text-sm text-muted">
                                <span>Subtotal</span>
                                <span id="summary_subtotal">{{ format_price($subtotal) }}</span>
                            </div>
                            <div id="summary_discount_row" class="hidden flex justify-between text-sm text-green-600 font-semibold">
                                <span id="summary_discount_label">Diskon Promo</span>
                                <span id="summary_discount_value">-Rp 0</span>
                            </div>
                            <div id="summary_total_row" class="hidden flex justify-between text-sm font-bold text-ink border-t border-line pt-2 mt-1">
                                <span>Total Setelah Diskon</span>
                                <span id="summary_total_value" class="text-clay">{{ format_price($subtotal) }}</span>
                            </div>
                            <p class="text-xs text-muted">Pajak, layanan, dan asuransi dihitung final saat checkout.</p>
                        </div>

                        <button type="submit" class="btn-primary w-full py-4 rounded-xl flex items-center justify-center gap-2 text-sm font-bold">
                            <i class="fas fa-lock"></i> Checkout & Bayar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</main>

<script>
(function () {
    const subtotalRaw = {{ $subtotal }};

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    const btn        = document.getElementById('apply_promo_btn');
    const input      = document.getElementById('promo_code_input');
    const feedback   = document.getElementById('promo_feedback');
    const discountRow = document.getElementById('summary_discount_row');
    const discountLabel = document.getElementById('summary_discount_label');
    const discountValue = document.getElementById('summary_discount_value');
    const totalRow   = document.getElementById('summary_total_row');
    const totalValue = document.getElementById('summary_total_value');

    function showFeedback(msg, isSuccess) {
        feedback.textContent = msg;
        feedback.className = 'mt-2 text-xs font-semibold rounded-lg px-3 py-2 ' +
            (isSuccess
                ? 'bg-green-50 text-green-700 border border-green-200'
                : 'bg-red-50 text-red-600 border border-red-200');
        feedback.classList.remove('hidden');
    }

    function resetDiscount() {
        discountRow.classList.add('hidden');
        totalRow.classList.add('hidden');
    }

    if (btn && input) {
        btn.addEventListener('click', function () {
            const code = input.value.trim();
            if (!code) {
                showFeedback('Masukkan kode promo terlebih dahulu.', false);
                return;
            }

            btn.disabled = true;
            btn.textContent = '...';

            fetch('{{ route("cart.validatePromo") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ promo_code: code }),
            })
            .then(r => r.json())
            .then(data => {
                showFeedback(data.message, data.valid);

                if (data.valid) {
                    // Tampilkan baris diskon
                    discountLabel.textContent = data.label;
                    discountValue.textContent = '- ' + formatRupiah(data.discount);
                    discountRow.classList.remove('hidden');
                    discountRow.style.display = 'flex';

                    // Tampilkan total akhir
                    totalValue.textContent = formatRupiah(data.final_total);
                    totalRow.classList.remove('hidden');
                    totalRow.style.display = 'flex';
                } else {
                    resetDiscount();
                    // Kosongkan field jika kode tidak valid supaya tidak ikut saat submit
                    input.value = '';
                }
            })
            .catch(() => {
                showFeedback('Terjadi kesalahan. Coba lagi.', false);
                resetDiscount();
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Terapkan';
            });
        });

        // Reset preview ketika kode diubah manual
        input.addEventListener('input', function () {
            feedback.classList.add('hidden');
            resetDiscount();
        });
    }
})();
</script>

@endsection
