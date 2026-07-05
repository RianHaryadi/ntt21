<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_booking_voucher_downloads_as_pdf(): void
    {
        $hotel = Hotel::factory()->create();
        $booking = BookingHotel::create([
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '081234567890',
            'check_in_date' => now()->addDays(3),
            'check_out_date' => now()->addDays(5),
            'night_count' => 2,
            'room_price' => 500000,
            'tax' => 50000,
            'service_charge' => 25000,
            'total_price' => 1075000,
            'discount_amount' => 0,
            'status' => 'checked-in',
            'booking_number' => 'BK-TEST123',
        ]);

        $response = $this->get(route('booking.voucher', $booking->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_transaction_ticket_downloads_as_pdf(): void
    {
        $user = User::factory()->create();
        $destination = Destination::factory()->create();

        $transaction = Transaction::create([
            'booking_code' => 'TRX-TEST123',
            'user_id' => $user->id,
            'customer_name' => 'John Smith',
            'customer_email' => 'john@example.com',
            'customer_phone' => '081234567890',
            'destination_id' => $destination->id,
            'booking_date' => now()->addDays(5),
            'number_of_tickets' => 2,
            'package_price' => 150000,
            'total_price' => 300000,
            'status' => Transaction::STATUS_PAID,
        ]);

        Ticket::create([
            'transaction_id' => $transaction->id,
            'ticket_code' => 'TKT-0001',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('transactions.ticket', $transaction->booking_code));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_transaction_ticket_requires_authentication(): void
    {
        $destination = Destination::factory()->create();
        $transaction = Transaction::create([
            'booking_code' => 'TRX-TEST456',
            'customer_name' => 'Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '081234567890',
            'destination_id' => $destination->id,
            'booking_date' => now()->addDays(5),
            'number_of_tickets' => 1,
            'package_price' => 150000,
            'total_price' => 150000,
            'status' => Transaction::STATUS_PAID,
        ]);

        $response = $this->get(route('transactions.ticket', $transaction->booking_code));

        $response->assertRedirect(route('login'));
    }
}
