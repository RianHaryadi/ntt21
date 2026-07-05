{{--
    Partial: Wishlist Button
    Variabel: $wishlistType ('destination'|'hotel'), $wishlistId (int)
--}}
@auth
@php
    $isWishlisted = auth()->user()->wishlists()
        ->where('wishlistable_type', 'App\\Models\\' . ucfirst($wishlistType === 'destination' ? 'Destination' : 'Hotel'))
        ->where('wishlistable_id', $wishlistId)
        ->exists();
@endphp
<button
    onclick="toggleWishlist('{{ $wishlistType }}', {{ $wishlistId }}, this)"
    class="wishlist-btn flex items-center gap-1.5 text-sm font-semibold px-4 py-2 rounded-full border transition-all
        {{ $isWishlisted ? 'bg-red-50 border-red-300 text-red-500' : 'bg-white/10 border-white/30 text-white hover:bg-white/20' }}">
    <i class="fas fa-heart {{ $isWishlisted ? '' : 'opacity-60' }}"></i>
    <span>{{ $isWishlisted ? 'Tersimpan' : 'Simpan' }}</span>
</button>
@endauth

@push('scripts')
<script>
function toggleWishlist(type, id, btn) {
    fetch('{{ route("wishlist.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ type, id })
    })
    .then(r => r.json())
    .then(data => {
        const saved = data.wishlisted;
        btn.className = btn.className
            .replace(/bg-red-50|border-red-300|text-red-500|bg-white\/10|border-white\/30|text-white|hover:bg-white\/20/g, '').trim();
        if (saved) {
            btn.classList.add('bg-red-50', 'border-red-300', 'text-red-500');
        } else {
            btn.classList.add('bg-white/10', 'border-white/30', 'text-white', 'hover:bg-white/20');
        }
        btn.querySelector('span').textContent = saved ? 'Tersimpan' : 'Simpan';
        btn.querySelector('i').classList.toggle('opacity-60', !saved);
    });
}
</script>
@endpush
