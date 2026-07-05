<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\TourBooking;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function makeTransaction(array $overrides = []): Transaction
    {
        return Transaction::create(array_merge([
            'booking_code' => 'BOOK-TEST-' . uniqid(),
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(3),
            'number_of_tickets' => 2,
            'package_price' => 500000,
            'discount' => 0,
            'total_price' => 500000,
            'status' => 'pending',
        ], $overrides));
    }

    private function signedPayload(Transaction $transaction, string $transactionStatus, ?string $fraudStatus = null, string $paymentType = 'bank_transfer'): array
    {
        $statusCode = '200';
        $grossAmount = number_format($transaction->total_price, 2, '.', '');
        $serverKey = config('services.midtrans.server_key');

        $signature = hash('sha512', $transaction->booking_code . $statusCode . $grossAmount . $serverKey);

        return [
            'order_id' => $transaction->booking_code,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payment_type' => $paymentType,
        ];
    }

    public function test_webhook_marks_transaction_paid_on_settlement(): void
    {
        $transaction = $this->makeTransaction();

        $response = $this->postJson(route('midtrans.notification'), $this->signedPayload($transaction, 'settlement'));

        $response->assertOk();
        $this->assertEquals('paid', $transaction->fresh()->status);
    }

    public function test_webhook_marks_transaction_paid_on_capture_with_accept_fraud_status(): void
    {
        $transaction = $this->makeTransaction();

        $response = $this->postJson(route('midtrans.notification'), $this->signedPayload($transaction, 'capture', 'accept'));

        $response->assertOk();
        $this->assertEquals('paid', $transaction->fresh()->status);
    }

    public function test_webhook_keeps_transaction_pending_on_pending_status(): void
    {
        $transaction = $this->makeTransaction();

        $this->postJson(route('midtrans.notification'), $this->signedPayload($transaction, 'pending'));

        $this->assertEquals('pending', $transaction->fresh()->status);
    }

    public function test_webhook_cancels_transaction_on_deny(): void
    {
        $transaction = $this->makeTransaction();

        $this->postJson(route('midtrans.notification'), $this->signedPayload($transaction, 'deny'));

        $this->assertEquals('cancelled', $transaction->fresh()->status);
    }

    public function test_webhook_expires_transaction_on_expire(): void
    {
        $transaction = $this->makeTransaction();

        $this->postJson(route('midtrans.notification'), $this->signedPayload($transaction, 'expire'));

        $this->assertEquals('expired', $transaction->fresh()->status);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $transaction = $this->makeTransaction();

        $payload = $this->signedPayload($transaction, 'settlement');
        $payload['signature_key'] = 'tampered-signature';

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertStatus(403);
        $this->assertEquals('pending', $transaction->fresh()->status);
    }

    public function test_webhook_returns_404_for_unknown_order_id(): void
    {
        $payload = [
            'order_id' => 'NONEXISTENT-CODE',
            'status_code' => '200',
            'gross_amount' => '100000.00',
            'signature_key' => hash('sha512', 'NONEXISTENT-CODE200100000.00' . config('services.midtrans.server_key')),
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
        ];

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertStatus(404);
    }

    public function test_webhook_syncs_linked_tour_booking_status_to_confirmed(): void
    {
        $tourPackage = TourPackage::factory()->create();
        $transaction = $this->makeTransaction(['tour_package_id' => $tourPackage->id]);

        TourBooking::create([
            'tour_package_id' => $tourPackage->id,
            'customer_name' => $transaction->customer_name,
            'customer_email' => $transaction->customer_email,
            'customer_phone' => $transaction->customer_phone,
            'tour_price' => $transaction->total_price,
            'total_price' => $transaction->total_price,
            'status' => 'pending',
            'booking_number' => $transaction->booking_code,
        ]);

        $this->postJson(route('midtrans.notification'), $this->signedPayload($transaction, 'settlement'));

        $this->assertDatabaseHas('tour_bookings', [
            'booking_number' => $transaction->booking_code,
            'status' => 'confirmed',
        ]);
    }

    public function test_webhook_endpoint_is_exempt_from_csrf(): void
    {
        // Panggilan tanpa header CSRF token — harus tetap diterima (403 karena signature, bukan CSRF)
        $transaction = $this->makeTransaction();
        $payload = $this->signedPayload($transaction, 'settlement');

        $response = $this->post(route('midtrans.notification'), $payload);

        $response->assertStatus(200); // bukan 419 (CSRF mismatch)
    }
}
