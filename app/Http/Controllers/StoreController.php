<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    public function index()
    {
        $esAdmin = Auth::check() && Auth::user()->isAdmin();

        return view('store.index', compact('esAdmin'));
    }
}
