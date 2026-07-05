<?php

use Illuminate\Support\Facades\App;

if (!function_exists('current_currency')) {
    /**
     * Kode mata uang aktif untuk sesi ini ('IDR' atau 'USD').
     */
    function current_currency(): string
    {
        return App::bound('currentCurrency') ? App::make('currentCurrency') : 'IDR';
    }
}

if (!function_exists('format_price')) {
    /**
     * Format nominal (selalu disimpan dalam Rupiah di database) sesuai mata uang aktif.
     */
    function format_price(float|int|null $amountInIdr): string
    {
        $amountInIdr = $amountInIdr ?? 0;

        if (current_currency() === 'USD') {
            $rate = config('services.currency.usd_rate', 15800);
            $usd = $rate > 0 ? $amountInIdr / $rate : 0;

            return '$' . number_format($usd, 2, '.', ',');
        }

        return 'Rp' . number_format($amountInIdr, 0, ',', '.');
    }
}
