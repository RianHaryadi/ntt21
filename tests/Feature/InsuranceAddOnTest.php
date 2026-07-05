<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Transaction;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsuranceAddOnTest extends TestCase
{
    use RefreshDatabase;

    public function test_destination_booking_with_insurance_adds_cost_per_ticket(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);

        $response = $this->actingAs($user)->post(route('destinations.store'), [
            'destination_id' => $destination->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'number_of_tickets' => 3,
            'has_insurance' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $transaction = Transaction::first();
        $expectedInsurance = config('services.insurance.price_per_ticket') * 3;

        $this->assertTrue($transaction->has_insurance);
        $this->assertEquals($expectedInsurance, $transaction->insurance_amount);
        $this->assertEquals(300000 + $expectedInsurance, $transaction->total_price);
    }

    public function test_destination_booking_without_insurance_has_zero_insurance_amount(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);

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

        $this->assertFalse($transaction->has_insurance);
        $this->assertEquals(0, $transaction->insurance_amount);
        $this->assertEquals(200000, $transaction->total_price);
    }

    public function test_hotel_booking_with_insurance_adds_flat_fee(): void
    {
        $hotel = Hotel::factory()->create(['single_room_price' => 500000]);
        $checkIn = now()->addDays(3)->format('Y-m-d');
        $checkOut = now()->addDays(4)->format('Y-m-d');
        $basePrice = 500000;
        $tax = $basePrice * 0.10;
        $service = $basePrice * 0.05;
        $insurance = config('services.insurance.price_per_booking');
        $total = $basePrice + $tax + $service + $insurance;

        $response = $this->post(route('booking.hotel.store'), [
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'customer_name' => 'Test Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '081234567890',
            'payment_method' => 'transfer',
            'agree_terms' => '1',
            'room_price' => $basePrice,
            'night_count' => 1,
            'tax' => $tax,
            'service_charge' => $service,
            'total_price' => $total,
            'discount_amount' => 0,
            'status' => 'pending',
            'has_insurance' => '1',
        ]);

        $booking = BookingHotel::first();
        $this->assertNotNull($booking, 'Booking was not created: ' . json_encode(session('errors')?->all()));
        $this->assertTrue($booking->has_insurance);
        $this->assertEquals($insurance, $booking->insurance_amount);
        $this->assertEquals($total, $booking->total_price);
    }

    public function test_hotel_booking_without_insurance_has_zero_insurance_amount(): void
    {
        $hotel = Hotel::factory()->create(['single_room_price' => 500000]);
        $checkIn = now()->addDays(3)->format('Y-m-d');
        $checkOut = now()->addDays(4)->format('Y-m-d');
        $basePrice = 500000;
        $tax = $basePrice * 0.10;
        $service = $basePrice * 0.05;
        $total = $basePrice + $tax + $service;

        $this->post(route('booking.hotel.store'), [
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'customer_name' => 'Test Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '081234567890',
            'payment_method' => 'transfer',
            'agree_terms' => '1',
            'room_price' => $basePrice,
            'night_count' => 1,
            'tax' => $tax,
            'service_charge' => $service,
            'total_price' => $total,
            'discount_amount' => 0,
            'status' => 'pending',
        ]);

        $booking = BookingHotel::first();
        $this->assertFalse($booking->has_insurance);
        $this->assertEquals(0, $booking->insurance_amount);
    }

    public function test_hotel_booking_cannot_fake_total_by_claiming_insurance_without_paying_for_it(): void
    {
        $hotel = Hotel::factory()->create(['single_room_price' => 500000]);
        $checkIn = now()->addDays(3)->format('Y-m-d');
        $checkOut = now()->addDays(4)->format('Y-m-d');
        $basePrice = 500000;
        $tax = $basePrice * 0.10;
        $service = $basePrice * 0.05;
        // Client claims has_insurance but sends total_price WITHOUT the insurance fee added
        $fakeTotal = $basePrice + $tax + $service;

        $this->post(route('booking.hotel.store'), [
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'customer_name' => 'Test Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '081234567890',
            'payment_method' => 'transfer',
            'agree_terms' => '1',
            'room_price' => $basePrice,
            'night_count' => 1,
            'tax' => $tax,
            'service_charge' => $service,
            'total_price' => $fakeTotal,
            'discount_amount' => 0,
            'status' => 'pending',
            'has_insurance' => '1',
        ]);

        $booking = BookingHotel::first();
        $expectedTotal = $fakeTotal + config('services.insurance.price_per_booking');
        // Server should override with the correct total including insurance, not trust the client
        $this->assertEquals($expectedTotal, $booking->total_price);
    }

    public function test_tour_package_booking_with_insurance_adds_cost_per_ticket(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $tourPackage = TourPackage::factory()->create(['destination_id' => $destination->id, 'price' => 200000, 'includes_hotel' => false]);

        $response = $this->actingAs($user)->post(route('paket-tour.store'), [
            'tour_package_id' => $tourPackage->id,
            'destination_id' => $destination->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(2)->format('Y-m-d'),
            'number_of_tickets' => 2,
            'has_insurance' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $transaction = Transaction::first();
        $expectedInsurance = config('services.insurance.price_per_ticket') * 2;

        $this->assertTrue($transaction->has_insurance);
        $this->assertEquals($expectedInsurance, $transaction->insurance_amount);
        $this->assertEquals(400000 + $expectedInsurance, $transaction->total_price);
    }
}
