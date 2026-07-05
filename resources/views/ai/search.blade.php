@extends('layouts.app')
@section('title', 'AI Smart Search — Pesona NTT')

@section('content')
<div class="pt-32 pb-20 min-h-screen bg-gray-50">
<div class="max-w-5xl mx-auto px-4">

    {{-- Header --}}
    <div class="text-center mb-12">
        <span class="inline-flex items-center gap-2 bg-laut/10 text-laut text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full mb-4">
            <i class="fas fa-robot"></i> AI Smart Search
        </span>
        <h1 class="text-4xl md:text-5xl font-black text-ink font-serif tracking-tight mb-4">
            Cari dengan Bahasa <span class="text-laut">Natural</span>
        </h1>
        <p class="text-gray-500 max-w-xl mx-auto">Ketik apa yang kamu inginkan — AI kami akan memahami dan menemukan destinasi yang paling cocok.</p>
    </div>

    {{-- Search Box --}}
    <div class="relative max-w-2xl mx-auto mb-10">
        <div class="flex gap-3 bg-white rounded-2xl shadow-hover p-2 border border-gray-100">
            <div class="flex-1 flex items-center gap-3 px-3">
                <i class="fas fa-search text-gray-400"></i>
                <input id="ai-query" type="text"
                    placeholder='Contoh: "pantai sepi untuk honeymoon budget 500rb" atau "wisata budaya di Flores"'
                    class="flex-1 text-sm text-gray-800 placeholder-gray-400 focus:outline-none bg-transparent"
                    onkeydown="if(event.key==='Enter') doSearch()">
            </div>
            <button onclick="doSearch()"
                class="btn-primary px-6 py-3 text-sm whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-magic"></i> Cari dengan AI
            </button>
        </div>

        {{-- Contoh Query --}}
        <div class="flex flex-wrap gap-2 mt-3 justify-center">
            @foreach(['pantai tersembunyi di Flores', 'gunung untuk pendaki pemula', 'wisata budaya murah', 'alam terbuka keluarga rating tinggi'] as $q)
            <button onclick="document.getElementById('ai-query').value='{{ $q }}'; doSearch();"
                class="text-xs bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-full hover:border-laut hover:text-laut transition">
                {{ $q }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Loading State --}}
    <div id="ai-loading" class="hidden text-center py-12">
        <div class="inline-flex items-center gap-3 bg-white rounded-2xl px-8 py-5 shadow-soft">
            <div class="w-5 h-5 border-2 border-laut border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm font-semibold text-gray-600">AI sedang memahami pencarian Anda...</span>
        </div>
    </div>

    {{-- Filter Info --}}
    <div id="ai-filter-info" class="hidden mb-6 bg-petrol/5 border border-petrol/10 rounded-2xl px-5 py-3">
        <p class="text-xs font-semibold text-ink">
            <i class="fas fa-brain text-laut mr-2"></i>
            AI memahami pencarian Anda sebagai: <span id="filter-text" class="text-laut"></span>
        </p>
    </div>

    {{-- Results --}}
    <div id="ai-results" class="hidden">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-ink font-serif tracking-tight" id="result-title">Hasil Pencarian</h2>
            <a href="{{ route('destinations.index') }}" class="text-xs text-laut hover:underline">Lihat semua →</a>
        </div>
        <div id="result-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>
    </div>

    {{-- Empty State --}}
    <div id="ai-empty" class="hidden text-center py-16">
        <i class="fas fa-search text-gray-200 text-5xl mb-4"></i>
        <p class="text-gray-500 font-medium">Tidak ada destinasi yang cocok dengan pencarian Anda.</p>
        <p class="text-gray-400 text-sm mt-1">Coba kata kunci yang berbeda atau lebih umum.</p>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function doSearch() {
    const q = document.getElementById('ai-query').value.trim();
    if (!q) return;

    document.getElementById('ai-loading').classList.remove('hidden');
    document.getElementById('ai-results').classList.add('hidden');
    document.getElementById('ai-empty').classList.add('hidden');
    document.getElementById('ai-filter-info').classList.add('hidden');

    fetch(`{{ route('ai.smart-search') }}?q=${encodeURIComponent(q)}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('ai-loading').classList.add('hidden');

        // Filter info
        const f = data.filters || {};
        const parts = [];
        if (f.category && f.category !== 'null') parts.push(`Kategori: ${f.category}`);
        if (f.location && f.location !== 'null') parts.push(`Lokasi: ${f.location}`);
        if (f.max_price) parts.push(`Maks harga: ${fmt(f.max_price)}`);
        if (f.min_rating) parts.push(`Rating ≥ ${f.min_rating}`);
        if (f.keywords && f.keywords !== 'null') parts.push(`Kata kunci: "${f.keywords}"`);

        if (parts.length) {
            document.getElementById('filter-text').textContent = parts.join(' · ');
            document.getElementById('ai-filter-info').classList.remove('hidden');
        }

        if (!data.results || data.results.length === 0) {
            document.getElementById('ai-empty').classList.remove('hidden');
            return;
        }

        document.getElementById('result-title').textContent = `${data.total} Destinasi Ditemukan`;
        document.getElementById('result-grid').innerHTML = data.results.map(d => `
            <a href="/destinations/${d.id}" class="cinematic-card block group">
                <div class="card-img-wrap h-44">
                    <img src="${d.image || 'https://via.placeholder.com/400x300'}"
                         class="w-full h-full object-cover" alt="${d.name}" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <span class="absolute top-3 left-3 bg-white/20 backdrop-blur text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
                        ${d.category}
                    </span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-ink text-sm mb-1 truncate">${d.name}</h3>
                    <p class="text-xs text-gray-400 mb-2"><i class="fas fa-map-marker-alt text-laut mr-1"></i>${d.location}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-laut font-bold text-sm">${fmt(d.price)}</span>
                        <div class="flex items-center gap-1 text-xs text-gray-500">
                            <i class="fas fa-star text-laut"></i> ${Number(d.rating).toFixed(1)}
                        </div>
                    </div>
                </div>
            </a>
        `).join('');

        document.getElementById('ai-results').classList.remove('hidden');
    })
    .catch(() => {
        document.getElementById('ai-loading').classList.add('hidden');
        document.getElementById('ai-empty').classList.remove('hidden');
    });
}
</script>
@endpush
