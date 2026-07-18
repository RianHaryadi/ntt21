<?php

namespace Database\Seeders;

use App\Models\TourPackage;
use Illuminate\Database\Seeder;

/**
 * Varian harga untuk tiap paket tour, diturunkan dari harga dasar (per orang):
 * - Open Trip     : per orang, harga dasar — gabung wisatawan lain
 * - Paket Keluarga: harga total untuk 3-5 orang (4.2x harga dasar — per orang lebih murah
 *                   daripada open trip untuk rombongan 5)
 * - Grup Besar    : per orang diskon ~15%, min 6 orang
 * - Private Trip  : harga total charter max 10 orang, jadwal fleksibel
 *
 * Idempoten: paket yang sudah punya varian dilewati.
 */
class TourPackageVariantSeeder extends Seeder
{
    public function run(): void
    {
        $round = fn (float $x) => round($x / 50000) * 50000;

        foreach (TourPackage::all() as $tour) {
            if ($tour->variants()->exists()) {
                continue;
            }

            $base = (float) $tour->price;

            $tour->variants()->createMany([
                [
                    'name'       => 'Open Trip',
                    'price_type' => 'per_person',
                    'price'      => $base,
                    'min_pax'    => 1,
                    'max_pax'    => null,
                    'notes'      => 'Gabung dengan wisatawan lain, jadwal mengikuti operator',
                ],
                [
                    'name'       => 'Paket Keluarga',
                    'price_type' => 'flat',
                    'price'      => $round($base * 4.2),
                    'min_pax'    => 3,
                    'max_pax'    => 5,
                    'notes'      => 'Harga total untuk 3-5 orang, lebih hemat untuk keluarga',
                ],
                [
                    'name'       => 'Grup Besar',
                    'price_type' => 'per_person',
                    'price'      => $round($base * 0.85),
                    'min_pax'    => 6,
                    'max_pax'    => 15,
                    'notes'      => 'Diskon per orang untuk rombongan minimal 6',
                ],
                [
                    'name'       => 'Private Trip',
                    'price_type' => 'flat',
                    'price'      => $round($base * 7),
                    'min_pax'    => 1,
                    'max_pax'    => 10,
                    'notes'      => 'Trip privat khusus rombonganmu, jadwal fleksibel',
                ],
            ]);
        }
    }
}
