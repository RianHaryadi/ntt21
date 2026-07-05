<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_wishlist_button_appears_on_destination_show_page_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $response = $this->actingAs($user)->get(route('destinations.show', $destination->id));

        $response->assertOk();
        $response->assertSee('toggleWishlist(\'destination\'', false);
    }

    public function test_wishlist_button_appears_on_hotel_show_page_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();

        $response = $this->actingAs($user)->get(route('hotels.show', $hotel->id));

        $response->assertOk();
        $response->assertSee('toggleWishlist(\'hotel\'', false);
    }

    public function test_wishlist_button_hidden_for_guests(): void
    {
        $destination = Destination::factory()->create();

        $response = $this->get(route('destinations.show', $destination->id));

        $response->assertOk();
        $response->assertDontSee('wishlist-btn', false);
    }

    public function test_toggling_wishlist_from_destination_page_persists(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $response = $this->actingAs($user)->postJson(route('wishlist.toggle'), [
            'type' => 'destination',
            'id' => $destination->id,
        ]);

        $response->assertOk()->assertJson(['wishlisted' => true]);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'wishlistable_type' => Destination::class,
            'wishlistable_id' => $destination->id,
        ]);
    }

    public function test_hotel_show_page_displays_real_review_count_not_random(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->get(route('hotels.show', $hotel->id));

        $response->assertOk();
        $response->assertSee('0 reviews');
    }
}
