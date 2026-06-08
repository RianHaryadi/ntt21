<?php

namespace App\Http\Controllers;

use App\Models\TravelChatSession;
use Illuminate\Http\Request;

class TravelChatController extends Controller
{
    /**
     * Show the Travel Chatbot page.
     */
    public function chat()
    {
        return view('travel.chat');
    }

    /**
     * Show the recommendation result page.
     */
    public function recommendation($token)
    {
        // Verify the session exists and is completed
        $session = TravelChatSession::where('session_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        return view('travel.recommendation', ['token' => $token]);
    }
}
