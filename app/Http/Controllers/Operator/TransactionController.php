<?php

namespace App\Http\Controllers\Operator;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invoice;

class TransactionController extends Controller
{
    public function index()
    {
        $pelaporans = Pelaporan::where('user_id', auth()->user()->id)
            ->where('is_sptpd_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.operator.transaction.index', compact('pelaporans'));
    }

    public function show($ulid)
    {
        $pelaporan = Pelaporan::with(['invoices'])
            ->where('user_id', auth()->user()->id)
            ->where('ulid', $ulid)
            ->firstOrFail();
        return view('pages.operator.transaction.show', compact('pelaporan'));
    }

    public function showInvoice($ulid)
    {
        $invoice = Invoice::whereHas('pelaporan', function ($query) use ($ulid) {
            $query->where('user_id', auth()->user()->id);
        })
            ->where('ulid', $ulid)
            ->where('payment_status', 'pending')
            ->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }
}
