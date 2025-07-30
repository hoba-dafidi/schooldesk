<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ManualPayment extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'bank_name',
        'account_name',
        'account_number',
        'reference',
        'proof_path',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * User who submitted the manual payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Related invoice.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Admin user who reviewed the payment.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the full URL to the proof file.
     */
    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_path ? asset("storage/{$this->proof_path}") : null;
    }

    /**
     * Check if the payment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payment is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
