{{-- Variabel: $reviewableType ('destination'|'hotel'), $reviewableId --}}
<div id="ai-summary-box" class="mb-8 bg-gradient-to-r from-petrol to-ink rounded-2xl p-5 text-white">
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-laut/20 flex items-center justify-center">
                <i class="fas fa-robot text-laut text-xs"></i>
            </div>
            <span class="text-sm font-bold">Ringkasan AI</span>
            <span class="text-[10px] bg-laut/20 text-laut px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider">Beta</span>
        </div>
        <button id="ai-summary-btn" onclick="loadAISummary()"
            class="text-xs bg-laut hover:bg-laut/90 text-white px-3 py-1.5 rounded-full font-semibold transition flex items-center gap-1.5">
            <i class="fas fa-magic"></i> Muat Ringkasan
        </button>
    </div>
    <div id="ai-summary-content" class="text-sm text-gray-300 leading-relaxed hidden">
        <div id="ai-summary-loading" class="flex items-center gap-2 text-gray-400">
            <div class="w-3.5 h-3.5 border border-laut border-t-transparent rounded-full animate-spin"></div>
            <span>AI sedang membaca ulasan...</span>
        </div>
        <p id="ai-summary-text" class="hidden"></p>
    </div>
    <p class="text-xs text-gray-500 mt-2">AI merangkum semua ulasan pengunjung untuk Anda.</p>
</div>

@push('scripts')
<script>
function loadAISummary() {
    document.getElementById('ai-summary-btn').style.display = 'none';
    document.getElementById('ai-summary-content').classList.remove('hidden');

    fetch(`{{ route('ai.review-summary') }}?type={{ $reviewableType }}&id={{ $reviewableId }}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('ai-summary-loading').classList.add('hidden');
        const textEl = document.getElementById('ai-summary-text');
        textEl.textContent = data.summary || data.message || 'Tidak ada ringkasan tersedia.';
        textEl.classList.remove('hidden');
    })
    .catch(() => {
        document.getElementById('ai-summary-loading').classList.add('hidden');
        const textEl = document.getElementById('ai-summary-text');
        textEl.textContent = 'Gagal memuat ringkasan. Coba lagi nanti.';
        textEl.classList.remove('hidden');
    });
}
</script>
@endpush
