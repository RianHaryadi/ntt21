<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ClaudeService;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    public function __construct(private ClaudeService $claude) {}

    // ── 1. Ringkasan Review ──────────────────────────────────────────────
    public function reviewSummary(Request $request)
    {
        $request->validate([
            'type' => 'required|in:destination,hotel',
            'id'   => 'required|integer',
        ]);

        $model = $request->type === 'destination'
            ? Destination::findOrFail($request->id)
            : Hotel::findOrFail($request->id);

        $reviews = $model->reviews()
            ->select('rating', 'body')
            ->latest()
            ->take(30)
            ->get()
            ->toArray();

        if (empty($reviews)) {
            return response()->json(['summary' => null, 'message' => 'Belum ada ulasan untuk diringkas.']);
        }

        $summary = $this->claude->summarizeReviews($request->type, $request->id, $reviews);

        return response()->json([
            'summary' => $summary ?? 'Tidak dapat memuat ringkasan saat ini.',
            'count'   => count($reviews),
        ]);
    }

    // ── 2. Smart Search ──────────────────────────────────────────────────
    public function smartSearch(Request $request)
    {
        $request->validate(['q' => 'required|string|max:200']);

        $filters = $this->claude->parseSearchQuery($request->q);

        $query = Destination::query()->where('status', 'active');

        if (!empty($filters['category']) && $filters['category'] !== 'null') {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (!empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }
        if (!empty($filters['location']) && $filters['location'] !== 'null') {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }
        if (!empty($filters['keywords']) && $filters['keywords'] !== 'null') {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['keywords'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['keywords'] . '%');
            });
        }

        $results = $query->orderBy('rating', 'desc')->take(12)->get();

        return response()->json([
            'results' => $results,
            'filters' => $filters,
            'total'   => $results->count(),
        ]);
    }

    // ── 3. Best Time to Visit ────────────────────────────────────────────
    public function bestTime(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $destination = Destination::findOrFail($request->id);

        $advice = $this->claude->bestTimeToVisit(
            $destination->name,
            $destination->location,
            $destination->category
        );

        return response()->json([
            'advice' => $advice ?? 'Tidak dapat memuat saran saat ini.',
        ]);
    }

    // ── 4. Rekomendasi Personal ──────────────────────────────────────────
    public function personalRecommendations()
    {
        /** @var User $user */
        $user = Auth::user();

        // Kumpulkan riwayat: hotel booking + wishlist
        $history = [];

        $user->hotelBookings()->with('hotel')->latest()->take(10)->get()
            ->each(function ($b) use (&$history) {
                if ($b->hotel) {
                    $history[] = [
                        'name'     => $b->hotel->name,
                        'type'     => 'Hotel',
                        'category' => 'Akomodasi',
                    ];
                }
            });

        $user->wishlists()->with('wishlistable')->get()
            ->each(function ($w) use (&$history) {
                $obj = $w->wishlistable;
                if ($obj) {
                    $history[] = [
                        'name'     => $obj->name,
                        'type'     => class_basename($obj),
                        'category' => $obj->category ?? 'Wisata',
                    ];
                }
            });

        if (empty($history)) {
            return response()->json(['recommendations' => [], 'message' => 'Tambahkan booking atau favorit dulu agar AI bisa belajar preferensi Anda.']);
        }

        $availableDestinations = Destination::where('status', 'active')
            ->select('id', 'name', 'category', 'location')
            ->orderBy('rating', 'desc')
            ->take(30)
            ->get()
            ->toArray();

        $raw = $this->claude->personalRecommendations($history, $availableDestinations);

        if (!$raw) {
            return response()->json(['recommendations' => [], 'message' => 'Tidak dapat memuat rekomendasi saat ini.']);
        }

        try {
            $parsed = json_decode($raw, true);
            $ids = collect($parsed)->pluck('id')->filter()->values();
            $reasons = collect($parsed)->keyBy('id');

            $destinations = Destination::whereIn('id', $ids)
                ->select('id', 'name', 'location', 'category', 'image', 'price', 'rating')
                ->get()
                ->map(fn($d) => array_merge($d->toArray(), [
                    'reason' => $reasons[$d->id]['reason'] ?? '',
                ]));

            return response()->json(['recommendations' => $destinations]);
        } catch (\Exception) {
            return response()->json(['recommendations' => [], 'message' => 'Format respons AI tidak valid.']);
        }
    }

    // ── Halaman AI Hub ───────────────────────────────────────────────────
    public function hub()
    {
        return view('ai.hub');
    }

    // ── Halaman AI Itinerary Builder ─────────────────────────────────────
    public function itinerary()
    {
        return view('ai.itinerary');
    }

    // ── Halaman Smart Search ─────────────────────────────────────────────
    public function searchPage()
    {
        return view('ai.search');
    }
}
