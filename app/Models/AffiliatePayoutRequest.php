<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliatePayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'account_name',
        'account_number',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'float',
    ];

    /**
     * The affiliate user who made the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quick accessor for checking if paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }
}
