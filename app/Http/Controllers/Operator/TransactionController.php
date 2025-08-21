<?php

namespace App\Http\Controllers\Operator;

use App\Models\Bunga;
use App\Models\Invoice;
use App\Models\Pelaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

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
        // $existingBungas = Bunga::where('pelaporan_id', $pelaporan->id)
        //             ->orderBy('bunga_ke', 'desc')
        //             ->get();
        $invoice = Invoice::with(['pelaporan.bunga' => function ($query) use ($ulid) {
            $query->orderBy('bunga_ke', 'desc');
        }])
            ->whereHas('pelaporan', function ($query) use ($ulid) {
            $query->where('user_id', auth()->user()->id);
        })
            ->where('ulid', $ulid)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        $invoice->next_due_date = $invoice->expires_at;
        $invoice->sipay_transaction_date = Carbon::parse($invoice->sipay_transaction_date)->setTimezone('GMT+8');
        //format the next_due_date to a string not including time in indonesian format
        $invoice->next_due_date = $invoice->next_due_date->locale('id')->isoFormat('D MMMM YYYY');
        return response()->json([
            'success' => true,
            'data' => $invoice->only([
                'ulid',
                'invoice_number',
                'customer_npwpd',
                'customer_name',
                'customer_email',
                'customer_phone',
                'amount',
                'description',
                'items.*',
                'sipay_transaction_date',
                'payment_status',
                'sipay_invoice',
                'sipay_virtual_account',
                'next_due_date',
            ]),
        ]);
    }
}
