<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Pelaporan;
use Illuminate\Http\Request;

class PelaporanController extends Controller
{
    public function index()
    {
        $pelaporans = Pelaporan::where('user_id', auth()->user()->id)->get();
        return view('pages.operator.pelaporan.index', compact(
            'pelaporans'
        ));
    }
}
