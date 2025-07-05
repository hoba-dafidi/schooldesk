<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'payment_gateway_id',
        'invoice_number',
        'title',
        'description',
        'total_amount',       // in kobo or cents
        'tax_amount',
        'discount_amount',
        'currency',           // e.g. NGN, USD
        'status',             // e.g. pending, paid, void, refunded
        'due_date',
        'paid_at',
        'external_reference', // gateway transaction reference
        'notes',
        'meta',               // JSON (optional metadata)
    ];

    protected $casts = [
        'due_date'   => 'datetime',
        'paid_at'    => 'datetime',
        'meta'       => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedTotalAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->total_amount / 100, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->tax_amount / 100, 2);
    }

    public function getFormattedDiscountAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->discount_amount / 100, 2);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid'     => '<span class="badge bg-success">Paid</span>',
            'pending'  => '<span class="badge bg-warning">Pending</span>',
            'refunded' => '<span class="badge bg-danger">Refunded</span>',
            'void'     => '<span class="badge bg-danger">Void</span>',
            default    => '<span class="badge bg-secondary">'.ucfirst($this->status).'</span>',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDue($query)
    {
        return $query->pending()->whereDate('due_date', '<=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->pending()->whereDate('due_date', '<', now()->subDay());
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }
}
