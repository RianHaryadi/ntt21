<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Services\RecentlyViewedService;
use Illuminate\Support\Facades\Cache;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $locations = Cache::remember('hotel.locations', 3600, fn() =>
            Hotel::select('location')
                ->distinct()
                ->whereNotNull('location')
                ->orderBy('location')
                ->pluck('location')
        );

        // Filter hotels based on location if provided
        $query = Hotel::when($request->location, function($query) use ($request) {
            return $query->where('location', $request->location);
        })
        ->withCount(['bookings as bookings_count' => function ($q) {
            $q->whereIn('status', ['checked-in', 'checked-out']);
        }])
        ->withAvg('reviews as reviews_avg_rating', 'rating');

        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('single_room_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('single_room_price', '<=', $request->max_price);
        }

        if ($request->filled('min_rating') && is_numeric($request->min_rating)) {
            $query->having('reviews_avg_rating', '>=', $request->min_rating);
        }

        match ($request->sort) {
            'most_booked' => $query->orderByDesc('bookings_count'),
            'price_asc' => $query->orderBy('single_room_price'),
            'price_desc' => $query->orderByDesc('single_room_price'),
            'rating_desc' => $query->orderByDesc('reviews_avg_rating'),
            default => $query->orderBy('name'), // 'recommended'
        };

        $hotels = $query->paginate(12)->appends($request->query());

        return view('hotel.index', [
            'hotels' => $hotels,
            'locations' => $locations,
            'selectedLocation' => $request->location,
            'selectedSort' => $request->sort,
        ]);
    }

    public function show($id, RecentlyViewedService $recentlyViewed)
    {
        $hotel = Hotel::withAvg('reviews as reviews_avg_rating', 'rating')
            ->withCount('reviews')
            ->with(['questions.user', 'questions.answers.user'])
            ->findOrFail($id);

        $recentlyViewedItems = $recentlyViewed->get('hotel', $hotel->id);

        $similarHotels = Hotel::where('location', $hotel->location)
            ->where('id', '!=', $hotel->id)
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->limit(4)
            ->get();

        $recentlyViewed->record('hotel', $hotel->id);

        return view('hotel.show', compact('hotel', 'similarHotels', 'recentlyViewedItems'));
    }

    
}