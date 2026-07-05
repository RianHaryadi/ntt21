<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(LoyaltyService $loyalty)
    {
        /** @var User $user */
        $user = Auth::user();

        $chatSessions = $user->chatSessions()
            ->withCount('messages')
            ->latest()
            ->take(10)
            ->get();

        $hotelBookings = $user->hotelBookings()
            ->with('hotel')
            ->latest()
            ->take(10)
            ->get();

        $wishlists = $user->wishlists()->with('wishlistable')->latest()->get();

        $totalPoints = $user->totalPoints();
        $pointsHistory = $user->loyaltyPoints()->latest()->take(10)->get();
        $referralCount = $user->referrals()->count();

        $lifetimePoints = $user->lifetimeLoyaltyPoints();
        $loyaltyTier = $loyalty->tierFor($lifetimePoints);
        $nextTier = $loyalty->nextTierInfo($lifetimePoints);

        return view('user.dashboard', compact(
            'user', 'chatSessions', 'hotelBookings', 'wishlists',
            'totalPoints', 'pointsHistory', 'referralCount',
            'lifetimePoints', 'loyaltyTier', 'nextTier'
        ));
    }
}
