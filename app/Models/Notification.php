<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;
use Illuminate\Support\Facades\Broadcast;

class Notification extends Model implements ShouldBroadcast, ShouldQueue
{
    use HasFactory;

    public const TYPE_SYSTEM = 'system';
    public const TYPE_BILLING = 'billing';
    public const TYPE_SUPPORT = 'support';

    protected $fillable = [
        'type',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // 🔁 Polymorphic relationship
    public function notifiable()
    {
        return $this->morphTo();
    }

    // ✅ Scope for unread
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // ✅ Mark as read
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

     public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->notifiable_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'data' => $this->data,
            'read_at' => $this->read_at,
        ];
    }
}
