<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return response()->json($notifications);
    }
}
