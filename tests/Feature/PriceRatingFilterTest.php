<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceRatingFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_destination_index_filters_by_price_range(): void
    {
        $cheap = Destination::factory()->create(['name' => 'Cheap Spot', 'price' => 50000]);
        $expensive = Destination::factory()->create(['name' => 'Expensive Spot', 'price' => 500000]);

        $response = $this->get(route('destinations.index', ['min_price' => 100000, 'max_price' => 600000]));

        $response->assertOk();
        $response->assertSee('Expensive Spot');
        $response->assertDontSee('Cheap Spot');
    }

    public function test_destination_index_filters_by_minimum_rating(): void
    {
        $highRated = Destination::factory()->create(['name' => 'High Rated Spot', 'rating' => 4.8]);
        $lowRated = Destination::factory()->create(['name' => 'Low Rated Spot', 'rating' => 2.5]);

        $response = $this->get(route('destinations.index', ['min_rating' => 4]));

        $response->assertOk();
        $response->assertSee('High Rated Spot');
        $response->assertDontSee('Low Rated Spot');
    }

    public function test_hotel_index_filters_by_price_range(): void
    {
        $cheap = Hotel::factory()->create(['name' => 'Budget Inn', 'single_room_price' => 100000]);
        $expensive = Hotel::factory()->create(['name' => 'Luxury Resort', 'single_room_price' => 2000000]);

        $response = $this->get(route('hotels.index', ['min_price' => 500000]));

        $response->assertOk();
        $response->assertSee('Luxury Resort');
        $response->assertDontSee('Budget Inn');
    }

    public function test_hotel_index_filters_by_minimum_rating(): void
    {
        $user = User::factory()->create();
        $highRated = Hotel::factory()->create(['name' => 'Five Star Hotel']);
        $lowRated = Hotel::factory()->create(['name' => 'Two Star Hotel']);

        $highRated->reviews()->create(['user_id' => $user->id, 'rating' => 5, 'body' => 'Amazing']);
        $lowRated->reviews()->create(['user_id' => $user->id, 'rating' => 2, 'body' => 'Meh']);

        $response = $this->get(route('hotels.index', ['min_rating' => 4]));

        $response->assertOk();
        $response->assertSee('Five Star Hotel');
        $response->assertDontSee('Two Star Hotel');
    }

    public function test_destination_index_shows_reset_link_only_when_filters_active(): void
    {
        Destination::factory()->create();

        $withoutFilter = $this->get(route('destinations.index'));
        $withoutFilter->assertDontSee('Reset filter');

        $withFilter = $this->get(route('destinations.index', ['min_price' => 10000]));
        $withFilter->assertSee('Reset filter');
    }
}
