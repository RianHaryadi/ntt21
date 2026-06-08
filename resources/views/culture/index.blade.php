@extends('layouts.app')

@section('title', 'NTT Culture')

@push('styles')
<style>
    .culture-card {
        transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .culture-card:hover {
        transform: translateY(-5px);
    }
    .number-badge {
        background: #ff6b35;
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Montserrat', sans-serif;
        font-weight: 900; font-size: 1rem; color: white;
        flex-shrink: 0;
        box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
    }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="relative min-h-[60vh] flex items-center justify-center text-white overflow-hidden bg-ocean-900 reveal">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fit=crop&w=2070&q=80" 
             class="w-full h-full object-cover opacity-40 mix-blend-overlay" alt="Culture Hero">
        <div class="absolute inset-0 bg-gradient-to-t from-ocean-900 via-ocean-900/40 to-transparent"></div>
    </div>
    
    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto py-20">
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 border border-white/20 text-[10px] tracking-[0.2em] font-black text-sunset-500 mb-8 uppercase backdrop-blur-md">
            <i class="fas fa-masks-theater"></i> Living Heritage
        </div>
        <h1 class="text-6xl md:text-8xl font-black mb-8 font-montserrat tracking-tight leading-none drop-shadow-2xl">
            Immerse in <span class="text-sunset-500">Culture</span>
        </h1>
        <p class="text-lg md:text-xl text-white/70 max-w-2xl mx-auto font-medium tracking-wide font-inter">
            Discover the rich traditions, unique customs, and vibrant festivals that define the soul of East Nusa Tenggara.
        </p>
    </div>
</section>

{{-- ── CULTURE ENTRIES ── --}}
<section class="py-32 bg-light min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(isset($cultures) && $cultures->isEmpty())
        <div class="text-center py-32 bg-white rounded-[40px] shadow-2xl border-0 reveal">
            <div class="w-24 h-24 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-8 shadow-inner border border-gray-100">
                <i class="fas fa-masks-theater text-4xl text-gray-200"></i>
            </div>
            <h3 class="text-3xl font-black text-ocean-900 font-montserrat mb-4 tracking-tight">Check back soon</h3>
            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">We are currently curating the best cultural stories for you.</p>
        </div>
        @endif

        <div class="space-y-40">
            @foreach($cultures as $index => $culture)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24 items-center reveal">

                {{-- Image Component --}}
                <div class="lg:col-span-7 {{ $index % 2 === 0 ? '' : 'lg:order-2' }} relative group">
                    <div class="relative rounded-[40px] overflow-hidden shadow-2xl aspect-[16/10]">
                        <img src="{{ asset('storage/'.$culture->image) }}"
                             alt="{{ $culture->title }}"
                             class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        <div class="absolute inset-0 bg-ocean-900/10 transition-opacity group-hover:opacity-0"></div>
                    </div>
                    {{-- Decorative --}}
                    <div class="absolute -z-10 -bottom-10 {{ $index % 2 === 0 ? '-left-10' : '-right-10' }} w-64 h-64 bg-sunset-500/10 rounded-full filter blur-3xl"></div>
                </div>

                {{-- Text Content --}}
                <div class="lg:col-span-5 {{ $index % 2 === 0 ? '' : 'lg:order-1' }}">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="number-badge">{{ str_pad($index+1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="h-[2px] w-12 bg-sunset-500 opacity-30"></div>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-sunset-500">Heritage Study</span>
                    </div>

                    <h3 class="text-4xl md:text-5xl font-black text-ocean-900 font-montserrat leading-tight tracking-tight mb-8">{{ $culture->title }}</h3>

                    <div class="space-y-6 text-gray-500 leading-relaxed font-medium">
                        <p class="text-lg text-ocean-900/80">{{ $culture->description_1 }}</p>
                        @if($culture->description_2)
                        <p class="text-sm opacity-70">{{ $culture->description_2 }}</p>
                        @endif
                    </div>

                    @if($culture->tags)
                    <div class="flex flex-wrap gap-2 mt-12">
                        @foreach($culture->tags as $tag)
                        <span class="px-5 py-2.5 rounded-full bg-white border border-gray-100 text-ocean-900 text-[10px] font-black uppercase tracking-widest shadow-sm hover:border-sunset-500 transition-all cursor-default">
                            {{ trim($tag) }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── INSPIRING CTA ── --}}
<section class="relative py-32 bg-ocean-900 overflow-hidden reveal">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=2000&q=80" 
             class="w-full h-full object-cover opacity-20" alt="CTA Background">
        <div class="absolute inset-0 bg-gradient-to-r from-ocean-900 via-ocean-900/40 to-transparent"></div>
    </div>
    
    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
        <span class="text-sunset-500 font-black tracking-[0.3em] uppercase mb-6 block text-[10px]">Experience it Yourself</span>
        <h2 class="text-5xl md:text-7xl font-black text-white mb-12 font-montserrat leading-tight tracking-tight">
            The Soul of <span class="text-sunset-500">NTT</span> Awaits
        </h2>
        <a href="{{ route('paket-tours.index') }}" class="btn-primary py-5 px-12 text-lg shadow-2xl shadow-sunset-500/30 font-black transition-all active:scale-95">
            Book Cultural Journey <i class="fas fa-arrow-right ml-3 text-sm"></i>
        </a>
    </div>
</section>

@endsection