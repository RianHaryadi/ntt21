<?php

namespace App\Services;

use App\Models\CartItem;
use Illuminate\Support\Str;

class CartService
{
    private const SESSION_KEY = 'cart_token';

    /** Ambil (atau buat) token keranjang untuk tamu tanpa akun. */
    public function guestToken(): string
    {
        if (!session()->has(self::SESSION_KEY)) {
            session([self::SESSION_KEY => (string) Str::uuid()]);
        }

        return session(self::SESSION_KEY);
    }

    public function query()
    {
        if (auth()->check()) {
            return CartItem::where('user_id', auth()->id());
        }

        return CartItem::where('cart_token', $this->guestToken())->whereNull('user_id');
    }

    public function items()
    {
        return $this->query()->with('itemable')->latest()->get();
    }

    public function add(string $itemableType, int $itemableId, array $details): CartItem
    {
        return CartItem::create([
            'user_id' => auth()->id(),
            'cart_token' => auth()->check() ? null : $this->guestToken(),
            'itemable_type' => $itemableType,
            'itemable_id' => $itemableId,
            'details' => $details,
        ]);
    }

    public function remove(int $cartItemId): bool
    {
        $item = $this->query()->where('id', $cartItemId)->first();

        if (!$item) {
            return false;
        }

        $item->delete();

        return true;
    }

    public function clear(): void
    {
        $this->query()->delete();
    }

    public function count(): int
    {
        return $this->query()->count();
    }
}
