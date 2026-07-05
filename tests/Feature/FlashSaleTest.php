<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlashSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_destination_on_active_flash_sale_reports_discounted_price(): void
    {
        $destination = Destination::factory()->create([
            'price' => 100000,
            'flash_sale_discount_percent' => 20,
            'flash_sale_ends_at' => now()->addHours(2),
        ]);

        $this->assertTrue($destination->isOnFlashSale());
        $this->assertEquals(80000, $destination->flash_sale_price);
    }

    public function test_destination_with_expired_flash_sale_is_not_on_sale(): void
    {
        $destination = Destination::factory()->create([
            'price' => 100000,
            'flash_sale_discount_percent' => 20,
            'flash_sale_ends_at' => now()->subHour(),
        ]);

        $this->assertFalse($destination->isOnFlashSale());
        $this->assertNull($destination->flash_sale_price);
    }

    public function test_destination_without_flash_sale_fields_is_not_on_sale(): void
    {
        $destination = Destination::factory()->create([
            'price' => 100000,
            'flash_sale_discount_percent' => null,
            'flash_sale_ends_at' => null,
        ]);

        $this->assertFalse($destination->isOnFlashSale());
    }

    public function test_booking_a_destination_on_flash_sale_charges_the_discounted_price(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create([
            'price' => 100000,
            'flash_sale_discount_percent' => 25,
            'flash_sale_ends_at' => now()->addHours(3),
        ]);

        $response = $this->actingAs($user)->post(route('destinations.store'), [
            'destination_id' => $destination->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'number_of_tickets' => 2,
        ]);

        $response->assertSessionHasNoErrors();
        $transaction = Transaction::first();
        // 75000 per ticket (25% off 100000) x 2 tickets = 150000
        $this->assertEquals(75000, $transaction->package_price);
        $this->assertEquals(150000, $transaction->total_price);
    }

    public function test_hotel_flash_sale_price_is_computed_from_room_price(): void
    {
        $hotel = Hotel::factory()->create([
            'single_room_price' => 500000,
            'flash_sale_discount_percent' => 30,
            'flash_sale_ends_at' => now()->addHours(5),
        ]);

        $this->assertTrue($hotel->isOnFlashSale());
        $this->assertEquals(350000, $hotel->flashSalePrice($hotel->single_room_price));
    }

    public function test_home_page_shows_flash_sale_section_when_items_on_sale(): void
    {
        Destination::factory()->create([
            'name' => 'On Sale Destination',
            'flash_sale_discount_percent' => 15,
            'flash_sale_ends_at' => now()->addHours(1),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Flash Sale');
        $response->assertSee('On Sale Destination');
    }
}
