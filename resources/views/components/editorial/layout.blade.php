<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Wander | Editorial Travel' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-ink font-sans antialiased min-h-screen flex flex-col selection:bg-clay/20 selection:text-ink">
    <!-- Header with thin nav -->
    <header class="border-b border-line bg-surface/50 backdrop-blur-md sticky top-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/editorial/home" class="font-serif text-2xl tracking-tight text-ink transition-opacity hover:opacity-80">
                Wander.
            </a>
            <nav class="hidden md:flex gap-8 text-sm font-medium text-muted">
                <a href="/editorial/home" class="hover:text-ink transition-colors duration-200">Discover</a>
                <a href="/editorial/search" class="hover:text-ink transition-colors duration-200">Destinations</a>
                <a href="/editorial/itinerary" class="hover:text-ink transition-colors duration-200">Itineraries</a>
            </nav>
            <div class="flex items-center gap-4">
                <a href="#" class="text-sm font-medium text-muted hover:text-clay transition-colors duration-200 hidden sm:block">Log in</a>
                <x-editorial.btn as="a" href="#">Sign up</x-editorial.btn>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <footer class="border-t border-line bg-surface py-12 mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="font-serif text-2xl text-ink">Wander.</div>
            <div class="text-sm text-muted">© {{ date('Y') }} Wander Travel. Crafted with care.</div>
        </div>
    </footer>
</body>
</html>
