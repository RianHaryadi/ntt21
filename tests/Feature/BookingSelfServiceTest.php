<?php

namespace Tests\Feature;

use App\Mail\HotelBookingMail;
use App\Models\BookingHotel;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingSelfServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeBooking(Hotel $hotel, array $overrides = []): BookingHotel
    {
        return BookingHotel::create(array_merge([
            'hotel_id' => $hotel->id,
            'room_type' => 'single',
            'customer_name' => 'Test Guest',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '081234567890',
            'check_in_date' => now()->addDays(3),
            'check_out_date' => now()->addDays(4),
            'night_count' => 1,
            'room_price' => 500000,
            'tax' => 50000,
            'service_charge' => 25000,
            'total_price' => 575000,
            'discount_amount' => 0,
            'status' => 'checked-in',
            'booking_number' => 'BK-' . uniqid(),
        ], $overrides));
    }

    public function test_guest_can_resend_confirmation_email_without_logging_in(): void
    {
        Mail::fake();
        $hotel = Hotel::factory()->create();
        $booking = $this->makeBooking($hotel);

        $response = $this->post(route('booking.resendEmail', $booking->booking_number));

        $response->assertSessionHasNoErrors();
        Mail::assertQueued(HotelBookingMail::class, function ($mail) use ($booking) {
            return $mail->hasTo($booking->customer_email);
        });
    }

    public function test_resend_email_for_unknown_booking_number_redirects_with_error(): void
    {
        $response = $this->post(route('booking.resendEmail', 'NOT-A-REAL-BOOKING'));

        $response->assertSessionHasErrors('booking_number');
    }

    public function test_guest_can_request_cancellation_with_matching_email(): void
    {
        $hotel = Hotel::factory()->create();
        $booking = $this->makeBooking($hotel);

        $response = $this->post(route('booking.requestCancellation', $booking->booking_number), [
            'customer_email' => 'guest@example.com',
            'reason' => 'Rencana perjalanan berubah.',
        ]);

        $response->assertSessionHasNoErrors();
        $booking->refresh();
        $this->assertEquals('requested', $booking->cancellation_status);
        $this->assertEquals('Rencana perjalanan berubah.', $booking->cancellation_reason);
    }

    public function test_cancellation_request_rejected_when_email_does_not_match(): void
    {
        $hotel = Hotel::factory()->create();
        $booking = $this->makeBooking($hotel);

        $response = $this->post(route('booking.requestCancellation', $booking->booking_number), [
            'customer_email' => 'wrong@example.com',
            'reason' => 'Trying to cancel someone else\'s booking.',
        ]);

        $response->assertSessionHasErrors('customer_email');
        $booking->refresh();
        $this->assertNull($booking->cancellation_status);
    }

    public function test_cancellation_request_rejected_for_non_cancellable_booking(): void
    {
        $hotel = Hotel::factory()->create();
        $booking = $this->makeBooking($hotel, ['status' => 'pending']);

        $response = $this->post(route('booking.requestCancellation', $booking->booking_number), [
            'customer_email' => 'guest@example.com',
            'reason' => 'Test',
        ]);

        $response->assertSessionHasErrors('customer_email');
        $booking->refresh();
        $this->assertNull($booking->cancellation_status);
    }

    public function test_booking_check_page_shows_cancellation_and_resend_actions(): void
    {
        $hotel = Hotel::factory()->create();
        $booking = $this->makeBooking($hotel);

        $response = $this->post(route('booking.check'), ['booking_number' => $booking->booking_number]);

        $response->assertOk();
        $response->assertSee('Kirim Ulang Email');
        $response->assertSee('Ajukan Pembatalan Booking');
        $response->assertSee('Download Voucher PDF');
    }
}
