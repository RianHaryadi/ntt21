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
        background: #0F6E63;
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Satoshi', sans-serif;
        font-weight: 900; font-size: 1rem; color: white;
        flex-shrink: 0;
        box-shadow: 0 10px 20px rgba(15, 110, 99, 0.3);
    }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="relative min-h-[60vh] flex items-center justify-center text-paper overflow-hidden bg-ink pt-28">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1519817650390-64a93db51149?auto=format&fit=crop&w=2070&q=80" 
             class="w-full h-full object-cover opacity-40" alt="Culture Hero">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/40 to-transparent"></div>
    </div>
    
    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto py-20">
        <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-clay/10 border border-clay/20 text-[10px] tracking-[0.2em] font-bold text-clay mb-8 uppercase backdrop-blur-md">
            <i class="fas fa-masks-theater"></i> Living Heritage
        </div>
        <h1 class="text-6xl md:text-8xl font-bold mb-8 font-serif tracking-tight leading-none drop-shadow-2xl">
            Immerse in Culture
        </h1>
        <p class="text-lg md:text-xl text-paper/70 max-w-2xl mx-auto font-medium tracking-wide">
            Discover the rich traditions, unique customs, and vibrant festivals that define the soul of East Nusa Tenggara.
        </p>
    </div>
</section>

{{-- ── CULTURE ENTRIES ── --}}
<section class="py-32 bg-paper min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(isset($cultures) && $cultures->isEmpty())
        <div class="text-center py-32 bg-surface rounded-[40px] border border-line reveal">
            <div class="w-24 h-24 rounded-full bg-paper flex items-center justify-center mx-auto mb-8 border border-line">
                <i class="fas fa-masks-theater text-4xl text-muted"></i>
            </div>
            <h3 class="text-3xl font-bold text-ink font-serif mb-4 tracking-tight">Check back soon</h3>
            <p class="text-muted font-bold uppercase tracking-widest text-xs">We are currently curating the best cultural stories for you.</p>
        </div>
        @endif

        <div class="space-y-40">
            @foreach($cultures as $index => $culture)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24 items-center reveal">

                {{-- Image Component --}}
                <div class="lg:col-span-7 {{ $index % 2 === 0 ? '' : 'lg:order-2' }} relative group">
                    <div class="relative rounded-[40px] overflow-hidden shadow-2xl aspect-[16/10] border border-line">
                        <img src="{{ asset('storage/'.$culture->image) }}"
                             alt="{{ $culture->title }}"
                             class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                        <div class="absolute inset-0 bg-ink/10 transition-opacity group-hover:opacity-0"></div>
                    </div>
                    {{-- Decorative --}}
                    <div class="absolute -z-10 -bottom-10 {{ $index % 2 === 0 ? '-left-10' : '-right-10' }} w-64 h-64 bg-clay/10 rounded-full filter blur-3xl"></div>
                </div>

                {{-- Text Content --}}
                <div class="lg:col-span-5 {{ $index % 2 === 0 ? '' : 'lg:order-1' }}">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="number-badge">{{ str_pad($index+1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="h-[2px] w-12 bg-clay opacity-30"></div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-clay">Heritage Study</span>
                    </div>

                    <h3 class="text-4xl md:text-5xl font-bold text-ink font-serif leading-tight tracking-tight mb-8">{{ $culture->title }}</h3>

                    <div class="space-y-6 text-muted leading-relaxed font-medium">
                        <p class="text-lg text-ink/80">{{ $culture->description_1 }}</p>
                        @if($culture->description_2)
                        <p class="text-sm opacity-70">{{ $culture->description_2 }}</p>
                        @endif
                    </div>

                    @if($culture->tags)
                    <div class="flex flex-wrap gap-2 mt-12">
                        @foreach($culture->tags as $tag)
                        <span class="px-5 py-2.5 rounded-full bg-surface border border-line text-ink text-[10px] font-bold uppercase tracking-widest hover:border-clay transition-all cursor-default">
                            {{ trim($tag) }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($cultures->hasPages())
        <div class="mt-10 flex justify-center">
            {{ $cultures->links() }}
        </div>
        @endif
    </div>
</section>

{{-- ── INSPIRING CTA ── --}}
<section class="relative py-32 bg-ink overflow-hidden reveal">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=2000&q=80" 
             class="w-full h-full object-cover opacity-20" alt="CTA Background">
        <div class="absolute inset-0 bg-gradient-to-r from-ink via-petrol/40 to-transparent"></div>
    </div>
    
    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">
        <span class="text-clay font-bold tracking-[0.3em] uppercase mb-6 block text-[10px]">Experience it Yourself</span>
        <h2 class="text-5xl md:text-7xl font-bold text-paper mb-12 font-serif leading-tight tracking-tight">
            The Soul of NTT Awaits
        </h2>
        <a href="{{ route('paket-tours.index') }}" class="inline-flex items-center justify-center gap-3 bg-clay text-paper font-bold py-5 px-12 text-lg rounded-full hover:bg-clay/90 active:scale-95 transition-all">
            Book Cultural Journey <i class="fas fa-arrow-right text-sm"></i>
        </a>
    </div>
</section>

@endsection