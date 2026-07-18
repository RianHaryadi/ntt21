<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Destination;
use App\Models\TourPackage;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    /**
     * Bug fix #11: mutateFormDataBeforeCreate harus di Pages\CreateRecord, bukan di Resource.
     * Ini yang dipanggil Filament sebelum data form disimpan ke database.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $price = 0;

        if (!empty($data['tour_package_id'])) {
            $price = TourPackage::find($data['tour_package_id'])?->price ?? 0;
        } elseif (!empty($data['destination_id'])) {
            $price = Destination::find($data['destination_id'])?->price ?? 0;
        }

        $qty            = $data['number_of_tickets'] ?? 1;
        $subtotal       = $price * $qty;
        $discountAmount  = $data['discount_amount'] ?? 0;
        $discountPercent = $data['discount_percent'] ?? 0;

        $calculatedDiscount = $discountAmount > 0
            ? $discountAmount
            : ($discountPercent > 0 ? ($subtotal * ($discountPercent / 100)) : 0);

        $data['package_price'] = $price;
        $data['total_price']   = max($subtotal - $calculatedDiscount, 0);

        return $data;
    }
}
