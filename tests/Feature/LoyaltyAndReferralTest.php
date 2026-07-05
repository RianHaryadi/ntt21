<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Hotel;
use App\Models\Transaction;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoyaltyAndReferralTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_gets_unique_referral_code_on_creation(): void
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user->referral_code);
        $this->assertEquals(6, strlen($user->referral_code));
    }

    public function test_two_users_never_get_the_same_referral_code(): void
    {
        $users = User::factory()->count(20)->create();

        $codes = $users->pluck('referral_code');
        $this->assertEquals($codes->count(), $codes->unique()->count());
    }

    public function test_awards_points_for_hotel_booking_based_on_total_price(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $booking = BookingHotel::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => 'Test',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
            'check_in_date' => '2026-08-01',
            'check_out_date' => '2026-08-03',
            'night_count' => 2,
            'room_price' => 500000,
            'tax' => 0,
            'service_charge' => 0,
            'total_price' => 1250000, // -> 125 points at Rp10.000/point
            'discount_amount' => 0,
            'status' => 'checked-in',
        ]);

        app(LoyaltyService::class)->awardForHotelBooking($booking);

        $this->assertEquals(125, $user->fresh()->totalPoints());
    }

    public function test_does_not_award_points_for_guest_booking_without_user(): void
    {
        $hotel = Hotel::factory()->create();
        $booking = BookingHotel::create([
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => 'Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '081234567890',
            'check_in_date' => '2026-08-01',
            'check_out_date' => '2026-08-03',
            'night_count' => 2,
            'room_price' => 500000,
            'tax' => 0,
            'service_charge' => 0,
            'total_price' => 1000000,
            'discount_amount' => 0,
            'status' => 'checked-in',
        ]);

        app(LoyaltyService::class)->awardForHotelBooking($booking);

        $this->assertDatabaseCount('loyalty_points', 0);
    }

    public function test_does_not_double_award_points_for_the_same_booking(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $booking = BookingHotel::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => 'Test',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
            'check_in_date' => '2026-08-01',
            'check_out_date' => '2026-08-03',
            'night_count' => 2,
            'room_price' => 500000,
            'tax' => 0,
            'service_charge' => 0,
            'total_price' => 1000000,
            'discount_amount' => 0,
            'status' => 'checked-in',
        ]);

        $service = app(LoyaltyService::class);
        $service->awardForHotelBooking($booking);
        $service->awardForHotelBooking($booking); // retry / double call

        $this->assertDatabaseCount('loyalty_points', 1);
    }

    public function test_referrer_gets_bonus_when_referred_user_completes_first_booking(): void
    {
        $referrer = User::factory()->create();
        $referredUser = User::factory()->create(['referred_by_id' => $referrer->id]);
        $hotel = Hotel::factory()->create();

        $booking = BookingHotel::create([
            'user_id' => $referredUser->id,
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => 'Referred',
            'customer_email' => 'referred@example.com',
            'customer_phone' => '081234567890',
            'check_in_date' => '2026-08-01',
            'check_out_date' => '2026-08-02',
            'night_count' => 1,
            'room_price' => 100000,
            'tax' => 0,
            'service_charge' => 0,
            'total_price' => 100000,
            'discount_amount' => 0,
            'status' => 'checked-in',
        ]);

        app(LoyaltyService::class)->awardForHotelBooking($booking);

        $this->assertEquals(500, $referrer->fresh()->totalPoints());
    }

    public function test_referral_bonus_only_awarded_once_not_on_second_booking(): void
    {
        $referrer = User::factory()->create();
        $referredUser = User::factory()->create(['referred_by_id' => $referrer->id]);
        $hotel = Hotel::factory()->create(['room_count_single' => 5]);

        $service = app(LoyaltyService::class);

        foreach ([['2026-08-01', '2026-08-02'], ['2026-09-01', '2026-09-02']] as [$in, $out]) {
            $booking = BookingHotel::create([
                'user_id' => $referredUser->id,
                'hotel_id' => $hotel->id,
                'room_type' => 'single',
                'customer_name' => 'Referred',
                'customer_email' => 'referred@example.com',
                'customer_phone' => '081234567890',
                'check_in_date' => $in,
                'check_out_date' => $out,
                'night_count' => 1,
                'room_price' => 100000,
                'tax' => 0,
                'service_charge' => 0,
                'total_price' => 100000,
                'discount_amount' => 0,
                'status' => 'checked-in',
            ]);
            $service->awardForHotelBooking($booking);
        }

        $referralBonusCount = $referrer->fresh()->loyaltyPoints()->where('type', 'referral_bonus')->count();
        $this->assertEquals(1, $referralBonusCount);
    }

    public function test_awards_points_for_paid_transaction(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::create([
            'booking_code' => 'BOOK-' . uniqid(),
            'user_id' => $user->id,
            'customer_name' => 'Test',
            'customer_email' => 'test@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(3),
            'number_of_tickets' => 1,
            'package_price' => 500000,
            'discount' => 0,
            'total_price' => 500000, // -> 50 points
            'status' => 'paid',
        ]);

        app(LoyaltyService::class)->awardForTransaction($transaction);

        $this->assertEquals(50, $user->fresh()->totalPoints());
    }

    public function test_registration_links_user_to_referrer_via_code(): void
    {
        $referrer = User::factory()->create();

        Livewire::test(\App\Livewire\RegisterForm::class)
            ->set('name', 'New User')
            ->set('email', 'newuser@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('referral_code', $referrer->referral_code)
            ->call('register');

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals($referrer->id, $newUser->referred_by_id);
    }

    public function test_registration_with_invalid_referral_code_still_succeeds(): void
    {
        Livewire::test(\App\Livewire\RegisterForm::class)
            ->set('name', 'New User')
            ->set('email', 'newuser2@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('referral_code', 'NOTREAL')
            ->call('register');

        $newUser = User::where('email', 'newuser2@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertNull($newUser->referred_by_id);
    }

    public function test_registration_without_referral_code_succeeds_normally(): void
    {
        Livewire::test(\App\Livewire\RegisterForm::class)
            ->set('name', 'New User')
            ->set('email', 'newuser3@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');

        $newUser = User::where('email', 'newuser3@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertNull($newUser->referred_by_id);
    }
}
