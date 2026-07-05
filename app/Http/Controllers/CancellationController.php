<?php

namespace App\Http\Controllers;

use App\Models\BookingHotel;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CancellationController extends Controller
{
    public function request(Request $request, int $id)
    {
        $booking = BookingHotel::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$booking->isCancellable()) {
            return back()->with('error', 'Booking ini tidak dapat dibatalkan.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'cancellation_status'       => 'requested',
            'cancellation_reason'       => $request->reason,
            'cancellation_requested_at' => now(),
        ]);

        return back()->with('success', 'Permintaan pembatalan berhasil dikirim. Tim kami akan memproses dalam 1-3 hari kerja.');
    }

    public function approve(int $id)
    {
        $booking = BookingHotel::where('cancellation_status', 'requested')->findOrFail($id);

        $booking->update([
            'cancellation_status'        => 'approved',
            'cancellation_processed_at'  => now(),
            'status'                     => 'canceled',
        ]);

        // Kembalikan kamar
        if ($booking->hotel) {
            $field = match ($booking->room_type) {
                'single' => 'room_count_single',
                'double' => 'room_count_double',
                'family' => 'room_count_family',
                default  => null,
            };
            if ($field) $booking->hotel->increment($field);
        }

        // Notifikasi ke user
        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Pembatalan Booking Disetujui',
            'body'    => "Pembatalan booking #{$booking->booking_number} telah disetujui. Refund akan diproses dalam 3-5 hari kerja.",
            'type'    => 'success',
            'link'    => route('dashboard'),
        ]);

        return back()->with('success', 'Pembatalan disetujui dan notifikasi dikirim ke user.');
    }

    public function reject(Request $request, int $id)
    {
        $booking = BookingHotel::where('cancellation_status', 'requested')->findOrFail($id);

        $booking->update([
            'cancellation_status'       => 'rejected',
            'cancellation_processed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Permintaan Pembatalan Ditolak',
            'body'    => "Permintaan pembatalan booking #{$booking->booking_number} ditolak. Hubungi CS kami untuk informasi lebih lanjut.",
            'type'    => 'warning',
            'link'    => route('dashboard'),
        ]);

        return back()->with('success', 'Pembatalan ditolak dan notifikasi dikirim ke user.');
    }
}
