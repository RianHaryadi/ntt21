<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
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

        return view('user.dashboard', compact('user', 'chatSessions', 'hotelBookings'));
    }
}
