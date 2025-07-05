<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;



    protected $fillable = [
        'user_id',
        'school_id',
        'subject',
        'description',
        'status',        // open, in_progress, closed, awaiting_reply
        'priority',      // low, medium, high, urgent
        'category',      // billing, technical, general, etc.
        'source',        // web, email, phone, api
        'assigned_to',
        'tags',          // optional: comma-separated or JSON
        'attachments',   // JSON of uploaded files or paths
        'first_response_at',
        'resolved_at',
        'closed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
    'status' => TicketStatus::class,
    'priority' => TicketPriority::class,
    'closed_at' => 'datetime',
    'resolved_at' => 'datetime',
    'first_response_at' => 'datetime',
    'attachments' => 'array',
    'tags' => 'array',
];

    /** Relationships */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
