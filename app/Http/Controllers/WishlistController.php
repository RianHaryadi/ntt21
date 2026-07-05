<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('wishlistable')->latest()->get();
        return view('user.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|in:destination,hotel',
            'id'   => 'required|integer',
        ]);

        $model = match($request->type) {
            'destination' => Destination::findOrFail($request->id),
            'hotel'       => Hotel::findOrFail($request->id),
        };

        $existing = $model->wishlists()
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            $model->wishlists()->create(['user_id' => auth()->id()]);
            $wishlisted = true;
        }

        if ($request->expectsJson()) {
            return response()->json(['wishlisted' => $wishlisted]);
        }

        return back()->with('success', $wishlisted ? 'Ditambahkan ke favorit.' : 'Dihapus dari favorit.');
    }
}
