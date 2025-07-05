<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subdomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'link',
        'is_active',
        'db_host',
        'db_username',
        'db_password',
        'db_table',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function setDbPasswordAttribute($value)
    {
        $this->attributes['db_password'] = encrypt($value);
    }

    public function getDbPasswordAttribute($value)
    {
        return decrypt($value);
    }

}
