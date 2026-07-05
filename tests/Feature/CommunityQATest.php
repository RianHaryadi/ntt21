<?php

namespace Tests\Feature;

use App\Models\Answer;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityQATest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_ask_a_question_about_a_destination(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'questionable_type' => 'destination',
            'questionable_id' => $destination->id,
            'body' => 'Apakah cocok untuk anak-anak?',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('questions', [
            'user_id' => $user->id,
            'questionable_type' => Destination::class,
            'questionable_id' => $destination->id,
            'body' => 'Apakah cocok untuk anak-anak?',
        ]);
    }

    public function test_logged_in_user_can_ask_a_question_about_a_hotel(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'questionable_type' => 'hotel',
            'questionable_id' => $hotel->id,
            'body' => 'Apakah tersedia airport shuttle?',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('questions', [
            'user_id' => $user->id,
            'questionable_type' => Hotel::class,
            'questionable_id' => $hotel->id,
        ]);
    }

    public function test_guest_cannot_ask_a_question(): void
    {
        $destination = Destination::factory()->create();

        $response = $this->post(route('questions.store'), [
            'questionable_type' => 'destination',
            'questionable_id' => $destination->id,
            'body' => 'Test question',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('questions', ['body' => 'Test question']);
    }

    public function test_any_logged_in_user_can_answer_a_question(): void
    {
        $asker = User::factory()->create();
        $answerer = User::factory()->create();
        $destination = Destination::factory()->create();
        $question = $destination->questions()->create(['user_id' => $asker->id, 'body' => 'Q?']);

        $response = $this->actingAs($answerer)->post(route('questions.answers.store', $question->id), [
            'body' => 'Ya, sangat cocok!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('answers', [
            'question_id' => $question->id,
            'user_id' => $answerer->id,
            'body' => 'Ya, sangat cocok!',
        ]);
    }

    public function test_question_owner_can_delete_their_own_question(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $question = $destination->questions()->create(['user_id' => $user->id, 'body' => 'Q?']);

        $response = $this->actingAs($user)->delete(route('questions.destroy', $question->id));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }

    public function test_user_cannot_delete_someone_elses_question(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $destination = Destination::factory()->create();
        $question = $destination->questions()->create(['user_id' => $owner->id, 'body' => 'Q?']);

        $response = $this->actingAs($otherUser)->delete(route('questions.destroy', $question->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('questions', ['id' => $question->id]);
    }

    public function test_answer_owner_can_delete_their_own_answer(): void
    {
        $asker = User::factory()->create();
        $answerer = User::factory()->create();
        $destination = Destination::factory()->create();
        $question = $destination->questions()->create(['user_id' => $asker->id, 'body' => 'Q?']);
        $answer = $question->answers()->create(['user_id' => $answerer->id, 'body' => 'A!']);

        $response = $this->actingAs($answerer)->delete(route('answers.destroy', $answer->id));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('answers', ['id' => $answer->id]);
    }

    public function test_destination_page_shows_questions_and_answers(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $question = $destination->questions()->create(['user_id' => $user->id, 'body' => 'Apakah buka setiap hari?']);
        $question->answers()->create(['user_id' => $user->id, 'body' => 'Ya, buka setiap hari.']);

        $response = $this->get(route('destinations.show', $destination->id));

        $response->assertOk();
        $response->assertSee('Tanya Jawab Komunitas');
        $response->assertSee('Apakah buka setiap hari?');
        $response->assertSee('Ya, buka setiap hari.');
    }
}
