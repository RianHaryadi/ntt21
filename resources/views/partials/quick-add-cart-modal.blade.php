{{--
    Modal global "Tambah ke Keranjang" — dipicu dari kartu listing destinasi/hotel/tour
    via JS `openQuickAdd({ type, id, name, price })`. Hanya untuk user yang login.
--}}
@auth
<div id="quick-add-modal" class="hidden fixed inset-0 z-[150] flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4">
    <div class="bg-paper rounded-3xl shadow-2xl max-w-md w-full p-6 relative">
        <button type="button" id="quick-add-close" class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center text-muted hover:text-ink hover:bg-surface transition-colors">
            <i class="fas fa-times"></i>
        </button>

        <div class="flex items-center gap-2 text-clay text-xs font-bold uppercase tracking-widest mb-2">
            <i class="fas fa-shopping-bag"></i> Tambah ke Keranjang
        </div>
        <h3 class="font-serif font-bold text-lg text-ink mb-5" id="quick-add-title">Item</h3>

        <form id="quick-add-form">
            <div id="quick-add-fields-ticket" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Tanggal Kunjungan</label>
                    <input type="date" id="qa-booking-date" required
                           class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Jumlah Tiket</label>
                    <input type="number" id="qa-qty" min="1" max="50" value="1" required
                           class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut">
                </div>
            </div>

            <div id="quick-add-fields-hotel" class="hidden space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-ink mb-1.5">Tipe Kamar</label>
                    <select id="qa-room-type" class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut">
                        <option value="single">Single Room</option>
                        <option value="double">Double Room</option>
                        <option value="family">Family Room</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Check-in</label>
                        <input type="date" id="qa-checkin" required
                               class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink mb-1.5">Check-out</label>
                        <input type="date" id="qa-checkout" required
                               class="w-full border border-line rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-laut">
                    </div>
                </div>
            </div>

            <p id="quick-add-error" class="hidden text-red-500 text-xs font-semibold mt-3"></p>

            <button type="submit" id="quick-add-submit" class="btn-primary w-full py-3.5 rounded-xl mt-6 flex items-center justify-center gap-2 text-sm font-bold">
                <i class="fas fa-shopping-bag text-xs"></i> Tambah ke Keranjang
            </button>
        </form>
    </div>
</div>
@endauth
