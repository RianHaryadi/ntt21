<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewHelpfulVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_mark_someone_elses_review_as_helpful(): void
    {
        $author = User::factory()->create();
        $voter = User::factory()->create();
        $destination = Destination::factory()->create();
        $review = $destination->reviews()->create(['user_id' => $author->id, 'rating' => 5, 'body' => 'Bagus!']);

        $response = $this->actingAs($voter)->post(route('reviews.helpful', $review->id));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('review_votes', [
            'review_id' => $review->id,
            'user_id' => $voter->id,
        ]);
    }

    public function test_voting_helpful_twice_toggles_it_off(): void
    {
        $author = User::factory()->create();
        $voter = User::factory()->create();
        $destination = Destination::factory()->create();
        $review = $destination->reviews()->create(['user_id' => $author->id, 'rating' => 5, 'body' => 'Bagus!']);

        $this->actingAs($voter)->post(route('reviews.helpful', $review->id));
        $this->actingAs($voter)->post(route('reviews.helpful', $review->id));

        $this->assertDatabaseMissing('review_votes', [
            'review_id' => $review->id,
            'user_id' => $voter->id,
        ]);
    }

    public function test_user_cannot_vote_their_own_review_as_helpful(): void
    {
        $author = User::factory()->create();
        $destination = Destination::factory()->create();
        $review = $destination->reviews()->create(['user_id' => $author->id, 'rating' => 5, 'body' => 'Bagus!']);

        $response = $this->actingAs($author)->post(route('reviews.helpful', $review->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('review_votes', ['review_id' => $review->id]);
    }

    public function test_guest_cannot_vote_helpful(): void
    {
        $author = User::factory()->create();
        $destination = Destination::factory()->create();
        $review = $destination->reviews()->create(['user_id' => $author->id, 'rating' => 5, 'body' => 'Bagus!']);

        $response = $this->post(route('reviews.helpful', $review->id));

        $response->assertRedirect(route('login'));
    }

    public function test_destination_page_shows_helpful_vote_count(): void
    {
        $author = User::factory()->create();
        $voter = User::factory()->create();
        $destination = Destination::factory()->create();
        $review = $destination->reviews()->create(['user_id' => $author->id, 'rating' => 5, 'body' => 'Bagus sekali!']);
        $review->helpfulVotes()->create(['user_id' => $voter->id]);

        $response = $this->actingAs($voter)->get(route('destinations.show', $destination->id));

        $response->assertOk();
        $response->assertSee('Membantu');
    }
}
