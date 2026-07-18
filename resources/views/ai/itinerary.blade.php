@extends('layouts.app')

@section('title', 'AI Itinerary Builder — Pesona NTT')
@section('meta_description', 'Susun rencana perjalanan NTT harian secara otomatis dengan AI — dari destinasi, hotel, hingga paket tour asli yang langsung bisa dibooking.')

@section('content')
<div class="pt-24 pb-20 min-h-screen bg-paper">

    {{-- Hero --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="inline-flex items-center gap-2 bg-laut/10 border border-laut/25 text-laut text-xs font-bold px-3 py-1.5 rounded-full mb-4">
            <span class="w-1.5 h-1.5 bg-laut rounded-full animate-pulse"></span>
            AI Itinerary Builder · Powered by Claude
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-ink font-serif tracking-tight leading-tight">
            Rencana Perjalanan NTT-mu,<br class="hidden md:block">
            <span class="text-laut">disusun AI dalam hitungan detik.</span>
        </h1>
        <p class="text-muted text-base leading-relaxed max-w-2xl mt-4">
            Cukup pilih wilayah, tanggal, dan minatmu — AI akan meracik itinerary harian dari
            destinasi, hotel, dan paket tour <strong class="text-ink">asli</strong> di katalog kami,
            lengkap dengan estimasi budget dan langsung bisa dibooking.
        </p>
    </div>

    {{-- Builder --}}
    @livewire('itinerary-builder')

</div>
@endsection
