<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\HotelBookingMail;
use App\Models\BookingHotel;
use App\Models\CodePromotion;
use App\Models\Hotel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    /**
     * Tampilkan form cek status booking.
     */
    public function checkForm()
    {
        return view('booking.check');
    }

    /**
     * Proses pencarian booking berdasarkan nomor booking.
     */
    public function check(Request $request)
    {
        $request->validate([
            'booking_number' => 'required|string',
        ]);

        $bookingNumber = $request->booking_number;

        // Cari booking hotel beserta relasi promo dan hotel
        $hotel = BookingHotel::with('promoCode', 'hotel')
                    ->where('booking_number', $bookingNumber)
                    ->first();

        if ($hotel) {
            return view('booking.check', [
                'bookingType' => 'hotel',
                'data' => $hotel,
            ]);
        }

        return back()->withErrors(['booking_number' => 'Nomor booking tidak ditemukan.']);
    }

    /**
     * Akses langsung ke detail booking via /booking/{booking_number}
     */
    public function show($booking_number)
    {
        $hotel = BookingHotel::with('promoCode', 'hotel')
                    ->where('booking_number', $booking_number)
                    ->first();

        if ($hotel) {
            return view('booking.check', [
                'bookingType' => 'hotel',
                'data' => $hotel,
            ]);
        }

        return redirect()->route('booking.checkForm')
                         ->withErrors(['booking_number' => 'Nomor booking tidak ditemukan.']);
    }

    /**
     * Kirim ulang email konfirmasi booking (swalayan, tanpa perlu login).
     */
    public function resendEmail($booking_number)
    {
        $booking = BookingHotel::with('hotel')->where('booking_number', $booking_number)->first();

        if (!$booking) {
            return redirect()->route('booking.checkForm')
                             ->withErrors(['booking_number' => 'Nomor booking tidak ditemukan.']);
        }

        Mail::to($booking->customer_email)->send(new HotelBookingMail($booking));

        return back()->with('success', 'Email konfirmasi telah dikirim ulang ke ' . $booking->customer_email . '.');
    }

    /**
     * Ajukan pembatalan booking secara swalayan dari halaman cek booking.
     * Diverifikasi dengan email pemesan (bukan sesi login) karena tamu tanpa akun juga bisa booking.
     */
    public function requestCancellation(Request $request, $booking_number)
    {
        $request->validate([
            'customer_email' => 'required|email',
            'reason' => 'required|string|max:500',
        ]);

        $booking = BookingHotel::where('booking_number', $booking_number)->first();

        if (!$booking || strcasecmp($booking->customer_email, $request->customer_email) !== 0) {
            return back()->withErrors(['customer_email' => 'Email tidak cocok dengan data booking ini.']);
        }

        if (!$booking->isCancellable()) {
            return back()->withErrors(['customer_email' => 'Booking ini tidak dapat dibatalkan.']);
        }

        $booking->update([
            'cancellation_status'       => 'requested',
            'cancellation_reason'       => $request->reason,
            'cancellation_requested_at' => now(),
        ]);

        return back()->with('success', 'Permintaan pembatalan berhasil dikirim. Tim kami akan memproses dalam 1-3 hari kerja.');
    }

    /**
     * Tampilkan form booking hotel beserta daftar promo.
     */
    public function bookHotel(Hotel $hotel)
{
    $promoCodes = Cache::remember('promos.valid', 600, fn() =>
        CodePromotion::active()
            ->where(fn($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
            ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
            ->get()
    );

$formattedPromoCodes = $promoCodes->mapWithKeys(function ($promo) {
    return [
        strtoupper($promo->code) => [
            'promo_code_id' => $promo->id,
            'type' => $promo->discount_percent > 0 ? 'percentage' : 'fixed',
            'amount' => $promo->discount_percent > 0 ? $promo->discount_percent : $promo->discount_amount
        ]
    ];
})->toArray();
    return view('booking.create', compact('hotel', 'promoCodes', 'formattedPromoCodes'));
}

}
