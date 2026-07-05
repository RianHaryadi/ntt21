<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function isConfigured(): bool
    {
        return !empty(config('services.midtrans.server_key')) && !empty(config('services.midtrans.client_key'));
    }

    /**
     * Ambil Snap token untuk sebuah transaksi. order_id Midtrans = booking_code
     * (unik per booking, aman dipakai ulang selama status transaksi masih pending).
     */
    public function getSnapToken(Transaction $transaction): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('MidtransService: server/client key belum diatur.');
            return null;
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $transaction->booking_code,
                    'gross_amount' => (int) round($transaction->total_price),
                ],
                'customer_details' => [
                    'first_name' => $transaction->customer_name,
                    'email' => $transaction->customer_email,
                    'phone' => $transaction->customer_phone,
                ],
                'item_details' => [[
                    'id' => $transaction->tour_package_id ?? $transaction->destination_id ?? 'item',
                    'price' => (int) round($transaction->total_price),
                    'quantity' => 1,
                    'name' => 'Booking ' . $transaction->booking_code,
                ]],
            ];

            $snapToken = Snap::getSnapToken($params);

            $transaction->update(['snap_token' => $snapToken]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('MidtransService::getSnapToken failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil Snap token untuk sebuah Order gabungan (keranjang lintas tipe).
     * order_id Midtrans = order_code (unik, aman dipakai ulang selama status masih pending).
     */
    public function getSnapTokenForOrder(Order $order): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('MidtransService: server/client key belum diatur.');
            return null;
        }

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_code,
                    'gross_amount' => (int) round($order->total_price),
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'item_details' => [[
                    'id' => 'order',
                    'price' => (int) round($order->total_price),
                    'quantity' => 1,
                    'name' => 'Pesanan ' . $order->order_code . ' (' . $order->itemCount() . ' item)',
                ]],
            ];

            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            Log::error('MidtransService::getSnapTokenForOrder failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verifikasi signature key notifikasi webhook Midtrans.
     * Dokumentasi: signature = sha512(order_id + status_code + gross_amount + server_key)
     */
    public function isValidSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . config('services.midtrans.server_key'));

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Mapping status transaksi Midtrans -> status internal aplikasi.
     */
    public function resolveInternalStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        return match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => Transaction::STATUS_PAID,
            $transactionStatus === 'settlement' => Transaction::STATUS_PAID,
            $transactionStatus === 'pending' => Transaction::STATUS_PENDING,
            in_array($transactionStatus, ['deny', 'cancel']) => Transaction::STATUS_CANCELLED,
            $transactionStatus === 'expire' => Transaction::STATUS_EXPIRED,
            default => Transaction::STATUS_PENDING,
        };
    }
}
