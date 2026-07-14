<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Jobs\SendWhatsAppMessage;
use App\Mail\HotelBookingMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BookingHotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hotel_id',
        'hotel_room_id',
        'room_type',
        'customer_name',
        'customer_email',
        'customer_phone',
        'check_in_date',
        'check_out_date',
        'night_count',
        'room_price',
        'tax',
        'service_charge',
        'total_price',
        'discount_percent',
        'discount_amount',
        'promo_code_id',
        'promo_code',
        'special_requests',
        'status',
        'payment_method',
        'booking_number',
        'cancellation_status',
        'cancellation_reason',
        'cancellation_requested_at',
        'cancellation_processed_at',
        'has_insurance',
        'insurance_amount',
        'order_id',
    ];


    protected $casts = [
        'check_in_date'                => 'date',
        'check_out_date'               => 'date',
        'cancellation_requested_at'    => 'datetime',
        'cancellation_processed_at'    => 'datetime',
        'has_insurance'                => 'boolean',
        'insurance_amount'             => 'float',
    ];

    public function isCancellable(): bool
    {
        return $this->status === 'checked-in'
            && is_null($this->cancellation_status);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function hotelRoom()
    {
        return $this->belongsTo(HotelRoom::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(CodePromotion::class, 'promo_code_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getHotelNameAttribute()
    {
        return $this->hotel->name ?? 'Unknown';
    }

    public function getNightCountAttribute()
    {
        if ($this->check_in_date && $this->check_out_date) {
            return $this->check_in_date->diffInDays($this->check_out_date);
        }
        return 0;
    }

    public function getRoomPriceAttribute($value)
    {
        if ($value !== null) return $value;

        if ($this->hotel) {
            return match ($this->room_type) {
                'single' => $this->hotel->single_room_price,
                'double' => $this->hotel->double_room_price,
                'family' => $this->hotel->family_room_price,
                default => 0
            };
        }

        return 0;
    }

    public function calculateTotalPrice()
    {
        $nights = $this->night_count;
        $basePrice = $this->room_price * $nights;
        $tax = $basePrice * 0.10;
        $service = $basePrice * 0.05;

        $discount = 0;
        if ($this->promoCode) {
            $discount = $this->promoCode->discount_type === 'percentage'
                ? $basePrice * ($this->promoCode->discount_amount / 100)
                : $this->promoCode->discount_amount;
        }

        $this->tax = $tax;
        $this->service_charge = $service;
        $this->discount_amount = $discount;
        $this->total_price = max($basePrice + $tax + $service - $discount, 0);

        return $this->total_price;
    }

    public function updateBookingStatusToCheckout()
    {
        if ($this->hotelRoom && $this->hotelRoom->status == 'available') {
            $this->update(['status' => 'checked-out']);
        }
    }

    protected static function booted()
{
    static::creating(function ($booking) {
        if (empty($booking->booking_number)) {
            $booking->booking_number = 'BOOK-' . Carbon::now()->format('YmdHis') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        }
    });

    static::created(function ($booking) {
        // Kirim email jika metode pembayaran transfer atau qris
        try {
            if (in_array($booking->payment_method, ['transfer', 'qris']) && $booking->customer_email) {
                Mail::to($booking->customer_email)->send(new HotelBookingMail($booking));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send hotel booking email: ' . $e->getMessage());
        }

        // Kirim notifikasi WhatsApp
        try {
            if ($booking->customer_phone) {
                $hotelName = $booking->hotel->name ?? 'Hotel';
                $checkIn = $booking->check_in_date?->format('d M Y');
                $checkOut = $booking->check_out_date?->format('d M Y');
                $total = 'Rp' . number_format($booking->total_price, 0, ',', '.');

                $message = "Halo {$booking->customer_name}! 👋\n\n"
                    . "Booking Anda di *Pesona NTT* berhasil dibuat.\n\n"
                    . "🏨 Hotel: {$hotelName}\n"
                    . "📋 No. Booking: {$booking->booking_number}\n"
                    . "📅 Check-in: {$checkIn}\n"
                    . "📅 Check-out: {$checkOut}\n"
                    . "💰 Total: {$total}\n\n"
                    . "Status booking Anda saat ini: *menunggu konfirmasi*. Kami akan segera memprosesnya. Terima kasih! 🙏";

                SendWhatsAppMessage::dispatch($booking->customer_phone, $message);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send hotel booking WhatsApp: ' . $e->getMessage());
        }
    });
}
}
