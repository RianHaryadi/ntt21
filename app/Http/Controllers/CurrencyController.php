<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    private const SUPPORTED = ['IDR', 'USD'];

    public function switch(Request $request, string $currency)
    {
        $currency = strtoupper($currency);

        if (in_array($currency, self::SUPPORTED)) {
            session(['currency' => $currency]);
        }

        return redirect()->back();
    }
}
