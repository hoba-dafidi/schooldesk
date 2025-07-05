<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',    // in minor units (e.g., kobo, cents)
        'total_price',   // in minor units
        'currency',
        'meta',          // optional extra data (JSON)
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedUnitPriceAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->unit_price / 100, 2);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->total_price / 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->total_price)) {
                $item->total_price = $item->quantity * $item->unit_price;
            }
        });
    }
}
