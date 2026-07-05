<x-editorial.layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center mb-16">
            <span class="text-sm font-semibold tracking-widest uppercase text-sage mb-4 block">Sample Journey</span>
            <h1 class="font-serif text-4xl md:text-5xl mb-6">The Classic Japan Itinerary</h1>
            <p class="text-muted text-lg">10 Days / 9 Nights through Tokyo, Kyoto, and Osaka.</p>
        </div>

        <div class="space-y-12 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-line">
            
            @php
                $days = [
                    ['day' => 1, 'title' => 'Arrival in Tokyo', 'desc' => 'Welcome to the neon-lit capital. Transfer to your hotel in Shinjuku and rest. '],
                    ['day' => 2, 'title' => 'Traditional Tokyo', 'desc' => 'Visit Asakusa, Senso-ji temple, and enjoy a traditional tea ceremony.'],
                    ['day' => 3, 'title' => 'Bullet Train to Kyoto', 'desc' => 'Experience the Shinkansen. Arrive in Kyoto and explore the Gion district at dusk.'],
                    ['day' => 4, 'title' => 'Temples & Shrines', 'desc' => 'Fushimi Inari Taisha early morning, followed by Kiyomizu-dera and a traditional kaiseki dinner.'],
                ];
            @endphp

            @foreach($days as $day)
                <div class="relative flex items-start justify-between md:justify-normal md:odd:flex-row-reverse group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-line bg-surface text-sage font-serif shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 relative shadow-sm mt-4">
                        {{ $day['day'] }}
                    </div>
                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-3rem)] bg-surface p-8 rounded-2xl border border-line shadow-sm hover:shadow-md transition-shadow duration-300">
                        <span class="text-xs font-semibold tracking-widest uppercase text-clay mb-2 block">Day {{ $day['day'] }}</span>
                        <h3 class="font-serif text-2xl mb-4 text-ink">{{ $day['title'] }}</h3>
                        <p class="text-muted leading-relaxed">{{ $day['desc'] }}</p>
                    </div>
                </div>
            @endforeach

        </div>
        
        <div class="mt-20 text-center bg-surface border border-line rounded-2xl p-12">
            <h3 class="font-serif text-3xl mb-4 text-ink">Ready to embark?</h3>
            <p class="text-muted mb-8 max-w-lg mx-auto">Customize this itinerary with our travel designers or book it exactly as planned.</p>
            <x-editorial.btn>Start Planning</x-editorial.btn>
        </div>
    </div>
</x-editorial.layout>
