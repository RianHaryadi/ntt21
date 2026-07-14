<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private function makeBooking(Hotel $hotel, string $checkIn, string $checkOut, string $status = 'pending'): BookingHotel
    {
        return BookingHotel::create([
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => 'Test Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '081234567890',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'night_count' => 1,
            'room_price' => $hotel->single_room_price,
            'tax' => 0,
            'service_charge' => 0,
            'total_price' => $hotel->single_room_price,
            'discount_amount' => 0,
            'status' => $status,
        ]);
    }

    private function validPayload(Hotel $hotel, string $checkIn, string $checkOut): array
    {
        $roomPrice = $hotel->single_room_price;
        $nights = (new \DateTime($checkIn))->diff(new \DateTime($checkOut))->days;
        $basePrice = $roomPrice * $nights;
        $tax = $basePrice * 0.10;
        $service = $basePrice * 0.05;
        $total = $basePrice + $tax + $service;

        return [
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
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

    public function test_hotel_model_reports_correct_available_rooms(): void
    {
        $hotel = Hotel::factory()->create(['room_count_single' => 2]);

        $this->assertEquals(2, $hotel->availableRooms('single', '2026-08-01', '2026-08-03'));

        $this->makeBooking($hotel, '2026-08-01', '2026-08-03');
        $this->assertEquals(1, $hotel->availableRooms('single', '2026-08-01', '2026-08-03'));

        $this->makeBooking($hotel, '2026-08-02', '2026-08-04'); // overlaps
        $this->assertEquals(0, $hotel->availableRooms('single', '2026-08-01', '2026-08-03'));
    }

    public function test_non_overlapping_dates_do_not_block_availability(): void
    {
        $hotel = Hotel::factory()->create(['room_count_single' => 1]);
        $this->makeBooking($hotel, '2026-08-01', '2026-08-03');

        // completely different month, should not be blocked
        $this->assertEquals(1, $hotel->availableRooms('single', '2026-09-01', '2026-09-03'));
    }

    public function test_failed_and_checked_out_bookings_do_not_count_toward_availability(): void
    {
        $hotel = Hotel::factory()->create(['room_count_single' => 1]);
        $this->makeBooking($hotel, '2026-08-01', '2026-08-03', 'failed');
        $this->makeBooking($hotel, '2026-08-01', '2026-08-03', 'checked-out');

        $this->assertEquals(1, $hotel->availableRooms('single', '2026-08-01', '2026-08-03'));
    }

    public function test_booking_form_rejects_overlapping_dates_for_fully_booked_room(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create(['room_count_single' => 1]);
        $this->makeBooking($hotel, '2026-08-01', '2026-08-05');

        $response = $this->actingAs($user)->post(
            route('booking.hotel.store'),
            $this->validPayload($hotel, '2026-08-03', '2026-08-04') // overlaps existing booking
        );

        $response->assertSessionHasErrors('room_type');
        $this->assertDatabaseCount('booking_hotels', 1); // still just the original booking
    }

    public function test_booking_form_accepts_non_overlapping_dates(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create(['room_count_single' => 1]);
        $this->makeBooking($hotel, '2026-08-01', '2026-08-05');

        $response = $this->actingAs($user)->post(
            route('booking.hotel.store'),
            $this->validPayload($hotel, '2026-09-01', '2026-09-03')
        );

        $this->assertDatabaseCount('booking_hotels', 2);
        $response->assertRedirect();
    }

    public function test_availability_endpoint_returns_json(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create(['room_count_single' => 1]);
        $this->makeBooking($hotel, '2026-08-01', '2026-08-05');

        $response = $this->actingAs($user)->getJson(route('hotels.availability', $hotel->id) . '?room_type=single&check_in_date=2026-08-02&check_out_date=2026-08-03');

        $response->assertOk()->assertJson(['available' => false, 'remaining' => 0]);
    }
}
