<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the authenticated user's notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->get();
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark the specified notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure the authenticated user owns this notification
        if (Auth::id() !== $notification->user_id) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);
        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Notification $notification)
    {
        // Ensure the authenticated user owns this notification
        if (Auth::id() !== $notification->user_id) {
            abort(403);
        }

        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    // You might also have methods for creating notifications (e.g., from provisioning jobs)
    // For example, a method to dispatch a notification:
    // public static function dispatch(User $user, $type, $title, $message, $data = null)
    // {
    //     Notification::create([
    //         'user_id' => $user->id,
    //         'type' => $type,
    //         'title' => $title,
    //         'message' => $message,
    //         'data' => $data,
    //     ]);
    // }
}
