<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\CodePromotion;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_cart(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_add_a_destination_to_cart(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);

        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'destination',
            'itemable_id' => $destination->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'number_of_tickets' => 2,
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'itemable_type' => Destination::class,
            'itemable_id' => $destination->id,
        ]);
    }

    public function test_user_can_add_a_hotel_to_cart(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create(['single_room_price' => 500000]);

        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'hotel',
            'itemable_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => now()->addDays(3)->format('Y-m-d'),
            'check_out_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('cart.index'));
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'itemable_type' => Hotel::class,
            'itemable_id' => $hotel->id,
        ]);
    }

    public function test_cannot_add_unavailable_hotel_room_to_cart(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create(['single_room_price' => 500000, 'room_count_single' => 1]);
        $checkIn = now()->addDays(3);
        $checkOut = now()->addDays(5);

        BookingHotel::create([
            'hotel_id' => $hotel->id, 'room_type' => 'single',
            'customer_name' => 'X', 'customer_email' => 'x@example.com', 'customer_phone' => '08123',
            'check_in_date' => $checkIn, 'check_out_date' => $checkOut,
            'night_count' => 2, 'room_price' => 500000, 'tax' => 0, 'service_charge' => 0,
            'total_price' => 1000000, 'discount_amount' => 0, 'status' => 'checked-in',
            'booking_number' => 'BK-EXISTING',
        ]);

        $response = $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'hotel',
            'itemable_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => $checkIn->format('Y-m-d'),
            'check_out_date' => $checkOut->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors('itemable_id');
        $this->assertDatabaseMissing('cart_items', ['itemable_type' => Hotel::class]);
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'destination',
            'itemable_id' => $destination->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'number_of_tickets' => 1,
        ]);

        $cartItemId = \App\Models\CartItem::first()->id;

        $response = $this->actingAs($user)->delete(route('cart.remove', $cartItemId));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItemId]);
    }

    public function test_checkout_creates_one_order_with_mixed_item_types(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);
        $hotel = Hotel::factory()->create(['single_room_price' => 500000]);

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'destination',
            'itemable_id' => $destination->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'number_of_tickets' => 2, // 200,000
        ]);

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'hotel',
            'itemable_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => now()->addDays(3)->format('Y-m-d'),
            'check_out_date' => now()->addDays(4)->format('Y-m-d'), // 1 night: 500,000 + 10% tax + 5% service = 575,000
        ]);

        $response = $this->actingAs($user)->post(route('cart.checkout'), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
        ]);

        $order = Order::first();
        $this->assertNotNull($order, 'Order was not created: ' . json_encode(session('errors')?->all()));
        $response->assertRedirect(route('orders.payment', $order->order_code));

        $this->assertEquals(1, Transaction::count());
        $this->assertEquals(1, BookingHotel::count());
        $this->assertEquals($order->id, Transaction::first()->order_id);
        $this->assertEquals($order->id, BookingHotel::first()->order_id);

        $this->assertEqualsWithDelta(200000 + 575000, $order->total_price, 0.01);

        // Cart should be empty after checkout
        $this->assertEquals(0, \App\Models\CartItem::where('user_id', $user->id)->count());
    }

    public function test_checkout_applies_promo_code_discount_across_whole_order(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);

        CodePromotion::create([
            'code' => 'SAVE10',
            'discount_percent' => 10,
            'active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDays(30),
        ]);

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'destination',
            'itemable_id' => $destination->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'number_of_tickets' => 2, // 200,000
        ]);

        $this->actingAs($user)->post(route('cart.checkout'), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'promo_code' => 'SAVE10',
        ]);

        $order = Order::first();
        $this->assertEqualsWithDelta(20000, $order->discount_amount, 0.01);
        $this->assertEqualsWithDelta(180000, $order->total_price, 0.01);
    }

    public function test_checkout_with_insurance_adds_cost_for_each_eligible_item(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create(['price' => 100000]);
        $hotel = Hotel::factory()->create(['single_room_price' => 500000]);

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'destination',
            'itemable_id' => $destination->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'number_of_tickets' => 2,
        ]);

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'hotel',
            'itemable_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => now()->addDays(3)->format('Y-m-d'),
            'check_out_date' => now()->addDays(4)->format('Y-m-d'),
        ]);

        $this->actingAs($user)->post(route('cart.checkout'), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
            'has_insurance' => '1',
        ]);

        $order = Order::first();
        $expectedInsurance = (config('services.insurance.price_per_ticket') * 2) + config('services.insurance.price_per_booking');
        $this->assertEqualsWithDelta($expectedInsurance, $order->insurance_amount, 0.01);
    }

    public function test_checkout_fails_gracefully_when_hotel_room_becomes_unavailable_before_checkout(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create(['single_room_price' => 500000, 'room_count_single' => 1]);
        $checkIn = now()->addDays(3)->format('Y-m-d');
        $checkOut = now()->addDays(4)->format('Y-m-d');

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'hotel',
            'itemable_id' => $hotel->id,
            'room_type' => 'single',
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
        ]);

        // Someone else books the only room in the meantime
        BookingHotel::create([
            'hotel_id' => $hotel->id, 'room_type' => 'single',
            'customer_name' => 'X', 'customer_email' => 'x@example.com', 'customer_phone' => '08123',
            'check_in_date' => $checkIn, 'check_out_date' => $checkOut,
            'night_count' => 1, 'room_price' => 500000, 'tax' => 0, 'service_charge' => 0,
            'total_price' => 500000, 'discount_amount' => 0, 'status' => 'checked-in',
            'booking_number' => 'BK-RACE',
        ]);

        $response = $this->actingAs($user)->post(route('cart.checkout'), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
        ]);

        $response->assertSessionHasErrors('cart');
        $this->assertEquals(0, Order::count());
        // Cart item should NOT be cleared since checkout failed
        $this->assertEquals(1, \App\Models\CartItem::where('user_id', $user->id)->count());
    }

    public function test_checkout_creates_transaction_for_tour_package(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();
        $tour = TourPackage::factory()->create(['destination_id' => $destination->id, 'price' => 300000, 'includes_hotel' => false]);

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'tour',
            'itemable_id' => $tour->id,
            'booking_date' => now()->addDays(5)->format('Y-m-d'),
            'number_of_tickets' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('cart.checkout'), [
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '081234567890',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $transaction = Transaction::first();
        $this->assertEquals($tour->id, $transaction->tour_package_id);
        $this->assertEquals($destination->id, $transaction->destination_id);
        $response->assertRedirect(route('orders.payment', $order->order_code));
    }
}
