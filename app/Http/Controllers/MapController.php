<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
    public function index()
    {
        return view('map.index');
    }

    public function data()
    {
        $destinations = Cache::remember('map.destinations', 1800, fn() =>
            Destination::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where('status', 'active')
                ->select('id', 'name', 'location', 'category', 'latitude', 'longitude', 'price', 'rating', 'image')
                ->get()
        );

        return response()->json($destinations);
    }
}
