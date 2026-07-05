<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewTest extends TestCase
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
            'discount' => 0,
            'total_price' => 50000,
            'status' => Transaction::STATUS_PAID,
        ]);
    }

    public function test_user_can_submit_review_without_photo(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $this->givePaidBooking($user, $destination);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 5,
            'body' => 'Tempatnya luar biasa indah!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'rating' => 5,
            'photo' => null,
        ]);
    }

    public function test_user_can_submit_review_with_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $this->givePaidBooking($user, $destination);
        $photo = UploadedFile::fake()->image('review.jpg');

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 4,
            'body' => 'Bagus, tapi aksesnya agak sulit.',
            'photo' => $photo,
        ]);

        $response->assertSessionHasNoErrors();
        $review = Review::first();
        $this->assertNotNull($review->photo);
        Storage::disk('public')->assertExists($review->photo);
    }

    public function test_review_rejects_non_image_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $this->givePaidBooking($user, $destination);
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        $response = $this->actingAs($user)->post(route('reviews.store'), [
            'reviewable_type' => 'destination',
            'reviewable_id' => $destination->id,
            'rating' => 4,
            'body' => 'Test',
            'photo' => $file,
        ]);

        $response->assertSessionHasErrors('photo');
    }

    public function test_deleting_review_removes_its_photo_from_storage(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $photo = UploadedFile::fake()->image('review.jpg');
        $photoPath = $photo->store('reviews', 'public');

        $review = $destination->reviews()->create([
            'user_id' => $user->id,
            'rating' => 5,
            'body' => 'Mantap',
            'photo' => $photoPath,
        ]);

        $this->actingAs($user)->delete(route('reviews.destroy', $review->id));

        Storage::disk('public')->assertMissing($photoPath);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }
}
