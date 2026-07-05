{{-- Variabel: $endsAt (Carbon instance) --}}
<div class="flash-badge inline-flex items-center gap-1.5 bg-coral text-paper text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">
    <i class="fas fa-bolt"></i> Flash Sale
    <span class="font-mono tabular-nums" data-flash-sale-ends="{{ $endsAt->toIso8601String() }}">--:--:--</span>
</div>
