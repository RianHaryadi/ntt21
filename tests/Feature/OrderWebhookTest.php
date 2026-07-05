<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Hotel;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function signedPayload(Order $order, string $transactionStatus, ?string $fraudStatus = null, string $paymentType = 'bank_transfer'): array
    {
        $statusCode = '200';
        $grossAmount = number_format($order->total_price, 2, '.', '');
        $serverKey = config('services.midtrans.server_key');

        $signature = hash('sha512', $order->order_code . $statusCode . $grossAmount . $serverKey);

        return [
            'order_id' => $order->order_code,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payment_type' => $paymentType,
        ];
    }

    private function checkoutCart(User $user): Order
    {
        $destination = Destination::factory()->create(['price' => 100000]);
        $hotel = Hotel::factory()->create(['single_room_price' => 500000]);

        $this->actingAs($user)->postJson(route('cart.add'), [
            'itemable_type' => 'destination',
            'itemable_id' => $destination->id,
            'booking_date' => now()->addDays(3)->format('Y-m-d'),
            'number_of_tickets' => 1,
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
        ]);

        return Order::first();
    }

    public function test_webhook_marks_order_and_all_child_items_paid_on_settlement(): void
    {
        $user = User::factory()->create();
        $order = $this->checkoutCart($user);

        $response = $this->postJson(route('midtrans.notification'), $this->signedPayload($order, 'settlement'));

        $response->assertOk();
        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals('paid', Transaction::first()->status);
        $this->assertEquals('checked-in', BookingHotel::first()->status);
    }

    public function test_webhook_awards_loyalty_points_for_both_transaction_and_hotel_booking_in_order(): void
    {
        $user = User::factory()->create();
        $order = $this->checkoutCart($user);

        $this->postJson(route('midtrans.notification'), $this->signedPayload($order, 'settlement'));

        $types = LoyaltyPoint::where('user_id', $user->id)->pluck('type')->all();
        $this->assertContains('tour_booking', $types);
        $this->assertContains('hotel_booking', $types);
    }

    public function test_webhook_keeps_order_pending_on_pending_status(): void
    {
        $user = User::factory()->create();
        $order = $this->checkoutCart($user);

        $this->postJson(route('midtrans.notification'), $this->signedPayload($order, 'pending'));

        $order->refresh();
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('pending', Transaction::first()->status);
        $this->assertEquals('pending', BookingHotel::first()->status);
    }

    public function test_webhook_cancels_order_and_children_on_deny(): void
    {
        $user = User::factory()->create();
        $order = $this->checkoutCart($user);

        $this->postJson(route('midtrans.notification'), $this->signedPayload($order, 'deny'));

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals('cancelled', Transaction::first()->status);
    }

    public function test_legacy_standalone_transaction_webhook_still_works_unaffected_by_order_logic(): void
    {
        $transaction = Transaction::create([
            'booking_code' => 'DST-STANDALONE-' . uniqid(),
            'customer_name' => 'Solo Guest',
            'customer_email' => 'solo@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(3),
            'number_of_tickets' => 1,
            'package_price' => 100000,
            'total_price' => 100000,
            'status' => 'pending',
        ]);

        $statusCode = '200';
        $grossAmount = number_format($transaction->total_price, 2, '.', '');
        $signature = hash('sha512', $transaction->booking_code . $statusCode . $grossAmount . config('services.midtrans.server_key'));

        $response = $this->postJson(route('midtrans.notification'), [
            'order_id' => $transaction->booking_code,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ]);

        $response->assertOk();
        $this->assertEquals('paid', $transaction->fresh()->status);
        $this->assertEquals(0, Order::count());
    }
}
