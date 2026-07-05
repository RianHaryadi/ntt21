<x-editorial.layout>
    <!-- Hero Section -->
    <section class="relative py-24 lg:py-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="font-serif text-5xl md:text-7xl leading-[1.05] tracking-tight mb-8">
                    Discover the world's most <span class="italic text-clay">extraordinary</span> places.
                </h1>
                <p class="text-lg md:text-xl text-muted leading-relaxed mb-10 max-w-xl">
                    Curated journeys for the discerning traveler. Immerse yourself in culture, nature, and unforgettable experiences.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-editorial.btn as="a" href="/editorial/search">Explore Destinations</x-editorial.btn>
                    <x-editorial.btn variant="secondary" as="a" href="/editorial/itinerary">View Itineraries</x-editorial.btn>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Curated Trips (Horizontal Scroll) -->
    <section class="py-20 bg-surface/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-6">
            <div>
                <h2 class="font-serif text-3xl md:text-4xl mb-3">Curated Journeys</h2>
                <p class="text-muted">Handpicked experiences for your next adventure.</p>
            </div>
            <a href="/editorial/search" class="inline-flex items-center text-sm font-medium hover:text-clay transition-colors group">
                View all <span class="ml-2 transition-transform group-hover:translate-x-1">→</span>
            </a>
        </div>
        
        <!-- Horizontal Scroll Container -->
        <div class="flex overflow-x-auto pb-8 snap-x snap-mandatory hide-scrollbar pl-4 sm:pl-6 lg:pl-8 pr-4 sm:pr-6 lg:pr-8 gap-6 max-w-7xl mx-auto">
            @php
                $trips = [
                    ['name' => 'Kyoto Serenity', 'subtitle' => 'Japan', 'price' => '$2,400', 'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Amalfi Coast', 'subtitle' => 'Italy', 'price' => '$3,100', 'image' => 'https://images.unsplash.com/photo-1533682805518-48d1f5e8fc43?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Sahara Expedition', 'subtitle' => 'Morocco', 'price' => '$1,850', 'image' => 'https://images.unsplash.com/photo-1542401886-65d6c61db217?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Patagonia Trails', 'subtitle' => 'Chile', 'price' => '$2,900', 'image' => 'https://images.unsplash.com/photo-1534067783941-51c9c23ecefd?q=80&w=800&auto=format&fit=crop'],
                ];
            @endphp
            
            @foreach($trips as $trip)
                <div class="snap-start shrink-0 w-[280px] md:w-[320px]">
                    <x-editorial.card 
                        :title="$trip['name']" 
                        :subtitle="$trip['subtitle']"
                        :price="$trip['price']"
                        :image="$trip['image']"
                        href="/editorial/destination"
                    />
                </div>
            @endforeach
        </div>
    </section>
</x-editorial.layout>
