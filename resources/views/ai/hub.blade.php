@extends('layouts.app')

@section('title', 'AI Engine — Pesona NTT')

@section('content')
<div class="pt-24 pb-20 min-h-screen" x-data="aiHub()">

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
        <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl overflow-hidden p-8 md:p-12 border border-white/5">

            {{-- BG grid decoration --}}
            <div class="absolute inset-0 opacity-[0.03]"
                 style="background-image: linear-gradient(rgba(255,255,255,.3) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.3) 1px, transparent 1px); background-size: 40px 40px;"></div>

            {{-- Glow --}}
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-laut rounded-full opacity-10 blur-[80px]"></div>
            <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-blue-500 rounded-full opacity-10 blur-[80px]"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-8">

                <div class="flex-1">
                    <div class="inline-flex items-center gap-2 bg-laut/10 border border-laut/25 text-laut text-xs font-bold px-3 py-1.5 rounded-full mb-4">
                        <span class="w-1.5 h-1.5 bg-laut rounded-full animate-pulse"></span>
                        Powered by Claude AI · Anthropic
                    </div>

                    <h1 class="text-4xl md:text-5xl font-black text-white font-serif tracking-tight leading-tight mb-3">
                        AI Engine<br>
                        <span class="text-laut">Pesona NTT</span>
                    </h1>
                    <p class="text-slate-400 text-base leading-relaxed max-w-xl">
                        Platform ini mengintegrasikan <strong class="text-white">5 fitur kecerdasan buatan</strong>
                        yang saling terhubung untuk memberikan pengalaman wisata yang personal dan cerdas.
                    </p>

                    <div class="flex flex-wrap gap-2 mt-5">
                        @foreach([
                            ['icon' => 'fa-robot',         'label' => 'Ara Travel Guide'],
                            ['icon' => 'fa-search',        'label' => 'Smart Search'],
                            ['icon' => 'fa-star',          'label' => 'Review Analyzer'],
                            ['icon' => 'fa-calendar-alt',  'label' => 'Best Time AI'],
                            ['icon' => 'fa-magic',         'label' => 'Personal Rec.'],
                        ] as $f)
                        <span class="inline-flex items-center gap-1.5 bg-white/5 border border-white/10 text-slate-300 text-xs font-semibold px-3 py-1.5 rounded-full">
                            <i class="fas {{ $f['icon'] }} text-laut text-[10px]"></i>
                            {{ $f['label'] }}
                        </span>
                        @endforeach
                    </div>
                </div>

                {{-- Model card --}}
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 min-w-[220px] shrink-0">
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-3">Model Info</p>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-xs">Provider</span>
                            <span class="text-white text-xs font-bold">OmniRoute</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-xs">Model</span>
                            <span class="text-laut text-xs font-bold font-mono">claude-sonnet-4-6</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-xs">Format</span>
                            <span class="text-emerald-400 text-xs font-bold">OpenAI-compat.</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-xs">Cache</span>
                            <span class="text-blue-400 text-xs font-bold">Laravel Cache</span>
                        </div>
                        <div class="pt-2 border-t border-white/10">
                            <span class="text-slate-500 text-[10px]">Framework: Laravel 11 + Livewire 3</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         TABS
    ═══════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Tab navigation --}}
        <div class="flex overflow-x-auto gap-2 pb-1 mb-6 scrollbar-none">
            @foreach([
                ['id' => 'ara',     'icon' => 'fa-robot',        'label' => 'Ara Guide',        'color' => 'sunset'],
                ['id' => 'search',  'icon' => 'fa-search',       'label' => 'Smart Search',     'color' => 'blue'],
                ['id' => 'review',  'icon' => 'fa-star',         'label' => 'Review Analyzer',  'color' => 'amber'],
                ['id' => 'time',    'icon' => 'fa-calendar-alt', 'label' => 'Best Time AI',     'color' => 'emerald'],
                ['id' => 'rec',     'icon' => 'fa-magic',        'label' => 'Personal Rec.',    'color' => 'purple'],
            ] as $tab)
            <button
                @click="active = '{{ $tab['id'] }}'"
                :class="active === '{{ $tab['id'] }}'
                    ? 'bg-slate-800 border-slate-600 text-white shadow-sm'
                    : 'bg-white border-slate-200/60 text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold transition-all shrink-0">
                <i class="fas {{ $tab['icon'] }} text-xs"
                   :class="active === '{{ $tab['id'] }}' ? 'text-laut' : 'text-slate-400'"></i>
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        {{-- ─────────────────────────────────────
             TAB 1: ARA GUIDE
        ───────────────────────────────────── --}}
        <div x-show="active === 'ara'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left: info --}}
                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-laut flex items-center justify-center">
                                <i class="fas fa-robot text-white text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-black text-slate-800 font-serif tracking-tight">Ara — Travel Planning AI</h2>
                                <p class="text-xs text-slate-500 font-semibold">Chatbot pemandu wisata personal</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Ara adalah asisten AI yang memandu user merencanakan perjalanan ke NTT melalui tanya jawab percakapan.
                                Menggunakan <strong class="text-slate-800">multi-turn conversation</strong> dengan riwayat pesan penuh
                                yang dikirimkan ke Claude di setiap request.
                            </p>

                            <div class="grid grid-cols-2 gap-3">
                                @foreach([
                                    ['icon' => 'fa-comments',       'label' => 'Multi-turn Chat',    'desc' => 'Ingat konteks percakapan'],
                                    ['icon' => 'fa-map-marked-alt', 'label' => 'Itinerary Generator', 'desc' => 'Buat rencana perjalanan'],
                                    ['icon' => 'fa-flag-checkered', 'label' => 'Trigger Detection',  'desc' => 'Tag [RECOMMENDATION_READY]'],
                                    ['icon' => 'fa-database',       'label' => 'Session Persisted',  'desc' => 'Disimpan di database'],
                                ] as $f)
                                <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <i class="fas {{ $f['icon'] }} text-laut text-sm mt-0.5 shrink-0"></i>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ $f['label'] }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $f['desc'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <a href="{{ route('travel.chat') }}"
                               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-laut hover:bg-laut/90 text-white text-sm font-bold transition-all">
                                <i class="fas fa-robot text-xs"></i>
                                Coba Chat dengan Ara →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right: system prompt --}}
                <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-slate-500 text-xs font-mono">TravelChatService.php — system_prompt</span>
                    </div>
                    <div class="p-5 overflow-y-auto" style="max-height: 420px">
                        <pre class="text-xs text-slate-300 leading-relaxed whitespace-pre-wrap font-mono"><span class="text-slate-500">// System Prompt dikirim di setiap request ke Claude</span>

<span class="text-emerald-400">Kamu adalah Ara</span>, pemandu wisata NTT
yang ramah dan antusias. Tugasmu
membantu merencanakan perjalanan ke
<span class="text-sky-400">hidden gems NTT</span> dengan tanya jawab
santai — <span class="text-amber-400">SATU pertanyaan per pesan</span>.

<span class="text-purple-400">FASE 1</span> — Tanya satu per satu:
  1. Wilayah NTT yang ingin dikunjungi
     (Flores, Sumba, Timor, Labuan Bajo,
     Rote, Alor)?
  2. Total budget perjalanan?
  3. Berapa orang dan berapa hari?
  4. Jenis pengalaman (alam, budaya,
     petualangan, relaksasi)?
  5. Preferensi akomodasi?

<span class="text-purple-400">FASE 2</span> — Setelah info cukup:
  - Destinasi hidden gem + alasan uniknya
  - Itinerary per hari (ringkas)
  - 2 pilihan akomodasi + estimasi harga
  - Rekomendasi kuliner lokal
  - Estimasi budget total
  - Tips praktis

<span class="text-slate-500">// Aturan:</span>
<span class="text-amber-400">- Satu pertanyaan per pesan</span>
- Jawab sesuai bahasa user
- Respons ringkas dan padat

<span class="text-red-400">PENTING:</span> Saat itinerary selesai, akhiri
dengan tag:
<span class="text-laut font-bold">[RECOMMENDATION_READY]</span></pre>
                    </div>
                    <div class="border-t border-white/5 px-5 py-3 flex items-center gap-2">
                        <span class="text-[10px] text-slate-600 font-mono">max_tokens: 1024 · stream: false · history: all messages</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────
             TAB 2: SMART SEARCH
        ───────────────────────────────────── --}}
        <div x-show="active === 'search'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="space-y-5">
                    {{-- Info card --}}
                    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center">
                                <i class="fas fa-search text-white text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-black text-slate-800 font-serif tracking-tight">Smart Search</h2>
                                <p class="text-xs text-slate-500 font-semibold">Natural language → structured filters</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                User mengetik pencarian bebas dalam Bahasa Indonesia. AI mengekstrak filter terstruktur
                                (<code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono text-slate-700">category, max_price, min_rating, location, keywords</code>)
                                yang langsung dipakai sebagai parameter query database.
                            </p>

                            {{-- Flow diagram --}}
                            <div class="flex items-center gap-2 text-[11px] font-semibold overflow-x-auto py-1">
                                <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1.5 rounded-lg shrink-0">Query teks bebas</span>
                                <i class="fas fa-arrow-right text-slate-400 text-xs shrink-0"></i>
                                <span class="bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-1.5 rounded-lg shrink-0">Claude AI (JSON)</span>
                                <i class="fas fa-arrow-right text-slate-400 text-xs shrink-0"></i>
                                <span class="bg-slate-50 text-slate-700 border border-slate-200 px-2.5 py-1.5 rounded-lg shrink-0">Laravel Query</span>
                                <i class="fas fa-arrow-right text-slate-400 text-xs shrink-0"></i>
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1.5 rounded-lg shrink-0">Hasil</span>
                            </div>

                            {{-- Live Demo --}}
                            <div class="border border-slate-200 rounded-xl overflow-hidden">
                                <div class="bg-slate-50 border-b border-slate-200 px-4 py-2.5 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-xs font-bold text-slate-600">Live Demo</span>
                                </div>
                                <div class="p-4 space-y-3">
                                    <div class="flex gap-2">
                                        <input x-model="searchQuery" type="text"
                                            placeholder='cth: "pantai murah di Flores untuk 3 orang"'
                                            class="flex-1 text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-400 bg-white text-slate-800 placeholder:text-slate-400">
                                        <button @click="runSearch"
                                            :disabled="searchLoading"
                                            class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm font-bold transition-all">
                                            <i class="fas fa-search text-xs" x-show="!searchLoading"></i>
                                            <svg x-show="searchLoading" class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Parsed filters output --}}
                                    <div x-show="searchFilters" class="bg-slate-900 rounded-lg p-3">
                                        <p class="text-[10px] text-slate-500 font-mono mb-1.5">// Hasil ekstraksi Claude →</p>
                                        <pre class="text-xs text-emerald-400 font-mono" x-text="JSON.stringify(searchFilters, null, 2)"></pre>
                                    </div>

                                    {{-- Results --}}
                                    <div x-show="searchResults.length > 0" class="space-y-2">
                                        <p class="text-xs text-slate-500 font-semibold" x-text="`${searchResults.length} destinasi ditemukan`"></p>
                                        <template x-for="r in searchResults.slice(0,3)" :key="r.id">
                                            <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-lg border border-slate-100">
                                                <div class="w-8 h-8 rounded-lg bg-slate-200 overflow-hidden shrink-0">
                                                    <img :src="r.image ? `/storage/${r.image}` : ''" class="w-full h-full object-cover"
                                                         onerror="this.style.display='none'">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-slate-800 truncate" x-text="r.name"></p>
                                                    <p class="text-[10px] text-slate-500 truncate" x-text="r.location"></p>
                                                </div>
                                                <span class="text-xs font-bold text-amber-500 flex items-center gap-0.5">
                                                    <i class="fas fa-star text-[9px]"></i>
                                                    <span x-text="r.rating ?? '-'"></span>
                                                </span>
                                            </div>
                                        </template>
                                    </div>
                                    <div x-show="searchError" class="text-xs text-red-500 bg-red-50 rounded-lg p-2.5" x-text="searchError"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Prompt code --}}
                <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-slate-500 text-xs font-mono">ClaudeService.php — parseSearchQuery()</span>
                    </div>
                    <div class="p-5 overflow-y-auto" style="max-height: 480px">
                        <pre class="text-xs text-slate-300 leading-relaxed whitespace-pre-wrap font-mono"><span class="text-slate-500">// Input: query string bebas dari user</span>
<span class="text-slate-500">// Output: JSON structured filters</span>

<span class="text-emerald-400">Kamu adalah AI parser.</span> Ekstrak informasi
dari query pencarian wisata berikut
ke format JSON.

Query: <span class="text-amber-400">"{$query}"</span>

Kembalikan <span class="text-red-400">HANYA JSON valid</span>
(tanpa penjelasan, tanpa markdown)
dengan struktur:

<span class="text-sky-400">{
  "category": "Beach|Mountain|Culture
              |Nature|null",
  "max_price": number_or_null,
  "min_rating": number_or_null,
  "keywords": "string_or_null",
  "location": "string_or_null"
}</span>

<span class="text-slate-500">// Contoh:</span>
<span class="text-slate-400">Query: "pantai murah di flores rating bagus"</span>
<span class="text-emerald-400">→ {"category":"Beach","max_price":null,
   "min_rating":4,"keywords":"murah",
   "location":"flores"}</span>

<span class="text-slate-500">// Config:
// max_tokens: 256
// Cache: tidak (real-time query)
// Pembersihan: regex strip ```json```</span></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────
             TAB 3: REVIEW ANALYZER
        ───────────────────────────────────── --}}
        <div x-show="active === 'review'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500 flex items-center justify-center">
                                <i class="fas fa-star text-white text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-black text-slate-800 font-serif tracking-tight">Review Analyzer</h2>
                                <p class="text-xs text-slate-500 font-semibold">Ringkasan ulasan dengan AI + caching</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Mengambil hingga <strong class="text-slate-800">30 ulasan terbaru</strong> dari destinasi atau hotel,
                                mengirimkannya ke Claude, dan menghasilkan ringkasan 3-4 kalimat yang objektif.
                                Hasilnya di-<strong class="text-slate-800">cache 24 jam</strong> per item untuk efisiensi.
                            </p>

                            <div class="grid grid-cols-2 gap-3">
                                @foreach([
                                    ['icon' => 'fa-clock', 'label' => 'Cache 24 Jam',    'desc' => 'Hemat API calls', 'color' => 'text-blue-600'],
                                    ['icon' => 'fa-star',  'label' => 'Max 30 Reviews',  'desc' => 'Input teratas terbaru', 'color' => 'text-amber-500'],
                                    ['icon' => 'fa-hotel', 'label' => 'Dual Target',     'desc' => 'Destination + Hotel', 'color' => 'text-emerald-600'],
                                    ['icon' => 'fa-bolt',  'label' => '512 Token Output', 'desc' => 'Ringkas & cepat', 'color' => 'text-purple-600'],
                                ] as $f)
                                <div class="flex gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <i class="fas {{ $f['icon'] }} {{ $f['color'] }} text-sm mt-0.5 shrink-0"></i>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ $f['label'] }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $f['desc'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Where it appears --}}
                            <div class="bg-amber-50 border border-amber-200/60 rounded-xl p-4">
                                <p class="text-xs font-bold text-amber-800 mb-2 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i> Digunakan di:
                                </p>
                                <ul class="space-y-1">
                                    <li class="text-xs text-amber-700 flex items-center gap-2">
                                        <i class="fas fa-check text-amber-500 text-[10px]"></i>
                                        Halaman detail destinasi (tab Reviews)
                                    </li>
                                    <li class="text-xs text-amber-700 flex items-center gap-2">
                                        <i class="fas fa-check text-amber-500 text-[10px]"></i>
                                        Halaman detail hotel (bagian Ulasan)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-slate-500 text-xs font-mono">ClaudeService.php — summarizeReviews()</span>
                    </div>
                    <div class="p-5 overflow-y-auto" style="max-height: 480px">
                        <pre class="text-xs text-slate-300 leading-relaxed whitespace-pre-wrap font-mono"><span class="text-slate-500">// Cache key: ai.review_summary.{type}.{id}
// TTL: 86400 detik (24 jam)
// Hanya hits API jika cache miss</span>

<span class="text-emerald-400">Kamu adalah AI asisten wisata.</span>
Berikut adalah kumpulan ulasan
pengunjung tentang sebuah
<span class="text-amber-400">{$type}</span> wisata:

<span class="text-sky-400">- [5/5] Pemandangan luar biasa, air...
- [4/5] Akses jalan agak sulit tapi...
- [3/5] Fasilitas perlu ditingkatkan...
... (hingga 30 ulasan)</span>

Buatkan ringkasan singkat
(<span class="text-purple-400">3-4 kalimat</span>) dalam Bahasa Indonesia:

1. Poin-poin <span class="text-emerald-400">positif</span> yang sering disebut
2. <span class="text-red-400">Kekurangan</span> yang perlu diperhatikan
3. Kesimpulan: direkomendasikan?

Tulis <span class="text-red-400">langsung ringkasannya</span>
tanpa intro seperti
"Berikut ringkasannya:"

<span class="text-slate-500">// Config:
// max_tokens: 512
// type: destination | hotel
// Input: rating + body per review</span></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────
             TAB 4: BEST TIME AI
        ───────────────────────────────────── --}}
        <div x-show="active === 'time'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-black text-slate-800 font-serif tracking-tight">Best Time to Visit AI</h2>
                                <p class="text-xs text-slate-500 font-semibold">Saran waktu terbaik berbasis konteks lokasi</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Mengirimkan nama destinasi, lokasi, dan kategori ke Claude untuk mendapatkan
                                saran <strong class="text-slate-800">waktu terbaik berkunjung</strong> dalam 2-3 kalimat.
                                Di-cache <strong class="text-slate-800">7 hari</strong> menggunakan MD5 dari nama destinasi sebagai key.
                            </p>

                            {{-- Cache strategy --}}
                            <div class="bg-emerald-50 border border-emerald-200/60 rounded-xl p-4">
                                <p class="text-xs font-bold text-emerald-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-database"></i> Cache Strategy
                                </p>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-600">Cache Key</span>
                                        <code class="bg-slate-800 text-emerald-400 px-2 py-0.5 rounded font-mono">ai.best_time.{md5(name)}</code>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-600">TTL</span>
                                        <span class="text-emerald-700 font-bold">604800 detik = 7 hari</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-600">Driver</span>
                                        <span class="text-emerald-700 font-bold">Laravel Cache (file/redis)</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-4">
                                <p class="text-xs font-bold text-slate-700 mb-2">Contoh output:</p>
                                <p class="text-xs text-slate-600 italic leading-relaxed">
                                    "Waktu terbaik mengunjungi Pantai Pink Komodo adalah bulan April hingga Oktober,
                                    saat musim kering dengan kondisi laut yang tenang dan visibilitas snorkeling
                                    yang optimal. Hindari Desember–Februari karena gelombang tinggi dan curah hujan
                                    intens di wilayah Flores."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-slate-500 text-xs font-mono">ClaudeService.php — bestTimeToVisit()</span>
                    </div>
                    <div class="p-5 overflow-y-auto" style="max-height: 480px">
                        <pre class="text-xs text-slate-300 leading-relaxed whitespace-pre-wrap font-mono"><span class="text-slate-500">// Cache::remember 7 hari per destinasi</span>

<span class="text-emerald-400">Kamu adalah pakar wisata NTT</span>
(Nusa Tenggara Timur), Indonesia.

Berikan rekomendasi
<span class="text-amber-400">waktu terbaik untuk mengunjungi</span>:

- Nama:     <span class="text-sky-400">{$destinationName}</span>
- Lokasi:   <span class="text-sky-400">{$location}</span>
- Kategori: <span class="text-sky-400">{$category}</span>

Jawab dalam <span class="text-purple-400">2-3 kalimat</span> Bahasa
Indonesia yang informatif dan praktis.

Sebutkan:
✓ Bulan terbaik
✓ Alasan (cuaca, musim, kondisi alam)

<span class="text-red-400">Jangan gunakan intro</span> seperti
"Tentu saja" atau "Berikut adalah".

<span class="text-slate-500">// Config:
// max_tokens: 256
// Cache: md5(name) → 604800s</span></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────
             TAB 5: PERSONAL RECOMMENDATIONS
        ───────────────────────────────────── --}}
        <div x-show="active === 'rec'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="space-y-5">
                    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-600 flex items-center justify-center">
                                <i class="fas fa-magic text-white text-sm"></i>
                            </div>
                            <div>
                                <h2 class="font-black text-slate-800 font-serif tracking-tight">Personal Recommendations</h2>
                                <p class="text-xs text-slate-500 font-semibold">AI belajar dari riwayat user → saran destinasi</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Menganalisis riwayat <strong class="text-slate-800">booking hotel & wishlist</strong> user,
                                lalu merekomendasikan 3 destinasi yang paling relevan dari database.
                                Claude mengembalikan <strong class="text-slate-800">JSON array</strong> berisi ID + alasan singkat.
                            </p>

                            {{-- Pipeline --}}
                            <div class="space-y-2">
                                @foreach([
                                    ['step' => '1', 'label' => 'Kumpulkan riwayat', 'desc' => 'Hotel bookings + Wishlist user (max 10)', 'color' => 'bg-purple-500'],
                                    ['step' => '2', 'label' => 'Format sebagai teks', 'desc' => '- NamaItem (Type, kategori: X)', 'color' => 'bg-blue-500'],
                                    ['step' => '3', 'label' => 'Kirim ke Claude', 'desc' => 'Riwayat + daftar 30 destinasi aktif', 'color' => 'bg-indigo-500'],
                                    ['step' => '4', 'label' => 'Parse JSON response', 'desc' => '[{id, reason}, ...] → query DB', 'color' => 'bg-emerald-500'],
                                ] as $s)
                                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="w-6 h-6 rounded-full {{ $s['color'] }} text-white text-[10px] font-black flex items-center justify-center shrink-0">
                                        {{ $s['step'] }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ $s['label'] }}</p>
                                        <p class="text-[11px] text-slate-500 font-mono">{{ $s['desc'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            @auth
                                <a href="{{ route('ai.recommendations') }}"
                                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold transition-all">
                                    <i class="fas fa-magic text-xs"></i>
                                    Lihat Rekomendasi Saya →
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold transition-all">
                                    <i class="fas fa-sign-in-alt text-xs"></i>
                                    Login untuk Lihat Rekomendasi
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-2xl border border-white/5 overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-slate-500 text-xs font-mono">ClaudeService.php — personalRecommendations()</span>
                    </div>
                    <div class="p-5 overflow-y-auto" style="max-height: 480px">
                        <pre class="text-xs text-slate-300 leading-relaxed whitespace-pre-wrap font-mono"><span class="text-emerald-400">Kamu adalah AI rekomendasi wisata NTT.</span>
Berdasarkan riwayat perjalanan user:

<span class="text-amber-400">- Komodo Resort (Hotel, Akomodasi)
- Pantai Pink (Destination, Beach)
- Kelimutu (Destination, Mountain)
... (dari DB: hotel_bookings + wishlists)</span>

Dari daftar destinasi yang tersedia:
<span class="text-sky-400">- ID:12 | Pantai Nihiwatu | Beach | Sumba
- ID:7  | Bukit Wairinding | Mountain | Sumba
- ID:23 | Desa Ratenggaro | Culture | Sumba
... (30 destinasi aktif, sort by rating)</span>

Rekomendasikan <span class="text-purple-400">3 destinasi</span> yang
paling cocok untuk user ini.

Format jawaban (<span class="text-red-400">HANYA JSON array</span>,
tanpa penjelasan lain):

<span class="text-emerald-400">[
  {"id": 12, "reason": "Cocok karena suka pantai"},
  {"id": 7,  "reason": "Suka alam & trekking"},
  {"id": 23, "reason": "Pernah ke area Sumba"}
]</span>

<span class="text-slate-500">// Config:
// max_tokens: 512
// No cache (personalized per user)
// Requires auth middleware</span></pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             TECH STACK FOOTER
        ═══════════════════════════════════════════ --}}
        <div class="mt-10 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-5 text-center">Arsitektur Teknologi</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach([
                    ['icon' => 'fa-brain',      'label' => 'Claude AI',      'sub' => 'Anthropic',      'color' => 'text-laut',  'bg' => 'bg-laut/10'],
                    ['icon' => 'fa-laravel',    'label' => 'Laravel 11',     'sub' => 'PHP Framework',  'color' => 'text-red-600',     'bg' => 'bg-red-500/10'],
                    ['icon' => 'fa-bolt',       'label' => 'Livewire 3',     'sub' => 'Reactive UI',    'color' => 'text-pink-500',    'bg' => 'bg-pink-500/10'],
                    ['icon' => 'fa-wind',       'label' => 'Tailwind CSS',   'sub' => 'Styling',        'color' => 'text-sky-500',     'bg' => 'bg-sky-500/10'],
                    ['icon' => 'fa-mountain',   'label' => 'Alpine.js',      'sub' => 'Interactivity',  'color' => 'text-emerald-500', 'bg' => 'bg-emerald-500/10'],
                    ['icon' => 'fa-database',   'label' => 'MySQL',          'sub' => 'Database',       'color' => 'text-blue-500',    'bg' => 'bg-blue-500/10'],
                ] as $t)
                <div class="flex flex-col items-center gap-2 p-4 {{ $t['bg'] }} rounded-xl border border-current border-opacity-10 text-center">
                    <div class="w-9 h-9 rounded-xl {{ $t['bg'] }} flex items-center justify-center">
                        <i class="fas {{ $t['icon'] }} {{ $t['color'] }}"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">{{ $t['label'] }}</p>
                        <p class="text-[10px] text-slate-500">{{ $t['sub'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function aiHub() {
    return {
        active: 'ara',

        // Smart Search demo
        searchQuery: '',
        searchLoading: false,
        searchFilters: null,
        searchResults: [],
        searchError: null,

        async runSearch() {
            if (!this.searchQuery.trim()) return;
            this.searchLoading = true;
            this.searchFilters = null;
            this.searchResults = [];
            this.searchError = null;

            try {
                const res = await fetch(`/ai/smart-search?q=${encodeURIComponent(this.searchQuery)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.error) {
                    this.searchError = data.error;
                } else {
                    this.searchFilters = data.filters;
                    this.searchResults = data.results ?? [];
                }
            } catch (e) {
                this.searchError = 'Gagal terhubung ke server.';
            } finally {
                this.searchLoading = false;
            }
        }
    }
}
</script>
@endpush
@endsection
