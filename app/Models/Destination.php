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
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'rating' => 'float',
        'rating_count' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Get the hotels for the destination.
     * Sebuah destinasi memiliki BANYAK hotel.
     */
    public function hotels()
    {

        return $this->hasMany(Hotel::class);
    }

    /**
     * Get the tour packages for the destination.
     * Sebuah destinasi memiliki BANYAK paket tur.
     */
    public function tourPackages()
    {
        return $this->hasMany(TourPackage::class);
    }
}