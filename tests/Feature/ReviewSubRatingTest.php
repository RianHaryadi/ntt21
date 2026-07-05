<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewSubRatingTest extends TestCase
{
    use RefreshDatabase;

    private function givePaidBooking(User $user, Destination $destination): void
    {
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
            'total_price' => 50000,
            'status' => Transaction::STATUS_PAID,
        ]);
    }

    public function test_user_can_submit_review_with_sub_ratings(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $this->givePaidBooking($user, $destination);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 5,
            'body' => 'Sangat bagus dan bersih!',
            'cleanliness_rating' => 5,
            'location_rating' => 4,
            'value_rating' => 5,
            'service_rating' => 4,
        ]);

        $response->assertSessionHasNoErrors();
        $review = Review::first();
        $this->assertTrue($review->hasSubRatings());
        $this->assertEquals(5, $review->cleanliness_rating);
        $this->assertEquals(4, $review->location_rating);
    }

    public function test_review_without_sub_ratings_still_works(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $this->givePaidBooking($user, $destination);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 5,
            'body' => 'Bagus!',
        ]);

        $response->assertSessionHasNoErrors();
        $review = Review::first();
        $this->assertFalse($review->hasSubRatings());
    }

    public function test_sub_rating_out_of_range_is_rejected(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $this->givePaidBooking($user, $destination);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 5,
            'body' => 'Test',
            'cleanliness_rating' => 8,
        ]);

        $response->assertSessionHasErrors('cleanliness_rating');
    }
}
