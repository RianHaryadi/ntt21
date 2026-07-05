<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class SearchSuggestionController extends Controller
{
    /**
     * Saran pencarian ringan untuk autocomplete — mencari lintas destinasi, hotel, dan paket tour.
     */
    public function suggest(Request $request)
    {
        $query = trim((string) $request->get('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $destinations = Destination::where('name', 'like', "%{$query}%")
            ->limit(4)
            ->get(['id', 'name', 'location'])
            ->map(fn ($d) => [
                'type' => 'destination',
                'label' => $d->name,
                'sublabel' => $d->location,
                'url' => route('destinations.show', $d->id),
            ]);

        $hotels = Hotel::where('name', 'like', "%{$query}%")
            ->limit(4)
            ->get(['id', 'name', 'location'])
            ->map(fn ($h) => [
                'type' => 'hotel',
                'label' => $h->name,
                'sublabel' => $h->location,
                'url' => route('hotels.show', $h->id),
            ]);

        $tourPackages = TourPackage::where('name', 'like', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name', 'location'])
            ->map(fn ($t) => [
                'type' => 'tour',
                'label' => $t->name,
                'sublabel' => $t->location,
                'url' => route('paket-tours.show', $t->id),
            ]);

        $results = $destinations->concat($hotels)->concat($tourPackages)->take(10)->values();

        return response()->json(['results' => $results]);
    }
}
