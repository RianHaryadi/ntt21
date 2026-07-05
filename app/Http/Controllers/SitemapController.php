<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', 3600, function () {
            $urls = collect();

            // Halaman statis
            foreach ([
                ['loc' => route('home'), 'priority' => '1.0'],
                ['loc' => route('destinations.index'), 'priority' => '0.9'],
                ['loc' => route('hotels.index'), 'priority' => '0.9'],
                ['loc' => route('paket-tours.index'), 'priority' => '0.9'],
                ['loc' => route('cultures.index'), 'priority' => '0.8'],
                ['loc' => route('map.index'), 'priority' => '0.6'],
                ['loc' => route('ai.hub'), 'priority' => '0.5'],
                ['loc' => route('booking.checkForm'), 'priority' => '0.4'],
            ] as $entry) {
                $urls->push($entry);
            }

            Destination::where('status', '!=', 'cancelled')->get()->each(function ($d) use ($urls) {
                $urls->push([
                    'loc' => route('destinations.show', $d->id),
                    'priority' => '0.8',
                    'lastmod' => $d->updated_at?->toAtomString(),
                ]);
            });

            Hotel::all()->each(function ($h) use ($urls) {
                $urls->push([
                    'loc' => route('hotels.show', $h->id),
                    'priority' => '0.8',
                    'lastmod' => $h->updated_at?->toAtomString(),
                ]);
            });

            TourPackage::all()->each(function ($t) use ($urls) {
                $urls->push([
                    'loc' => route('paket-tours.show', $t->id),
                    'priority' => '0.8',
                    'lastmod' => $t->updated_at?->toAtomString(),
                ]);
            });

            return view('sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
