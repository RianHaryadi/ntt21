<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\TourPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourPackageFactory extends Factory
{
    protected $model = TourPackage::class;

    public function definition(): array
    {
        return [
            'name' => fake()->catchPhrase() . ' Tour',
            'destination_id' => Destination::factory(),
            'price' => fake()->numberBetween(500000, 5000000),
            'days' => fake()->numberBetween(1, 5),
            'includes_hotel' => false,
            'location' => fake()->city(),
            'category' => fake()->randomElement(['Adventure', 'Culture', 'Nature']),
            'description' => fake()->paragraph(),
            'rating' => fake()->randomFloat(1, 3, 5),
        ];
    }
}
