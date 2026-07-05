<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourBundlePricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tour_without_hotel_bundle_charges_tour_price_only(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $tourPackage = TourPackage::factory()->create([
            'destination_id' => $destination->id,
            'price' => 500000,
            'days' => 3,
            'includes_hotel' => false,
        ]);

        $response = $this->actingAs($user)->post(route('paket-tour.store'), [
            'tour_package_id' => $tourPackage->id,
            'destination_id' => $destination->id,
            'customer_name' => 'Budi',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(5)->toDateString(),
            'number_of_tickets' => 2,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('transactions', [
            'total_price' => 1000000, // 500000 * 2, no hotel
        ]);
        $this->assertDatabaseHas('tour_bookings', [
            'hotel_price' => 0,
        ]);
    }

    public function test_tour_with_hotel_bundle_adds_discounted_hotel_price(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $hotel = Hotel::factory()->create([
            'single_room_price' => 200000,
            'double_room_price' => 300000,
            'family_room_price' => 400000,
        ]);
        $tourPackage = TourPackage::factory()->create([
            'destination_id' => $destination->id,
            'price' => 500000,
            'days' => 3, // -> 2 nights
            'includes_hotel' => true,
        ]);
        $tourPackage->hotels()->attach($hotel->id);

        $response = $this->actingAs($user)->post(route('paket-tour.store'), [
            'tour_package_id' => $tourPackage->id,
            'destination_id' => $destination->id,
            'customer_name' => 'Budi',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(5)->toDateString(),
            'number_of_tickets' => 1,
        ]);

        $response->assertSessionHasNoErrors();

        // cheapest room = 200000/night * 2 nights = 400000, minus 10% bundle discount = 360000
        // total = 500000 (tour) + 360000 (hotel) = 860000
        $this->assertDatabaseHas('transactions', [
            'total_price' => 860000,
        ]);
        $this->assertDatabaseHas('tour_bookings', [
            'tour_price' => 500000,
            'hotel_price' => 360000,
            'total_price' => 860000,
        ]);

        $booking = TourBooking::first();
        $this->assertEquals($hotel->id, $booking->hotel_id);
    }

    public function test_bundle_hotel_price_scales_with_ticket_count(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $hotel = Hotel::factory()->create([
            'single_room_price' => 100000,
            'double_room_price' => 150000,
            'family_room_price' => 200000,
        ]);
        $tourPackage = TourPackage::factory()->create([
            'destination_id' => $destination->id,
            'price' => 200000,
            'days' => 2, // -> 1 night
            'includes_hotel' => true,
        ]);
        $tourPackage->hotels()->attach($hotel->id);

        $this->actingAs($user)->post(route('paket-tour.store'), [
            'tour_package_id' => $tourPackage->id,
            'destination_id' => $destination->id,
            'customer_name' => 'Budi',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(5)->toDateString(),
            'number_of_tickets' => 3,
        ]);

        // cheapest room = 100000/night * 1 night * 3 tickets = 300000, minus 10% = 270000
        $this->assertDatabaseHas('tour_bookings', [
            'hotel_price' => 270000,
        ]);
    }
}
