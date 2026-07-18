<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- ═══════════════ ERROR ═══════════════ --}}
    @if ($error)
        <div class="mb-6 flex items-start gap-3 bg-coral/10 border border-coral/30 text-coral rounded-2xl px-5 py-4">
            <i class="fas fa-triangle-exclamation mt-0.5"></i>
            <p class="text-sm font-semibold">{{ $error }}</p>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         FORM  (tampil saat belum ada rencana)
    ═══════════════════════════════════════════════════════════ --}}
    @if (! $plan)
        <div class="bg-white rounded-3xl border border-line shadow-sm overflow-hidden">
            <div class="px-6 md:px-8 py-6 border-b border-line bg-paper/50">
                <h2 class="text-2xl font-black text-ink font-serif tracking-tight">Rancang Perjalananmu</h2>
                <p class="text-sm text-muted mt-1">Isi preferensimu, biar AI menyusun rencana harian dari destinasi, hotel &amp; tour asli.</p>
            </div>

            <div class="p-6 md:p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Wilayah --}}
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wide mb-2">Wilayah NTT</label>
                        <select wire:model="region"
                                class="w-full rounded-xl border border-line bg-paper px-4 py-3 text-sm text-ink focus:outline-none focus:border-laut">
                            <option value="">✨ Rekomendasikan untuk saya</option>
                            @foreach (\App\Livewire\ItineraryBuilder::REGIONS as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal mulai --}}
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wide mb-2">Tanggal Mulai</label>
                        <input type="date" wire:model="startDate" min="{{ now()->toDateString() }}"
                               class="w-full rounded-xl border border-line bg-paper px-4 py-3 text-sm text-ink focus:outline-none focus:border-laut">
                        @error('startDate') <p class="text-xs text-coral mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jumlah hari --}}
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wide mb-2">Jumlah Hari</label>
                        <input type="number" wire:model="days" min="1" max="10"
                               class="w-full rounded-xl border border-line bg-paper px-4 py-3 text-sm text-ink focus:outline-none focus:border-laut">
                        @error('days') <p class="text-xs text-coral mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jumlah orang --}}
                    <div>
                        <label class="block text-xs font-bold text-ink uppercase tracking-wide mb-2">Jumlah Orang</label>
                        <input type="number" wire:model="pax" min="1" max="20"
                               class="w-full rounded-xl border border-line bg-paper px-4 py-3 text-sm text-ink focus:outline-none focus:border-laut">
                        @error('pax') <p class="text-xs text-coral mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Budget --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-ink uppercase tracking-wide mb-2">
                            Perkiraan Budget <span class="text-muted font-medium normal-case">(opsional, Rupiah)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm font-semibold">Rp</span>
                            <input type="number" wire:model="budget" min="0" step="100000" placeholder="cth: 8.000.000"
                                   class="w-full rounded-xl border border-line bg-paper pl-11 pr-4 py-3 text-sm text-ink focus:outline-none focus:border-laut">
                        </div>
                    </div>
                </div>

                {{-- Minat --}}
                <div>
                    <label class="block text-xs font-bold text-ink uppercase tracking-wide mb-3">Minat Wisata</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach (\App\Livewire\ItineraryBuilder::INTERESTS as $label => $icon)
                            <button type="button" wire:click="toggleInterest('{{ $label }}')"
                                    @class([
                                        'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold transition-all',
                                        'bg-laut text-white border-laut shadow-sm' => in_array($label, $interests),
                                        'bg-paper text-muted border-line hover:border-laut/50' => ! in_array($label, $interests),
                                    ])>
                                <i class="fas {{ $icon }} text-xs"></i>
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Tombol generate --}}
                <button wire:click="generate" wire:loading.attr="disabled" wire:target="generate"
                        class="w-full flex items-center justify-center gap-2.5 py-4 rounded-2xl bg-ink hover:bg-ink/90 disabled:opacity-60 text-paper text-base font-bold transition-all">
                    <span wire:loading.remove wire:target="generate" class="flex items-center gap-2.5">
                        <i class="fas fa-wand-magic-sparkles"></i>
                        Susun Rencana dengan AI
                    </span>
                    <span wire:loading wire:target="generate" class="flex items-center gap-2.5">
                        <i class="fas fa-circle-notch fa-spin"></i>
                        Menyusun rencana perjalananmu…
                    </span>
                </button>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════════════════
         HASIL  (rencana perjalanan)
    ═══════════════════════════════════════════════════════════ --}}
    @else
        @php
            $total     = $this->estimatedTotal;
            $budgetVal = (float) ($budget ?? 0);
            $pct       = $budgetVal > 0 ? min(100, round($total / $budgetVal * 100)) : null;
            $over      = $budgetVal > 0 && $total > $budgetVal;
            $fallback  = asset('images/fallback.jpg');
        @endphp

        {{-- Header rencana --}}
        <div class="bg-gradient-to-br from-petrol to-laut rounded-3xl overflow-hidden p-7 md:p-9 mb-6 text-white relative">
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 bg-white/15 text-white text-[11px] font-bold px-3 py-1 rounded-full mb-3">
                    <i class="fas fa-wand-magic-sparkles text-[10px]"></i> Disusun oleh AI
                </div>
                <h2 class="text-2xl md:text-3xl font-black font-serif tracking-tight leading-tight">{{ $plan['title'] }}</h2>
                @if ($plan['summary'])
                    <p class="text-white/85 text-sm md:text-base mt-2 max-w-2xl leading-relaxed">{{ $plan['summary'] }}</p>
                @endif
                <div class="flex flex-wrap gap-4 mt-5 text-sm">
                    <span class="inline-flex items-center gap-1.5"><i class="fas fa-calendar-day text-white/70"></i> {{ count($plan['days']) }} hari</span>
                    <span class="inline-flex items-center gap-1.5"><i class="fas fa-users text-white/70"></i> {{ $pax }} orang</span>
                    @if ($region)
                        <span class="inline-flex items-center gap-1.5"><i class="fas fa-location-dot text-white/70"></i> {{ $region }}</span>
                    @endif
                    <span class="inline-flex items-center gap-1.5"><i class="fas fa-flag-checkered text-white/70"></i> Mulai {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- ─────────── TIMELINE (kolom kiri) ─────────── --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach ($plan['days'] as $day)
                    <div class="bg-white rounded-2xl border border-line shadow-sm overflow-hidden">
                        <div class="flex items-center gap-4 px-5 py-4 border-b border-line bg-paper/40">
                            <div class="w-12 h-12 rounded-2xl bg-laut text-white flex flex-col items-center justify-center shrink-0 leading-none">
                                <span class="text-[9px] font-bold uppercase opacity-80">Hari</span>
                                <span class="text-lg font-black">{{ $day['day'] }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-black text-ink font-serif tracking-tight truncate">{{ $day['theme'] }}</p>
                                <p class="text-xs text-muted">{{ $day['date_label'] }}</p>
                            </div>
                        </div>

                        <div class="p-5 space-y-4">
                            {{-- Destinasi hari ini --}}
                            @foreach ($day['destinations'] as $d)
                                <div class="flex gap-4">
                                    <div class="w-20 h-20 rounded-xl bg-surface overflow-hidden shrink-0">
                                        <img src="{{ $d['image'] ? asset('storage/' . ltrim($d['image'], '/')) : $fallback }}"
                                             onerror="this.src='{{ $fallback }}'"
                                             alt="{{ $d['name'] }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="font-bold text-ink truncate">{{ $d['name'] }}</p>
                                                <p class="text-xs text-muted"><i class="fas fa-location-dot text-[10px]"></i> {{ $d['location'] }} · {{ $d['category'] }}</p>
                                            </div>
                                            <span class="text-sm font-black text-laut whitespace-nowrap">Rp{{ number_format($d['unit_price'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Aktivitas --}}
                            @if (! empty($day['activities']))
                                <ul class="space-y-1.5 pl-1">
                                    @foreach ($day['activities'] as $act)
                                        <li class="flex items-start gap-2 text-sm text-ink/80">
                                            <i class="fas fa-circle text-[5px] text-laut mt-2 shrink-0"></i>
                                            <span>{{ $act }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Kuliner --}}
                            @if ($day['food'])
                                <div class="flex items-start gap-2 text-sm bg-coral/5 border border-coral/15 rounded-xl px-3 py-2.5">
                                    <i class="fas fa-utensils text-coral text-xs mt-0.5 shrink-0"></i>
                                    <span class="text-ink/80"><span class="font-semibold text-coral">Kuliner:</span> {{ $day['food'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Tips --}}
                @if (! empty($plan['tips']))
                    <div class="bg-petrol/5 border border-petrol/15 rounded-2xl p-5">
                        <p class="font-bold text-petrol text-sm mb-2 flex items-center gap-2"><i class="fas fa-lightbulb"></i> Tips Praktis</p>
                        <ul class="space-y-1.5">
                            @foreach ($plan['tips'] as $tip)
                                <li class="flex items-start gap-2 text-sm text-ink/75">
                                    <i class="fas fa-check text-petrol text-[10px] mt-1.5 shrink-0"></i>
                                    <span>{{ $tip }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- ─────────── PANEL BOOKING (kolom kanan, sticky) ─────────── --}}
            <div class="lg:sticky lg:top-24 space-y-4">
                <div class="bg-white rounded-2xl border border-line shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-line">
                        <h3 class="font-black text-ink font-serif tracking-tight">Ringkasan &amp; Booking</h3>
                        <p class="text-xs text-muted mt-0.5">Hilangkan centang item yang tak diinginkan.</p>
                    </div>

                    <div class="p-5 space-y-3">
                        {{-- Hotel --}}
                        @if ($plan['hotel'])
                            @php $h = $plan['hotel']; @endphp
                            <button type="button" wire:click="toggle('hotel')"
                                    @class([
                                        'w-full flex items-center gap-3 p-3 rounded-xl border text-left transition-all',
                                        'border-laut bg-laut/5' => $this->isIncluded('hotel'),
                                        'border-line opacity-50' => ! $this->isIncluded('hotel'),
                                    ])>
                                <i @class([
                                    'fas text-lg shrink-0',
                                    'fa-square-check text-laut' => $this->isIncluded('hotel'),
                                    'fa-square text-muted' => ! $this->isIncluded('hotel'),
                                ])></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-muted">Hotel · {{ ucfirst($h['room_type']) }} · {{ $h['nights'] }} malam</p>
                                    <p class="font-bold text-ink text-sm truncate">{{ $h['name'] }}</p>
                                    <p class="text-xs text-laut font-bold">Rp{{ number_format($h['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            </button>
                        @endif

                        {{-- Destinasi --}}
                        @foreach ($plan['destinations'] as $d)
                            <button type="button" wire:click="toggle('dest-{{ $d['id'] }}')"
                                    @class([
                                        'w-full flex items-center gap-3 p-3 rounded-xl border text-left transition-all',
                                        'border-laut bg-laut/5' => $this->isIncluded('dest-' . $d['id']),
                                        'border-line opacity-50' => ! $this->isIncluded('dest-' . $d['id']),
                                    ])>
                                <i @class([
                                    'fas text-lg shrink-0',
                                    'fa-square-check text-laut' => $this->isIncluded('dest-' . $d['id']),
                                    'fa-square text-muted' => ! $this->isIncluded('dest-' . $d['id']),
                                ])></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-muted">Tiket · {{ $pax }} orang</p>
                                    <p class="font-bold text-ink text-sm truncate">{{ $d['name'] }}</p>
                                    <p class="text-xs text-laut font-bold">Rp{{ number_format($d['unit_price'] * $pax, 0, ',', '.') }}</p>
                                </div>
                            </button>
                        @endforeach

                        {{-- Tours --}}
                        @foreach ($plan['tours'] as $t)
                            <button type="button" wire:click="toggle('tour-{{ $t['id'] }}')"
                                    @class([
                                        'w-full flex items-center gap-3 p-3 rounded-xl border text-left transition-all',
                                        'border-laut bg-laut/5' => $this->isIncluded('tour-' . $t['id']),
                                        'border-line opacity-50' => ! $this->isIncluded('tour-' . $t['id']),
                                    ])>
                                <i @class([
                                    'fas text-lg shrink-0',
                                    'fa-square-check text-laut' => $this->isIncluded('tour-' . $t['id']),
                                    'fa-square text-muted' => ! $this->isIncluded('tour-' . $t['id']),
                                ])></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-muted">Paket Tour · {{ $t['days'] }} hari · {{ $pax }} orang</p>
                                    <p class="font-bold text-ink text-sm truncate">{{ $t['name'] }}</p>
                                    <p class="text-xs text-laut font-bold">Rp{{ number_format($t['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    {{-- Budget & total --}}
                    <div class="px-5 pb-5 pt-1 space-y-3">
                        @if ($pct !== null)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="text-muted font-semibold">Terhadap budget</span>
                                    <span @class(['font-bold', 'text-coral' => $over, 'text-laut' => ! $over])>
                                        {{ $pct }}%{{ $over ? ' · melebihi budget' : '' }}
                                    </span>
                                </div>
                                <div class="h-2 rounded-full bg-surface overflow-hidden">
                                    <div @class(['h-full rounded-full transition-all', 'bg-coral' => $over, 'bg-laut' => ! $over])
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-2 border-t border-line">
                            <span class="text-sm font-semibold text-muted">Estimasi total</span>
                            <span class="text-xl font-black text-ink">Rp{{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[11px] text-muted leading-snug">*Belum termasuk pajak &amp; layanan hotel yang dihitung saat checkout.</p>

                        {{-- CTA --}}
                        @auth
                            <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart"
                                    class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-laut hover:bg-laut/90 disabled:opacity-60 text-white text-sm font-bold transition-all">
                                <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2">
                                    <i class="fas fa-cart-plus"></i> Tambah Semua ke Keranjang
                                </span>
                                <span wire:loading wire:target="addToCart" class="flex items-center gap-2">
                                    <i class="fas fa-circle-notch fa-spin"></i> Memproses…
                                </span>
                            </button>
                        @else
                            <a href="{{ route('login', ['redirect' => route('ai.itinerary')]) }}"
                               class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-laut hover:bg-laut/90 text-white text-sm font-bold transition-all">
                                <i class="fas fa-right-to-bracket"></i> Login untuk Booking
                            </a>
                        @endauth

                        <button wire:click="startOver"
                                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl border border-line text-muted hover:text-ink text-sm font-semibold transition-all">
                            <i class="fas fa-rotate-left text-xs"></i> Buat Rencana Baru
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════ LOADING OVERLAY ═══════════════ --}}
    <div wire:loading.flex wire:target="generate"
         class="fixed inset-0 z-50 items-center justify-center bg-ink/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl px-8 py-7 shadow-xl flex flex-col items-center gap-4 max-w-xs text-center">
            <div class="w-14 h-14 rounded-2xl bg-laut/10 flex items-center justify-center">
                <i class="fas fa-wand-magic-sparkles text-laut text-2xl animate-pulse"></i>
            </div>
            <div>
                <p class="font-black text-ink font-serif">AI sedang menyusun…</p>
                <p class="text-xs text-muted mt-1">Memilih destinasi, hotel &amp; tour terbaik dari katalog untukmu.</p>
            </div>
        </div>
    </div>
</div>
