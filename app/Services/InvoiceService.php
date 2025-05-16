<?php

namespace App\Services;

use App\Models\Pelaporan;
use Illuminate\Support\Carbon;

class InvoiceService
{
    public static function generateInvoice(Pelaporan $pelaporan)
    {
        if($pelaporan->invoices()->where('payment_status', 'paid')->exists()){
            return null;
        }

        // Generate invoice number and receipt number
        $existingInvoice = $pelaporan->invoices()->where('payment_status', 'pending')->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        $pelaporan->load([
            'denda',
            'bunga',
            'sptpd',
        ]);

        $invoice = $pelaporan->invoices()->create([
            'customer_npwpd' => $pelaporan->user->userDetail->npwpd,
            'customer_name' => $pelaporan->user->name,
            'customer_email' => $pelaporan->user->email,
            'customer_phone' => $pelaporan->user->userDetail->nomor_telepon,
            'customer_address' => $pelaporan->user->userDetail->alamat,
            'month' => $pelaporan->bulan,
            'year' => $pelaporan->tahun,
            'description' => 'Periode ' . Carbon::create($pelaporan->tahun, $pelaporan->bulan)->translatedFormat('F Y'),
            'amount' => $pelaporan->sptpd->total_pbbkb + $pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga'),
            'items' => [
                'pbbkb' => $pelaporan->sptpd->total_pbbkb,
                'denda' => $pelaporan->denda->sum('denda'),
                'bunga' => $pelaporan->bunga->sum('bunga'),
            ]
        ]);

        // sipay
        $sipay_service = new SipayService();
        $sipay = $sipay_service->createVirtualAccount($invoice);
        if($sipay){
            $invoice->update([
                'sipay_record_id' => $sipay['record_id'],
                'sipay_virtual_account' => $sipay['nomor_virtual_account'],
                'sipay_transaction_date' => Carbon::createFromFormat('Y-m-d H:i:s', $sipay['transaction_date'], 'GMT+8')->setTimezone('UTC'),
                'sipay_expired_date' => Carbon::createFromFormat('Y-m-d H:i:s', $sipay['expired_date'], 'GMT+8')->setTimezone('UTC'),
                'sipay_nomor_tagihan' => $sipay['detail_invoice'][0]['nomor_tagihan'],
                'sipay_status_invoice' => $sipay['status_invoice'],
                'sipay_status_bpd' => $sipay['status_bpd'],
                'expires_at' => now()->addDays(3),
            ]);
        }

        return $invoice;
    }
}
