<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class AffiliateCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id', 'name', 'slug', 'referral_code',
        'clicks', 'unique_signups', 'custom_commission_rate',
        'starts_at', 'ends_at'
    ];

    protected static function booted()
    {
        static::creating(function ($campaign) {
            // Auto-generate slug and referral code if not set
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->name . '-' . Str::random(4));
            }

            if (empty($campaign->referral_code)) {
                $campaign->referral_code = strtoupper(Str::random(8));
            }
        });
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function active(): bool
    {
        return (!$this->starts_at || now()->gte($this->starts_at)) &&
               (!$this->ends_at || now()->lte($this->ends_at));
    }
}
