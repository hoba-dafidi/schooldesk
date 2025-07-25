<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'old_plan_id',
        'new_plan_id',
        'action',
        'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldPlan()
    {
        return $this->belongsTo(Plan::class, 'old_plan_id');
    }

    public function newPlan()
    {
        return $this->belongsTo(Plan::class, 'new_plan_id');
    }
}
