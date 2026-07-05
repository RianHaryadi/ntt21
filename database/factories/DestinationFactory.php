<?php

namespace Database\Factories;

use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        return [
            'name' => fake()->city() . ' ' . fake()->randomElement(['Beach', 'Hill', 'Falls']),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'category' => fake()->randomElement(['Beach', 'Mountain', 'Culture', 'Nature']),
            'price' => fake()->numberBetween(10000, 100000),
            'rating' => fake()->randomFloat(1, 3, 5),
            'status' => 'confirmed',
        ];
    }
}
