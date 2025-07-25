<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'max_uses',
        'uses',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        return $this->is_active
            && (!$this->expires_at || $this->expires_at->isFuture())
            && (!$this->max_uses || $this->uses < $this->max_uses);
    }

    public function applyToAmount(float $amount): float
    {
        return $this->type === 'percentage'
            ? $amount * ($this->value / 100)
            : min($this->value, $amount);
    }
}
