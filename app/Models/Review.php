<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'rating', 'body', 'photo',
        'cleanliness_rating', 'location_rating', 'value_rating', 'service_rating',
    ];

    public function hasSubRatings(): bool
    {
        return $this->cleanliness_rating && $this->location_rating && $this->value_rating && $this->service_rating;
    }

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function helpfulVotes()
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function isMarkedHelpfulBy(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->helpfulVotes->contains('user_id', $userId);
    }
}
