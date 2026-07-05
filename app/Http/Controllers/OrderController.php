<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;

class OrderController extends Controller
{
    public function __construct(private MidtransService $midtrans)
    {
    }

    /**
     * Halaman pembayaran untuk Order gabungan (hasil checkout keranjang).
     */
    public function payment($order_code)
    {
        $order = Order::where('order_code', $order_code)->firstOrFail();

        $snapToken = null;
        if ($order->status === Order::STATUS_PENDING) {
            $snapToken = $order->payment_gateway_token ?: $this->midtrans->getSnapTokenForOrder($order);
        }

        return view('orders.payment', [
            'order' => $order,
            'snapToken' => $snapToken,
            'midtransConfigured' => $this->midtrans->isConfigured(),
            'clientKey' => config('services.midtrans.client_key'),
            'isProduction' => (bool) config('services.midtrans.is_production'),
        ]);
    }

    /**
     * Halaman sukses setelah pembayaran Order gabungan.
     */
    public function success($order_code)
    {
        $order = Order::with(['transactions.destinationDirect', 'transactions.tourPackage', 'bookingHotels.hotel'])
            ->where('order_code', $order_code)
            ->firstOrFail();

        return view('orders.success', compact('order'));
    }
}
