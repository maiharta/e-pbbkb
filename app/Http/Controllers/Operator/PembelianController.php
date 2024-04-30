<?php

namespace App\Http\Controllers\Operator;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PembelianController extends Controller
{
    public function index(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('ulid', $ulid)->firstOrFail();
        $pembelians = $pelaporan->pembelian()->get();
        return view('pages.operator.pelaporan.pembelian.index', compact('pelaporan', 'pembelians'));
    }
}
