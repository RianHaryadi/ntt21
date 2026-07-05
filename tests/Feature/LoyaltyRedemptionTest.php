<?php

namespace Tests\Feature;

use App\Models\CodePromotion;
use App\Models\Destination;
use App\Models\LoyaltyPoint;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyRedemptionTest extends TestCase
{
    use RefreshDatabase;

    private function givePoints(User $user, int $points): void
    {
        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => 'hotel_booking',
            'description' => 'seed points for test',
        ]);
    }

    public function test_user_can_redeem_points_for_a_personal_promo_code(): void
    {
        $user = User::factory()->create();
        $this->givePoints($user, 500);

        $response = $this->actingAs($user)->post(route('loyalty.redeem'), ['points' => 500]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('promotions', [
            'user_id' => $user->id,
            'discount_amount' => 50000,
            'active' => true,
        ]);
        $this->assertEquals(0, $user->fresh()->totalPoints());
    }

    public function test_cannot_redeem_with_insufficient_points(): void
    {
        $user = User::factory()->create();
        $this->givePoints($user, 100);

        $response = $this->actingAs($user)->post(route('loyalty.redeem'), ['points' => 500]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('promotions', 0);
        $this->assertEquals(100, $user->fresh()->totalPoints());
    }

    public function test_cannot_redeem_with_invalid_points_option(): void
    {
        $user = User::factory()->create();
        $this->givePoints($user, 5000);

        $response = $this->actingAs($user)->post(route('loyalty.redeem'), ['points' => 999]);

        $response->assertSessionHasErrors('points');
        $this->assertDatabaseCount('promotions', 0);
    }

    public function test_personal_promo_code_is_scoped_to_owner_only(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $promo = CodePromotion::create([
            'user_id' => $owner->id,
            'code' => 'RDM-TEST1234',
            'discount_amount' => 50000,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'active' => true,
        ]);

        $this->assertTrue($promo->isUsableBy($owner->id));
        $this->assertFalse($promo->isUsableBy($otherUser->id));
        $this->assertFalse($promo->isUsableBy(null));
    }

    public function test_global_promo_code_is_usable_by_anyone(): void
    {
        $promo = CodePromotion::create([
            'user_id' => null,
            'code' => 'GLOBAL2026',
            'discount_amount' => 20000,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'active' => true,
        ]);

        $this->assertTrue($promo->isUsableBy(1));
        $this->assertTrue($promo->isUsableBy(null));
    }

    public function test_destination_booking_ignores_client_supplied_discount_without_valid_promo(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);

        // Client mencoba kirim discount_amount langsung tanpa promo_code_id valid
        $response = $this->actingAs($user)->post(route('destinations.store'), [
            'destination_id' => $destination->id,
            'customer_name' => 'Budi',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(3)->toDateString(),
            'number_of_tickets' => 1,
            'discount_amount' => 90000, // percobaan manipulasi
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('transactions', [
            'destination_id' => $destination->id,
            'total_price' => 100000, // diskon diabaikan karena tidak ada promo valid
        ]);
    }

    public function test_destination_booking_rejects_another_users_personal_promo(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);

        $promo = CodePromotion::create([
            'user_id' => $owner->id,
            'code' => 'RDM-OWNERONLY',
            'discount_amount' => 50000,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'active' => true,
        ]);

        $response = $this->actingAs($attacker)->post(route('destinations.store'), [
            'destination_id' => $destination->id,
            'customer_name' => 'Attacker',
            'customer_email' => 'attacker@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(3)->toDateString(),
            'number_of_tickets' => 1,
            'promo_code_id' => $promo->id,
            'discount_amount' => 50000,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('transactions', [
            'destination_id' => $destination->id,
            'total_price' => 100000, // promo milik owner, tidak berlaku untuk attacker
        ]);
    }
}
