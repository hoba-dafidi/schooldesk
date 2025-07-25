<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications->sortByDesc('created_at');
        $users = User::all();

        return view('admin.notifications.index', compact('notifications', 'users'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'level' => 'required|in:info,warning,danger',
            'recipient_type' => 'required|in:role,user',
            'role' => 'required_if:recipient_type,role',
            'user_id' => 'required_if:recipient_type,user|exists:users,id',
        ]);

        $notificationData = [
            'message' => $request->message,
            'level' => $request->level,
            'icon' => match ($request->level) {
                'danger' => 'bx-error-circle',
                'warning' => 'bx-error',
                default => 'bx-info-circle',
            },
            'urgency' => 'info',
        ];

        // Select recipients
        $users = $request->recipient_type === 'role'
            ? User::role($request->role)->get()
            : User::where('id', $request->user_id)->get();

        Notification::send($users, new AdminBroadcastNotification($notificationData));

        return back()->with('success', 'Notification sent successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required|in:read,delete',
        ]);

        $notifications = Auth::user()->notifications->whereIn('id', $request->ids);

        foreach ($notifications as $notification) {
            if ($request->action === 'read') {
                $notification->markAsRead();
            } elseif ($request->action === 'delete') {
                $notification->delete();
            }
        }

        return back()->with('success', 'Bulk action completed.');
    }
}
