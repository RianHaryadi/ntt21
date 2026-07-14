import './bootstrap';

/**
 * Scroll-triggered reveal — mengaktifkan class .reveal / .reveal-group
 * yang sudah dipakai di banyak halaman tapi belum pernah punya animasi nyata.
 */
function initRevealObserver() {
    const targets = document.querySelectorAll('.reveal:not(.is-visible), .reveal-group:not(.is-visible)');
    if (!targets.length) return;

    if (!('IntersectionObserver' in window)) {
        targets.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        // threshold rendah: grup yang sangat tinggi (mis. daftar kartu bertumpuk) harus
        // langsung muncul begitu ujung atasnya masuk viewport, bukan menunggu 12% terlihat.
        { threshold: 0.01, rootMargin: '0px 0px -40px 0px' }
    );

    targets.forEach((el) => observer.observe(el));
}

/** Navbar berubah tampilan (bayangan lembut) saat halaman discroll. */
function initNavbarScroll() {
    const navbar = document.getElementById('main-navbar');
    if (!navbar) return;

    const onScroll = () => {
        navbar.classList.toggle('navbar-scrolled', window.scrollY > 16);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

/** Countdown timer untuk badge Flash Sale — elemen dengan [data-flash-sale-ends="ISO8601"]. */
function initFlashSaleCountdowns() {
    const nodes = document.querySelectorAll('[data-flash-sale-ends]');
    if (!nodes.length) return;

    const tick = () => {
        nodes.forEach((el) => {
            const end = new Date(el.dataset.flashSaleEnds).getTime();
            const remaining = end - Date.now();

            if (remaining <= 0) {
                el.textContent = 'Berakhir';
                return;
            }

            const h = Math.floor(remaining / 3600000);
            const m = Math.floor((remaining % 3600000) / 60000);
            const s = Math.floor((remaining % 60000) / 1000);
            el.textContent = [h, m, s].map((n) => String(n).padStart(2, '0')).join(':');
        });
    };

    tick();
    if (window.__flashSaleInterval) clearInterval(window.__flashSaleInterval);
    window.__flashSaleInterval = setInterval(tick, 1000);
}

/** Autocomplete pencarian — input dengan [data-search-autocomplete] menampilkan saran dari /search/suggestions. */
function initSearchAutocomplete() {
    const inputs = document.querySelectorAll('[data-search-autocomplete]:not([data-autocomplete-bound])');
    if (!inputs.length) return;

    const typeIcons = { destination: 'fa-map-marker-alt', hotel: 'fa-hotel', tour: 'fa-suitcase-rolling' };

    inputs.forEach((input) => {
        input.setAttribute('data-autocomplete-bound', '1');
        const container = input.closest('form') || input.parentElement;
        const results = container.querySelector('[data-search-autocomplete-results]');
        if (!results) return;

        let debounceTimer = null;
        let activeIndex = -1;

        const hide = () => {
            results.classList.add('hidden');
            results.innerHTML = '';
            activeIndex = -1;
        };

        const render = (items) => {
            if (!items.length) {
                hide();
                return;
            }
            results.innerHTML = items.map((item, i) => `
                <a href="${item.url}" data-autocomplete-item
                   class="flex items-center gap-3 px-4 py-3 hover:bg-surface transition-colors border-b border-line last:border-b-0">
                    <i class="fas ${typeIcons[item.type] || 'fa-search'} text-clay text-xs w-4 flex-shrink-0"></i>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-ink truncate">${item.label}</span>
                        ${item.sublabel ? `<span class="block text-xs text-muted truncate">${item.sublabel}</span>` : ''}
                    </span>
                </a>
            `).join('');
            results.classList.remove('hidden');
        };

        input.addEventListener('input', () => {
            const q = input.value.trim();
            clearTimeout(debounceTimer);

            if (q.length < 2) {
                hide();
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`/search/suggestions?q=${encodeURIComponent(q)}`, { headers: { Accept: 'application/json' } })
                    .then((r) => r.json())
                    .then((data) => render(data.results || []))
                    .catch(() => hide());
            }, 250);
        });

        input.addEventListener('keydown', (e) => {
            const items = results.querySelectorAll('[data-autocomplete-item]');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                items[activeIndex].click();
                return;
            } else if (e.key === 'Escape') {
                hide();
                return;
            } else {
                return;
            }

            items.forEach((el, i) => el.classList.toggle('bg-surface', i === activeIndex));
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) hide();
        });
    });
}

function initAnimations() {
    initRevealObserver();
    initNavbarScroll();
    initFlashSaleCountdowns();
    initSearchAutocomplete();
}

document.addEventListener('DOMContentLoaded', initAnimations);
document.addEventListener('livewire:navigated', initAnimations);

// Jaring pengaman global: gambar yang gagal dimuat (404, dsb.) diganti fallback
// alih-alih menampilkan ikon gambar rusak / teks alt.
document.addEventListener('error', (e) => {
    const img = e.target;
    if (!(img instanceof HTMLImageElement) || img.dataset.fallbackApplied) return;
    img.dataset.fallbackApplied = '1';
    img.src = '/images/fallback.jpg';
}, true);

// Re-scan setelah Livewire re-render (mis. hasil pencarian AI, dsb.)
document.addEventListener('livewire:update', initRevealObserver);

// Jaring pengaman: paksa tampil kalau observer gagal memicu (mis. elemen di luar viewport tak terjangkau,
// atau screenshot/capture terjadi sebelum discroll). Dipicu dari DOMContentLoaded (bukan 'load', yang bisa
// molor lama di halaman dengan banyak gambar) dan dengan delay singkat agar tidak terlihat "kosong".
function forceRevealFallback() {
    setTimeout(() => {
        document.querySelectorAll('.reveal:not(.is-visible), .reveal-group:not(.is-visible)')
            .forEach((el) => el.classList.add('is-visible'));
    }, 350);
}
document.addEventListener('DOMContentLoaded', forceRevealFallback);
document.addEventListener('livewire:navigated', forceRevealFallback);
