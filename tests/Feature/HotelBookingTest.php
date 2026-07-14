<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelBookingTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(Hotel $hotel): array
    {
        $roomPrice = $hotel->single_room_price; // 300000
        $nights = 2;
        $basePrice = $roomPrice * $nights;
        $tax = $basePrice * 0.10;
        $service = $basePrice * 0.05;
        $total = $basePrice + $tax + $service;

        return [
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => now()->addDay()->toDateString(),
            'check_out_date' => now()->addDays(1 + $nights)->toDateString(),
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'agree_terms' => '1',
            'room_price' => $roomPrice,
            'night_count' => $nights,
            'tax' => $tax,
            'service_charge' => $service,
            'total_price' => $total,
            'discount_amount' => 0,
            'status' => 'pending',
        ];
    }

    public function test_logged_in_user_can_create_hotel_booking_with_valid_data(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('booking.hotel.store'), $this->validPayload($hotel));

        $this->assertDatabaseHas('booking_hotels', [
            'hotel_id' => $hotel->id,
            'customer_email' => 'budi@example.com',
            'status' => 'pending',
        ]);

        // Booking langsung kini dibungkus Order dan diarahkan ke pembayaran Midtrans
        $booking = BookingHotel::first();
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals($order->id, $booking->order_id);
        $response->assertRedirect(route('orders.payment', $order->order_code));
    }

    public function test_guest_can_create_hotel_booking_without_logging_in(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->post(route('booking.hotel.store'), $this->validPayload($hotel));

        $this->assertDatabaseHas('booking_hotels', [
            'hotel_id' => $hotel->id,
            'customer_email' => 'budi@example.com',
            'user_id' => null,
            'status' => 'pending',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.payment', $order->order_code));
    }

    public function test_booking_rejects_tampered_room_price(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $payload = $this->validPayload($hotel);
        $payload['room_price'] = 1; // client mencoba manipulasi harga

        $response = $this->actingAs($user)
            ->post(route('booking.hotel.store'), $payload);

        $response->assertSessionHasErrors('room_price');
        $this->assertDatabaseCount('booking_hotels', 0);
    }

    public function test_booking_requires_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('booking.hotel.store'), []);

        $response->assertSessionHasErrors(['hotel_id', 'room_type', 'customer_name', 'customer_email']);
    }
}
