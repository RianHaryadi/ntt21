{{-- Variabel: $destinationId --}}
<div id="best-time-box" class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-calendar-alt text-emerald-500"></i>
            <span class="font-bold text-sm text-emerald-800">Waktu Terbaik Berkunjung</span>
            <span class="text-[10px] bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider">AI</span>
        </div>
        <button id="best-time-btn" onclick="loadBestTime()"
            class="text-xs bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-full font-semibold transition flex items-center gap-1.5">
            <i class="fas fa-magic"></i> Tanya AI
        </button>
    </div>
    <div id="best-time-content" class="hidden mt-3">
        <div id="best-time-loading" class="flex items-center gap-2 text-emerald-600 text-sm">
            <div class="w-3.5 h-3.5 border border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
            <span>AI sedang menganalisis...</span>
        </div>
        <p id="best-time-text" class="hidden text-sm text-emerald-800 leading-relaxed"></p>
    </div>
</div>

@push('scripts')
<script>
function loadBestTime() {
    document.getElementById('best-time-btn').style.display = 'none';
    document.getElementById('best-time-content').classList.remove('hidden');

    fetch(`{{ route('ai.best-time') }}?id={{ $destinationId }}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('best-time-loading').classList.add('hidden');
        const el = document.getElementById('best-time-text');
        el.textContent = data.advice || 'Tidak dapat memuat saran.';
        el.classList.remove('hidden');
    });
}
</script>
@endpush
