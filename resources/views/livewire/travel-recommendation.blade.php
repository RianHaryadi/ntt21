<div class="min-h-screen bg-transparent">

    <!-- Top Bar -->
    <div class="bg-slate-900 border-b border-slate-950 sticky top-0 z-30 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-laut flex items-center justify-center">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div>
                    <p class="text-white font-bold text-sm font-serif tracking-tight">Rekomendasi dari Ara</p>
                    <p class="text-slate-300 text-[11px] font-bold">Itinerary personal NTT Anda sudah siap</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}"
                   class="text-xs text-slate-300 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-white/10">
                    <i class="fas fa-home"></i> <span class="hidden sm:inline">Beranda</span>
                </a>
                <button onclick="window.print()"
                   class="text-xs text-slate-300 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-white/10 font-bold no-print">
                    <i class="fas fa-print"></i> <span class="hidden sm:inline">Cetak</span>
                </button>
                @auth
                    <a href="{{ route('cart.index') }}"
                       class="text-xs bg-laut hover:bg-laut/90 text-white font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 font-bold">
                        <i class="fas fa-shopping-cart"></i> <span class="hidden sm:inline">Keranjang</span>
                    </a>
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                       class="text-xs bg-laut hover:bg-laut/90 text-white font-semibold px-3 py-1.5 rounded-lg transition font-bold">
                        Login &amp; Simpan
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- ===== NOTIFIKASI ===== --}}
    @if($showSuccess)
        <div class="bg-emerald-50 border-b border-emerald-200/60 px-4 py-3 no-print"
             x-data x-init="setTimeout(() => $wire.dismissNotification(), 4000)">
            <div class="flex items-center gap-2 text-emerald-800 text-sm font-medium max-w-7xl mx-auto w-full">
                <i class="fas fa-check-circle text-green-500"></i>
                Pilihan Anda berhasil disimpan!
                <button wire:click="dismissNotification" class="ml-auto text-emerald-800 hover:text-emerald-950"><i class="fas fa-times text-xs"></i></button>
            </div>
        </div>
    @endif

    @if($cartError)
        <div class="bg-coral/10 border-b border-coral/20 px-4 py-3">
            <div class="flex items-center gap-2 text-coral text-sm font-semibold max-w-7xl mx-auto w-full">
                <i class="fas fa-triangle-exclamation"></i> {{ $cartError }}
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        {{-- ===== DESTINATION CARDS (bisa ditambah/hapus dari rencana) ===== --}}
        @if($destinations->isNotEmpty())
        <div class="no-print">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-laut flex items-center justify-center shadow-md shadow-laut/20">
                    <i class="fas fa-map-marked-alt text-white text-sm"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 font-serif tracking-tight text-lg">Destinasi Rekomendasi Ara</h2>
                    <p class="text-xs text-slate-500">Klik <span class="font-bold text-laut">+ Tambah</span> untuk memasukkan tiket ke rencana checkout Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($displayDestinations as $dest)
                @php
                    $mentioned = $this->isMentioned($dest->name);
                    $inPlan    = in_array($dest->id, $selectedDestinationIds);
                @endphp
                <div wire:key="dest-{{ $dest->id }}"
                     class="group bg-white rounded-2xl overflow-hidden shadow-sm border transition-all
                            {{ $inPlan ? 'border-laut ring-2 ring-laut/30' : ($mentioned ? 'border-laut/40' : 'border-slate-200/60') }}">
                    <a href="{{ route('destinations.show', $dest->id) }}" class="block relative h-36 overflow-hidden bg-slate-950">
                        @if($dest->image)
                            <img src="{{ asset('storage/' . $dest->image) }}" alt="{{ $dest->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-700 to-slate-600 flex items-center justify-center">
                                <i class="fas fa-mountain text-white/30 text-4xl"></i>
                            </div>
                        @endif
                        @if($mentioned)
                            <div class="absolute top-2 left-2 bg-laut text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-full shadow-lg">
                                <i class="fas fa-robot mr-0.5 text-[8px]"></i> Ara Rekomen
                            </div>
                        @endif
                        <div class="absolute top-2 right-2 bg-black/40 backdrop-blur-sm text-white text-[10px] font-semibold px-2 py-0.5 rounded-full">
                            {{ $dest->category }}
                        </div>
                    </a>
                    <div class="p-3">
                        <p class="font-bold text-sm text-slate-800 truncate">{{ $dest->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1 truncate">
                            <i class="fas fa-map-marker-alt text-laut text-[9px]"></i> {{ $dest->location }}
                        </p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs font-bold text-laut font-serif tracking-tight">
                                Rp {{ number_format($this->destUnitPrice($dest), 0, ',', '.') }}
                            </span>
                            @if($dest->rating)
                            <span class="text-[10px] text-amber-500 font-semibold flex items-center gap-0.5">
                                <i class="fas fa-star text-[9px]"></i> {{ number_format($dest->rating, 1) }}
                            </span>
                            @endif
                        </div>
                        <button wire:click="toggleDestination({{ $dest->id }})"
                                class="mt-3 w-full py-2 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5
                                       {{ $inPlan ? 'bg-laut text-white hover:bg-laut/90' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            @if($inPlan)
                                <i class="fas fa-check"></i> Ditambahkan
                            @else
                                <i class="fas fa-plus"></i> Tambah
                            @endif
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            @if($destinations->count() > 8)
                <div class="mt-4 text-center no-print">
                    <button wire:click="$toggle('showAllDestinations')"
                            class="text-xs font-bold text-laut hover:text-petrol transition inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-laut/30 hover:bg-laut/5">
                        @if($showAllDestinations)
                            <i class="fas fa-chevron-up"></i> Tampilkan lebih sedikit
                        @else
                            <i class="fas fa-chevron-down"></i> Lihat semua {{ $destinations->count() }} destinasi
                        @endif
                    </button>
                </div>
            @endif
        </div>
        @endif

        {{-- ===== GRID: AI TEXT + PICKER ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            <!-- ===== LEFT: AI Recommendation Text ===== -->
            <div class="lg:col-span-3 print-full-col">
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-200/60">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3 bg-white">
                        <div class="w-8 h-8 rounded-lg bg-laut/10 border border-laut/20 flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-laut text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-slate-800 font-bold text-sm">Itinerary Lengkap dari Ara</h2>
                            <p class="text-slate-500 text-[11px] font-bold">Rencana perjalanan personal Anda</p>
                        </div>
                    </div>

                    <div class="overflow-y-auto p-5 bg-white print-full prose prose-sm max-w-none prose-slate
                                prose-strong:text-laut prose-a:text-laut prose-hr:my-5
                                prose-h2:border-b-2 prose-h2:border-laut prose-h2:pb-2
                                prose-h3:border-b prose-h3:border-slate-100 prose-h3:pb-2
                                prose-li:my-0.5 prose-table:text-xs prose-th:bg-slate-50"
                         style="max-height: 70vh;">
                        {!! $this->recommendationHtml !!}
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT: Settings + Pickers + Summary ===== -->
            <div class="lg:col-span-2 space-y-5 no-print">

                <!-- Pengaturan Trip -->
                <div class="bg-white border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-laut/10 border border-laut/20 flex items-center justify-center">
                            <i class="fas fa-sliders text-laut text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Pengaturan Trip</h3>
                            <p class="text-slate-500 text-[11px] font-bold">Sesuaikan tanggal &amp; jumlah orang</p>
                        </div>
                    </div>
                    <div class="p-5 grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1.5">Tanggal Mulai</label>
                            <input type="date" wire:model.live="startDate" min="{{ now()->toDateString() }}"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-laut">
                            @error('startDate') <p class="text-[11px] text-coral mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wide mb-1.5">Jumlah Orang</label>
                            <input type="number" min="1" max="30" wire:model.live.debounce.400ms="pax"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-laut">
                            @error('pax') <p class="text-[11px] text-coral mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Hotel Picker -->
                <div class="bg-white border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3 bg-white">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200/60 flex items-center justify-center">
                            <i class="fas fa-hotel text-laut text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Pilih Hotel / Menginap</h3>
                            <p class="text-slate-500 text-[11px] font-bold">Boleh lebih dari satu (mis. beda wilayah) · klik lagi untuk hapus</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto bg-white">
                        @forelse($hotels as $hotel)
                            @php
                                $hotelPrices = array_filter([$hotel->single_room_price, $hotel->double_room_price, $hotel->family_room_price]);
                                $hotelFrom = $hotelPrices ? min($hotelPrices) : 0;
                            @endphp
                            @php $inStay = collect($stays)->contains(fn ($s) => (int) $s['hotel_id'] === $hotel->id); @endphp
                            <div wire:key="hotel-{{ $hotel->id }}" wire:click="toggleHotel({{ $hotel->id }})"
                                 class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all
                                        {{ $inStay
                                            ? 'bg-laut/10 border-l-2 border-laut'
                                            : 'hover:bg-slate-50/50 border-l-2 border-transparent bg-white' }}">
                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 bg-slate-50 border border-slate-200/60">
                                    @if($hotel->image)
                                        <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-hotel"></i></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $hotel->name }}</p>
                                        @if($this->isMentioned($hotel->name))
                                            <span class="shrink-0 bg-laut/10 text-laut text-[9px] font-black uppercase px-1.5 py-0.5 rounded-full"><i class="fas fa-robot text-[8px]"></i> Ara</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $hotel->location }}</p>
                                    <p class="text-xs font-bold text-laut mt-0.5">
                                        Mulai Rp {{ number_format($hotelFrom, 0, ',', '.') }}<span class="text-slate-500 font-normal">/malam</span>
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    @if($inStay)
                                        <div class="w-5 h-5 rounded-full bg-laut flex items-center justify-center"><i class="fas fa-check text-white text-[9px]"></i></div>
                                    @else
                                        <div class="w-5 h-5 rounded-full border-2 border-slate-300 bg-white"></div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-slate-500 text-sm bg-white">
                                <i class="fas fa-hotel text-2xl mb-2 block text-slate-400"></i> Belum ada hotel tersedia
                            </div>
                        @endforelse
                    </div>

                    {{-- Konfigurasi tiap menginap (tanggal berantai otomatis) --}}
                    @if(count($stays))
                        <div class="border-t border-slate-100 divide-y divide-slate-100">
                            @foreach($stays as $i => $stay)
                                @php
                                    $sh = $hotels->firstWhere('id', $stay['hotel_id']);
                                    if (!$sh) continue;
                                    $roomTypes = $this->roomTypesFor($sh);
                                    [$ci, $co] = $this->stayDates($i);
                                @endphp
                                <div wire:key="stay-{{ $stay['hotel_id'] }}" class="p-4 bg-slate-50/50">
                                    <div class="flex items-center justify-between gap-2 mb-2.5">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">
                                                <span class="text-laut">Menginap {{ $i + 1 }}:</span> {{ $sh->name }}
                                            </p>
                                            <p class="text-[10px] text-slate-500 mt-0.5">
                                                <i class="fas fa-calendar text-[8px] mr-0.5"></i>
                                                {{ $ci->format('d M') }} &rarr; {{ $co->format('d M Y') }}
                                            </p>
                                        </div>
                                        <button wire:click="toggleHotel({{ $sh->id }})"
                                                class="shrink-0 text-[11px] text-coral hover:text-red-600 font-bold transition">
                                            <i class="fas fa-times"></i> Hapus
                                        </button>
                                    </div>

                                    {{-- Komposisi kamar: jumlah per tipe (boleh campur, mis. 1 family + 1 single) --}}
                                    <div class="space-y-1.5 mb-2.5">
                                        @foreach($roomTypes as $key => $rt)
                                            <div wire:key="stay-{{ $stay['hotel_id'] }}-rt-{{ $key }}" class="flex items-center justify-between gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2">
                                                <div class="min-w-0">
                                                    <p class="text-[11px] font-bold text-slate-700">{{ $rt['label'] }} <span class="font-normal text-slate-400">· {{ \App\Livewire\TravelRecommendation::ROOM_CAPACITY[$key] ?? 2 }} org/kamar</span></p>
                                                    <p class="text-[10px] text-slate-500">Rp {{ number_format($rt['price'], 0, ',', '.') }}/malam</p>
                                                </div>
                                                <input type="number" min="0" max="10" wire:model.live.debounce.400ms="stays.{{ $i }}.rooms.{{ $key }}"
                                                       class="w-16 shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1.5 text-sm text-slate-800 text-center focus:outline-none focus:border-laut">
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="flex items-center justify-between gap-2">
                                        <div>
                                            @php $cap = $this->stayCapacity($stay); @endphp
                                            <p class="text-[10px] font-bold {{ $cap >= max((int) $pax, 1) ? 'text-slate-500' : 'text-coral' }}">
                                                Kapasitas: {{ $cap }} / {{ max((int) $pax, 1) }} orang
                                                @if($cap < max((int) $pax, 1)) — kurang muat! @endif
                                            </p>
                                            <p class="text-[10px] text-slate-400">Rp {{ number_format($this->stayNightPrice($sh, $stay), 0, ',', '.') }}/malam</p>
                                        </div>
                                        <div class="shrink-0 w-28">
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wide mb-1 text-center">Malam</label>
                                            <input type="number" min="1" max="30" wire:model.live.debounce.400ms="stays.{{ $i }}.nights"
                                                   class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-sm text-slate-800 text-center focus:outline-none focus:border-laut">
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1.5">Komposisi disarankan otomatis dari {{ max((int) $pax, 1) }} orang — bebas diubah.</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Tour Package Picker -->
                <div class="bg-white border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3 bg-white">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200/60 flex items-center justify-center">
                            <i class="fas fa-route text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Tambah Paket Tour</h3>
                            <p class="text-slate-500 text-[11px] font-bold">Pilih satu atau beberapa paket</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto bg-white">
                        @forelse($tourPackages as $tour)
                            <div wire:key="tour-{{ $tour->id }}" wire:click="toggleTour({{ $tour->id }})"
                                 class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all
                                        {{ in_array($tour->id, $selectedTourIds)
                                            ? 'bg-blue-500/10 border-l-2 border-blue-500'
                                            : 'hover:bg-slate-50/50 border-l-2 border-transparent bg-white' }}">
                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 bg-slate-50 border border-slate-200/60">
                                    @if($tour->thumbnail)
                                        <img src="{{ asset('storage/' . $tour->thumbnail) }}" alt="{{ $tour->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-map-marked-alt"></i></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $tour->name }}</p>
                                        @if($this->isMentioned($tour->name))
                                            <span class="shrink-0 bg-laut/10 text-laut text-[9px] font-black uppercase px-1.5 py-0.5 rounded-full"><i class="fas fa-robot text-[8px]"></i> Ara</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[11px] text-slate-500">{{ $tour->days }} hari</span>
                                        @if($tour->includes_hotel)
                                            <span class="text-[10px] bg-emerald-500/10 text-emerald-600 px-1.5 py-0.5 rounded font-semibold">Hotel included</span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-blue-600 mt-0.5">Rp {{ number_format($tour->price, 0, ',', '.') }}<span class="text-slate-500 font-normal">/orang</span></p>
                                </div>
                                <div class="shrink-0">
                                    @if(in_array($tour->id, $selectedTourIds))
                                        <div class="w-5 h-5 rounded bg-blue-500 flex items-center justify-center"><i class="fas fa-check text-white text-[9px]"></i></div>
                                    @else
                                        <div class="w-5 h-5 rounded border-2 border-slate-300 bg-white"></div>
                                    @endif
                                </div>
                            </div>

                            {{-- Pilihan varian harga (Open Trip / Keluarga / Grup / Private) --}}
                            @if(in_array($tour->id, $selectedTourIds) && $tour->variants->isNotEmpty())
                                <div wire:key="tour-{{ $tour->id }}-variants" class="px-4 pb-3 pt-1 bg-blue-500/5 border-l-2 border-blue-500 space-y-1.5">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Pilihan Harga</p>
                                    @foreach($tour->variants as $v)
                                        @php
                                            $fits   = $v->fitsPax(max((int) $pax, 1));
                                            $active = ($tourVariants[$tour->id] ?? null) == $v->id;
                                        @endphp
                                        <button wire:key="tv-{{ $v->id }}" wire:click="selectTourVariant({{ $tour->id }}, {{ $v->id }})"
                                                @if(!$fits) disabled @endif
                                                class="w-full text-left px-3 py-2 rounded-xl border transition
                                                       {{ $active ? 'border-blue-500 bg-blue-500/10' : ($fits ? 'border-slate-200 bg-white hover:border-blue-400/50' : 'border-slate-100 bg-slate-50 opacity-50 cursor-not-allowed') }}">
                                            <span class="flex items-center justify-between gap-2">
                                                <span class="text-[11px] font-bold {{ $active ? 'text-blue-600' : 'text-slate-700' }}">
                                                    {{ $v->name }}
                                                    <span class="font-normal text-slate-400">
                                                        @if($v->max_pax) · {{ $v->min_pax }}&ndash;{{ $v->max_pax }} org
                                                        @elseif($v->min_pax > 1) · min {{ $v->min_pax }} org
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="text-[11px] font-bold text-blue-600 whitespace-nowrap">
                                                    Rp {{ number_format($v->price, 0, ',', '.') }}{{ $v->price_type === 'flat' ? ' total' : '/org' }}
                                                </span>
                                            </span>
                                            @if($v->notes)
                                                <span class="block text-[10px] text-slate-500 mt-0.5">{{ $v->notes }}</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @empty
                            <div class="px-4 py-8 text-center text-slate-500 text-sm bg-white">
                                <i class="fas fa-route text-2xl mb-2 block text-slate-400"></i> Belum ada paket tour tersedia
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Ringkasan Biaya & Checkout -->
                <div class="bg-slate-900 border border-slate-950 rounded-2xl p-5 text-white shadow-xl">
                    <h3 class="font-bold text-sm mb-4 flex items-center gap-2">
                        <i class="fas fa-receipt text-laut"></i> Ringkasan &amp; Checkout
                    </h3>

                    @php $anySelected = count($stays) || $selectedTours->isNotEmpty() || $selectedDestinations->isNotEmpty(); @endphp

                    @if($anySelected)
                        <div class="space-y-2.5 text-sm">
                            {{-- Hotel (per menginap) --}}
                            @foreach($stays as $i => $stay)
                                @php
                                    $sh = $hotels->firstWhere('id', $stay['hotel_id']);
                                    if (!$sh) continue;
                                    [$ci, $co] = $this->stayDates($i);
                                    $stayTotal = $this->stayNightPrice($sh, $stay) * max((int) $stay['nights'], 1);
                                    $compo = collect($stay['rooms'])->filter(fn ($n) => (int) $n > 0)
                                        ->map(fn ($n, $k) => $n . ' ' . ucfirst($k))->implode(' + ') ?: '—';
                                @endphp
                                <div wire:key="sum-stay-{{ $stay['hotel_id'] }}" class="flex items-start justify-between gap-2">
                                    <span class="text-slate-300 min-w-0">
                                        <i class="fas fa-hotel text-laut text-xs mr-1"></i>
                                        <span>{{ $sh->name }}</span>
                                        <span class="block text-[11px] text-slate-500 ml-4">{{ $compo }} · {{ max((int) $stay['nights'], 1) }} malam · {{ $ci->format('d M') }}&ndash;{{ $co->format('d M') }}</span>
                                    </span>
                                    <span class="font-bold text-white whitespace-nowrap">Rp {{ number_format($stayTotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach

                            {{-- Tours --}}
                            @foreach($selectedTours as $st)
                                @php $sv = $this->tourVariantFor($st); @endphp
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-slate-300 min-w-0">
                                        <i class="fas fa-route text-blue-400 text-xs mr-1"></i>
                                        <span class="truncate">{{ $st->name }}</span>
                                        <span class="block text-[11px] text-slate-500 ml-4">
                                            @if($sv && $sv->price_type === 'flat')
                                                {{ $sv->name }} (harga paket, {{ max((int) $pax, 1) }} org)
                                            @elseif($sv)
                                                {{ $sv->name }} · {{ max((int) $pax, 1) }} orang
                                            @else
                                                {{ max((int) $pax, 1) }} orang
                                            @endif
                                            {{ $st->includes_hotel ? ' · sudah termasuk hotel' : '' }}
                                        </span>
                                    </span>
                                    <span class="font-bold text-white whitespace-nowrap">Rp {{ number_format($this->tourTotal($st), 0, ',', '.') }}</span>
                                </div>
                            @endforeach

                            {{-- Destinations --}}
                            @foreach($selectedDestinations as $sd)
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-slate-300 min-w-0">
                                        <i class="fas fa-ticket text-amber-400 text-xs mr-1"></i>
                                        <span class="truncate">{{ $sd->name }}</span>
                                        <span class="block text-[11px] text-slate-500 ml-4">{{ max((int) $pax, 1) }} tiket</span>
                                    </span>
                                    <span class="font-bold text-white whitespace-nowrap">Rp {{ number_format($this->destUnitPrice($sd) * max((int)$pax,1), 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Peringatan: paket tour sudah termasuk hotel --}}
                        @php $tourWithHotel = count($stays) ? $selectedTours->firstWhere('includes_hotel', true) : null; @endphp
                        @if($tourWithHotel)
                            <div class="mt-4 bg-amber-500/10 border border-amber-500/30 rounded-xl p-3 text-[11px] leading-relaxed text-amber-200 no-print">
                                <i class="fas fa-triangle-exclamation mr-1"></i>
                                Paket <strong>{{ $tourWithHotel->name }}</strong> sudah termasuk hotel.
                                Hapus menginap yang malam-malamnya sudah dicakup paket itu agar tidak bayar dobel —
                                menginap terpisah tetap wajar untuk wilayah/malam di luar paket.
                                <button wire:click="clearStays"
                                        class="mt-2 w-full bg-amber-500/20 hover:bg-amber-500/30 text-amber-100 font-bold py-1.5 rounded-lg transition">
                                    <i class="fas fa-times mr-1"></i> Hapus semua hotel dari rencana
                                </button>
                            </div>
                        @endif

                        {{-- Total --}}
                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-white/10">
                            <span class="text-sm font-semibold text-slate-300">Estimasi Total</span>
                            <span class="text-xl font-black text-laut">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">*Pajak &amp; layanan hotel dihitung saat checkout.</p>

                        {{-- Catatan --}}
                        <div class="mt-4">
                            <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1.5">Catatan khusus</label>
                            <textarea wire:model.live.debounce.600ms="customNotes" rows="2"
                                placeholder="Tambahkan catatan atau permintaan khusus..."
                                class="w-full bg-white/10 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-laut placeholder:text-white/40 resize-none"></textarea>
                        </div>

                        {{-- Tombol Keranjang / Checkout --}}
                        <div class="mt-4 space-y-2 no-print">
                            @auth
                                <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart"
                                    class="w-full bg-laut hover:bg-laut/90 disabled:opacity-60 text-white font-bold py-3.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2"><i class="fas fa-cart-plus"></i> Masukkan ke Keranjang &amp; Checkout</span>
                                    <span wire:loading wire:target="addToCart" class="flex items-center gap-2"><i class="fas fa-circle-notch fa-spin"></i> Memproses…</span>
                                </button>
                                <p class="text-[11px] text-slate-500 text-center">Kamu akan diarahkan ke keranjang untuk menyelesaikan pembayaran.</p>
                                <button wire:click="saveSelection"
                                    class="w-full text-slate-400 hover:text-white font-semibold py-2 rounded-xl text-xs transition flex items-center justify-center gap-2">
                                    <i class="fas fa-save"></i> Simpan pilihan (tanpa checkout)
                                </button>
                            @else
                                <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                                   class="w-full bg-laut hover:bg-laut/90 text-white font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                    <i class="fas fa-right-to-bracket"></i> Login untuk Checkout
                                </a>
                                <p class="text-[11px] text-slate-500 text-center">Pilihanmu akan tetap tersimpan setelah login.</p>
                            @endauth
                        </div>
                    @else
                        <p class="text-slate-400 text-sm text-center py-6">
                            <i class="fas fa-hand-pointer text-2xl mb-2 block text-slate-600"></i>
                            Pilih hotel, paket tour, atau destinasi untuk mulai menyusun checkout.
                        </p>
                    @endif
                </div>

                </div>
            {{-- end right col --}}
        </div>
        {{-- end grid AI + picker --}}

    </div>
    {{-- end max-w-7xl --}}
</div>
