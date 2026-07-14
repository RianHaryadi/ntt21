<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use App\Models\Transaction;
use App\Models\TourBooking;
use App\Models\Destination;
use App\Models\CodePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PaketTourController extends Controller
{
    /** Diskon untuk paket yang menyertakan hotel (bundle), dalam persen. */
    private const BUNDLE_DISCOUNT_PERCENT = 10;

    /**
     * Menampilkan daftar semua tour packages dengan filter.
     */
    public function index(Request $request)
    {
        $destinations = Cache::remember('paket_tour.locations', 3600, fn() =>
            TourPackage::select('location')->distinct()->orderBy('location')->pluck('location')
        );
        $query = TourPackage::query();

        if ($request->filled('q')) {
            $searchTerm = '%' . $request->q . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('location', 'like', $searchTerm);
            });
        }

        if ($request->filled('destination')) {
            $query->where('location', $request->destination);
        }

        if ($request->filled('duration')) {
            switch ($request->duration) {
                case '1-3': $query->where('days', '<=', 3); break;
                case '4-7': $query->whereBetween('days', [4, 7]); break;
                case '8+':  $query->where('days', '>=', 8); break;
            }
        }

        if ($request->filled('price')) {
            switch ($request->price) {
                case 'under-1000000': $query->where('price', '<', 1000000); break;
                case '1-3': $query->whereBetween('price', [1000000, 3000000]); break;
                case '3-5': $query->whereBetween('price', [3000000, 5000000]); break;
                case '5+':  $query->where('price', '>=', 5000000); break;
            }
        }

        $query->withCount(['transactions as bookings_count' => function ($q) {
            $q->where('status', \App\Models\Transaction::STATUS_PAID);
        }]);

        $sort = $request->input('sort', 'popular');
        switch ($sort) {
            case 'price-asc': $query->orderBy('price', 'asc'); break;
            case 'price-desc': $query->orderBy('price', 'desc'); break;
            case 'duration': $query->orderBy('days', 'asc'); break;
            case 'rating': $query->orderBy('rating', 'desc'); break;
            case 'popular':
            default: $query->orderByDesc('bookings_count')->orderBy('created_at', 'desc'); break;
        }

        $paketTours = $query->paginate(12)->appends($request->query());
        return view('paket_tour.index', compact('paketTours', 'destinations'));
    }

    /**
     * Menampilkan detail satu tour package.
     */
    public function show($id)
    {
        $paketTour = TourPackage::findOrFail($id);
        return view('paket_tour.show', compact('paketTour'));
    }

    /**
     * Menampilkan halaman pemesanan.
     */
    public function create(TourPackage $tourPackage)
{
    $tourPackage->load('hotels');

    $bundleHotel = $tourPackage->includes_hotel ? $tourPackage->hotels->first() : null;
    $bundleHotelPricePerNight = 0;
    $bundleNights = max($tourPackage->days - 1, 1);

    if ($bundleHotel) {
        $roomPrices = array_filter([
            $bundleHotel->single_room_price,
            $bundleHotel->double_room_price,
            $bundleHotel->family_room_price,
        ], fn ($v) => $v > 0);
        $bundleHotelPricePerNight = !empty($roomPrices) ? min($roomPrices) : 0;
    }

    $destinations = Cache::remember('destinations.list', 3600, fn() =>
        Destination::orderBy('name')->pluck('name', 'id')
    );
    $promos = Cache::remember('promos.valid', 600, fn() =>
        CodePromotion::active()
            ->where(fn($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
            ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
            ->get()
    );

    $promoJson = $promos->mapWithKeys(function ($promo) {
        return [strtoupper($promo->code) => [
            'id' => $promo->id,
            'amount' => $promo->discount_amount,
            'percent' => $promo->discount_percent,
            'valid_from' => optional($promo->valid_from)->toDateString(),
            'valid_until' => optional($promo->valid_until)->toDateString(),
            'active' => $promo->active,
        ]];
    });

    $bundleDiscountPercent = self::BUNDLE_DISCOUNT_PERCENT;

    return view('paket_tour.create', compact(
        'tourPackage', 'destinations', 'promos', 'promoJson',
        'bundleHotel', 'bundleHotelPricePerNight', 'bundleNights', 'bundleDiscountPercent'
    ));
}


    /**
     * Menyimpan pemesanan dan transaksi tour package.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_package_id'    => 'required|exists:tour_packages,id',
            'destination_id'     => 'required|exists:destinations,id',
            'customer_phone'     => 'required|string|max:20',
            'booking_date'       => 'required|date|after_or_equal:today',
            'number_of_tickets'  => 'required|integer|min:1',
            'promo_code_id'      => 'nullable|integer',
            'discount_amount'    => 'nullable|numeric|min:0',
            'discount_percent'   => 'nullable|numeric|min:0|max:100',
            'payment_method'     => 'nullable|in:transfer,qris,cash',
            'special_request'    => 'nullable|string',
            'has_insurance'      => 'nullable|boolean',
        ]);

        // Nama & email selalu diambil dari akun yang login (field readonly di form).
        $user = auth()->user();
        $validated['customer_name'] = $user->name;
        $validated['customer_email'] = $user->email;

        if (!$user->phone) {
            $user->update(['phone' => $validated['customer_phone']]);
        }

        $tourPackage = TourPackage::with('hotels')->findOrFail($validated['tour_package_id']);
        $destination = Destination::findOrFail($validated['destination_id']);
        $tourSubtotal = $tourPackage->price * $validated['number_of_tickets'];
        $bookingCode = 'PKT-' . strtoupper(Str::random(10));

        // Bundle hotel: jika paket menyertakan hotel, hitung harga inap nyata
        // (bukan sekadar flag tampilan) dengan diskon bundel 10%.
        $bundleDiscountPercent = self::BUNDLE_DISCOUNT_PERCENT;
        $bundleHotel = $tourPackage->includes_hotel ? $tourPackage->hotels->first() : null;
        $hotelPrice = 0;

        if ($bundleHotel) {
            $roomPrices = array_filter([
                $bundleHotel->single_room_price,
                $bundleHotel->double_room_price,
                $bundleHotel->family_room_price,
            ], fn ($v) => $v > 0);

            if (!empty($roomPrices)) {
                $pricePerNight = min($roomPrices);
                $nights = max($tourPackage->days - 1, 1);
                $hotelSubtotal = $pricePerNight * $nights * $validated['number_of_tickets'];
                $hotelPrice = $hotelSubtotal * (1 - $bundleDiscountPercent / 100);
            }
        }

        $subtotal = $tourSubtotal + $hotelPrice;

        // Validasi kode promo
        $promo = null;
        if ($validated['promo_code_id'] ?? false) {
            $promo = CodePromotion::active()
                ->where('id', $validated['promo_code_id'])
                ->whereDate('valid_from', '<=', now())
                ->whereDate('valid_until', '>=', now())
                ->where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', auth()->id()))
                ->first();
        }

        // Hitung diskon
        $discount = 0;
        if ($promo) {
            $discount = $promo->discount_amount ?: ($promo->discount_percent ? $subtotal * $promo->discount_percent / 100 : 0);
        } elseif ($validated['discount_amount'] ?? false) {
            $discount = $validated['discount_amount'];
        } elseif ($validated['discount_percent'] ?? false) {
            $discount = $subtotal * ($validated['discount_percent'] / 100);
        }

        // Asuransi perjalanan (opsional) — dihitung di server berdasarkan jumlah tiket
        $hasInsurance = (bool) ($validated['has_insurance'] ?? false);
        $insuranceAmount = $hasInsurance
            ? config('services.insurance.price_per_ticket') * $validated['number_of_tickets']
            : 0;

        $totalPrice = max($subtotal - $discount, 0) + $insuranceAmount;

        DB::transaction(function () use ($validated, $tourPackage, $destination, $bookingCode, $tourSubtotal, $hotelPrice, $discount, $totalPrice, $promo, $hasInsurance, $insuranceAmount) {
    Transaction::create([
        'booking_code'       => $bookingCode,
        'user_id'            => auth()->id(),
        'tour_package_id'    => $tourPackage->id,
        'destination_id'     => $destination->id,
        'customer_name'      => $validated['customer_name'],
        'customer_email'     => $validated['customer_email'],
        'customer_phone'     => $validated['customer_phone'],
        'booking_date'       => $validated['booking_date'],
        'number_of_tickets'  => $validated['number_of_tickets'],
        'package_price'      => $tourPackage->price,
        'discount_amount'    => $discount,
        'has_insurance'      => $hasInsurance,
        'insurance_amount'   => $insuranceAmount,
        'total_price'        => $totalPrice,
        'status'             => Transaction::STATUS_PENDING,
        'payment_method'     => $validated['payment_method'] ?? null, // Allow NULL instead of 'pending'
        'promo_code_id'      => $promo?->id,
    ]);

    TourBooking::create([
        'tour_package_id'    => $tourPackage->id,
        'destination_id'     => $destination->id,
        'hotel_id'           => $tourPackage->includes_hotel ? optional($tourPackage->hotels->first())->id : null,
        'customer_name'      => $validated['customer_name'],
        'customer_email'     => $validated['customer_email'],
        'customer_phone'     => $validated['customer_phone'],
        'tour_price'         => $tourSubtotal,
        'hotel_price'        => round($hotelPrice, 2),
        'total_price'        => $totalPrice,
        'status'             => 'pending',
        'payment_method'     => $validated['payment_method'] ?? null, // Allow NULL instead of 'pending'
        'booking_number'     => $bookingCode,
    ]);
});

        return redirect()->route('transaction.payment', $bookingCode)
                         ->with('success', 'Pemesanan berhasil! Silakan lanjutkan pembayaran.');
    }

    /**
     * Mengubah hotel untuk tour package (admin).
     */
    public function updateHotel(Request $request, $id)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
        ]);

        $tourPackage = TourPackage::findOrFail($id);
        $tourPackage->hotels()->sync([$validated['hotel_id']]);

        return redirect()->route('paket-tours.show', $id)->with('success', 'Hotel updated successfully.');
    }
}