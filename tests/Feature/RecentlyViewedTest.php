<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentlyViewedTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiting_a_destination_adds_it_to_recently_viewed_session(): void
    {
        $destination = Destination::factory()->create();

        $this->get(route('destinations.show', $destination->id));

        $this->assertEquals(
            [['type' => 'destination', 'id' => $destination->id]],
            session('recently_viewed')
        );
    }

    public function test_recently_viewed_list_excludes_the_current_item_and_shows_previous_ones(): void
    {
        $first = Destination::factory()->create(['name' => 'First Destination']);
        $second = Destination::factory()->create(['name' => 'Second Destination']);

        $this->get(route('destinations.show', $first->id));
        $response = $this->get(route('destinations.show', $second->id));

        $response->assertOk();
        $response->assertSee('Baru Dilihat');
        $response->assertSee('First Destination');
    }

    public function test_similar_destinations_are_shown_based_on_category(): void
    {
        $destination = Destination::factory()->create(['category' => 'Beach']);
        $similar = Destination::factory()->create(['category' => 'Beach', 'name' => 'Similar Beach']);
        Destination::factory()->create(['category' => 'Mountain', 'name' => 'Different Mountain']);

        $response = $this->get(route('destinations.show', $destination->id));

        $response->assertOk();
        $response->assertSee('Destinasi Serupa');
        $response->assertSee('Similar Beach');
        $response->assertDontSee('Different Mountain');
    }

    public function test_similar_hotels_are_shown_based_on_location(): void
    {
        $hotel = Hotel::factory()->create(['location' => 'Kupang']);
        $similar = Hotel::factory()->create(['location' => 'Kupang', 'name' => 'Similar Kupang Hotel']);
        Hotel::factory()->create(['location' => 'Labuan Bajo', 'name' => 'Different Labuan Bajo Hotel']);

        $response = $this->get(route('hotels.show', $hotel->id));

        $response->assertOk();
        $response->assertSee('Hotel Serupa');
        $response->assertSee('Similar Kupang Hotel');
        $response->assertDontSee('Different Labuan Bajo Hotel');
    }

    public function test_recently_viewed_list_caps_at_eight_items(): void
    {
        $destinations = Destination::factory()->count(10)->create();

        foreach ($destinations as $destination) {
            $this->get(route('destinations.show', $destination->id));
        }

        $this->assertCount(8, session('recently_viewed'));
    }
}
