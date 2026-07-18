<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackageVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'name',
        'price_type',
        'price',
        'min_pax',
        'max_pax',
        'notes',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    /** Apakah varian ini berlaku untuk jumlah rombongan tertentu. */
    public function fitsPax(int $pax): bool
    {
        return $pax >= max((int) $this->min_pax, 1)
            && ($this->max_pax === null || $pax <= (int) $this->max_pax);
    }

    /** Total harga varian untuk jumlah rombongan tertentu. */
    public function totalFor(int $pax): float
    {
        return $this->price_type === 'flat'
            ? (float) $this->price
            : (float) $this->price * max($pax, 1);
    }
}
