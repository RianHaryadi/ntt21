<?php

namespace Tests\Feature;

use App\Models\BookingHotel;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Transaction;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopularitySortTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_sort_most_booked_orders_by_real_confirmed_booking_count(): void
    {
        $popular = Hotel::factory()->create(['name' => 'Popular Hotel']);
        $quiet = Hotel::factory()->create(['name' => 'Quiet Hotel']);

        // 3 confirmed bookings for popular, 1 for quiet
        foreach (range(1, 3) as $i) {
            BookingHotel::create([
                'hotel_id' => $popular->id, 'room_type' => 'single',
                'customer_name' => 'G', 'customer_email' => "g{$i}@example.com", 'customer_phone' => '08123',
                'check_in_date' => now()->addDays($i), 'check_out_date' => now()->addDays($i + 1),
                'night_count' => 1, 'room_price' => 100000, 'tax' => 0, 'service_charge' => 0,
                'total_price' => 100000, 'discount_amount' => 0, 'status' => 'checked-in',
            ]);
        }
        BookingHotel::create([
            'hotel_id' => $quiet->id, 'room_type' => 'single',
            'customer_name' => 'G', 'customer_email' => 'g@example.com', 'customer_phone' => '08123',
            'check_in_date' => now()->addDay(), 'check_out_date' => now()->addDays(2),
            'night_count' => 1, 'room_price' => 100000, 'tax' => 0, 'service_charge' => 0,
            'total_price' => 100000, 'discount_amount' => 0, 'status' => 'checked-in',
        ]);
        // pending booking on quiet should NOT count
        BookingHotel::create([
            'hotel_id' => $quiet->id, 'room_type' => 'single',
            'customer_name' => 'G', 'customer_email' => 'g2@example.com', 'customer_phone' => '08123',
            'check_in_date' => now()->addDays(5), 'check_out_date' => now()->addDays(6),
            'night_count' => 1, 'room_price' => 100000, 'tax' => 0, 'service_charge' => 0,
            'total_price' => 100000, 'discount_amount' => 0, 'status' => 'pending',
        ]);

        $response = $this->get(route('hotels.index', ['sort' => 'most_booked']));

        $response->assertOk();
        $hotels = $response->viewData('hotels');
        $this->assertEquals($popular->id, $hotels->first()->id);
    }

    public function test_destination_sort_popular_orders_by_paid_transaction_count(): void
    {
        $popular = Destination::factory()->create(['name' => 'Popular Destination']);
        $quiet = Destination::factory()->create(['name' => 'Quiet Destination']);

        foreach (range(1, 3) as $i) {
            Transaction::create([
                'booking_code' => 'DST-' . uniqid() . $i,
                'customer_name' => 'G', 'customer_email' => "g{$i}@example.com", 'customer_phone' => '08123',
                'destination_id' => $popular->id,
                'booking_date' => now()->addDays($i), 'number_of_tickets' => 1,
                'package_price' => 50000, 'discount' => 0, 'total_price' => 50000,
                'status' => Transaction::STATUS_PAID,
            ]);
        }
        // unpaid transaction on quiet should NOT count
        Transaction::create([
            'booking_code' => 'DST-' . uniqid(),
            'customer_name' => 'G', 'customer_email' => 'g@example.com', 'customer_phone' => '08123',
            'destination_id' => $quiet->id,
            'booking_date' => now()->addDay(), 'number_of_tickets' => 1,
            'package_price' => 50000, 'discount' => 0, 'total_price' => 50000,
            'status' => Transaction::STATUS_PENDING,
        ]);

        $response = $this->get(route('destinations.index', ['sort' => 'popular']));

        $response->assertOk();
        $destinations = $response->viewData('destinations');
        $this->assertEquals($popular->id, $destinations->first()->id);
    }

    public function test_tour_package_sort_popular_orders_by_paid_transaction_count(): void
    {
        $destination = Destination::factory()->create();
        $popular = TourPackage::factory()->create(['destination_id' => $destination->id, 'name' => 'Popular Tour']);
        $quiet = TourPackage::factory()->create(['destination_id' => $destination->id, 'name' => 'Quiet Tour']);

        foreach (range(1, 2) as $i) {
            Transaction::create([
                'booking_code' => 'PKT-' . uniqid() . $i,
                'customer_name' => 'G', 'customer_email' => "g{$i}@example.com", 'customer_phone' => '08123',
                'tour_package_id' => $popular->id,
                'booking_date' => now()->addDays($i), 'number_of_tickets' => 1,
                'package_price' => 500000, 'discount' => 0, 'total_price' => 500000,
                'status' => Transaction::STATUS_PAID,
            ]);
        }

        $response = $this->get(route('paket-tours.index', ['sort' => 'popular']));

        $response->assertOk();
        $tours = $response->viewData('paketTours');
        $this->assertEquals($popular->id, $tours->first()->id);
    }
}
