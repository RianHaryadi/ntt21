<div class="min-h-screen bg-transparent">

    <!-- Top Bar -->
    <div class="bg-slate-900 border-b border-slate-950 sticky top-0 z-30">
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
                   class="text-xs text-slate-300 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-white/10 font-bold">
                    <i class="fas fa-print"></i> <span class="hidden sm:inline">Cetak</span>
                </button>
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-xs bg-laut hover:bg-laut/90 text-white font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 font-bold">
                        <i class="fas fa-th-large"></i> <span class="hidden sm:inline">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                       class="text-xs bg-laut hover:bg-laut/90 text-white font-semibold px-3 py-1.5 rounded-lg transition font-bold">
                        Login & Simpan
                    </a>
                @endauth
            </div>
        </div>
    </div>

    @if($showSuccess)
        <div class="bg-emerald-50 border-b border-emerald-200/60 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-800 text-sm font-medium max-w-7xl mx-auto w-full">
                <i class="fas fa-check-circle text-green-500"></i>
                Pilihan Anda berhasil disimpan!
                <button wire:click="dismissNotification" class="ml-auto text-emerald-800 hover:text-emerald-950"><i class="fas fa-times text-xs"></i></button>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        {{-- ===== DESTINATION CARDS ===== --}}
        @if($destinations->isNotEmpty())
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-laut flex items-center justify-center shadow-md shadow-laut/20">
                    <i class="fas fa-map-marked-alt text-white text-sm"></i>
                </div>
                <div>
                    <h2 class="font-black text-slate-800 font-serif tracking-tight text-lg">Destinasi Rekomendasi Ara</h2>
                    <p class="text-xs text-slate-500">Yang disorot = disebutkan dalam itinerary Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($destinations->take(8) as $dest)
                @php $mentioned = str_contains($raw, strtolower($dest->name)); @endphp
                <a href="{{ route('destinations.show', $dest->id) }}"
                   class="group bg-white rounded-2xl overflow-hidden shadow-sm border transition-all hover:-translate-y-1
                          {{ $mentioned ? 'border-laut ring-2 ring-laut/20' : 'border-slate-200/60 hover:border-slate-300/60' }}">
                    <div class="relative h-36 overflow-hidden bg-slate-950">
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
                    </div>
                    <div class="p-3">
                        <p class="font-bold text-sm text-slate-800 truncate">{{ $dest->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1 truncate">
                            <i class="fas fa-map-marker-alt text-laut text-[9px]"></i> {{ $dest->location }}
                        </p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs font-bold text-laut font-serif tracking-tight">
                                Rp {{ number_format($dest->price ?? 0, 0, ',', '.') }}
                            </span>
                            @if($dest->rating)
                            <span class="text-[10px] text-amber-500 font-semibold flex items-center gap-0.5">
                                <i class="fas fa-star text-[9px]"></i> {{ number_format($dest->rating, 1) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ===== GRID: AI TEXT + HOTEL & TOUR PICKER ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            <!-- ===== LEFT: AI Recommendation Text ===== -->
            <div class="lg:col-span-3">
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

                    <div class="overflow-y-auto p-5 text-sm text-slate-700 leading-relaxed space-y-2 bg-white" style="max-height: 70vh;">
                        @php
                            $text = e($session->recommendation_raw);
                            $text = preg_replace('/### (.*?)(\n|$)/', '<h4 class="text-sm font-bold text-petrol mt-5 mb-1.5 flex items-center gap-2"><i class="fas fa-circle text-[5px]"></i> $1</h4>', $text);
                            $text = preg_replace('/## (.*?)(\n|$)/', '<h3 class="text-base font-extrabold text-slate-800 mt-7 mb-3 border-b border-slate-100 pb-2">$1</h3>', $text);
                            $text = preg_replace('/# (.*?)(\n|$)/', '<h2 class="text-lg font-black text-slate-800 mt-8 mb-3 border-b-2 border-laut pb-2">$1</h2>', $text);
                            $text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-laut font-semibold">$1</strong>', $text);
                            $text = preg_replace('/^- (.*?)$/m', '<li class="text-slate-600 ml-3 list-disc pl-1 py-0.5">$1</li>', $text);
                            $text = nl2br($text);
                        @endphp
                        {!! $text !!}
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT: Hotel & Tour Selection ===== -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Hotel Picker -->
                <div class="bg-white border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3 bg-white">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-200/60 flex items-center justify-center">
                            <i class="fas fa-hotel text-laut text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Pilih Hotel</h3>
                            <p class="text-slate-500 text-[11px] font-bold">Klik hotel untuk memilih atau ganti</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto bg-white">
                        @forelse($hotels as $hotel)
                            <div wire:click="selectHotel({{ $hotel->id }})"
                                 class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all
                                        {{ $selectedHotelId == $hotel->id
                                            ? 'bg-laut/10 border-l-2 border-laut'
                                            : 'hover:bg-slate-50/50 border-l-2 border-transparent bg-white' }}">

                                <!-- Thumbnail -->
                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 bg-slate-50 border border-slate-200/60">
                                    @if($hotel->image)
                                        <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <i class="fas fa-hotel"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $hotel->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $hotel->location }}</p>
                                    <p class="text-xs font-bold text-laut mt-0.5">
                                        Rp {{ number_format($hotel->single_room_price, 0, ',', '.') }}<span class="text-slate-500 font-normal">/malam</span>
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @if($selectedHotelId == $hotel->id)
                                        <div class="w-5 h-5 rounded-full bg-laut flex items-center justify-center">
                                            <i class="fas fa-check text-white text-[9px]"></i>
                                        </div>
                                    @else
                                        <div class="w-5 h-5 rounded-full border-2 border-slate-300 bg-white"></div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-slate-500 text-sm bg-white">
                                <i class="fas fa-hotel text-2xl mb-2 block text-slate-400"></i>
                                Belum ada hotel tersedia
                            </div>
                        @endforelse
                    </div>
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
                            <div wire:click="toggleTour({{ $tour->id }})"
                                 class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all
                                        {{ in_array($tour->id, $selectedTourIds)
                                            ? 'bg-blue-500/10 border-l-2 border-blue-500 bg-white'
                                            : 'hover:bg-slate-50/50 border-l-2 border-transparent bg-white' }}">

                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 bg-slate-50 border border-slate-200/60">
                                    @if($tour->thumbnail)
                                        <img src="{{ asset('storage/' . $tour->thumbnail) }}" alt="{{ $tour->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $tour->name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[11px] text-slate-500">{{ $tour->days }} hari</span>
                                        @if($tour->includes_hotel)
                                            <span class="text-[10px] bg-emerald-500/10 text-emerald-600 px-1.5 py-0.5 rounded font-semibold">Hotel included</span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-blue-600 mt-0.5">
                                        Rp {{ number_format($tour->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @if(in_array($tour->id, $selectedTourIds))
                                        <div class="w-5 h-5 rounded bg-blue-500 flex items-center justify-center">
                                            <i class="fas fa-check text-white text-[9px]"></i>
                                        </div>
                                    @else
                                        <div class="w-5 h-5 rounded border-2 border-slate-300 bg-white"></div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-slate-500 text-sm bg-white">
                                <i class="fas fa-route text-2xl mb-2 block text-slate-400"></i>
                                Belum ada paket tour tersedia
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Summary & Notes -->
                @if($selectedHotel || count($selectedTourIds) > 0)
                <div class="bg-slate-900 border border-slate-950 rounded-2xl p-5 text-white shadow-xl">
                    <h3 class="font-bold text-sm mb-3 flex items-center gap-2">
                        <i class="fas fa-receipt text-laut"></i> Ringkasan Pilihan
                    </h3>

                    @if($selectedHotel)
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-slate-400">Hotel:</span>
                            <span class="font-semibold text-white truncate ml-2 max-w-[60%]">{{ $selectedHotel->name }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mb-3">
                            <span class="text-slate-400">Mulai dari:</span>
                            <span class="font-bold text-laut">Rp {{ number_format($selectedHotel->single_room_price, 0, ',', '.') }}/malam</span>
                        </div>
                    @endif

                    @foreach($selectedTours as $st)
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-slate-400 truncate mr-2">+ {{ $st->name }}</span>
                            <span class="font-semibold text-white shrink-0">Rp {{ number_format($st->price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <!-- Custom Notes -->
                    <div class="mt-4">
                        <label class="text-[11px] text-slate-400 uppercase tracking-wider font-bold block mb-1.5">Catatan khusus</label>
                        <textarea wire:model="customNotes" rows="2"
                            placeholder="Tambahkan catatan atau permintaan khusus..."
                            class="w-full bg-white/10 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-laut placeholder:text-white/40 resize-none"></textarea>
                    </div>

                    <!-- Save + Book Buttons -->
                    <div class="mt-4 space-y-2 font-black">
                        <button wire:click="saveSelection"
                            class="w-full bg-laut hover:bg-laut/90 text-white font-bold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Simpan Pilihan
                        </button>

                        @if($selectedHotel)
                            <a href="{{ route('hotels.book', $selectedHotel->id) }}"
                               class="w-full bg-white/10 border border-white/10 text-white hover:bg-white/20 font-bold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                <i class="fas fa-calendar-check text-laut"></i> Booking Hotel Sekarang
                            </a>
                        @endif

                        @foreach($selectedTours as $st)
                            <a href="{{ route('paket-tour.create', $st->id) }}"
                               class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                <i class="fas fa-suitcase-rolling"></i> Book: {{ Str::limit($st->name, 24) }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Not logged in prompt -->
                @guest
                    <div class="bg-amber-50 border border-amber-200/60 rounded-2xl p-4 text-sm reveal">
                        <p class="text-amber-800 font-semibold mb-1"><i class="fas fa-info-circle text-amber-500 mr-1"></i> Belum login</p>
                        <p class="text-slate-500 text-xs mb-3">Simpan pilihan ini ke akun Anda agar tidak hilang.</p>
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                           class="inline-block text-xs bg-laut text-white font-black px-4 py-2 rounded-lg hover:bg-laut/90 transition">
                            Login Sekarang
                        </a>
                    </div>
                @endguest

                </div>
            {{-- end right col --}}
        </div>
        {{-- end grid AI + picker --}}

    </div>
    {{-- end max-w-7xl --}}
</div>
