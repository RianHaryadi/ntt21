<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAutocompleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggestions_endpoint_returns_matching_destinations(): void
    {
        Destination::factory()->create(['name' => 'Pulau Komodo']);
        Destination::factory()->create(['name' => 'Danau Kelimutu']);

        $response = $this->getJson(route('search.suggestions', ['q' => 'Komodo']));

        $response->assertOk();
        $response->assertJsonFragment(['label' => 'Pulau Komodo']);
        $response->assertJsonMissing(['label' => 'Danau Kelimutu']);
    }

    public function test_suggestions_endpoint_searches_across_destinations_hotels_and_tours(): void
    {
        Destination::factory()->create(['name' => 'Bajo Beach']);
        Hotel::factory()->create(['name' => 'Bajo Grand Hotel']);
        $destination = Destination::factory()->create();
        TourPackage::factory()->create(['name' => 'Bajo Adventure Tour', 'destination_id' => $destination->id]);

        $response = $this->getJson(route('search.suggestions', ['q' => 'Bajo']));

        $response->assertOk();
        $types = collect($response->json('results'))->pluck('type')->all();
        $this->assertContains('destination', $types);
        $this->assertContains('hotel', $types);
        $this->assertContains('tour', $types);
    }

    public function test_suggestions_endpoint_returns_empty_for_short_query(): void
    {
        Destination::factory()->create(['name' => 'Komodo Island']);

        $response = $this->getJson(route('search.suggestions', ['q' => 'K']));

        $response->assertOk();
        $response->assertJson(['results' => []]);
    }

    public function test_suggestions_endpoint_returns_empty_for_no_matches(): void
    {
        Destination::factory()->create(['name' => 'Komodo Island']);

        $response = $this->getJson(route('search.suggestions', ['q' => 'Nonexistentplace']));

        $response->assertOk();
        $response->assertJson(['results' => []]);
    }
}
