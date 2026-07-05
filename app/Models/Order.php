<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'subtotal',
        'discount_amount',
        'insurance_amount',
        'total_price',
        'has_insurance',
        'promo_code_id',
        'payment_method',
        'payment_gateway_token',
        'payment_deadline',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'discount_amount' => 'float',
        'insurance_amount' => 'float',
        'total_price' => 'float',
        'has_insurance' => 'boolean',
        'payment_deadline' => 'datetime',
    ];

    public const STATUS_PENDING   = 'pending';
    public const STATUS_PAID      = 'paid';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED   = 'expired';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(CodePromotion::class, 'promo_code_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function bookingHotels()
    {
        return $this->hasMany(BookingHotel::class);
    }

    public function getTotalPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /** Jumlah item (destinasi/tour/hotel) yang tergabung dalam order ini. */
    public function itemCount(): int
    {
        return $this->transactions()->count() + $this->bookingHotels()->count();
    }
}
