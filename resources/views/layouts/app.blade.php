<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pesona NTT | Jelajahi Keindahan Nusa Tenggara Timur')</title>
    <meta name="description" content="@yield('meta_description', 'Temukan destinasi tersembunyi, hotel terbaik, dan paket tour eksklusif di Nusa Tenggara Timur — Komodo, Labuan Bajo, Flores, Sumba, dan lainnya.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    {{-- Open Graph / Twitter Card --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'Pesona NTT | Jelajahi Keindahan Nusa Tenggara Timur')">
    <meta property="og:description" content="@yield('meta_description', 'Temukan destinasi tersembunyi, hotel terbaik, dan paket tour eksklusif di Nusa Tenggara Timur.')">
    <meta property="og:image" content="@yield('og_image', asset('images/fallback.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Pesona NTT')">
    <meta name="twitter:description" content="@yield('meta_description', 'Jelajahi keindahan Nusa Tenggara Timur bersama Pesona NTT.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/fallback.jpg'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fraunces & Hanken Grotesk Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles
    @stack('styles')
    @yield('styles')
</head>
<body class="bg-paper text-ink font-sans antialiased min-h-screen flex flex-col selection:bg-clay/20 selection:text-ink">
    
    <!-- Header with thin nav -->
    <header id="main-navbar" class="border-b border-line bg-surface/80 backdrop-blur-md sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-serif font-black text-2xl tracking-tight text-ink transition-opacity hover:opacity-80">
                <x-logo-mark class="w-9 h-9 flex-shrink-0" />
                Pesona<span class="text-clay">NTT</span>
            </a>
            
            <!-- Desktop Nav -->
            <nav class="hidden md:flex gap-8 text-sm font-medium text-muted">
                <a href="{{ route('destinations.index') }}" class="hover:text-ink transition-colors duration-200">{{ __('site.nav_destinations') }}</a>
                <a href="{{ route('hotels.index') }}" class="hover:text-ink transition-colors duration-200">{{ __('site.nav_hotels') }}</a>
                <a href="{{ route('paket-tours.index') }}" class="hover:text-ink transition-colors duration-200">{{ __('site.nav_tours') }}</a>
                <a href="{{ route('cultures.index') }}" class="hover:text-ink transition-colors duration-200">{{ __('site.nav_culture') }}</a>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <!-- Language Toggle -->
                <div class="hidden sm:flex items-center bg-surface border border-line rounded-full p-0.5 text-[11px] font-bold">
                    <a href="{{ route('locale.switch', 'id') }}" class="px-2.5 py-1 rounded-full transition-colors {{ app()->getLocale() === 'id' ? 'bg-ink text-paper' : 'text-muted hover:text-ink' }}">ID</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-2.5 py-1 rounded-full transition-colors {{ app()->getLocale() === 'en' ? 'bg-ink text-paper' : 'text-muted hover:text-ink' }}">EN</a>
                </div>

                <!-- Currency Toggle -->
                <div class="hidden sm:flex items-center bg-surface border border-line rounded-full p-0.5 text-[11px] font-bold">
                    <a href="{{ route('currency.switch', 'IDR') }}" class="px-2.5 py-1 rounded-full transition-colors {{ current_currency() === 'IDR' ? 'bg-clay text-paper' : 'text-muted hover:text-ink' }}">IDR</a>
                    <a href="{{ route('currency.switch', 'USD') }}" class="px-2.5 py-1 rounded-full transition-colors {{ current_currency() === 'USD' ? 'bg-clay text-paper' : 'text-muted hover:text-ink' }}">USD</a>
                </div>

                <button onclick="document.dispatchEvent(new CustomEvent('open-chat'))" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-clay hover:text-clay/80 transition-colors">
                    <i class="fas fa-robot animate-pulse"></i> {{ __('site.nav_ai_guide') }}
                </button>

                @auth
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" id="cart-badge-link" class="relative text-muted hover:text-ink transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-line/50">
                        <i class="fas fa-shopping-bag text-sm"></i>
                        @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
                        <span id="cart-badge-count" class="absolute top-0 right-0 w-3.5 h-3.5 bg-clay rounded-full text-paper text-[9px] font-bold flex items-center justify-center {{ $cartCount > 0 ? '' : 'hidden' }}" data-count="{{ $cartCount }}">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                    </a>

                    <!-- Notifications -->
                    <div class="relative" id="notif-wrapper">
                        @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                        <button id="notif-btn" class="relative text-muted hover:text-ink transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-line/50">
                            <i class="fas fa-bell text-sm"></i>
                            @if($unreadCount > 0)
                                <span id="notif-badge" class="absolute top-0 right-0 w-3.5 h-3.5 bg-clay rounded-full text-paper text-[9px] font-bold flex items-center justify-center">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="notif-dropdown" class="hidden absolute right-0 top-full mt-2 w-80 bg-paper border border-line rounded-xl shadow-sm overflow-hidden z-50">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-line">
                                <span class="text-sm font-semibold text-ink">{{ __('site.nav_notifications') }}</span>
                                <button id="notif-mark-all-btn" class="text-xs text-clay hover:text-clay/80 transition-colors">{{ __('site.nav_mark_all_read') }}</button>
                            </div>
                            <div id="notif-list" class="max-h-80 overflow-y-auto">
                                <p class="text-center text-muted text-xs py-6">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative" id="user-menu-wrapper">
                        <button id="user-menu-btn" class="flex items-center gap-2 px-2 py-1.5 rounded-full hover:bg-line/50 transition-colors">
                            <div class="w-7 h-7 rounded-full bg-clay flex items-center justify-center text-paper text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </button>
                        <div id="user-dropdown" class="hidden absolute right-0 top-full mt-2 w-48 bg-paper border border-line rounded-xl shadow-sm overflow-hidden z-50">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-3 text-sm text-ink hover:bg-surface transition-colors">{{ __('site.nav_dashboard') }}</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-ink hover:bg-surface transition-colors">{{ __('site.nav_profile') }}</a>
                            <div class="border-t border-line"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-clay hover:bg-surface transition-colors">{{ __('site.nav_logout') }}</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-muted hover:text-ink transition-colors duration-200 hidden sm:block">{{ __('site.nav_login') }}</a>
                    <x-editorial.btn as="a" href="{{ route('register') }}" class="hidden sm:inline-flex">{{ __('site.nav_signup') }}</x-editorial.btn>
                @endauth
                
                <!-- Mobile Menu Toggle -->
                <button id="menu-btn" class="md:hidden text-ink p-2">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-paper border-b border-line shadow-sm">
            <div class="px-4 py-4 flex flex-col gap-4">
                <a href="{{ route('destinations.index') }}" class="text-ink font-medium">{{ __('site.nav_destinations') }}</a>
                <a href="{{ route('hotels.index') }}" class="text-ink font-medium">{{ __('site.nav_hotels') }}</a>
                <a href="{{ route('paket-tours.index') }}" class="text-ink font-medium">{{ __('site.nav_tours') }}</a>
                <a href="{{ route('cultures.index') }}" class="text-ink font-medium">{{ __('site.nav_culture') }}</a>
                <div class="flex items-center gap-2 pt-2 border-t border-line">
                    <a href="{{ route('locale.switch', 'id') }}" class="px-3 py-1.5 rounded-full text-xs font-bold {{ app()->getLocale() === 'id' ? 'bg-ink text-paper' : 'bg-surface text-muted' }}">ID</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1.5 rounded-full text-xs font-bold {{ app()->getLocale() === 'en' ? 'bg-ink text-paper' : 'bg-surface text-muted' }}">EN</a>
                    <a href="{{ route('currency.switch', 'IDR') }}" class="px-3 py-1.5 rounded-full text-xs font-bold {{ current_currency() === 'IDR' ? 'bg-clay text-paper' : 'bg-surface text-muted' }}">IDR</a>
                    <a href="{{ route('currency.switch', 'USD') }}" class="px-3 py-1.5 rounded-full text-xs font-bold {{ current_currency() === 'USD' ? 'bg-clay text-paper' : 'bg-surface text-muted' }}">USD</a>
                </div>
                @guest
                    <a href="{{ route('login') }}" class="text-ink font-medium">{{ __('site.nav_login') }}</a>
                    <a href="{{ route('register') }}" class="text-clay font-medium">{{ __('site.nav_signup') }}</a>
                @endguest
            </div>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-ink text-paper/80 py-20 border-t border-line relative overflow-hidden mt-0">
        <!-- Decoration -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 h-32 bg-clay/10 blur-[100px] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
                <!-- Brand -->
                <div class="md:col-span-5">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 font-serif font-black text-3xl text-paper tracking-tight mb-6">
                        <x-logo-mark class="w-11 h-11 flex-shrink-0" />
                        Pesona<span class="text-clay">NTT</span>
                    </a>
                    <p class="text-paper/60 text-sm max-w-md leading-relaxed mb-8">
                        {{ __('site.footer_tagline') }}
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-paper/5 flex items-center justify-center text-paper hover:bg-clay hover:scale-110 transition-all"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-paper/5 flex items-center justify-center text-paper hover:bg-clay hover:scale-110 transition-all"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-paper/5 flex items-center justify-center text-paper hover:bg-clay hover:scale-110 transition-all"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <!-- Links -->
                <div class="md:col-span-2 md:col-start-8">
                    <h4 class="text-paper font-bold mb-6 uppercase text-xs tracking-widest">{{ __('site.footer_discover') }}</h4>
                    <ul class="space-y-4 text-sm text-paper/60">
                        <li><a href="{{ route('destinations.index') }}" class="hover:text-clay transition-colors">{{ __('site.nav_destinations') }}</a></li>
                        <li><a href="{{ route('hotels.index') }}" class="hover:text-clay transition-colors">{{ __('site.footer_resorts_hotels') }}</a></li>
                        <li><a href="{{ route('paket-tours.index') }}" class="hover:text-clay transition-colors">{{ __('site.nav_tours') }}</a></li>
                        <li><a href="{{ route('cultures.index') }}" class="hover:text-clay transition-colors">{{ __('site.nav_culture') }}</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="md:col-span-3">
                    <h4 class="text-paper font-bold mb-6 uppercase text-xs tracking-widest">{{ __('site.footer_contact') }}</h4>
                    <div class="space-y-4 text-sm text-paper/60">
                        <p class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-clay mt-1"></i>
                            <span>{{ __('site.footer_location') }}</span>
                        </p>
                        <p class="flex items-center gap-3">
                            <i class="fas fa-envelope text-clay"></i>
                            <a href="mailto:explore@pesonantt.id" class="hover:text-paper transition">explore@pesonantt.id</a>
                        </p>
                        <p class="flex items-center gap-3">
                            <i class="fab fa-whatsapp text-clay"></i>
                            <a href="https://wa.me/{{ config('services.support.whatsapp') }}?text={{ urlencode('Halo, saya butuh bantuan terkait Pesona NTT.') }}"
                               target="_blank" rel="noopener" class="hover:text-paper transition">{{ __('site.footer_chat_admin') }}</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="border-t border-paper/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-paper/50">
                <p>© {{ date('Y') }} Pesona NTT. {{ __('site.footer_rights') }}</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-paper/80">{{ __('site.footer_privacy') }}</a>
                    <a href="#" class="hover:text-paper/80">{{ __('site.footer_terms') }}</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ====== CHAT POPUP + FLOATING BUBBLE ====== -->
    <div
        x-data="{ open: false }"
        x-on:open-chat.document="open = true"
        x-on:close-chat.document="open = false"
    >
        {{-- Floating bubble button (bottom-right, all pages) --}}
        <button
            x-show="!open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-50"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-50"
            @click="open = true"
            class="fixed bottom-6 right-6 z-[199] w-16 h-16 rounded-full bg-clay hover:bg-clay/90 text-white shadow-[0_8px_30px_rgba(249,115,22,0.5)] hover:shadow-[0_8px_40px_rgba(249,115,22,0.7)] flex items-center justify-center transition-all hover:scale-110 active:scale-95"
            style="display: none;"
        >
            <span class="absolute inset-0 rounded-full bg-clay animate-ping opacity-40"></span>
            <i class="fas fa-comment-dots text-2xl relative z-10"></i>
            <span class="absolute -top-1 -right-1 w-5 h-5 bg-sea rounded-full border-2 border-paper flex items-center justify-center">
                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
            </span>
        </button>

        {{-- Popup wrapper --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center sm:justify-end sm:p-4"
            style="display: none;"
        >
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>

            {{-- Panel --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-8 sm:translate-y-0 sm:translate-x-8 opacity-0"
                x-transition:enter-end="translate-y-0 sm:translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-y-0 sm:translate-x-0 opacity-100"
                x-transition:leave-end="translate-y-8 sm:translate-y-0 sm:translate-x-8 opacity-0"
                class="relative z-10 w-full sm:w-[420px] h-[85vh] sm:h-[600px] bg-ink rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden border border-white/10"
                @click.stop
            >
                @livewire('travel-chat', ['popup' => true], key('chat-popup'))
            </div>
        </div>
    </div>

    @include('partials.quick-add-cart-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dropdowns
            const userMenuBtn = document.getElementById('user-menu-btn');
            const userDropdown = document.getElementById('user-dropdown');
            const notifBtn = document.getElementById('notif-btn');
            const notifDropdown = document.getElementById('notif-dropdown');
            const mobileBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if (userMenuBtn) {
                userMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userDropdown.classList.toggle('hidden');
                    if(notifDropdown) notifDropdown.classList.add('hidden');
                });
            }

            if (notifBtn) {
                notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const willOpen = notifDropdown.classList.contains('hidden');
                    notifDropdown.classList.toggle('hidden');
                    if (userDropdown) userDropdown.classList.add('hidden');
                    if (willOpen) loadNotifications();
                });
            }

            const notifList = document.getElementById('notif-list');
            const notifBadge = document.getElementById('notif-badge');
            const notifMarkAllBtn = document.getElementById('notif-mark-all-btn');
            const notifTypeIcon = { success: 'fa-check-circle text-emerald-600', warning: 'fa-exclamation-triangle text-amber-500', info: 'fa-info-circle text-laut' };
            let notifLoaded = false;

            function timeAgo(dateStr) {
                const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
                if (diff < 60) return 'baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
                if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
                return Math.floor(diff / 86400) + ' hari lalu';
            }

            function renderNotifications(items) {
                if (!items.length) {
                    notifList.innerHTML = '<p class="text-center text-muted text-xs py-6">Belum ada notifikasi.</p>';
                    return;
                }
                notifList.innerHTML = items.map((n) => {
                    const icon = notifTypeIcon[n.type] || notifTypeIcon.info;
                    const unread = !n.read_at;
                    return `
                        <a href="${n.link || '#'}" data-notif-id="${n.id}" data-unread="${unread ? '1' : '0'}"
                           class="notif-item flex gap-3 px-4 py-3 border-b border-line last:border-b-0 hover:bg-surface transition-colors ${unread ? 'bg-clay/5' : ''}">
                            <i class="fas ${icon} mt-0.5 flex-shrink-0"></i>
                            <span class="flex-1 min-w-0">
                                <span class="block text-sm font-semibold text-ink">${n.title}</span>
                                <span class="block text-xs text-muted mt-0.5 line-clamp-2">${n.body}</span>
                                <span class="block text-[10px] text-muted/70 mt-1">${timeAgo(n.created_at)}</span>
                            </span>
                            ${unread ? '<span class="w-2 h-2 rounded-full bg-clay flex-shrink-0 mt-1.5"></span>' : ''}
                        </a>
                    `;
                }).join('');

                notifList.querySelectorAll('.notif-item').forEach((el) => {
                    el.addEventListener('click', () => {
                        if (el.dataset.unread === '1') {
                            fetch(`/notifications/${el.dataset.notifId}/read`, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            }).then(updateBadgeCount);
                        }
                    });
                });
            }

            function updateBadgeCount() {
                const stillUnread = notifList.querySelectorAll('[data-unread="1"]').length;
                if (notifBadge) {
                    if (stillUnread > 0) {
                        notifBadge.textContent = stillUnread > 9 ? '9+' : stillUnread;
                    } else {
                        notifBadge.remove();
                    }
                }
            }

            function loadNotifications(force = false) {
                if (notifLoaded && !force) return;
                fetch('{{ route('notifications.index') }}', { headers: { 'Accept': 'application/json' } })
                    .then((r) => r.json())
                    .then((json) => {
                        notifLoaded = true;
                        renderNotifications(json.data || []);
                    })
                    .catch(() => {
                        notifList.innerHTML = '<p class="text-center text-muted text-xs py-6">Gagal memuat notifikasi.</p>';
                    });
            }

            if (notifMarkAllBtn) {
                notifMarkAllBtn.addEventListener('click', () => {
                    fetch('{{ route('notifications.readAll') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    }).then(() => {
                        notifList.querySelectorAll('.notif-item').forEach((el) => {
                            el.dataset.unread = '0';
                            el.classList.remove('bg-clay/5');
                            el.querySelector('.bg-clay.rounded-full')?.remove();
                        });
                        if (notifBadge) notifBadge.remove();
                    });
                });
            }

            if (mobileBtn) {
                mobileBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            window.addEventListener('click', () => {
                if(userDropdown) userDropdown.classList.add('hidden');
                if(notifDropdown) notifDropdown.classList.add('hidden');
            });
        });
    </script>

    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qaModal = document.getElementById('quick-add-modal');
            if (!qaModal) return;

            const qaForm = document.getElementById('quick-add-form');
            const qaTitle = document.getElementById('quick-add-title');
            const qaError = document.getElementById('quick-add-error');
            const qaSubmit = document.getElementById('quick-add-submit');
            const qaFieldsTicket = document.getElementById('quick-add-fields-ticket');
            const qaFieldsHotel = document.getElementById('quick-add-fields-hotel');
            const qaBookingDate = document.getElementById('qa-booking-date');
            const qaQty = document.getElementById('qa-qty');
            const qaRoomType = document.getElementById('qa-room-type');
            const qaCheckin = document.getElementById('qa-checkin');
            const qaCheckout = document.getElementById('qa-checkout');
            const today = new Date().toISOString().split('T')[0];
            let currentItem = null;

            window.openQuickAdd = function (item) {
                currentItem = item;
                qaTitle.textContent = item.name;
                qaError.classList.add('hidden');
                qaSubmit.disabled = false;

                const isHotel = item.type === 'hotel';
                qaFieldsTicket.classList.toggle('hidden', isHotel);
                qaFieldsHotel.classList.toggle('hidden', !isHotel);

                // Field yang tersembunyi jangan ikut divalidasi HTML5 (required), atau submit
                // akan diblokir diam-diam oleh browser karena elemen tak terlihat tak bisa difokus.
                qaBookingDate.required = !isHotel;
                qaQty.required = !isHotel;
                qaCheckin.required = isHotel;
                qaCheckout.required = isHotel;

                qaBookingDate.min = today;
                qaBookingDate.value = '';
                qaQty.value = 1;
                qaCheckin.min = today;
                qaCheckin.value = '';
                qaCheckout.value = '';
                qaRoomType.value = 'single';

                qaModal.classList.remove('hidden');
            };

            function closeQuickAdd() {
                qaModal.classList.add('hidden');
            }

            document.getElementById('quick-add-close')?.addEventListener('click', closeQuickAdd);
            qaModal.addEventListener('click', (e) => { if (e.target === qaModal) closeQuickAdd(); });

            qaCheckin.addEventListener('change', () => {
                if (qaCheckin.value) {
                    const next = new Date(qaCheckin.value);
                    next.setDate(next.getDate() + 1);
                    qaCheckout.min = next.toISOString().split('T')[0];
                    if (!qaCheckout.value || qaCheckout.value <= qaCheckin.value) {
                        qaCheckout.value = qaCheckout.min;
                    }
                }
            });

            qaForm.addEventListener('submit', (e) => {
                e.preventDefault();
                if (!currentItem) return;

                const payload = { itemable_type: currentItem.type, itemable_id: currentItem.id };
                if (currentItem.type === 'hotel') {
                    payload.room_type = qaRoomType.value;
                    payload.check_in_date = qaCheckin.value;
                    payload.check_out_date = qaCheckout.value;
                } else {
                    payload.booking_date = qaBookingDate.value;
                    payload.number_of_tickets = qaQty.value;
                }

                qaSubmit.disabled = true;
                qaError.classList.add('hidden');

                fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                }).then((response) => {
                    if (response.ok || response.redirected) {
                        closeQuickAdd();
                        qaSubmit.disabled = false;
                        bumpCartBadge();
                        showAddedToCartToast(currentItem.name);
                        return;
                    }
                    return response.json().then((data) => {
                        const firstError = data.errors ? Object.values(data.errors)[0][0] : 'Gagal menambahkan ke keranjang.';
                        qaError.textContent = firstError;
                        qaError.classList.remove('hidden');
                        qaSubmit.disabled = false;
                    });
                }).catch(() => {
                    qaError.textContent = 'Gagal menambahkan ke keranjang. Coba lagi.';
                    qaError.classList.remove('hidden');
                    qaSubmit.disabled = false;
                });
            });

            function bumpCartBadge() {
                const badge = document.getElementById('cart-badge-count');
                if (!badge) return;
                const next = parseInt(badge.dataset.count || '0', 10) + 1;
                badge.dataset.count = next;
                badge.textContent = next > 9 ? '9+' : next;
                badge.classList.remove('hidden');
            }

            function showAddedToCartToast(itemName) {
                document.getElementById('quick-add-toast')?.remove();

                const toast = document.createElement('div');
                toast.id = 'quick-add-toast';
                toast.className = 'fixed bottom-6 right-6 z-[60] bg-ink text-paper rounded-xl shadow-xl px-4 py-3 flex items-center gap-3 max-w-sm animate-fade-in';

                const icon = document.createElement('i');
                icon.className = 'fas fa-check-circle text-emerald-400';

                const text = document.createElement('span');
                text.className = 'text-sm flex-1';
                text.textContent = `"${itemName}" ditambahkan ke keranjang.`;

                const link = document.createElement('a');
                link.href = '{{ route('cart.index') }}';
                link.className = 'text-xs font-semibold text-clay hover:underline whitespace-nowrap';
                link.textContent = 'Lihat Keranjang';

                toast.append(icon, text, link);
                document.body.appendChild(toast);

                setTimeout(() => toast.remove(), 4000);
            }
        });
    </script>
    @endauth

    @livewireScripts
    @stack('scripts')
    @yield('scripts')
</body>
</html>