<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - Wonderful NTT</title>
    <meta name="description" content="Explore East Nusa Tenggara's breathtaking destinations, luxury hotels, and curated tour packages. Book your dream trip to Wonderful NTT today.">

    <!-- Google Fonts: Montserrat + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        montserrat: ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        ocean: {
                            900: '#001a33', // Deep Ocean Blue
                            800: '#002b5e',
                            700: '#004080',
                            600: '#0059b3',
                            500: '#0073e6',
                        },
                        sunset: {
                            500: '#ff6b35', // Sunset Orange
                            600: '#e55a2b',
                            400: '#ff8559',
                        },
                        light: '#f8f9fa',
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                        'hover': '0 20px 40px -10px rgba(0,0,0,0.15)',
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #333; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Montserrat', sans-serif; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f8f9fa; }
        ::-webkit-scrollbar-thumb { background: #002b5e; border-radius: 4px; }

        /* Navbar */
        #main-navbar {
            transition: all 0.4s ease;
            background: rgba(0, 26, 51, 0.0);
        }
        #main-navbar.scrolled {
            background: rgba(0, 26, 51, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .nav-link {
            position: relative;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav-link:hover { color: #ff6b35; }
        .nav-link::after {
            content: '';
            position: absolute; width: 0; height: 2px;
            bottom: -4px; left: 0;
            background-color: #ff6b35;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after { width: 100%; }

        /* Buttons */
        .btn-primary {
            background-color: #ff6b35;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex; align-items: center; justify-content: center;
        }
        .btn-primary:hover {
            background-color: #e55a2b;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-outline {
            border: 2px solid white;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            background-color: white;
            color: #001a33;
            transform: translateY(-2px);
        }

        /* Cards */
        .cinematic-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .cinematic-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);
        }
        .card-img-wrap {
            overflow: hidden;
            position: relative;
        }
        .card-img-wrap img {
            transition: transform 0.6s ease;
        }
        .cinematic-card:hover .card-img-wrap img {
            transform: scale(1.1);
        }

        /* Reveal Animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    @stack('styles')
</head>
<body class="bg-light antialiased overflow-x-hidden selection:bg-sunset-500 selection:text-white">

<!-- ====== NAVBAR ====== -->
<nav id="main-navbar" class="fixed top-0 w-full z-[100] py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-14">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-full bg-sunset-500 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-compass text-xl"></i>
                </div>
                <span class="text-2xl font-black text-white tracking-tight font-montserrat">
                    Wonderful<span class="text-sunset-500">NTT</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="nav-link">Home</a>
                <a href="{{ route('destinations.index') }}" class="nav-link">Destinations</a>
                <a href="{{ route('hotels.index') }}" class="nav-link">Hotels</a>
                <a href="{{ route('paket-tours.index') }}" class="nav-link">Tours</a>
                <a href="{{ route('cultures.index') }}" class="nav-link">Culture</a>
                <a href="#" onclick="document.dispatchEvent(new CustomEvent('open-chat'));return false;" class="nav-link !text-sunset-500 hover:!text-sunset-400 font-bold"><i class="fas fa-robot mr-1"></i> AI Guide</a>
            </div>

            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('booking.checkForm') }}" class="btn-primary py-2 px-5 text-sm gap-2">
                    <i class="fas fa-ticket-alt"></i> Cek Booking
                </a>

                @auth
                    <!-- User Dropdown -->
                    <div class="relative" id="user-menu-wrapper">
                        <button id="user-menu-btn"
                            class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 px-3 py-2 rounded-full text-white text-sm font-semibold transition-all">
                            <div class="w-7 h-7 rounded-full bg-sunset-500 flex items-center justify-center text-white text-xs font-black">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs opacity-60"></i>
                        </button>
                        <div id="user-dropdown"
                            class="hidden absolute right-0 top-full mt-2 w-48 bg-ocean-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-50">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition">
                                <i class="fas fa-th-large w-4 text-sunset-500"></i> Dashboard
                            </a>
                            <a href="{{ route('travel.chat') }}"
                               class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition">
                                <i class="fas fa-robot w-4 text-sunset-500"></i> AI Chat
                            </a>
                            <div class="border-t border-white/10 mx-3"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-white/5 transition text-left">
                                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-white/80 hover:text-white font-semibold transition flex items-center gap-2">
                        <i class="fas fa-sign-in-alt text-sunset-500"></i> Login
                    </a>
                @endauth
            </div>

            <!-- Mobile Toggle -->
            <button id="menu-btn" class="md:hidden w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white backdrop-blur-md">
                <i id="menu-icon" class="fas fa-bars text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-ocean-900 border-t border-white/10 shadow-2xl">
        <div class="px-4 py-6 space-y-4">
            <a href="{{ route('home') }}" class="block text-white font-medium hover:text-sunset-500 transition"><i class="fas fa-home w-6 text-sunset-500"></i> Home</a>
            <a href="{{ route('destinations.index') }}" class="block text-white font-medium hover:text-sunset-500 transition"><i class="fas fa-map-marker-alt w-6 text-sunset-500"></i> Destinations</a>
            <a href="{{ route('hotels.index') }}" class="block text-white font-medium hover:text-sunset-500 transition"><i class="fas fa-hotel w-6 text-sunset-500"></i> Hotels</a>
            <a href="{{ route('paket-tours.index') }}" class="block text-white font-medium hover:text-sunset-500 transition"><i class="fas fa-suitcase-rolling w-6 text-sunset-500"></i> Tours</a>
            <a href="{{ route('cultures.index') }}" class="block text-white font-medium hover:text-sunset-500 transition"><i class="fas fa-masks-theater w-6 text-sunset-500"></i> Culture</a>
            <a href="#" onclick="document.dispatchEvent(new CustomEvent('open-chat'));this.closest('#mobile-menu').classList.add('hidden');return false;" class="block text-sunset-400 font-bold hover:text-sunset-500 transition"><i class="fas fa-robot w-6 text-sunset-500"></i> AI Guide</a>
            <div class="pt-4 border-t border-white/10 space-y-3">
                <a href="{{ route('booking.checkForm') }}" class="btn-primary w-full py-3 block text-center">Cek Booking</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block text-center w-full py-3 rounded-full border border-white/20 text-white text-sm font-semibold">
                        <i class="fas fa-th-large mr-2 text-sunset-500"></i>Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block text-center w-full py-3 rounded-full border border-red-500/30 text-red-400 text-sm font-semibold">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-center w-full py-3 rounded-full border border-white/20 text-white text-sm font-semibold">
                        <i class="fas fa-sign-in-alt mr-2 text-sunset-500"></i>Login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="min-h-screen">
    @yield('content')
</main>

<!-- ====== FOOTER ====== -->
<footer class="bg-ocean-900 text-gray-300 pt-20 pb-10 border-t-4 border-sunset-500 relative">
    <!-- Subtle Top Glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 h-1 bg-gradient-to-r from-transparent via-sunset-500 to-transparent blur-sm"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-16">
            <!-- Brand Column -->
            <div class="md:col-span-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-6">
                    <div class="w-10 h-10 rounded-full bg-sunset-500 flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-compass text-xl"></i>
                    </div>
                    <span class="text-2xl font-black text-white tracking-tight font-montserrat">Wonderful<span class="text-sunset-500">NTT</span></span>
                </a>
                <p class="text-gray-400 mb-6 leading-relaxed">
                    Elevating your travel experience with curated destinations, luxury stays, and unforgettable adventures across East Nusa Tenggara.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-sunset-500 transition-all"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-sunset-500 transition-all"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-sunset-500 transition-all"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-sunset-500 transition-all"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Links Column 1 -->
            <div class="md:col-span-2 md:col-start-6">
                <h4 class="text-white font-montserrat font-bold mb-6 uppercase text-sm tracking-wider">Discover</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('destinations.index') }}" class="hover:text-sunset-500 transition pl-0 hover:pl-2">Destinations</a></li>
                    <li><a href="{{ route('hotels.index') }}" class="hover:text-sunset-500 transition pl-0 hover:pl-2">Resorts & Hotels</a></li>
                    <li><a href="{{ route('paket-tours.index') }}" class="hover:text-sunset-500 transition pl-0 hover:pl-2">Tour Packages</a></li>
                    <li><a href="{{ route('cultures.index') }}" class="hover:text-sunset-500 transition pl-0 hover:pl-2">Culture & Blogs</a></li>
                </ul>
            </div>

            <!-- Links Column 2 -->
            <div class="md:col-span-2">
                <h4 class="text-white font-montserrat font-bold mb-6 uppercase text-sm tracking-wider">Support</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('booking.checkForm') }}" class="hover:text-sunset-500 transition pl-0 hover:pl-2">Manage Booking</a></li>
                    <li><a href="#" class="hover:text-sunset-500 transition pl-0 hover:pl-2">Contact Us</a></li>
                    <li><a href="#" class="hover:text-sunset-500 transition pl-0 hover:pl-2">FAQs</a></li>
                    <li><a href="#" class="hover:text-sunset-500 transition pl-0 hover:pl-2">Privacy Policy</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="md:col-span-3">
                <h4 class="text-white font-montserrat font-bold mb-6 uppercase text-sm tracking-wider">Contact</h4>
                <div class="space-y-4 text-sm">
                    <p class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-sunset-500 mt-1"></i>
                        <span>Kupang, East Nusa Tenggara, Indonesia</span>
                    </p>
                    <p class="flex items-center gap-3">
                        <i class="fas fa-phone text-sunset-500"></i>
                        <a href="tel:+6281234567890" class="hover:text-white transition">+62 812 3456 7890</a>
                    </p>
                    <p class="flex items-center gap-3">
                        <i class="fas fa-envelope text-sunset-500"></i>
                        <a href="mailto:explore@wonderfulntt.id" class="hover:text-white transition">explore@wonderfulntt.id</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            <p>© {{ date('Y') }} Wonderful NTT. All rights reserved.</p>
            <p class="flex items-center gap-2">Crafted with <i class="fas fa-heart text-sunset-500 text-xs"></i> for adventurers.</p>
        </div>
    </div>
</footer>

<!-- ====== SCRIPTS ====== -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Navbar Scroll Effect ──
    const navbar = document.getElementById('main-navbar');
    function checkScroll() {
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    }
    window.addEventListener('scroll', checkScroll, { passive: true });
    checkScroll();

    // ── Mobile Menu Toggle ──
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            if (mobileMenu.classList.contains('hidden')) {
                menuIcon.className = 'fas fa-bars text-lg';
            } else {
                menuIcon.className = 'fas fa-times text-lg';
            }
        });
    }

    // ── Scroll Reveal ──
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // ── User Dropdown ──
    const userBtn = document.getElementById('user-menu-btn');
    const userDropdown = document.getElementById('user-dropdown');
    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', () => userDropdown.classList.add('hidden'));
    }
});
</script>

<!-- ====== AI CHAT POPUP ====== -->
<div id="chat-popup-wrapper">
    <!-- Floating Toggle Button -->
    <button id="chat-fab"
        onclick="document.dispatchEvent(new CustomEvent('toggle-chat'))"
        class="fixed bottom-6 right-6 z-[90] group flex items-center gap-3 bg-gradient-to-r from-sunset-500 to-sunset-600 hover:from-sunset-600 hover:to-sunset-500 text-white font-bold px-4 py-3.5 rounded-full shadow-[0_10px_25px_rgba(255,107,53,0.4)] hover:scale-105 active:scale-95 transition-all duration-300">
        <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-500 ease-out text-sm font-montserrat tracking-tight">
            Tanya Ara
        </span>
        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
            <i id="fab-icon" class="fas fa-robot text-base"></i>
        </div>
    </button>

    <!-- Backdrop -->
    <div id="chat-backdrop"
        onclick="document.dispatchEvent(new CustomEvent('close-chat'))"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[95] transition-opacity"></div>

    <!-- Slide-in Chat Panel -->
    <div id="chat-panel"
        class="fixed top-0 right-0 h-full w-full max-w-md z-[100] transform translate-x-full transition-transform duration-300 ease-out flex flex-col"
        style="background: #000f1f;">
        <livewire:travel-chat :popup="true" />
    </div>
</div>

<script>
(function () {
    let chatOpen = false;
    const panel    = document.getElementById('chat-panel');
    const backdrop = document.getElementById('chat-backdrop');
    const fabIcon  = document.getElementById('fab-icon');

    function openChat() {
        chatOpen = true;
        panel.classList.remove('translate-x-full');
        backdrop.classList.remove('hidden');
        fabIcon.className = 'fas fa-times text-base';
        document.body.style.overflow = 'hidden';
    }

    function closeChat() {
        chatOpen = false;
        panel.classList.add('translate-x-full');
        backdrop.classList.add('hidden');
        fabIcon.className = 'fas fa-robot text-base';
        document.body.style.overflow = '';
    }

    document.addEventListener('toggle-chat', () => chatOpen ? closeChat() : openChat());
    document.addEventListener('open-chat',   openChat);
    document.addEventListener('close-chat',  closeChat);
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && chatOpen) closeChat(); });
})();
</script>

@stack('scripts')
</body>
</html>