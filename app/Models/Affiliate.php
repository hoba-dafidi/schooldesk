<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referral_code',
        'clicks',
        'signups',
    ];

    /**
     * The user who owns this affiliate record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The commissions earned by this affiliate.
     */
    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function totalCommission()
    {
        return $this->commissions()
            ->where('status', 'approved')
            ->orWhere('status', 'paid')
            ->sum('amount');
    }

    public function getTotalCommissionAttribute()
    {
        return $this->affiliate?->commissions()->approved()->sum('amount') ?? 0;
    }

    public function getTotalSignupsAttribute()
    {
        return $this->affiliate?->commissions()->count() ?? 0;
    }

    public function campaigns()
    {
        return $this->hasMany(AffiliateCampaign::class);
    }


}
