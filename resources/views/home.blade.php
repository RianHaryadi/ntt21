@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<style>
    /* HERO SECTION */
    .hero-cinematic {
        position: relative;
        height: 100vh;
        min-height: 600px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .hero-bg {
        position: absolute; inset: 0;
        background: url('https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=2000&q=80') center/cover no-repeat;
        /* Subtle Parallax fallback */
        background-attachment: fixed;
    }
    .hero-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to bottom, rgba(0,26,51,0.6) 0%, rgba(0,26,51,0.8) 100%);
    }

    /* SEARCH BAR */
    .search-panel {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 100px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    @media (max-width: 768px) {
        .search-panel { border-radius: 20px; }
    }

    /* HORIZONTAL PACKAGE CARD */
    .cinematic-package-card {
        display: flex;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .cinematic-package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.12);
    }
    @media (max-width: 768px) {
        .cinematic-package-card { flex-direction: column; }
    }

    /* EXPERIENCE CATEGORIES */
    .exp-card {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        aspect-ratio: 4/5;
    }
    .exp-card img {
        transition: transform 0.6s ease;
        width: 100%; height: 100%; object-fit: cover;
    }
    .exp-card:hover img { transform: scale(1.1); }
    .exp-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,26,51,0.9), transparent 60%);
    }

    /* CTA SECTION */
    .cta-cinematic {
        position: relative;
        padding: 100px 0;
        background: url('https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?auto=format&fit=crop&w=2000&q=80') center/cover no-repeat fixed;
    }
    .cta-cinematic::after {
        content: ''; position: absolute; inset: 0;
        background: rgba(0,26,51,0.7);
    }
</style>
@endpush

@section('content')

{{-- ════════════════════ HERO ════════════════════ --}}
<header class="hero-cinematic">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    
    <div class="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-20">
        <h1 class="text-5xl md:text-7xl font-black text-white mb-6 font-montserrat reveal" style="text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Explore The World
        </h1>
        <p class="text-xl md:text-2xl text-white/90 font-medium mb-12 max-w-3xl mx-auto reveal" style="transition-delay: 0.1s;">
            Discover unforgettable destinations and experiences curated just for you.
        </p>

        <!-- Search Bar -->
        <div class="search-panel max-w-4xl mx-auto p-3 hidden md:block reveal" style="transition-delay: 0.2s;">
            <form action="{{ route('destinations.index') }}" method="GET" class="flex items-center gap-2">
                <div class="flex-1 px-6 border-r border-gray-200">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest text-left mb-1">Destination</label>
                    <input type="text" placeholder="Where to?" class="w-full bg-transparent font-medium text-ocean-900 focus:outline-none placeholder-gray-400">
                </div>
                <div class="flex-1 px-6 border-r border-gray-200">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest text-left mb-1">Date</label>
                    <input type="text" placeholder="Add dates" class="w-full bg-transparent font-medium text-ocean-900 focus:outline-none placeholder-gray-400" onfocus="(this.type='date')" onblur="(this.type='text')">
                </div>
                <div class="flex-1 px-6">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest text-left mb-1">Travelers</label>
                    <input type="number" placeholder="2 Guests" class="w-full bg-transparent font-medium text-ocean-900 focus:outline-none placeholder-gray-400" min="1">
                </div>
                <button type="submit" class="btn-primary w-14 h-14 !p-0 rounded-full flex-shrink-0">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </form>
        </div>
        
        <!-- Mobile Search CTA -->
        <div class="md:hidden mt-8 reveal" style="transition-delay: 0.2s;">
            <a href="{{ route('destinations.index') }}" class="btn-primary px-8 py-4 text-lg">
                Start Your Journey
            </a>
        </div>
    </div>
</header>


{{-- ════════════════════ POPULAR DESTINATIONS ════════════════════ --}}
<section class="py-24 bg-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <h2 class="text-3xl md:text-4xl font-black text-ocean-900 mb-4 tracking-tight">Popular Destinations</h2>
            <div class="h-1 w-20 bg-sunset-500 rounded-full mx-auto mb-6"></div>
            <p class="text-gray-500 max-w-2xl mx-auto">Discover the most breathtaking locations carefully selected for your next adventure.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($destinations as $destination)
            <a href="{{ route('destinations.show', $destination) }}" class="cinematic-card block group reveal">
                <div class="card-img-wrap h-64">
                    <img src="{{ $destination->image ? asset('storage/' . $destination->image) : asset('images/fallback.jpg') }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-ocean-900/20 group-hover:bg-transparent transition-colors"></div>
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-ocean-900">
                        {{ $destination->category ?? 'Destination' }}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-ocean-900 mb-2 font-montserrat">{{ $destination->name }}</h3>
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                        {{ \Illuminate\Support\Str::limit($destination->description, 100) }}
                    </p>
                    <div class="flex items-center justify-between mt-auto">
                        <div class="flex items-center gap-1.5 text-sunset-500 text-sm font-medium">
                            <i class="fas fa-map-marker-alt"></i> {{ $destination->location }}
                        </div>
                        <div class="text-ocean-900 font-black">
                            Rp {{ number_format($destination->price, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full py-10 text-center text-gray-400">
                <i class="fas fa-map-signs text-4xl mb-4 text-gray-300"></i>
                <p>No destinations found.</p>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-12 reveal">
            <a href="{{ route('destinations.index') }}" class="btn-outline !text-ocean-900 !border-ocean-900 hover:!bg-ocean-900 hover:!text-white">
                View All Destinations
            </a>
        </div>
    </div>
</section>


{{-- ════════════════════ TRAVEL EXPERIENCES (CATEGORIES) ════════════════════ --}}
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 reveal">
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-ocean-900 mb-4 tracking-tight">Travel Experiences</h2>
                <div class="h-1 w-20 bg-sunset-500 rounded-full mb-6 relative"></div>
                <p class="text-gray-500 max-w-lg">Find the perfect trip based on your preferred travel style.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $experiences = [
                    ['title' => 'Adventure', 'img' => 'https://images.unsplash.com/photo-1522163182402-834f871fd851?w=800&q=80', 'count' => '12 Tours'],
                    ['title' => 'Beach Vibes', 'img' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80', 'count' => '18 Tours'],
                    ['title' => 'Culture', 'img' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=800&q=80', 'count' => '8 Tours'],
                    ['title' => 'Nature', 'img' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&q=80', 'count' => '15 Tours'],
                ];
            @endphp
            @foreach($experiences as $exp)
            <a href="#" class="exp-card group border border-gray-100 reveal">
                <img src="{{ $exp['img'] }}" alt="{{ $exp['title'] }}">
                <div class="exp-overlay"></div>
                <div class="absolute bottom-0 left-0 p-6 z-10 w-full">
                    <h3 class="text-2xl font-bold text-white mb-1 font-montserrat">{{ $exp['title'] }}</h3>
                    <p class="text-white/80 text-sm flex items-center justify-between">
                        {{ $exp['count'] }}
                        <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-opacity transform -translate-x-2 group-hover:translate-x-0 transition-transform"></i>
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>


{{-- ════════════════════ TRAVEL PACKAGES ════════════════════ --}}
<section class="py-24 bg-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <h2 class="text-3xl md:text-4xl font-black text-ocean-900 mb-4 tracking-tight">Exclusive Packages</h2>
            <div class="h-1 w-20 bg-sunset-500 rounded-full mx-auto mb-6"></div>
        </div>

        <div class="space-y-6">
            @forelse($TourPackage ?? [] as $idx => $package)
                @if($idx < 3)
                <div class="cinematic-package-card reveal">
                    <div class="md:w-1/3 relative h-60 md:h-auto overflow-hidden">
                        <img src="{{ $package->thumbnail ? asset('storage/' . $package->thumbnail) : asset('images/tour-fallback.jpg') }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-sunset-500 text-white text-xs font-bold px-3 py-1 rounded-full">Best Value</div>
                    </div>
                    <div class="md:w-2/3 p-6 flex flex-col justify-center relative">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-2xl font-bold text-ocean-900 font-montserrat tracking-tight">{{ $package->name }}</h3>
                            <div class="text-right">
                                <span class="text-xs text-gray-400 block">From</span>
                                <span class="text-xl font-black text-sunset-500">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-4 font-medium">
                            <span class="flex items-center gap-1.5"><i class="far fa-clock text-ocean-500"></i> {{ $package->duration ?? '3 Days 2 Nights' }}</span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-ocean-500"></i> {{ $package->location }}</span>
                            <span class="flex items-center gap-1.5 text-yellow-500"><i class="fas fa-star"></i> 4.8 (120 Reviews)</span>
                        </div>
                        
                        <p class="text-gray-600 mb-6 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit($package->description, 150) }}
                        </p>
                        
                        <div class="mt-auto flex justify-between items-center border-t border-gray-100 pt-5">
                            <div class="flex -space-x-2">
                                <img src="https://randomuser.me/api/portraits/women/10.jpg" class="w-8 h-8 rounded-full border-2 border-white">
                                <img src="https://randomuser.me/api/portraits/men/20.jpg" class="w-8 h-8 rounded-full border-2 border-white">
                                <img src="https://randomuser.me/api/portraits/women/30.jpg" class="w-8 h-8 rounded-full border-2 border-white">
                                <span class="w-8 h-8 rounded-full border-2 border-white bg-gray-100 text-xs flex items-center justify-center font-bold text-gray-500">+1k</span>
                            </div>
                            <a href="{{ route('paket-tour.create', $package->id) }}" class="btn-primary py-2 px-6">Book Package</a>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <div class="text-center py-10 text-gray-400 border border-dashed border-gray-300 rounded-2xl">
                    <p>No packages available at the moment.</p>
                </div>
            @endforelse
        </div>
        
        @if(isset($TourPackage) && count($TourPackage) > 0)
        <div class="text-center mt-12 reveal">
            <a href="{{ route('paket-tours.index') }}" class="btn-outline !text-ocean-900 !border-ocean-900 hover:!bg-ocean-900 hover:!text-white">
                View All Packages
            </a>
        </div>
        @endif
    </div>
</section>


{{-- ════════════════════ AI TRAVEL CHATBOT BANNER ════════════════════ --}}
<section class="py-20 bg-gradient-to-r from-ocean-900 to-ocean-950 text-white relative overflow-hidden">
    <!-- Background accents -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-sunset-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-ocean-500/10 rounded-full blur-3xl -ml-20 -mb-20"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-8 reveal">
                <div class="inline-flex items-center gap-2 bg-sunset-500/10 border border-sunset-500/30 text-sunset-500 font-bold text-xs uppercase tracking-widest px-3.5 py-1.5 rounded-full mb-6">
                    <i class="fas fa-robot"></i> Asisten Perjalanan Pintar
                </div>
                <h2 class="text-3xl md:text-5xl font-black mb-4 font-montserrat tracking-tight leading-tight">
                    Bingung Menentukan Itinerary di NTT?<br class="hidden md:inline"> Tanya <span class="text-sunset-500">Ara AI</span> Saja!
                </h2>
                <p class="text-gray-300 text-lg mb-8 max-w-2xl leading-relaxed">
                    Ara akan membantu Anda menyusun rencana liburan kustom ke destinasi-destinasi tersembunyi (*hidden gems*) di NTT yang jarang terjamah wisatawan umum, lengkap dengan estimasi akomodasi, rute harian, dan rekomendasi kuliner lokal.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('travel.chat') }}" class="btn-primary py-3.5 px-8 text-base font-bold shadow-[0_4px_15px_rgba(255,107,53,0.3)]">
                        Mulai Chat Dengan Ara
                    </a>
                    <a href="{{ route('destinations.index') }}" class="btn-outline border-white/20 hover:border-white py-3.5 px-8 text-base font-bold">
                        Jelajahi Manual
                    </a>
                </div>
            </div>
            
            <div class="lg:col-span-4 flex justify-center reveal" style="transition-delay: 0.2s;">
                <div class="relative">
                    <!-- Glowing frame -->
                    <div class="absolute -inset-1 bg-gradient-to-r from-sunset-500 to-ocean-500 rounded-2xl blur-lg opacity-30 animate-pulse"></div>
                    <div class="bg-white/5 border border-white/10 p-8 rounded-2xl backdrop-blur-md max-w-sm relative text-center">
                        <div class="w-16 h-16 rounded-full bg-sunset-500 flex items-center justify-center text-white text-3xl mx-auto mb-4 shadow-[0_0_20px_rgba(255,107,53,0.4)]">
                            <i class="fas fa-compass"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-2 font-montserrat">Ara Asisten AI</h3>
                        <p class="text-gray-400 text-sm mb-4">"Halo! Siap menjelajahi Kampung Adat Wae Rebo, Pulau Alor, atau Pantai Koka? Katakan apa preferensi liburan Anda!"</p>
                        <div class="flex justify-center gap-1.5 text-xs">
                            <span class="px-2.5 py-1 bg-sunset-500/10 text-sunset-500 rounded-full font-semibold">100% NTT Hidden Gems</span>
                            <span class="px-2.5 py-1 bg-sunset-500/10 text-sunset-500 rounded-full font-semibold">Gratis & Cepat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ════════════════════ TESTIMONIALS ════════════════════ --}}
<section class="py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16 reveal">
            <h2 class="text-3xl md:text-4xl font-black text-ocean-900 mb-4 tracking-tight">Traveler Stories</h2>
            <div class="h-1 w-20 bg-sunset-500 rounded-full mx-auto mb-6"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach([
                ['name'=>'Sarah D.', 'img'=>'women/12', 'text'=>'Absolutely magical experience. The landscapes were breathtaking and the entire trip was organized perfectly from start to finish.'],
                ['name'=>'Michael R.', 'img'=>'men/32', 'text'=>'The cultural tours opened my eyes to traditions I never knew existed. The cinematic beauty of the islands is unmatched.'],
                ['name'=>'Elena K.', 'img'=>'women/44', 'text'=>'A seamless booking experience and top-tier luxury. Watching the sunset over the ocean from our pristine resort was unforgettable.']
            ] as $t)
            <div class="bg-light p-8 rounded-2xl relative border border-gray-100 reveal">
                <i class="fas fa-quote-right absolute top-6 right-8 text-4xl text-gray-200"></i>
                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <img src="https://randomuser.me/api/portraits/{{ $t['img'] }}.jpg" class="w-14 h-14 rounded-full object-cover shadow-md">
                    <div>
                        <h4 class="font-bold text-ocean-900 font-montserrat">{{ $t['name'] }}</h4>
                        <div class="text-yellow-400 text-xs"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    </div>
                </div>
                <p class="text-gray-600 leading-relaxed text-sm relative z-10">"{{ $t['text'] }}"</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ════════════════════ BLOG / CULTURE ════════════════════ --}}
<section class="py-24 bg-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-16 reveal">
            <div>
                <h2 class="text-3xl md:text-4xl font-black text-ocean-900 mb-4 tracking-tight">Travel Guides & Tips</h2>
                <div class="h-1 w-20 bg-sunset-500 rounded-full"></div>
            </div>
            <a href="{{ route('cultures.index') }}" class="hidden md:inline-flex text-ocean-900 font-bold hover:text-sunset-500 transition-colors">
                View All Posts <i class="fas fa-arrow-right ml-2 mt-1"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($cultures ?? [] as $index => $culture)
                @if($index < 3)
                <div class="bg-white rounded-2xl overflow-hidden shadow-soft reveal group">
                    <div class="h-48 overflow-hidden relative">
                        <img src="{{ asset('storage/' . $culture->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 left-4 bg-ocean-900/80 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full">Culture</div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-ocean-900 mb-3 font-montserrat group-hover:text-sunset-500 transition-colors">{{ $culture->title }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $culture->description_1 }}</p>
                        <a href="{{ route('cultures.index') }}" class="text-sm font-bold text-sunset-500">Read More →</a>
                    </div>
                </div>
                @endif
            @empty
                <div class="col-span-full text-center text-gray-400">No guides found.</div>
            @endforelse
        </div>
    </div>
</section>


{{-- ════════════════════ CTA ════════════════════ --}}
<section class="cta-cinematic reveal">
    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
        <span class="text-sunset-500 font-bold tracking-widest uppercase mb-4 block">Are you ready?</span>
        <h2 class="text-4xl md:text-6xl font-black text-white mb-8 font-montserrat tracking-tight leading-tight">
            Your Next Adventure Awaits
        </h2>
        <a href="{{ route('destinations.index') }}" class="btn-primary py-4 px-10 text-lg">
            Start Planning
        </a>
    </div>
</section>

@endsection