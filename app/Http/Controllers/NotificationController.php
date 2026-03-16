<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::forUser(auth()->id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAllRead()
    {
        Notification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now(), 'status' => 'read']);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
