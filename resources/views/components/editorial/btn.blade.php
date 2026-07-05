@props(['variant' => 'primary', 'as' => 'button', 'href' => '#'])

@php
    $baseClasses = "inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-full transition-all duration-300 ease-out hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-clay/50";

    $variants = [
        'primary' => "bg-clay text-white hover:bg-clay/90 shadow-sm shadow-clay/20",
        'secondary' => "bg-paper text-ink border border-line hover:border-clay hover:text-clay",
        'ghost' => "bg-transparent text-ink hover:bg-surface",
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if($as === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
