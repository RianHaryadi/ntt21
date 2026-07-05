<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Hotel',
            'address' => fake()->address(),
            'description' => fake()->text(200),
            'destination_id' => Destination::factory(),
            'location' => fake()->city(),
            'single_room_price' => 300000,
            'double_room_price' => 500000,
            'family_room_price' => 800000,
            'room_count_single' => 5,
            'room_count_double' => 5,
            'room_count_family' => 5,
        ];
    }
}
