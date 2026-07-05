<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrency
{
    private const SUPPORTED = ['IDR', 'USD'];

    public function handle(Request $request, Closure $next): Response
    {
        $currency = session('currency', 'IDR');

        if (!in_array($currency, self::SUPPORTED)) {
            $currency = 'IDR';
        }

        app()->instance('currentCurrency', $currency);

        return $next($request);
    }
}
