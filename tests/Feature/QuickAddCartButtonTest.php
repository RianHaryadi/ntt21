<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickAddCartButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_add_button_appears_on_destination_index_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        Destination::factory()->create();

        $response = $this->actingAs($user)->get(route('destinations.index'));

        $response->assertOk();
        $response->assertSee("openQuickAdd({type: 'destination'", false);
        $response->assertSee('id="quick-add-modal"', false);
    }

    public function test_quick_add_button_appears_on_hotel_index_for_logged_in_user(): void
    {
        $user = User::factory()->create();
        Hotel::factory()->create();

        $response = $this->actingAs($user)->get(route('hotels.index'));

        $response->assertOk();
        $response->assertSee("openQuickAdd({type: 'hotel'", false);
    }

    public function test_quick_add_button_appears_on_tour_index_when_no_hotel_bundle(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        TourPackage::factory()->create(['destination_id' => $destination->id, 'includes_hotel' => false]);

        $response = $this->actingAs($user)->get(route('paket-tours.index'));

        $response->assertOk();
        $response->assertSee("openQuickAdd({type: 'tour'", false);
    }

    public function test_quick_add_button_hidden_on_tour_index_when_bundle_includes_hotel(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        TourPackage::factory()->create(['destination_id' => $destination->id, 'includes_hotel' => true]);

        $response = $this->actingAs($user)->get(route('paket-tours.index'));

        $response->assertOk();
        $response->assertDontSee("openQuickAdd({type: 'tour'", false);
    }

    public function test_quick_add_button_and_modal_hidden_for_guests(): void
    {
        Destination::factory()->create();

        $response = $this->get(route('destinations.index'));

        $response->assertOk();
        $response->assertDontSee('id="quick-add-modal"', false);
        $response->assertDontSee('openQuickAdd', false);
    }

    public function test_quick_add_button_appears_on_destination_and_hotel_show_pages(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $hotel = Hotel::factory()->create();

        $destResponse = $this->actingAs($user)->get(route('destinations.show', $destination->id));
        $destResponse->assertOk();
        $destResponse->assertSee("openQuickAdd({type: 'destination'", false);

        $hotelResponse = $this->actingAs($user)->get(route('hotels.show', $hotel->id));
        $hotelResponse->assertOk();
        $hotelResponse->assertSee("openQuickAdd({type: 'hotel'", false);
    }
}
