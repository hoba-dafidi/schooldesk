<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'school_id',
        'plan_id',
        'payment_gateway_id',
        'invoice_number',
        'title',
        'description',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'currency',
        'status',
        'due_date',
        'paid_at',
        'reference',
        'notes',
        'meta',
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

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }


    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function manualPayment()
    {
        return $this->hasOne(ManualPayment::class);
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
            default    => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getFormattedDueDateAttribute(): ?string
    {
        return $this->due_date?->format('M d, Y');
    }

    public function getFormattedPaidAtAttribute(): ?string
    {
        return $this->paid_at?->format('M d, Y h:i A');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'pending' && $this->due_date?->isPast();
    }

    public function getGatewayResponseAttribute()
    {
        return $this->meta['gateway_response'] ?? null;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'paid' => 'success',
            'unpaid' => 'warning',
            'overdue' => 'danger',
            default => 'secondary',
        };
    }

    public function getIsUnpaidAttribute()
    {
        return $this->status === 'unpaid';
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
        parent::booted();

        static::updating(function ($invoice) {
            if ($invoice->isDirty('status') && $invoice->status === 'paid') {
                Mail::to($invoice->user->email)->queue(new \App\Mail\InvoicePaid($invoice));
            }
        });

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
                if (empty($invoice->reference)) {
                    $invoice->reference = strtoupper('INV-' . uniqid() . '-' . Str::random(6));
                }
            }

            if (!$invoice->status) {
                $invoice->status = 'pending';
            }

            if (!$invoice->user_id && auth()->check()) {
                $invoice->user_id = auth()->id();
            }
        });
    }
}
