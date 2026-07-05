@extends('layouts.app')

@section('title', $paketTour->name . ' — Pesona NTT Tour Packages')
@section('meta_description', Str::limit(strip_tags($paketTour->description ?? "Paket tour {$paketTour->name}, {$paketTour->days} hari menjelajahi keindahan Nusa Tenggara Timur."), 160))
@section('og_title', $paketTour->name . ' — Pesona NTT')
@section('og_image', $paketTour->thumbnail ? asset('storage/' . $paketTour->thumbnail) : asset('images/default-tour-image.jpg'))

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<style>
  .hotel-image {
    max-height: 200px;
    width: 100%;
    object-fit: cover;
    border-radius: 1rem;
    margin-top: 1.5rem;
    display: none;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
  }
  .hotel-image.active {
    display: block;
    animation: fadeIn 0.5s ease-in-out;
  }
  @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
  }
</style>
@endsection

@section('content')
@php
  $photos = is_array($paketTour->photos) ? $paketTour->photos : json_decode($paketTour->photos ?? '[]', true);
  $fallbackImage = asset('images/default-tour-image.jpg');
  $fallbackHotelImage = asset('images/default-hotel-image.jpg');
@endphp

<div class="bg-paper min-h-screen pb-20 pt-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-16 reveal">
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-ink font-serif tracking-tight mb-6">{{ $paketTour->name }}</h1>
            
            <div class="mt-4 flex flex-wrap justify-center gap-4">
                <span class="px-5 py-2.5 rounded-full bg-surface border border-line text-ink text-sm font-semibold flex items-center uppercase tracking-widest">
                    <i class="fas fa-map-marker-alt text-clay mr-2 text-lg"></i> {{ $paketTour->location }}
                </span>
                
                @if ($paketTour->is_popular)
                    <span class="px-5 py-2.5 rounded-full bg-clay/10 border border-coral/25 text-clay text-sm font-semibold flex items-center uppercase tracking-widest">
                        <i class="fas fa-fire mr-2"></i> Popular Choice
                    </span>
                @endif
                <span class="px-5 py-2.5 rounded-full bg-surface border border-line text-ink text-sm font-semibold flex items-center uppercase tracking-widest">
                    <i class="fas fa-clock text-clay mr-2"></i> {{ $paketTour->days }} Days Journey
                </span>
            </div>
            
            <p class="mt-8 text-muted max-w-3xl mx-auto text-lg leading-relaxed">
                Explore the magnificent <span class="font-bold text-clay">{{ $paketTour->location }}</span> with our exclusive tour package designed for unforgettable memories.
            </p>
        </div>

        <!-- Image + Detail Grid -->
        <div class="grid lg:grid-cols-3 gap-10">
            <!-- Left: Image Section -->
            <div class="lg:col-span-2 space-y-6 reveal">
                @php
                    $main = $photos[0] ?? null;
                    $mainPath = is_array($main) ? ($main['path'] ?? '') : $main;
                @endphp
                <div class="rounded-2xl overflow-hidden shadow-xl border border-line aspect-[16/9] relative group">
                    <img src="{{ asset('storage/' . ltrim($mainPath, '/')) }}"
                         alt="Main Image"
                         data-clickable
                         class="w-full h-full object-cover cursor-zoom-in transition-transform duration-700 group-hover:scale-105"
                         onclick="showImageModal(this.src)"
                         onerror="this.src='{{ $fallbackImage }}';">
                    <div class="absolute inset-0 bg-ink/10 group-hover:bg-transparent transition-colors duration-500 pointer-events-none"></div>
                </div>

                <!-- Thumbnails Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach (array_slice($photos, 1, 4) as $thumb)
                        @php $path = is_array($thumb) ? ($thumb['path'] ?? '') : $thumb; @endphp
                        <div class="rounded-xl border border-line overflow-hidden relative group h-32">
                            <img src="{{ asset('storage/' . ltrim($path, '/')) }}"
                                 alt="Thumbnail"
                                 class="w-full h-full object-cover cursor-zoom-in transition-transform duration-500 group-hover:scale-110"
                                 onclick="showImageModal(this.src)"
                                 onerror="this.src='{{ $fallbackImage }}';">
                            <div class="absolute inset-0 bg-ink/10 group-hover:bg-transparent transition-colors duration-500 pointer-events-none"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Detail & Booking Setup -->
            <div class="space-y-8 reveal lg:mt-0 mt-8">
                <!-- Price Card -->
                <div class="bg-surface border border-line rounded-2xl p-8 relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-40 h-40 bg-clay rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] uppercase tracking-widest font-bold text-muted mb-2">Package Price</p>
                        <div class="text-4xl font-bold text-clay font-serif mb-4">
                            Rp {{ number_format($paketTour->price, 0, ',', '.') }}
                        </div>
                        <div class="flex items-center gap-2 text-sm font-medium text-ink bg-paper w-fit px-4 py-2 rounded-lg border border-line">
                            <i class="fas fa-calendar-alt text-clay"></i> {{ $paketTour->days }} Days Valid
                        </div>
                    </div>
                </div>

                <!-- Specs Card -->
                <div class="bg-surface border border-line rounded-2xl p-8">
                    <h3 class="text-2xl font-bold text-ink font-serif tracking-tight mb-6">Package Details</h3>
                    <div class="space-y-6 text-sm text-muted">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-paper border border-line hover:border-clay/30 hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-full bg-surface flex items-center justify-center text-clay shrink-0 shadow-sm border border-line">
                                <i class="fas fa-map-marked-alt text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] uppercase tracking-widest font-bold text-muted mb-1">Destination</h4>
                                <span class="font-bold text-ink text-base">{{ $paketTour->location }}</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-xl bg-paper border border-line hover:border-clay/30 hover:shadow-md transition-all">
                            <div class="w-10 h-10 rounded-full bg-surface flex items-center justify-center text-clay shrink-0 shadow-sm border border-line">
                                <i class="fas fa-layer-group text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] uppercase tracking-widest font-bold text-muted mb-1">Category</h4>
                                <span class="font-bold text-ink text-base">{{ ucfirst($paketTour->category ?? 'General') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-8 border-t border-line">
                        @auth
                        <a href="{{ route('paket-tour.create', $paketTour->id) }}" class="inline-flex items-center justify-center gap-2 bg-clay text-paper font-bold w-full py-4 text-center rounded-xl transition-all hover:-translate-y-0.5 text-lg group">
                            Book This Tour <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 w-full py-4 text-center rounded-xl font-bold text-lg text-paper transition-all bg-ink hover:bg-ink/90">
                            <i class="fas fa-lock mr-2 text-clay"></i> Login untuk Book Tour
                        </a>
                        <p class="text-xs text-muted text-center mt-3">Daftar gratis, booking mudah & aman</p>
                        @endauth
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Image Popup Modal -->
<div id="imageModal" class="fixed inset-0 z-[100] hidden bg-ink/95 backdrop-blur-sm items-center justify-center fade-in">
    <div class="relative w-full max-w-5xl mx-auto px-4 flex justify-center items-center h-full">
        <button onclick="closeImageModal()" class="absolute top-6 right-6 text-paper hover:text-clay transition-colors bg-paper/10 hover:bg-paper/20 rounded-full w-12 h-12 flex items-center justify-center backdrop-blur-md border border-paper/20 z-50">
            <i class="fas fa-times text-xl"></i>
        </button>
        <img id="modalImage" src="#" alt="Popup Image" class="max-h-[85vh] max-w-full rounded-2xl shadow-2xl object-contain border-4 border-paper/10" />
    </div>
</div>

<script>
    function showImageModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        if (src && modal && modalImg) {
            modalImg.src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // prevent scrolling
        }
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        if (modal && modalImg) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            modalImg.src = '';
            document.body.style.overflow = 'auto'; // allow scrolling
        }
    }

    document.addEventListener('click', function (e) {
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        if (modal && modal.classList.contains('flex') && !modalImg.contains(e.target) && !e.target.closest('img[data-clickable]') && !e.target.closest('button')) {
            closeImageModal();
        }
    });
</script>
@endsection