<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'description',
        'facilities',
        'location',
        'single_room_price',
        'double_room_price',
        'family_room_price',
        'room_count_single',
        'room_count_double',
        'room_count_family',
        'image',
        'flash_sale_discount_percent',
        'flash_sale_ends_at',
    ];

    protected $casts = [
        'flash_sale_ends_at' => 'datetime',
    ];

    public function isOnFlashSale(): bool
    {
        return $this->flash_sale_discount_percent
            && $this->flash_sale_ends_at
            && $this->flash_sale_ends_at->isFuture();
    }

    public function flashSalePrice(?float $basePrice): ?float
    {
        if (!$basePrice || !$this->isOnFlashSale()) {
            return null;
        }

        return round($basePrice - ($basePrice * $this->flash_sale_discount_percent / 100));
    }

    public function scopeOnFlashSale($query)
    {
        return $query->whereNotNull('flash_sale_discount_percent')
            ->where('flash_sale_ends_at', '>', now());
    }

    /**
     * Relasi ke tabel hotel_rooms
     */
    public function hotelRooms()
    {
        return $this->hasMany(HotelRoom::class);
    }

    /**
     * Alias relasi rooms (opsional, sama dengan hotelRooms)
     */
    public function rooms()
    {
        return $this->hasMany(HotelRoom::class);
    }

    /**
     * Total kamar (inventaris tetap) untuk tipe kamar tertentu.
     */
    public function totalRoomsOfType(string $roomType): int
    {
        return (int) match ($roomType) {
            'single' => $this->room_count_single,
            'double' => $this->room_count_double,
            'family' => $this->room_count_family,
            default => 0,
        };
    }

    /**
     * Jumlah kamar tersedia untuk tipe & rentang tanggal tertentu.
     * Dihitung dari inventaris tetap dikurangi booking aktif yang tanggalnya bentrok
     * (status pending/checked-in dianggap masih menempati kamar).
     *
     * @param int|null $excludeBookingId Kecualikan booking ini dari hitungan (dipakai saat re-cek booking yang sudah ada)
     */
    public function availableRooms(string $roomType, \DateTimeInterface|string $checkIn, \DateTimeInterface|string $checkOut, ?int $excludeBookingId = null): int
    {
        $total = $this->totalRoomsOfType($roomType);
        $checkIn = $checkIn instanceof \DateTimeInterface ? $checkIn->format('Y-m-d') : $checkIn;
        $checkOut = $checkOut instanceof \DateTimeInterface ? $checkOut->format('Y-m-d') : $checkOut;

        $overlapping = $this->bookings()
            ->where('room_type', $roomType)
            ->whereIn('status', ['pending', 'checked-in'])
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->when($excludeBookingId, fn ($q) => $q->where('id', '!=', $excludeBookingId))
            ->count();

        return max($total - $overlapping, 0);
    }

    /**
     * Apakah tipe kamar ini tersedia untuk rentang tanggal yang diminta.
     */
    public function isRoomAvailable(string $roomType, \DateTimeInterface|string $checkIn, \DateTimeInterface|string $checkOut, ?int $excludeBookingId = null): bool
    {
        return $this->availableRooms($roomType, $checkIn, $checkOut, $excludeBookingId) > 0;
    }

    /**
     * Relasi ke seluruh booking hotel ini.
     */
    public function bookings()
    {
        return $this->hasMany(BookingHotel::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable')->latest();
    }

    public function averageRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }
}
