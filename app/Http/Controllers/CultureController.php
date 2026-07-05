<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Culture;

class CultureController extends Controller
{
    /**
     * Display a listing of the NTT cultural contents.
     */
    public function index()
    {
        $cultures = Culture::latest()->paginate(12);
        return view('culture.index', compact('cultures'));
    }
}
