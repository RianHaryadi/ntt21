<?php

namespace App\Services;

use App\Models\BookingHotel;
use App\Models\CodePromotion;
use App\Models\LoyaltyPoint;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;
use RuntimeException;

class LoyaltyService
{
    private const POINTS_PER_RUPIAH = 10000; // 1 poin per Rp10.000 belanja
    private const REFERRAL_BONUS = 500;

    /** Opsi penukaran poin: [poin => diskon rupiah]. */
    public const REDEMPTION_OPTIONS = [
        500 => 50000,
        1000 => 120000,
        2000 => 300000,
    ];

    /**
     * Ambang batas tier berdasarkan poin yang PERNAH diperoleh seumur hidup
     * (bukan saldo poin saat ini) — agar tier tidak turun saat poin ditukar voucher.
     */
    public const TIERS = [
        'Bronze' => 0,
        'Silver' => 1000,
        'Gold' => 3000,
        'Platinum' => 7000,
    ];

    /** Tentukan tier berdasarkan total poin yang pernah diperoleh. */
    public function tierFor(int $lifetimePoints): string
    {
        $tier = 'Bronze';

        foreach (self::TIERS as $name => $threshold) {
            if ($lifetimePoints >= $threshold) {
                $tier = $name;
            }
        }

        return $tier;
    }

    /** Info tier berikutnya: nama & sisa poin yang dibutuhkan. Null jika sudah di tier tertinggi. */
    public function nextTierInfo(int $lifetimePoints): ?array
    {
        foreach (self::TIERS as $name => $threshold) {
            if ($lifetimePoints < $threshold) {
                return [
                    'name' => $name,
                    'threshold' => $threshold,
                    'points_needed' => $threshold - $lifetimePoints,
                ];
            }
        }

        return null;
    }

    public function awardForHotelBooking(BookingHotel $booking): void
    {
        if (!$booking->user_id) {
            return;
        }

        $this->award(
            user: $booking->user,
            points: $this->calculatePoints($booking->total_price),
            type: 'hotel_booking',
            description: "Booking hotel #{$booking->booking_number}",
            sourceType: BookingHotel::class,
            sourceId: $booking->id,
        );

        $this->maybeAwardReferralBonus($booking->user);
    }

    public function awardForTransaction(Transaction $transaction): void
    {
        if (!$transaction->user_id) {
            return;
        }

        $this->award(
            user: $transaction->user,
            points: $this->calculatePoints($transaction->total_price),
            type: 'tour_booking',
            description: "Booking #{$transaction->booking_code}",
            sourceType: Transaction::class,
            sourceId: $transaction->id,
        );

        $this->maybeAwardReferralBonus($transaction->user);
    }

    /**
     * Tukar poin milik user menjadi kode promo personal.
     * @throws RuntimeException jika opsi tidak valid atau poin tidak cukup.
     */
    public function redeem(User $user, int $pointsToSpend): CodePromotion
    {
        if (!array_key_exists($pointsToSpend, self::REDEMPTION_OPTIONS)) {
            throw new RuntimeException('Pilihan penukaran tidak valid.');
        }

        if ($user->totalPoints() < $pointsToSpend) {
            throw new RuntimeException('Poin Anda tidak mencukupi untuk penukaran ini.');
        }

        $discountAmount = self::REDEMPTION_OPTIONS[$pointsToSpend];

        $promo = CodePromotion::create([
            'user_id' => $user->id,
            'code' => 'RDM-' . strtoupper(Str::random(8)),
            'description' => "Voucher hasil penukaran {$pointsToSpend} poin",
            'discount_amount' => $discountAmount,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(60)->toDateString(),
            'active' => true,
        ]);

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => -$pointsToSpend,
            'type' => 'redemption',
            'description' => "Tukar {$pointsToSpend} poin -> voucher {$promo->code}",
            'source_type' => CodePromotion::class,
            'source_id' => $promo->id,
        ]);

        return $promo;
    }

    private function calculatePoints(float $amount): int
    {
        return (int) floor($amount / self::POINTS_PER_RUPIAH);
    }

    private function award(User $user, int $points, string $type, string $description, string $sourceType, int $sourceId): void
    {
        if ($points <= 0) {
            return;
        }

        // Hindari poin ganda untuk booking yang sama (mis. webhook Midtrans retry)
        $alreadyAwarded = LoyaltyPoint::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('type', $type)
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        LoyaltyPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => $type,
            'description' => $description,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);
    }

    /**
     * Beri bonus referral ke pereferensi, hanya sekali,
     * dipicu saat orang yang direferensikan menyelesaikan booking terkonfirmasi PERTAMA-nya.
     */
    private function maybeAwardReferralBonus(User $referredUser): void
    {
        if (!$referredUser->referred_by_id) {
            return;
        }

        // Hanya untuk booking terkonfirmasi pertama milik user ini
        $confirmedBookingCount = LoyaltyPoint::where('user_id', $referredUser->id)
            ->whereIn('type', ['hotel_booking', 'tour_booking'])
            ->count();

        if ($confirmedBookingCount !== 1) {
            return;
        }

        $alreadyRewarded = LoyaltyPoint::where('type', 'referral_bonus')
            ->where('source_type', User::class)
            ->where('source_id', $referredUser->id)
            ->exists();

        if ($alreadyRewarded) {
            return;
        }

        $referrer = $referredUser->referredBy;
        if (!$referrer) {
            return;
        }

        LoyaltyPoint::create([
            'user_id' => $referrer->id,
            'points' => self::REFERRAL_BONUS,
            'type' => 'referral_bonus',
            'description' => "Bonus referral: {$referredUser->name} menyelesaikan booking pertamanya",
            'source_type' => User::class,
            'source_id' => $referredUser->id,
        ]);
    }
}
