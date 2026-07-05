<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing', [
            'destinasiUnggulan' => Cache::remember('landing.destinations', 1800, fn() =>
                Destination::latest()->take(6)->get()
            ),
            'hotelRekomendasi' => Cache::remember('landing.hotels', 1800, fn() =>
                Hotel::latest()->take(5)->get()
            ),
            'paketTourPopuler' => Cache::remember('landing.tour_packages', 1800, fn() =>
                TourPackage::latest()->take(4)->get()
            ),
        ]);
    }
}
