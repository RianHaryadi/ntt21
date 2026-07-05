<x-editorial.layout>
    @php
        $destination = [
            'name' => 'Kyoto Serenity',
            'country' => 'Japan',
            'price' => '$2,400',
            'duration' => '7 Days',
            'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=1600&auto=format&fit=crop',
            'description' => 'Discover the ancient capital of Japan. Immerse yourself in the tranquility of zen gardens, majestic temples, and the timeless art of the tea ceremony.',
        ];
    @endphp

    <!-- Hero Image -->
    <div class="w-full h-[60vh] md:h-[70vh] relative">
        <img src="{{ $destination['image'] }}" alt="{{ $destination['name'] }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-ink/20"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-10 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-paper p-8 md:p-12 rounded-2xl shadow-sm border border-line mb-12">
                    <span class="text-sm font-semibold tracking-widest uppercase text-sage mb-4 block">{{ $destination['country'] }}</span>
                    <h1 class="font-serif text-4xl md:text-6xl mb-6">{{ $destination['name'] }}</h1>
                    <div class="flex flex-wrap gap-3 mb-8">
                        <x-editorial.tag>{{ $destination['duration'] }}</x-editorial.tag>
                        <x-editorial.tag>Cultural</x-editorial.tag>
                        <x-editorial.tag>Guided</x-editorial.tag>
                    </div>
                    <p class="text-lg text-muted leading-relaxed">{{ $destination['description'] }}</p>
                </div>

                <!-- Itinerary Timeline -->
                <div>
                    <h2 class="font-serif text-3xl mb-8">Itinerary</h2>
                    <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-line">
                        
                        <!-- Day 1 -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-line bg-surface text-sage font-serif shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 relative shadow-sm">
                                1
                            </div>
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-surface p-6 rounded-xl border border-line">
                                <h3 class="font-serif text-xl mb-2">Arrival in Kyoto</h3>
                                <p class="text-muted text-sm">Settle into your traditional ryokan and enjoy a welcome kaiseki dinner.</p>
                            </div>
                        </div>
                        
                        <!-- Day 2 -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-line bg-surface text-sage font-serif shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 relative shadow-sm">
                                2
                            </div>
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-surface p-6 rounded-xl border border-line">
                                <h3 class="font-serif text-xl mb-2">Temples & Gardens</h3>
                                <p class="text-muted text-sm">Visit Kinkaku-ji (Golden Pavilion) and the serene Ryoan-ji zen rock garden.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Sidebar / Sticky Booking -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-surface p-8 rounded-2xl border border-line shadow-sm">
                    <h3 class="font-serif text-2xl mb-2">Reserve this trip</h3>
                    <p class="text-muted text-sm mb-6">Secure your spot for the upcoming season.</p>
                    
                    <div class="flex justify-between items-end mb-6 pb-6 border-b border-line">
                        <span class="text-sm font-medium text-muted">Starting from</span>
                        <span class="text-3xl font-serif text-ink">{{ $destination['price'] }}</span>
                    </div>
                    
                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Duration</span>
                            <span class="font-medium text-ink">{{ $destination['duration'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted">Group Size</span>
                            <span class="font-medium text-ink">Max 8 people</span>
                        </div>
                    </div>

                    <x-editorial.btn class="w-full">Request Booking</x-editorial.btn>
                </div>
            </div>
            
        </div>
    </div>
</x-editorial.layout>
