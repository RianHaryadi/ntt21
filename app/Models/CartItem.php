<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'cart_token', 'itemable_type', 'itemable_id', 'details'];

    protected $casts = [
        'details' => 'array',
    ];

    public function itemable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itemType(): string
    {
        return match ($this->itemable_type) {
            Destination::class => 'destination',
            Hotel::class => 'hotel',
            TourPackage::class => 'tour',
            default => 'unknown',
        };
    }

    /** Harga satuan yang sudah dihitung & disimpan saat item ditambahkan ke keranjang. */
    public function unitPrice(): float
    {
        return (float) ($this->details['unit_price'] ?? 0);
    }

    public function quantity(): int
    {
        return (int) ($this->details['quantity'] ?? 1);
    }

    public function subtotal(): float
    {
        return $this->unitPrice() * $this->quantity();
    }
}
