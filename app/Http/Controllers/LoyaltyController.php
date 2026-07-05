<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    public function __construct(private LoyaltyService $loyalty) {}

    public function redeem(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|integer|in:' . implode(',', array_keys(LoyaltyService::REDEMPTION_OPTIONS)),
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            $promo = $this->loyalty->redeem($user, $validated['points']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Berhasil! Kode voucher Anda: {$promo->code} (berlaku 60 hari, diskon Rp" . number_format($promo->discount_amount, 0, ',', '.') . ").");
    }
}
