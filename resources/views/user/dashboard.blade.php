@extends('layouts.app')

@section('title', 'Dashboard Saya')

@section('content')
<div class="pt-28 pb-20 bg-paper min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-10 flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-clay to-ink flex items-center justify-center text-paper text-2xl font-bold shadow-sm">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-3xl font-bold text-ink font-serif tracking-tight">Halo, {{ $user->name }}!</h1>
                <p class="text-muted text-sm mt-1">{{ $user->email }}</p>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-8 bg-clay/10 border border-clay/25 text-clay px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-8 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-paper border border-line flex items-center justify-center text-blue-600 text-xl">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-ink">{{ $chatSessions->count() }}</p>
                    <p class="text-xs text-muted uppercase tracking-wider font-semibold">Sesi AI Chat</p>
                </div>
            </div>
            <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-paper border border-line flex items-center justify-center text-clay text-xl">
                    <i class="fas fa-hotel"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-ink">{{ $hotelBookings->count() }}</p>
                    <p class="text-xs text-muted uppercase tracking-wider font-semibold">Booking Hotel</p>
                </div>
            </div>
            <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-paper border border-line flex items-center justify-center text-emerald-600 text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-ink">{{ $hotelBookings->where('status', 'checked-in')->count() }}</p>
                    <p class="text-xs text-muted uppercase tracking-wider font-semibold">Booking Dikonfirmasi</p>
                </div>
            </div>
        </div>

        <!-- Loyalty Points & Referral -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <!-- Loyalty Points -->
            <div class="bg-gradient-to-br from-ink to-ink rounded-2xl p-6 shadow-sm text-paper relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-clay/20 rounded-full blur-2xl"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        @php
                            $tierColors = [
                                'Bronze' => 'bg-amber-700/20 border-amber-600/40 text-amber-500',
                                'Silver' => 'bg-slate-400/20 border-slate-300/40 text-slate-300',
                                'Gold' => 'bg-yellow-500/20 border-yellow-400/40 text-yellow-400',
                                'Platinum' => 'bg-clay/20 border-clay/40 text-clay',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 text-[10px] uppercase tracking-widest font-black px-2.5 py-1 rounded-full border mb-2 {{ $tierColors[$loyaltyTier] ?? $tierColors['Bronze'] }}">
                            <i class="fas fa-medal"></i> Tier {{ $loyaltyTier }}
                        </span>
                        <p class="text-paper/60 text-xs uppercase tracking-widest font-bold mb-2">Poin Loyalti Anda</p>
                        <p class="text-4xl font-black font-serif">{{ number_format($totalPoints, 0, ',', '.') }}</p>
                        <p class="text-paper/50 text-xs mt-1">1 poin per Rp10.000 belanja</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-clay/20 border border-clay/30 flex items-center justify-center text-clay text-2xl">
                        <i class="fas fa-award"></i>
                    </div>
                </div>

                @if($nextTier)
                @php $progress = min(100, round(($lifetimePoints / $nextTier['threshold']) * 100)); @endphp
                <div class="relative z-10 mt-5">
                    <div class="flex items-center justify-between text-[11px] text-paper/60 font-semibold mb-1.5">
                        <span>Menuju Tier {{ $nextTier['name'] }}</span>
                        <span>{{ number_format($nextTier['points_needed'], 0, ',', '.') }} poin lagi</span>
                    </div>
                    <div class="h-1.5 rounded-full bg-paper/10 overflow-hidden">
                        <div class="h-full rounded-full bg-clay" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                @else
                <p class="relative z-10 mt-5 text-[11px] text-clay font-bold"><i class="fas fa-crown mr-1"></i> Anda sudah di tier tertinggi!</p>
                @endif

                @if($pointsHistory->isNotEmpty())
                <div class="relative z-10 mt-6 pt-4 border-t border-paper/10 space-y-2 max-h-32 overflow-y-auto">
                    @foreach($pointsHistory->take(4) as $entry)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-paper/70 truncate max-w-[200px]">{{ $entry->description }}</span>
                        <span class="font-bold {{ $entry->points > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            {{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Referral -->
            <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-ink font-bold text-sm">Ajak Teman, Dapat Poin</p>
                        <p class="text-muted text-xs mt-0.5">{{ $referralCount }} orang sudah pakai kode Anda</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-paper border border-line flex items-center justify-center text-clay">
                        <i class="fas fa-gift"></i>
                    </div>
                </div>

                <div class="flex items-center gap-2 bg-paper border border-line rounded-xl px-4 py-3">
                    <code class="flex-1 text-ink font-bold text-sm tracking-widest">{{ $user->referral_code }}</code>
                    <button type="button" onclick="copyReferral()" id="copyReferralBtn"
                        class="text-xs font-bold text-clay hover:text-clay/80 transition-colors shrink-0">
                        <i class="fas fa-copy mr-1"></i> Salin Link
                    </button>
                </div>
                <input type="hidden" id="referralLink" value="{{ route('register', ['ref' => $user->referral_code]) }}">
                <p class="text-xs text-muted mt-3">Teman Anda dapat +500 poin bonus bagi Anda saat mereka menyelesaikan booking pertama.</p>
            </div>
        </div>

        <!-- Tukar Poin -->
        <div class="bg-surface rounded-2xl p-6 shadow-sm border border-line mb-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-paper border border-line flex items-center justify-center text-clay">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <p class="text-ink font-bold text-sm">Tukar Poin Jadi Voucher</p>
                    <p class="text-muted text-xs mt-0.5">Voucher berlaku 60 hari, khusus akun Anda.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach(\App\Services\LoyaltyService::REDEMPTION_OPTIONS as $points => $discount)
                <form method="POST" action="{{ route('loyalty.redeem') }}">
                    @csrf
                    <input type="hidden" name="points" value="{{ $points }}">
                    <button type="submit" {{ $totalPoints < $points ? 'disabled' : '' }}
                        class="w-full text-left p-4 rounded-xl border border-line bg-paper hover:border-clay transition-colors disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:border-line group">
                        <p class="text-lg font-black text-ink font-serif">Rp{{ number_format($discount, 0, ',', '.') }}</p>
                        <p class="text-xs text-muted mt-1">{{ number_format($points, 0, ',', '.') }} poin</p>
                        <p class="text-[11px] font-bold text-clay mt-2 group-hover:underline">
                            {{ $totalPoints < $points ? 'Poin belum cukup' : 'Tukar Sekarang' }}
                        </p>
                    </button>
                </form>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Riwayat AI Chat -->
            <div class="bg-surface rounded-2xl shadow-sm border border-line overflow-hidden">
                <div class="px-6 py-5 border-b border-line flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-paper border border-line flex items-center justify-center">
                            <i class="fas fa-robot text-clay text-sm"></i>
                        </div>
                        <h2 class="font-bold text-ink font-serif">Riwayat Chat dengan Ara</h2>
                    </div>
                    <a href="{{ route('travel.chat') }}" class="text-xs text-clay font-bold hover:underline flex items-center gap-1">
                        <i class="fas fa-plus"></i> Chat Baru
                    </a>
                </div>

                @if($chatSessions->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-14 h-14 rounded-full bg-paper border border-line flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment-slash text-muted text-2xl"></i>
                        </div>
                        <p class="text-muted text-sm">Belum ada sesi chat.</p>
                        <a href="{{ route('travel.chat') }}" class="mt-4 inline-block text-sm text-clay font-bold hover:underline">
                            Mulai chat dengan Ara →
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-line">
                        @foreach($chatSessions as $session)
                            <div class="px-6 py-4 flex items-center justify-between hover:bg-paper/50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $session->status === 'completed' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-blue-500/10 text-blue-600' }}">
                                        <i class="fas {{ $session->status === 'completed' ? 'fa-check' : 'fa-clock' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-ink">
                                            Sesi {{ $session->created_at->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-muted">
                                            {{ $session->messages_count }} pesan ·
                                            <span class="{{ $session->status === 'completed' ? 'text-emerald-600' : 'text-blue-600' }} font-semibold">
                                                {{ $session->status === 'completed' ? 'Selesai' : 'Aktif' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2 font-bold">
                                    @if($session->status === 'completed')
                                        <a href="{{ route('travel.recommendation', $session->session_token) }}"
                                           class="text-xs bg-clay text-paper px-3 py-1.5 rounded-lg font-semibold hover:bg-clay/90 transition">
                                            Lihat
                                        </a>
                                    @else
                                        <a href="{{ route('travel.chat', ['token' => $session->session_token]) }}"
                                           class="text-xs bg-paper text-ink border border-line px-3 py-1.5 rounded-lg font-semibold hover:bg-surface transition">
                                            Lanjut
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Riwayat Booking Hotel -->
            <div class="bg-surface rounded-2xl shadow-sm border border-line overflow-hidden">
                <div class="px-6 py-5 border-b border-line flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-paper border border-line flex items-center justify-center">
                            <i class="fas fa-hotel text-clay text-sm"></i>
                        </div>
                        <h2 class="font-bold text-ink font-serif">Riwayat Booking Hotel</h2>
                    </div>
                    <a href="{{ route('hotels.index') }}" class="text-xs text-clay font-bold hover:underline flex items-center gap-1">
                        <i class="fas fa-search"></i> Cari Hotel
                    </a>
                </div>

                @if($hotelBookings->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <div class="w-14 h-14 rounded-full bg-paper border border-line flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-hotel text-muted text-2xl"></i>
                        </div>
                        <p class="text-muted text-sm">Belum ada booking hotel.</p>
                        <a href="{{ route('hotels.index') }}" class="mt-4 inline-block text-sm text-clay font-bold hover:underline">
                            Jelajahi hotel →
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-line">
                        @foreach($hotelBookings as $booking)
                            <div class="px-6 py-4 hover:bg-paper/50 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-ink truncate">{{ $booking->hotel->name ?? 'Hotel' }}</p>
                                        <p class="text-xs text-muted mt-0.5">
                                            {{ $booking->check_in_date?->format('d M') }} – {{ $booking->check_out_date?->format('d M Y') }}
                                            · {{ ucfirst($booking->room_type) }}
                                        </p>
                                        <p class="text-xs text-muted mt-0.5 font-mono">{{ $booking->booking_number }}</p>

                                        {{-- Cancellation status badge --}}
                                        @if($booking->cancellation_status === 'requested')
                                            <span class="inline-block mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-yellow-500/10 text-yellow-600">
                                                <i class="fas fa-clock mr-0.5"></i> Permintaan Cancel Dikirim
                                            </span>
                                        @elseif($booking->cancellation_status === 'approved')
                                            <span class="inline-block mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-600">
                                                <i class="fas fa-check mr-0.5"></i> Pembatalan Disetujui — Refund Diproses
                                            </span>
                                        @elseif($booking->cancellation_status === 'rejected')
                                            <span class="inline-block mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-red-500/10 text-red-600">
                                                <i class="fas fa-times mr-0.5"></i> Pembatalan Ditolak
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full
                                            {{ $booking->status === 'approve' ? 'bg-emerald-500/10 text-emerald-600' :
                                               ($booking->status === 'pending' ? 'bg-yellow-500/10 text-yellow-600' :
                                               ($booking->status === 'cancelled' ? 'bg-surface text-muted' : 'bg-red-500/10 text-red-600')) }}">
                                            {{ $booking->status === 'approve' ? 'Dikonfirmasi' :
                                               ($booking->status === 'pending' ? 'Menunggu' :
                                               ($booking->status === 'cancelled' ? 'Dibatalkan' : ucfirst($booking->status))) }}
                                        </span>
                                        <p class="text-xs font-bold text-ink mt-1.5 font-serif">
                                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                        </p>
                                        @if($booking->isCancellable())
                                        <button onclick="openCancelModal({{ $booking->id }}, '{{ $booking->booking_number }}')"
                                            class="mt-2 text-[10px] text-red-600 hover:text-red-700 font-semibold underline">
                                            Ajukan Pembatalan
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

              <!-- Quick Actions -->
        <div class="mt-8 bg-surface rounded-3xl p-8 border border-line flex flex-col sm:flex-row items-center justify-between gap-6 shadow-sm col-span-full lg:col-span-2">
            <div>
                <h3 class="text-ink font-bold text-lg font-serif">Rencanakan Perjalanan Berikutnya</h3>
                <p class="text-muted text-sm mt-1 font-semibold">Chat dengan Ara, AI Guide NTT, dan temukan destinasi tersembunyi!</p>
            </div>
            <a href="{{ route('travel.chat') }}"
               class="shrink-0 bg-clay hover:bg-clay/90 text-paper font-bold px-6 py-3.5 rounded-xl transition-all flex items-center gap-2 text-sm">
                <i class="fas fa-robot"></i> Chat dengan Ara
            </a>
        </div>

        {{-- ── WISHLIST SECTION ── --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-line overflow-hidden mt-8 col-span-full lg:col-span-2">
            <div class="px-6 py-5 border-b border-line flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
                        <i class="fas fa-heart text-red-500 text-sm"></i>
                    </div>
                    <h2 class="font-bold text-ink font-serif">Favorit Saya</h2>
                </div>
                <span class="text-xs text-muted font-bold">{{ $wishlists->count() }} item</span>
            </div>

            @if($wishlists->isEmpty())
            <div class="px-6 py-10 text-center">
                <i class="fas fa-heart text-muted/30 text-4xl mb-3"></i>
                <p class="text-muted text-sm">Belum ada favorit tersimpan.</p>
                <a href="{{ route('destinations.index') }}" class="mt-3 inline-block text-clay text-sm font-semibold hover:underline">Jelajahi Destinasi</a>
            </div>
            @else
            <div class="divide-y divide-line">
                @foreach($wishlists as $item)
                @php $obj = $item->wishlistable; @endphp
                @if($obj)
                <div class="px-6 py-4 flex items-center gap-4 hover:bg-paper/50 transition">
                    <img src="{{ $obj->image ?? 'https://via.placeholder.com/60x60' }}"
                         class="w-12 h-12 rounded-xl object-cover flex-shrink-0" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-ink truncate">{{ $obj->name }}</p>
                        <p class="text-xs text-muted">{{ $obj->location ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($item->wishlistable_type === 'App\Models\Destination')
                        <a href="{{ route('destinations.show', $obj->id) }}" class="text-xs text-clay hover:underline font-bold">Lihat</a>
                        @else
                        <a href="{{ route('hotels.show', $obj->id) }}" class="text-xs text-clay hover:underline font-bold">Lihat</a>
                        @endif
                        <form method="POST" action="{{ route('wishlist.toggle') }}">
                            @csrf
                            <input type="hidden" name="type" value="{{ $item->wishlistable_type === 'App\Models\Destination' ? 'destination' : 'hotel' }}">
                            <input type="hidden" name="id" value="{{ $obj->id }}">
                            <button type="submit" class="text-red-500 hover:text-red-600 transition text-sm">
                                <i class="fas fa-heart-broken"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── AI REKOMENDASI PERSONAL ── --}}
        <div class="bg-surface rounded-3xl overflow-hidden mt-8 border border-line shadow-sm col-span-full lg:col-span-2">
            <div class="px-6 py-5 border-b border-line flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-clay/10 flex items-center justify-center">
                        <i class="fas fa-robot text-clay text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-ink font-serif">Rekomendasi AI untuk Anda</h2>
                        <p class="text-xs text-muted">Berdasarkan riwayat booking dan favorit Anda</p>
                    </div>
                </div>
                <button onclick="loadPersonalRec()" id="rec-btn"
                    class="text-xs bg-clay hover:bg-clay/90 text-paper px-4 py-2 rounded-full font-semibold transition flex items-center gap-1.5 shrink-0">
                    <i class="fas fa-magic"></i> Muat Rekomendasi
                </button>
            </div>

            <div id="rec-loading" class="hidden px-6 py-8 text-center">
                <div class="inline-flex items-center gap-3 text-muted text-sm">
                    <div class="w-4 h-4 border border-clay border-t-transparent rounded-full animate-spin"></div>
                    AI sedang menganalisis preferensi Anda...
                </div>
            </div>

            <div id="rec-results" class="hidden px-6 py-5">
                <div id="rec-grid" class="grid grid-cols-1 sm:grid-cols-3 gap-4"></div>
            </div>

            <div id="rec-empty" class="hidden px-6 py-8 text-center">
                <i class="fas fa-map-marked-alt text-muted/30 text-3xl mb-3"></i>
                <p class="text-muted text-sm" id="rec-msg">Lakukan booking atau tambahkan favorit dulu agar AI bisa belajar preferensi Anda.</p>
            </div>
        </div>

    </div>
</div>

{{-- ── CANCEL MODAL ── --}}
<div id="cancel-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-ink/60 backdrop-blur-sm" onclick="closeCancelModal()"></div>
    <div class="relative bg-surface border border-line rounded-2xl shadow-2xl max-w-md w-full p-6 z-10 text-ink">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
                <i class="fas fa-times-circle text-red-500"></i>
            </div>
            <div>
                <h3 class="font-bold text-ink text-lg">Ajukan Pembatalan</h3>
                <p class="text-xs text-muted font-bold font-mono" id="cancel-booking-num"></p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200/60 rounded-xl p-3 mb-5 text-xs text-amber-800">
            <i class="fas fa-info-circle mr-1"></i>
            Permintaan akan diproses tim kami dalam <strong>1–3 hari kerja</strong>. Refund dikirim ke rekening asal.
        </div>

        <form id="cancel-form" method="POST">
            @csrf
            @method('POST')
            <div class="mb-4">
                <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-2">Alasan Pembatalan</label>
                <textarea name="reason" rows="3" required maxlength="500"
                    class="w-full px-4 py-3 rounded-xl border border-line bg-paper text-ink text-sm focus:border-clay focus:outline-none resize-none"
                    placeholder="Ceritakan alasan pembatalan Anda..."></textarea>
            </div>
            <div class="flex gap-3 font-bold">
                <button type="button" onclick="closeCancelModal()"
                    class="flex-1 py-3 rounded-xl bg-paper border border-line text-ink text-sm font-semibold hover:bg-surface transition">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-bold transition">
                    Kirim Permintaan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyReferral() {
    const link = document.getElementById('referralLink').value;
    navigator.clipboard.writeText(link).then(() => {
        const btn = document.getElementById('copyReferralBtn');
        const original = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Tersalin!';
        setTimeout(() => { btn.innerHTML = original; }, 2000);
    });
}
function openCancelModal(bookingId, bookingNum) {
    document.getElementById('cancel-booking-num').textContent = bookingNum;
    document.getElementById('cancel-form').action = '/bookings/' + bookingId + '/cancel';
    const modal = document.getElementById('cancel-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeCancelModal() {
    const modal = document.getElementById('cancel-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function loadPersonalRec() {
    document.getElementById('rec-btn').style.display = 'none';
    document.getElementById('rec-loading').classList.remove('hidden');

    fetch('{{ route("ai.recommendations") }}', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('rec-loading').classList.add('hidden');

        if (!data.recommendations || data.recommendations.length === 0) {
            document.getElementById('rec-empty').classList.remove('hidden');
            if (data.message) document.getElementById('rec-msg').textContent = data.message;
            return;
        }

        const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');
        document.getElementById('rec-grid').innerHTML = data.recommendations.map(d => `
            <a href="/destinations/${d.id}" class="bg-paper border border-line hover:border-clay rounded-xl overflow-hidden transition group block">
                <div class="h-28 overflow-hidden">
                    <img src="${d.image || 'https://via.placeholder.com/300x200'}"
                          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="${d.name}">
                </div>
                <div class="p-3">
                    <p class="font-bold text-ink text-sm truncate">${d.name}</p>
                    <p class="text-xs text-muted mb-1"><i class="fas fa-map-marker-alt text-clay mr-1"></i>${d.location}</p>
                    <p class="text-xs text-clay/70 leading-snug">"${d.reason}"</p>
                    <p class="text-clay font-bold text-xs mt-2">${fmt(d.price)}</p>
                </div>
            </a>
        `).join('');

        document.getElementById('rec-results').classList.remove('hidden');
    })
    .catch(() => {
        document.getElementById('rec-loading').classList.add('hidden');
        document.getElementById('rec-empty').classList.remove('hidden');
    });
}
</script>
@endpush
@endsection
