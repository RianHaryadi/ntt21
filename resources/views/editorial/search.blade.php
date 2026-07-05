<x-editorial.layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        
        <!-- Header & Filters -->
        <div class="mb-16">
            <h1 class="font-serif text-4xl md:text-5xl mb-6">All Destinations</h1>
            <p class="text-muted text-lg max-w-2xl mb-8">Find your next escape from our curated collection of extraordinary places around the globe.</p>
            
            <div class="flex flex-wrap gap-3">
                <x-editorial.tag class="bg-ink text-paper border-ink hover:bg-ink">All Regions</x-editorial.tag>
                <x-editorial.tag class="cursor-pointer">Asia</x-editorial.tag>
                <x-editorial.tag class="cursor-pointer">Europe</x-editorial.tag>
                <x-editorial.tag class="cursor-pointer">Africa</x-editorial.tag>
                <x-editorial.tag class="cursor-pointer">South America</x-editorial.tag>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @php
                $trips = [
                    ['name' => 'Kyoto Serenity', 'subtitle' => 'Japan', 'price' => '$2,400', 'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Amalfi Coast', 'subtitle' => 'Italy', 'price' => '$3,100', 'image' => 'https://images.unsplash.com/photo-1533682805518-48d1f5e8fc43?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Sahara Expedition', 'subtitle' => 'Morocco', 'price' => '$1,850', 'image' => 'https://images.unsplash.com/photo-1542401886-65d6c61db217?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Patagonia Trails', 'subtitle' => 'Chile', 'price' => '$2,900', 'image' => 'https://images.unsplash.com/photo-1534067783941-51c9c23ecefd?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Swiss Alps', 'subtitle' => 'Switzerland', 'price' => '$3,500', 'image' => 'https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?q=80&w=800&auto=format&fit=crop'],
                    ['name' => 'Bali Retreat', 'subtitle' => 'Indonesia', 'price' => '$1,200', 'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?q=80&w=800&auto=format&fit=crop'],
                ];
            @endphp
            
            @foreach($trips as $trip)
                <x-editorial.card 
                    :title="$trip['name']" 
                    :subtitle="$trip['subtitle']"
                    :price="$trip['price']"
                    :image="$trip['image']"
                    href="/editorial/destination"
                />
            @endforeach
        </div>
        
        <div class="mt-16 text-center">
            <x-editorial.btn variant="secondary">Load More</x-editorial.btn>
        </div>

    </div>
</x-editorial.layout>
