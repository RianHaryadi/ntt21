<?php

namespace App\Http\Controllers;

use App\Models\BookingHotel;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TourBooking;
use App\Jobs\SendWhatsAppMessage;
use App\Services\LoyaltyService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
        private LoyaltyService $loyalty,
    ) {}

    /**
     * Menampilkan halaman pembayaran berdasarkan booking_code.
     * Mengambil Snap token dari Midtrans agar widget pembayaran bisa dibuka.
     */
    public function payment($booking_code)
    {
        $transaction = Transaction::where('booking_code', $booking_code)->firstOrFail();

        $snapToken = null;
        if ($transaction->status === Transaction::STATUS_PENDING) {
            $snapToken = $transaction->snap_token ?: $this->midtrans->getSnapToken($transaction);
        }

        return view('transaction.payment', [
            'transaction' => $transaction,
            'snapToken' => $snapToken,
            'midtransConfigured' => $this->midtrans->isConfigured(),
            'clientKey' => config('services.midtrans.client_key'),
            'isProduction' => (bool) config('services.midtrans.is_production'),
        ]);
    }

    /**
     * Webhook notifikasi status pembayaran dari server Midtrans.
     * Ini adalah satu-satunya sumber kebenaran untuk status "paid" — bukan aksi user di browser.
     */
    public function notification(Request $request)
    {
        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');
        $transactionStatus = (string) $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');
        $paymentType = $request->input('payment_type');

        if (!$this->midtrans->isValidSignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans notification: invalid signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $newStatus = $this->midtrans->resolveInternalStatus($transactionStatus, $fraudStatus);
        $paymentMethod = $this->mapPaymentMethod($paymentType);

        // Cart checkout: order_id Midtrans = order_code gabungan lintas tipe
        $order = Order::where('order_code', $orderId)->first();
        if ($order) {
            $this->applyOrderStatus($order, $newStatus, $paymentMethod, $paymentType);

            Log::info('Midtrans notification processed (order)', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'resolved_status' => $newStatus,
            ]);

            return response()->json(['message' => 'OK']);
        }

        // Alur lama: satu Transaction berdiri sendiri (booking langsung, bukan dari keranjang)
        $transaction = Transaction::where('booking_code', $orderId)->first();
        if (!$transaction) {
            Log::warning('Midtrans notification: transaction not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $this->applyTransactionStatus($transaction, $newStatus, $paymentMethod, $paymentType);

        Log::info('Midtrans notification processed', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'resolved_status' => $newStatus,
        ]);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Terapkan status pembayaran baru ke satu Transaction berdiri sendiri, plus efek samping
     * (sinkron TourBooking, WhatsApp, poin loyalti) — dipakai baik oleh alur booking langsung
     * maupun sebagai bagian dari cascade Order gabungan.
     */
    private function applyTransactionStatus(Transaction $transaction, string $newStatus, ?string $paymentMethod, ?string $paymentType): void
    {
        $wasAlreadyPaid = $transaction->status === Transaction::STATUS_PAID;

        $transaction->update([
            'status' => $newStatus,
            'payment_method' => $paymentMethod,
            'midtrans_payment_type' => $paymentType,
        ]);

        if ($newStatus !== Transaction::STATUS_PAID) {
            return;
        }

        $booking = TourBooking::where('booking_number', $transaction->booking_code)->first();
        if ($booking) {
            $booking->update([
                'status' => 'confirmed',
                'payment_method' => $paymentMethod,
            ]);
        }

        // Kirim notifikasi WhatsApp hanya sekali (Midtrans bisa retry notifikasi berkali-kali)
        if (!$wasAlreadyPaid && $transaction->customer_phone) {
            $total = 'Rp' . number_format($transaction->total_price, 0, ',', '.');
            $message = "Halo {$transaction->customer_name}! 🎉\n\n"
                . "Pembayaran Anda untuk booking *{$transaction->booking_code}* di Pesona NTT telah *berhasil dikonfirmasi*.\n\n"
                . "💰 Total dibayar: {$total}\n\n"
                . "Tiket/detail perjalanan akan segera dikirim ke email Anda. Selamat berlibur! 🌴";

            SendWhatsAppMessage::dispatch($transaction->customer_phone, $message);
        }

        $this->loyalty->awardForTransaction($transaction);
    }

    /**
     * Terapkan status pembayaran baru ke satu BookingHotel yang tergabung dalam Order.
     * Saat pembayaran berhasil (paid): status menjadi 'confirmed' (sudah bayar, menunggu check-in fisik).
     * Admin yang akan mengubah ke 'checked_in' lewat panel Filament saat tamu tiba.
     */
    private function applyBookingHotelStatus(BookingHotel $booking, string $newStatus, ?string $paymentMethod): void
    {
        $statusMap = [
            Transaction::STATUS_PAID      => 'confirmed',   // bayar lunas → menunggu check-in
            Transaction::STATUS_CANCELLED => 'cancelled',
            Transaction::STATUS_EXPIRED   => 'cancelled',
        ];

        $mappedStatus = $statusMap[$newStatus] ?? null;

        $booking->update([
            'status'         => $mappedStatus ?? $booking->status,
            'payment_method' => $paymentMethod,
        ]);

        if ($newStatus === Transaction::STATUS_PAID) {
            $this->loyalty->awardForHotelBooking($booking);
        }
    }

    /**
     * Terapkan status pembayaran Order gabungan ke Order itu sendiri, lalu cascade
     * ke setiap Transaction/BookingHotel anaknya.
     */
    private function applyOrderStatus(Order $order, string $newStatus, ?string $paymentMethod, ?string $paymentType): void
    {
        $order->update([
            'status' => $newStatus,
            'payment_method' => $paymentMethod,
        ]);

        foreach ($order->transactions as $transaction) {
            $this->applyTransactionStatus($transaction, $newStatus, $paymentMethod, $paymentType);
        }

        foreach ($order->bookingHotels as $booking) {
            $this->applyBookingHotelStatus($booking, $newStatus, $paymentMethod);
        }
    }

    private function mapPaymentMethod(?string $paymentType): ?string
    {
        return match ($paymentType) {
            null => null,
            'bank_transfer', 'echannel', 'permata', 'bca_va', 'bni_va', 'bri_va' => 'transfer',
            'qris', 'gopay', 'shopeepay' => 'qris',
            // Tipe lain (credit_card, dst.) dipetakan ke 'transfer' agar muat di enum kolom;
            // tipe mentahnya tetap tersimpan di kolom midtrans_payment_type.
            default => 'transfer',
        };
    }

    /**
     * Menampilkan halaman sukses setelah pembayaran.
     */
    public function success($booking_code)
    {
        $transaction = Transaction::where('booking_code', $booking_code)->firstOrFail();
        return view('transaction.success', compact('transaction'));
    }

    /**
     * Generate and download the official PDF e-ticket for a transaction.
     */
    public function downloadTicket($booking_code)
    {
        $transaction = Transaction::with(['tickets', 'destinationDirect', 'tourPackage.destination'])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.transaction-ticket', compact('transaction'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('E-Ticket-' . $transaction->booking_code . '.pdf');
    }
}
