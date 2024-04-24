<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->is_admin){
            return redirect()->route('penjualan.index');
        }

        return view('pages.dashboard.index');
    }
}
