<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Pelaporan;
use Illuminate\Support\Carbon;

class InvoiceService
{
    public static function generateInvoice(Pelaporan $pelaporan)
    {
        if ($pelaporan->invoices()->where('payment_status', 'paid')->exists()) {
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

        $expires_at = CutiService::getDateAfterCuti(
            now()->startOfMonth()->addMonth(),
            10,
        );

        $invoice = $pelaporan->invoices()->create([
            'customer_npwpd' => $pelaporan->user->userDetail->npwpd,
            'customer_name' => $pelaporan->user->name,
            'customer_email' => $pelaporan->user->email,
            'customer_phone' => $pelaporan->user->userDetail->nomor_telepon,
            'customer_address' => $pelaporan->user->userDetail->alamat,
            'month' => $pelaporan->bulan,
            'year' => $pelaporan->tahun,
            'expires_at' => $expires_at,
            'description' => 'Periode ' . Carbon::create($pelaporan->tahun, $pelaporan->bulan)->translatedFormat('F Y'),
            'amount' => $pelaporan->sptpd->total_pbbkb + $pelaporan->denda->sum('denda') +
                ($pelaporan->bunga->sum('bunga') * $pelaporan->sptpd->total_pbbkb),
            'items' => [
                'pbbkb' => [
                    'nomor_kwitansi' => 'P-' . $pelaporan->bulan . '-' . $pelaporan->tahun . '-' . now()->format('His'),
                    'nominal' => $pelaporan->sptpd->total_pbbkb,
                    'kode_tujuan_pelimpahan' => '0110',
                    'keterangan' => 'PBBKB Periode ' . Carbon::create($pelaporan->tahun, $pelaporan->bulan)->translatedFormat('F Y'),
                ],
                'sanksi' => [
                    'nomor_kwitansi' => 'S-' . $pelaporan->bulan . '-' . $pelaporan->tahun . '-' . now()->format('His'),
                    'nominal' => $pelaporan->denda->sum('denda') + ($pelaporan->bunga->sum('bunga') * $pelaporan->sptpd->total_pbbkb),
                    'keterangan' => 'Sanksi Periode ' . Carbon::create($pelaporan->tahun, $pelaporan->bulan)->translatedFormat('F Y'),
                    'kode_tujuan_pelimpahan' => '700',
                    'items' => [
                        'denda' => $pelaporan->denda->map(function ($denda) {
                            return [
                                'nominal' => $denda->denda,
                                'keterangan' => $denda->keterangan,
                            ];
                        })->toArray(),
                        'bunga' => $pelaporan->bunga->map(function ($bunga) use ($pelaporan) {
                            return [
                                'nominal' => $bunga->bunga * $pelaporan->sptpd->total_pbbkb,
                                'keterangan' => $bunga->keterangan,
                            ];
                        })->toArray(),
                    ]
                ],
            ]
        ]);

        // sipay
        return self::sendToSipay($invoice);
    }


    public static function sendToSipay(Invoice $invoice)
    {
        $sipay_service = new SipayService();
        $sipay = $sipay_service->createVirtualAccount($invoice);
        if ($sipay) {
            $invoice->update([
                'sipay_record_id' => $sipay['record_id'],
                'sipay_virtual_account' => $sipay['nomor_virtual_account'],
                'sipay_transaction_date' => Carbon::createFromFormat('Y-m-d H:i:s', $sipay['transaction_date'], 'GMT+8')->setTimezone('UTC'),
                'sipay_expired_date' => Carbon::createFromFormat('Y-m-d H:i:s', $sipay['expired_date'], 'GMT+8')->setTimezone('UTC'),
                'sipay_nomor_tagihan' => $sipay['detail_invoice'][0]['nomor_tagihan'],
                'sipay_status_invoice' => $sipay['status_invoice'],
                'sipay_status_bpd' => $sipay['status_bpd'],
                'sipay_invoice' => $sipay['no_invoice'],
                'sipay_response' => $sipay,
            ]);
        }

        return $invoice;
    }
}
