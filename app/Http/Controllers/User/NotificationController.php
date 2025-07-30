<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(10); // Or ->get() if you don't want pagination

        return view('user.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllAsRead()
    {
        Notification::where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }


}
