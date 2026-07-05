<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use App\Models\Culture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $destinations = Cache::remember('home.destinations', 1800, fn() =>
            Destination::latest()->take(3)->get()
        );

        $hotels = Cache::remember('home.hotels', 1800, fn() =>
            Hotel::latest()->take(6)->get()
        );

        $TourPackage = Cache::remember('home.tour_packages', 1800, fn() =>
            TourPackage::latest()->take(3)->get()
        );

        $cultures = Cache::remember('home.cultures', 1800, fn() =>
            Culture::latest()->take(3)->get()
        );

        // Flash sale tidak di-cache agar status aktif/berakhir selalu akurat.
        $flashSaleDestinations = Destination::onFlashSale()->orderBy('flash_sale_ends_at')->take(4)->get();
        $flashSaleHotels = Hotel::onFlashSale()->orderBy('flash_sale_ends_at')->take(4)->get();

        return view('home', compact('destinations', 'hotels', 'TourPackage', 'cultures', 'flashSaleDestinations', 'flashSaleHotels'));
    }
}
