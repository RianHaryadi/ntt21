<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TourPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Peta destinasi untuk konsistensi data
        $destinationMapping = [
            'Labuan Bajo' => 1,
            'Sumba Barat Daya' => 4,
            'Ende' => 8,
            'Ruteng' => 11,
        ];

        DB::table('tour_packages')->insert([
            [
                'name' => 'Sailing Komodo 3D2N - Phinisi Experience',
                'destination_id' => $destinationMapping['Labuan Bajo'],
                'price' => 2750000.00,
                'days' => 3,
                'includes_hotel' => true, // Menginap di kapal (Live on Board)
                'location' => 'Labuan Bajo, NTT',
                'thumbnail' => 'thumbnails/sailing_komodo.jpg',
                'category' => 'Sailing & Adventure',
                'photos' => json_encode([
                    'photos/komodo_dragon.jpg',
                    'photos/padar_island_view.jpg',
                    'photos/pink_beach_snorkeling.jpg',
                ]),
                'description' => 'Nikmati pengalaman tak terlupakan berlayar dengan kapal Phinisi, mengunjungi pulau-pulau ikonik di Taman Nasional Komodo. Bertemu langsung dengan Komodo, trekking di Pulau Padar, dan snorkeling di Pink Beach.',
                'rating' => 4.9,
                'rating_count' => 215,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Exotic Sumba Overland Adventure 4D3N',
                'destination_id' => $destinationMapping['Sumba Barat Daya'],
                'price' => 3800000.00,
                'days' => 4,
                'includes_hotel' => true,
                'location' => 'Sumba Barat Daya, NTT',
                'thumbnail' => 'thumbnails/sumba_overland.jpg',
                'category' => 'Cultural & Nature',
                'photos' => json_encode([
                    'photos/ratenggaro_village.jpg',
                    'photos/weekuri_lagoon.jpg',
                    'photos/lapopu_waterfall.jpg',
                ]),
                'description' => 'Jelajahi keindahan magis Pulau Sumba, dari rumah adat dengan atap menjulang di Ratenggaro, laguna air asin Weekuri yang jernih, hingga kesegaran Air Terjun Lapopu.',
                'rating' => 4.8,
                'rating_count' => 178,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kelimutu Sunrise & Flores Culture 3D2N',
                'destination_id' => $destinationMapping['Ende'],
                'price' => 2950000.00,
                'days' => 3,
                'includes_hotel' => true,
                'location' => 'Moni, Ende, NTT',
                'thumbnail' => 'thumbnails/kelimutu_sunrise.jpg',
                'category' => 'Nature & Trekking',
                'photos' => json_encode([
                    'photos/kelimutu_three_colors.jpg',
                    'photos/wologai_village.jpg',
                    'photos/moni_scenery.jpg',
                ]),
                'description' => 'Saksikan fenomena alam Danau Tiga Warna Kelimutu saat matahari terbit. Tur ini juga akan membawa Anda mengunjungi desa adat Lio untuk mengenal budaya lokal Flores.',
                'rating' => 4.7,
                'rating_count' => 123,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Full Day Komodo Speedboat Trip',
                'destination_id' => $destinationMapping['Labuan Bajo'],
                'price' => 1350000.00,
                'days' => 1,
                'includes_hotel' => false,
                'location' => 'Labuan Bajo, NTT',
                'thumbnail' => 'thumbnails/komodo_speedboat.jpg',
                'category' => 'Island Hopping',
                'photos' => json_encode([
                    'photos/speedboat_on_water.jpg',
                    'photos/manta_point_snorkeling.jpg',
                    'photos/kanawa_island.jpg',
                ]),
                'description' => 'Pilihan tepat bagi Anda dengan waktu terbatas. Jelajahi destinasi utama Taman Nasional Komodo seperti Pulau Padar, Pulau Komodo, dan Manta Point dalam satu hari menggunakan speedboat.',
                'rating' => 4.6,
                'rating_count' => 302,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Wae Rebo Village Cultural Trekking 2D1N',
                'destination_id' => $destinationMapping['Ruteng'],
                'price' => 1800000.00,
                'days' => 2,
                'includes_hotel' => true, // Menginap di rumah adat
                'location' => 'Denge, Ruteng, NTT',
                'thumbnail' => 'thumbnails/wae_rebo_village.jpg',
                'category' => 'Cultural & Trekking',
                'photos' => json_encode([
                    'photos/wae_rebo_full_view.jpg',
                    'photos/trekking_to_waerebo.jpg',
                    'photos/coffee_ceremony_waerebo.jpg',
                ]),
                'description' => 'Sebuah perjalanan spiritual ke desa adat Wae Rebo yang terpencil di atas awan. Nikmati pengalaman menginap di Mbaru Niang, rumah kerucut tradisional, dan berinteraksi dengan masyarakat lokal.',
                'rating' => 4.9,
                'rating_count' => 95,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}