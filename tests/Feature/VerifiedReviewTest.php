<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifiedReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_any_booking_cannot_review_destination(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 5,
            'body' => 'Bagus banget padahal belum pernah ke sini',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_user_with_paid_transaction_can_review_destination(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        Transaction::create([
            'booking_code' => 'DST-' . uniqid(),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'destination_id' => $destination->id,
            'booking_date' => now()->addDays(2),
            'number_of_tickets' => 1,
            'package_price' => 50000,
            'discount' => 0,
            'total_price' => 50000,
            'status' => Transaction::STATUS_PAID,
        ]);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 5,
            'body' => 'Memang indah!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'rating' => 5]);
    }

    public function test_user_with_pending_transaction_cannot_review_destination(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        Transaction::create([
            'booking_code' => 'DST-' . uniqid(),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'destination_id' => $destination->id,
            'booking_date' => now()->addDays(2),
            'number_of_tickets' => 1,
            'package_price' => 50000,
            'discount' => 0,
            'total_price' => 50000,
            'status' => Transaction::STATUS_PENDING, // belum bayar
        ]);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 4,
            'body' => 'Belum bayar tapi review duluan',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_user_without_hotel_stay_cannot_review_hotel(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'hotel',
            'reviewable_id' => $hotel->id,
            'rating' => 5,
            'body' => 'Belum pernah menginap',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_checked_in_guest_can_review_hotel(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        BookingHotel::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'check_in_date' => now()->subDays(2),
            'check_out_date' => now()->subDay(),
            'night_count' => 1,
            'room_price' => 300000,
            'tax' => 0,
            'service_charge' => 0,
            'total_price' => 300000,
            'discount_amount' => 0,
            'status' => 'checked-in',
        ]);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'hotel',
            'reviewable_id' => $hotel->id,
            'rating' => 5,
            'body' => 'Nyaman sekali!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'rating' => 5]);
    }

    public function test_pending_hotel_booking_cannot_review(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        BookingHotel::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'check_in_date' => now()->addDays(2),
            'check_out_date' => now()->addDays(3),
            'night_count' => 1,
            'room_price' => 300000,
            'tax' => 0,
            'service_charge' => 0,
            'total_price' => 300000,
            'discount_amount' => 0,
            'status' => 'pending', // belum disetujui admin
        ]);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'hotel',
            'reviewable_id' => $hotel->id,
            'rating' => 5,
            'body' => 'Baru submit booking, belum menginap',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('reviews', 0);
    }
}
