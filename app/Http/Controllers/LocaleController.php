<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    private const SUPPORTED = ['id', 'en'];

    public function switch(Request $request, string $locale)
    {
        if (in_array($locale, self::SUPPORTED)) {
            session(['locale' => $locale]);
        }

        return redirect()->back();
    }
}
