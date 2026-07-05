<?php

namespace Tests\Feature;

use App\Models\LoyaltyPoint;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyTierTest extends TestCase
{
    use RefreshDatabase;

    public function test_tier_thresholds_are_correct(): void
    {
        $loyalty = new LoyaltyService();

        $this->assertEquals('Bronze', $loyalty->tierFor(0));
        $this->assertEquals('Bronze', $loyalty->tierFor(999));
        $this->assertEquals('Silver', $loyalty->tierFor(1000));
        $this->assertEquals('Silver', $loyalty->tierFor(2999));
        $this->assertEquals('Gold', $loyalty->tierFor(3000));
        $this->assertEquals('Gold', $loyalty->tierFor(6999));
        $this->assertEquals('Platinum', $loyalty->tierFor(7000));
        $this->assertEquals('Platinum', $loyalty->tierFor(50000));
    }

    public function test_next_tier_info_shows_points_needed(): void
    {
        $loyalty = new LoyaltyService();

        $next = $loyalty->nextTierInfo(500);
        $this->assertEquals('Silver', $next['name']);
        $this->assertEquals(500, $next['points_needed']);

        $this->assertNull($loyalty->nextTierInfo(7000));
    }

    public function test_lifetime_points_do_not_decrease_after_redemption(): void
    {
        $user = User::factory()->create();

        LoyaltyPoint::create(['user_id' => $user->id, 'points' => 1000, 'type' => 'hotel_booking', 'description' => 'Booking A']);
        $this->assertEquals(1000, $user->lifetimeLoyaltyPoints());
        $this->assertEquals('Silver', (new LoyaltyService())->tierFor($user->lifetimeLoyaltyPoints()));

        LoyaltyPoint::create(['user_id' => $user->id, 'points' => -500, 'type' => 'redemption', 'description' => 'Redeemed']);

        // Balance drops, but lifetime (and therefore tier) does not
        $this->assertEquals(500, $user->totalPoints());
        $this->assertEquals(1000, $user->lifetimeLoyaltyPoints());
        $this->assertEquals('Silver', (new LoyaltyService())->tierFor($user->lifetimeLoyaltyPoints()));
    }

    public function test_dashboard_shows_loyalty_tier_badge(): void
    {
        $user = User::factory()->create();
        LoyaltyPoint::create(['user_id' => $user->id, 'points' => 3500, 'type' => 'hotel_booking', 'description' => 'Big booking']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Tier Gold');
        $response->assertSee('Menuju Tier Platinum');
    }

    public function test_dashboard_shows_max_tier_message_for_platinum(): void
    {
        $user = User::factory()->create();
        LoyaltyPoint::create(['user_id' => $user->id, 'points' => 8000, 'type' => 'hotel_booking', 'description' => 'Huge booking']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Tier Platinum');
        $response->assertSee('sudah di tier tertinggi');
    }
}
