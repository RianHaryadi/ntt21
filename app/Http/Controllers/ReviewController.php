<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingHotel;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reviewable_type' => 'required|in:destination,hotel',
            'reviewable_id'   => 'required|integer',
            'rating'          => 'required|integer|min:1|max:5',
            'body'            => 'required|string|max:1000',
            'photo'           => 'nullable|image|max:3072',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'location_rating'    => 'nullable|integer|min:1|max:5',
            'value_rating'       => 'nullable|integer|min:1|max:5',
            'service_rating'     => 'nullable|integer|min:1|max:5',
        ]);

        $model = match($request->reviewable_type) {
            'destination' => Destination::findOrFail($request->reviewable_id),
            'hotel'       => Hotel::findOrFail($request->reviewable_id),
        };

        // Cek apakah user sudah pernah review item ini
        $existing = $model->reviews()->where('user_id', auth()->id())->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk ini.');
        }

        // Verified-stay: hanya tamu yang benar-benar sudah booking & menyelesaikan pembayaran
        if (!$this->hasCompletedBooking($request->reviewable_type, $request->reviewable_id)) {
            return back()->with('error', 'Anda hanya bisa memberi ulasan untuk hotel/destinasi yang sudah Anda booking dan selesaikan pembayarannya.');
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reviews', 'public');
        }

        $model->reviews()->create([
            'user_id' => auth()->id(),
            'rating'  => $request->rating,
            'body'    => $request->body,
            'photo'   => $photoPath,
            'cleanliness_rating' => $request->cleanliness_rating,
            'location_rating'    => $request->location_rating,
            'value_rating'       => $request->value_rating,
            'service_rating'     => $request->service_rating,
        ]);

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    /**
     * Cek apakah user login benar-benar pernah booking & selesai membayar item ini.
     */
    private function hasCompletedBooking(string $type, int $reviewableId): bool
    {
        $userId = auth()->id();

        return match ($type) {
            'hotel' => BookingHotel::where('user_id', $userId)
                ->where('hotel_id', $reviewableId)
                ->whereIn('status', ['checked-in', 'checked-out'])
                ->exists(),
            'destination' => Transaction::where('user_id', $userId)
                ->where('destination_id', $reviewableId)
                ->where('status', Transaction::STATUS_PAID)
                ->exists(),
            default => false,
        };
    }

    public function destroy($id)
    {
        $review = auth()->user()->reviews()->findOrFail($id);

        if ($review->photo) {
            Storage::disk('public')->delete($review->photo);
        }

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    /**
     * Tandai/batalkan tanda "membantu" pada sebuah ulasan.
     */
    public function toggleHelpful(Review $review)
    {
        if ($review->user_id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menandai ulasan sendiri.');
        }

        $existing = $review->helpfulVotes()->where('user_id', auth()->id())->first();

        if ($existing) {
            $existing->delete();
        } else {
            $review->helpfulVotes()->create(['user_id' => auth()->id()]);
        }

        return back();
    }
}
