<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'description',
        'source_type',
        'source_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
