<?php

namespace App\Http\Controllers;

use App\Models\BookingHotel;
use App\Models\CodePromotion;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\TourPackage;
use App\Services\CartService;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(CartService $cart)
    {
        $items = $cart->items();
        $subtotal = $items->sum(fn ($item) => $item->subtotal());

        return view('cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request, CartService $cart)
    {
        $request->validate([
            'itemable_type' => 'required|in:destination,hotel,tour',
            'itemable_id' => 'required|integer',
        ]);

        $typeMap = [
            'destination' => Destination::class,
            'hotel' => Hotel::class,
            'tour' => TourPackage::class,
        ];

        if ($request->itemable_type === 'destination') {
            $request->validate([
                'booking_date' => 'required|date|after_or_equal:today',
                'number_of_tickets' => 'required|integer|min:1|max:50',
            ]);

            $destination = Destination::findOrFail($request->itemable_id);
            $unitPrice = $destination->isOnFlashSale() ? $destination->flash_sale_price : $destination->price;

            $details = [
                'label' => $destination->name,
                'booking_date' => $request->booking_date,
                'quantity' => (int) $request->number_of_tickets,
                'unit_price' => (float) $unitPrice,
            ];
        } elseif ($request->itemable_type === 'tour') {
            $request->validate([
                'booking_date' => 'required|date|after_or_equal:today',
                'number_of_tickets' => 'required|integer|min:1|max:50',
            ]);

            $tour = TourPackage::findOrFail($request->itemable_id);

            $details = [
                'label' => $tour->name,
                'booking_date' => $request->booking_date,
                'quantity' => (int) $request->number_of_tickets,
                'unit_price' => (float) $tour->price,
            ];
        } else {
            $request->validate([
                'room_type' => 'required|in:single,double,family',
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
            ]);

            $hotel = Hotel::findOrFail($request->itemable_id);
            $checkIn = Carbon::parse($request->check_in_date);
            $checkOut = Carbon::parse($request->check_out_date);

            if (!$hotel->isRoomAvailable($request->room_type, $checkIn, $checkOut)) {
                return back()->withErrors(['itemable_id' => 'Kamar tidak tersedia untuk tanggal yang dipilih.'])->withInput();
            }

            $priceKey = $request->room_type . '_room_price';
            $basePrice = $hotel->$priceKey ?? 0;
            $unitPrice = $hotel->flashSalePrice($basePrice) ?? $basePrice;
            $nights = $checkIn->diffInDays($checkOut);

            $details = [
                'label' => $hotel->name,
                'room_type' => $request->room_type,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'quantity' => $nights,
                'unit_price' => (float) $unitPrice,
            ];
        }

        $cart->add($typeMap[$request->itemable_type], (int) $request->itemable_id, $details);

        return redirect()->route('cart.index')->with('success', 'Item ditambahkan ke keranjang.');
    }

    public function remove($id, CartService $cart)
    {
        $cart->remove((int) $id);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function checkout(Request $request, CartService $cart, MidtransService $midtrans)
    {
        $request->validate([
            'customer_phone' => 'required|string|max:20',
            'promo_code' => 'nullable|string|max:50',
            'has_insurance' => 'nullable|boolean',
        ]);

        // Nama & email selalu diambil dari akun yang login, bukan input klien,
        // karena field itu ditampilkan readonly di form (identitas akun).
        $user = auth()->user();
        $request->merge([
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ]);

        if (!$user->phone) {
            $user->update(['phone' => $request->customer_phone]);
        }

        $items = $cart->items();

        if ($items->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang Anda kosong.']);
        }

        try {
            $order = DB::transaction(function () use ($request, $items) {
                return $this->buildOrderFromCart($request, $items);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        $cart->clear();

        $snapToken = $midtrans->getSnapTokenForOrder($order);
        if ($snapToken) {
            $order->update(['payment_gateway_token' => $snapToken]);
        }

        return redirect()->route('orders.payment', $order->order_code);
    }

    private function buildOrderFromCart(Request $request, $items): Order
    {
        $subtotal = 0;
        $prepared = [];

        foreach ($items as $cartItem) {
            $type = $cartItem->itemType();
            $details = $cartItem->details;

            if ($type === 'destination') {
                $destination = Destination::findOrFail($cartItem->itemable_id);
                $unitPrice = $destination->isOnFlashSale() ? $destination->flash_sale_price : $destination->price;
                $qty = (int) $details['quantity'];
                $total = $unitPrice * $qty;
                $subtotal += $total;

                $prepared[] = [
                    'type' => 'destination', 'model' => $destination, 'qty' => $qty,
                    'unit_price' => $unitPrice, 'total' => $total,
                    'booking_date' => $details['booking_date'],
                ];
            } elseif ($type === 'tour') {
                $tour = TourPackage::findOrFail($cartItem->itemable_id);
                $unitPrice = (float) $tour->price;
                $qty = (int) $details['quantity'];
                $total = $unitPrice * $qty;
                $subtotal += $total;

                $prepared[] = [
                    'type' => 'tour', 'model' => $tour, 'qty' => $qty,
                    'unit_price' => $unitPrice, 'total' => $total,
                    'booking_date' => $details['booking_date'],
                ];
            } else {
                $hotel = Hotel::where('id', $cartItem->itemable_id)->lockForUpdate()->first();
                $roomType = $details['room_type'];
                $checkIn = Carbon::parse($details['check_in_date']);
                $checkOut = Carbon::parse($details['check_out_date']);

                if (!$hotel || !$hotel->isRoomAvailable($roomType, $checkIn, $checkOut)) {
                    throw new \RuntimeException("Maaf, kamar di {$details['label']} sudah tidak tersedia untuk tanggal yang dipilih.");
                }

                $nights = $checkIn->diffInDays($checkOut);
                $priceKey = $roomType . '_room_price';
                $basePrice = $hotel->$priceKey ?? 0;
                $roomPrice = $hotel->flashSalePrice($basePrice) ?? $basePrice;
                $base = $roomPrice * $nights;
                $tax = $base * 0.10;
                $service = $base * 0.05;
                $total = $base + $tax + $service;
                $subtotal += $total;

                $prepared[] = [
                    'type' => 'hotel', 'model' => $hotel, 'room_type' => $roomType,
                    'check_in' => $checkIn, 'check_out' => $checkOut, 'nights' => $nights,
                    'room_price' => $roomPrice, 'tax' => $tax, 'service' => $service, 'total' => $total,
                ];
            }
        }

        $discount = 0;
        $promo = null;

        if ($request->filled('promo_code')) {
            $promo = CodePromotion::where('code', strtoupper($request->promo_code))
                ->where('active', true)
                ->whereDate('valid_from', '<=', now())
                ->whereDate('valid_until', '>=', now())
                ->where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', auth()->id()))
                ->first();

            if ($promo) {
                $discount = $promo->discount_percent
                    ? ($subtotal * $promo->discount_percent / 100)
                    : ($promo->discount_amount ?? 0);
            }
        }

        $hasInsurance = (bool) $request->boolean('has_insurance');
        $insuranceAmount = 0;

        if ($hasInsurance) {
            foreach ($prepared as $item) {
                $insuranceAmount += in_array($item['type'], ['destination', 'tour'], true)
                    ? config('services.insurance.price_per_ticket') * $item['qty']
                    : config('services.insurance.price_per_booking');
            }
        }

        $total = max($subtotal - $discount, 0) + $insuranceAmount;

        $order = Order::create([
            'order_code' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'insurance_amount' => $insuranceAmount,
            'total_price' => $total,
            'has_insurance' => $hasInsurance,
            'promo_code_id' => $promo?->id,
            'status' => Order::STATUS_PENDING,
        ]);

        foreach ($prepared as $item) {
            if ($item['type'] === 'destination') {
                Transaction::create([
                    'order_id' => $order->id,
                    'booking_code' => 'DST-' . strtoupper(Str::random(10)),
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'destination_id' => $item['model']->id,
                    'booking_date' => $item['booking_date'],
                    'number_of_tickets' => $item['qty'],
                    'package_price' => $item['unit_price'],
                    'total_price' => $item['total'],
                    'status' => Transaction::STATUS_PENDING,
                ]);
            } elseif ($item['type'] === 'tour') {
                Transaction::create([
                    'order_id' => $order->id,
                    'booking_code' => 'PKT-' . strtoupper(Str::random(10)),
                    'user_id' => auth()->id(),
                    'tour_package_id' => $item['model']->id,
                    'destination_id' => $item['model']->destination_id,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'booking_date' => $item['booking_date'],
                    'number_of_tickets' => $item['qty'],
                    'package_price' => $item['unit_price'],
                    'total_price' => $item['total'],
                    'status' => Transaction::STATUS_PENDING,
                ]);
            } else {
                BookingHotel::create([
                    'order_id' => $order->id,
                    'user_id' => auth()->id(),
                    'hotel_id' => $item['model']->id,
                    'room_type' => $item['room_type'],
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'check_in_date' => $item['check_in'],
                    'check_out_date' => $item['check_out'],
                    'night_count' => $item['nights'],
                    'room_price' => round($item['room_price'], 2),
                    'tax' => round($item['tax'], 2),
                    'service_charge' => round($item['service'], 2),
                    'discount_amount' => 0,
                    'total_price' => round($item['total'], 2),
                    'status' => 'pending',
                    'booking_number' => 'BOOK-' . now()->format('YmdHis') . '-' . str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT),
                ]);
            }
        }

        return $order;
    }
}
