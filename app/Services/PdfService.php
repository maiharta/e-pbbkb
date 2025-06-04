<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Pelaporan;
use Barryvdh\DomPDF\PDF;

class PdfService
{
    public static function generateBuktiBayar(Pelaporan $pelaporan)
    {
        if(!$pelaporan->is_paid) {
            throw new ServiceException('Pelaporan belum dibayar.');
        }

        $invoice = $pelaporan->invoices->where('payment_status', 'paid')->first();

        if (!$invoice) {
            throw new ServiceException('Invoice tidak ditemukan.');
        }

        $pdf = app(PDF::class)->loadView('pdf.bukti-bayar', [
            'pelaporan' => $pelaporan,
            'invoice' => $invoice,
        ]);

        $pdf->setPaper('A4');

        // save to storage
        $fileName = 'bukti-bayar-' . $pelaporan->id . '.pdf';
        $pdf->save(storage_path('app/public/' . $fileName));

        return $pdf;
    }
}
