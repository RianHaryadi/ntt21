<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'location',
        'image',
        'category',
        'is_popular',
        'rating',
        'rating_count',
        'latitude',
        'longitude',
        'maps_url',
        'price',
        'payment_method',
        'status',
        'flash_sale_discount_percent',
        'flash_sale_ends_at',
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'rating' => 'float',
        'rating_count' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'flash_sale_ends_at' => 'datetime',
    ];

    public function isOnFlashSale(): bool
    {
        return $this->flash_sale_discount_percent
            && $this->flash_sale_ends_at
            && $this->flash_sale_ends_at->isFuture();
    }

    public function getFlashSalePriceAttribute(): ?float
    {
        if (!$this->isOnFlashSale()) {
            return null;
        }

        return round($this->price - ($this->price * $this->flash_sale_discount_percent / 100));
    }

    /**
     * Get the hotels for the destination.
     * Sebuah destinasi memiliki BANYAK hotel.
     */
    public function hotels()
    {

        return $this->hasMany(Hotel::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the tour packages for the destination.
     * Sebuah destinasi memiliki BANYAK paket tur.
     */
    public function tourPackages()
    {
        return $this->hasMany(TourPackage::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable')->latest();
    }

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function scopeOnFlashSale($query)
    {
        return $query->whereNotNull('flash_sale_discount_percent')
            ->where('flash_sale_ends_at', '>', now());
    }
}