<div class="min-h-screen bg-gray-50">

    <!-- Top Bar -->
    <div class="bg-ocean-900 border-b border-white/10 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-sunset-500 flex items-center justify-center">
                    <i class="fas fa-robot text-white text-xs"></i>
                </div>
                <div>
                    <p class="text-white font-bold text-sm font-montserrat">Rekomendasi dari Ara</p>
                    <p class="text-gray-400 text-[11px]">Itinerary personal NTT Anda sudah siap</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}"
                   class="text-xs text-gray-400 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-white/5">
                    <i class="fas fa-home"></i> <span class="hidden sm:inline">Beranda</span>
                </a>
                <button onclick="window.print()"
                   class="text-xs text-gray-400 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-white/5">
                    <i class="fas fa-print"></i> <span class="hidden sm:inline">Cetak</span>
                </button>
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-xs bg-sunset-500 hover:bg-sunset-600 text-white font-semibold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                        <i class="fas fa-th-large"></i> <span class="hidden sm:inline">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                       class="text-xs bg-sunset-500 hover:bg-sunset-600 text-white font-semibold px-3 py-1.5 rounded-lg transition">
                        Login & Simpan
                    </a>
                @endauth
            </div>
        </div>
    </div>

    @if($showSuccess)
        <div class="bg-green-50 border-b border-green-200 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-green-700 text-sm font-medium max-w-7xl mx-auto w-full">
                <i class="fas fa-check-circle text-green-500"></i>
                Pilihan Anda berhasil disimpan!
                <button wire:click="dismissNotification" class="ml-auto text-green-400 hover:text-green-600"><i class="fas fa-times text-xs"></i></button>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            <!-- ===== LEFT: AI Recommendation Text ===== -->
            <div class="lg:col-span-3">
                <div class="bg-ocean-900 rounded-2xl overflow-hidden shadow-xl sticky top-20">
                    <div class="px-5 py-4 border-b border-white/10 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-sunset-500/20 border border-sunset-500/30 flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-sunset-500 text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-sm">Itinerary Ara</h2>
                            <p class="text-gray-500 text-[11px]">Rencana perjalanan personal Anda</p>
                        </div>
                    </div>

                    <div class="overflow-y-auto p-5 text-sm text-gray-200 leading-relaxed space-y-2" style="max-height: calc(100vh - 180px);">
                        @php
                            $text = e($session->recommendation_raw);
                            $text = preg_replace('/### (.*?)(\n|$)/', '<h4 class="text-sm font-bold text-sunset-400 mt-5 mb-1.5 flex items-center gap-2"><i class="fas fa-circle text-[5px]"></i> $1</h4>', $text);
                            $text = preg_replace('/## (.*?)(\n|$)/', '<h3 class="text-base font-extrabold text-white mt-7 mb-3 border-b border-white/10 pb-2">$1</h3>', $text);
                            $text = preg_replace('/# (.*?)(\n|$)/', '<h2 class="text-lg font-black text-white mt-8 mb-3 border-b-2 border-sunset-500 pb-2">$1</h2>', $text);
                            $text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="text-sunset-400 font-semibold">$1</strong>', $text);
                            $text = preg_replace('/^- (.*?)$/m', '<li class="text-gray-300 ml-3 list-disc pl-1 py-0.5">$1</li>', $text);
                            $text = nl2br($text);
                        @endphp
                        {!! $text !!}
                    </div>
                </div>
            </div>

            <!-- ===== RIGHT: Hotel & Tour Selection ===== -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Hotel Picker -->
                <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-hotel text-sunset-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-ocean-900 text-sm">Pilih Hotel</h3>
                            <p class="text-gray-400 text-[11px]">Klik hotel untuk memilih atau ganti</p>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
                        @forelse($hotels as $hotel)
                            <div wire:click="selectHotel({{ $hotel->id }})"
                                 class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all
                                        {{ $selectedHotelId == $hotel->id
                                            ? 'bg-sunset-500/5 border-l-2 border-sunset-500'
                                            : 'hover:bg-gray-50 border-l-2 border-transparent' }}">

                                <!-- Thumbnail -->
                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                                    @if($hotel->image)
                                        <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-hotel"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $hotel->name }}</p>
                                    <p class="text-[11px] text-gray-400 truncate">{{ $hotel->location }}</p>
                                    <p class="text-xs font-bold text-sunset-500 mt-0.5">
                                        Rp {{ number_format($hotel->single_room_price, 0, ',', '.') }}<span class="text-gray-400 font-normal">/malam</span>
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @if($selectedHotelId == $hotel->id)
                                        <div class="w-5 h-5 rounded-full bg-sunset-500 flex items-center justify-center">
                                            <i class="fas fa-check text-white text-[9px]"></i>
                                        </div>
                                    @else
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-200"></div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                <i class="fas fa-hotel text-2xl mb-2 block text-gray-200"></i>
                                Belum ada hotel tersedia
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tour Package Picker -->
                <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                            <i class="fas fa-route text-ocean-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-ocean-900 text-sm">Tambah Paket Tour</h3>
                            <p class="text-gray-400 text-[11px]">Pilih satu atau beberapa paket</p>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                        @forelse($tourPackages as $tour)
                            <div wire:click="toggleTour({{ $tour->id }})"
                                 class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all
                                        {{ in_array($tour->id, $selectedTourIds)
                                            ? 'bg-blue-50/60 border-l-2 border-ocean-600'
                                            : 'hover:bg-gray-50 border-l-2 border-transparent' }}">

                                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                                    @if($tour->thumbnail)
                                        <img src="{{ asset('storage/' . $tour->thumbnail) }}" alt="{{ $tour->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $tour->name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[11px] text-gray-400">{{ $tour->days }} hari</span>
                                        @if($tour->includes_hotel)
                                            <span class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded font-semibold">Hotel included</span>
                                        @endif
                                    </div>
                                    <p class="text-xs font-bold text-ocean-600 mt-0.5">
                                        Rp {{ number_format($tour->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @if(in_array($tour->id, $selectedTourIds))
                                        <div class="w-5 h-5 rounded bg-ocean-600 flex items-center justify-center">
                                            <i class="fas fa-check text-white text-[9px]"></i>
                                        </div>
                                    @else
                                        <div class="w-5 h-5 rounded border-2 border-gray-200"></div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                <i class="fas fa-route text-2xl mb-2 block text-gray-200"></i>
                                Belum ada paket tour tersedia
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Summary & Notes -->
                @if($selectedHotel || count($selectedTourIds) > 0)
                <div class="bg-ocean-900 rounded-2xl p-5 text-white">
                    <h3 class="font-bold text-sm mb-3 flex items-center gap-2">
                        <i class="fas fa-receipt text-sunset-500"></i> Ringkasan Pilihan
                    </h3>

                    @if($selectedHotel)
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-400">Hotel:</span>
                            <span class="font-semibold text-white truncate ml-2 max-w-[60%]">{{ $selectedHotel->name }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm mb-3">
                            <span class="text-gray-400">Mulai dari:</span>
                            <span class="font-bold text-sunset-500">Rp {{ number_format($selectedHotel->single_room_price, 0, ',', '.') }}/malam</span>
                        </div>
                    @endif

                    @foreach($selectedTours as $st)
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-gray-400 truncate mr-2">+ {{ $st->name }}</span>
                            <span class="font-semibold text-white shrink-0">Rp {{ number_format($st->price, 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <!-- Custom Notes -->
                    <div class="mt-4">
                        <label class="text-[11px] text-gray-400 uppercase tracking-wider font-bold block mb-1.5">Catatan khusus</label>
                        <textarea wire:model="customNotes" rows="2"
                            placeholder="Tambahkan catatan atau permintaan khusus..."
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:ring-1 focus:ring-sunset-500 placeholder:text-gray-600 resize-none"></textarea>
                    </div>

                    <!-- Save + Book Buttons -->
                    <div class="mt-4 space-y-2">
                        <button wire:click="saveSelection"
                            class="w-full bg-sunset-500 hover:bg-sunset-600 text-white font-bold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i> Simpan Pilihan
                        </button>

                        @if($selectedHotel)
                            <a href="{{ route('hotels.book', $selectedHotel->id) }}"
                               class="w-full bg-white text-ocean-900 hover:bg-gray-100 font-bold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                <i class="fas fa-calendar-check text-sunset-500"></i> Booking Hotel Sekarang
                            </a>
                        @endif

                        @foreach($selectedTours as $st)
                            <a href="{{ route('paket-tour.create', $st->id) }}"
                               class="w-full bg-ocean-800 hover:bg-ocean-700 text-white font-bold py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                <i class="fas fa-suitcase-rolling"></i> Book: {{ Str::limit($st->name, 24) }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Not logged in prompt -->
                @guest
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm">
                        <p class="text-amber-800 font-semibold mb-1"><i class="fas fa-info-circle text-amber-500 mr-1"></i> Belum login</p>
                        <p class="text-amber-700 text-xs mb-3">Simpan pilihan ini ke akun Anda agar tidak hilang.</p>
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}"
                           class="inline-block text-xs bg-sunset-500 text-white font-bold px-4 py-2 rounded-lg hover:bg-sunset-600 transition">
                            Login Sekarang
                        </a>
                    </div>
                @endguest

            </div>
            {{-- end right col --}}
        </div>
    </div>
</div>
