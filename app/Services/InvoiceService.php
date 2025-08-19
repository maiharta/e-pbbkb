<?php

namespace App\Services;

use Exception;
use App\Models\Invoice;
use App\Models\Pelaporan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;

class InvoiceService
{
    public static function cancelAllInvoices(Pelaporan $pelaporan)
    {
        try {
            $sipayService = new SipayService();
            // Cancel all invoices related to the Pelaporan
            $pelaporan->invoices()->where('payment_status', '!=', 'paid')->each(function ($invoice) use ($sipayService) {
                // Cancel the invoice in Sipay
                $sipayService->cancelInvoice($invoice);
                $invoice->update(['payment_status' => 'expired']);
            });
        } catch (Exception $e) {
            Log::error('Failed to cancel invoices for Pelaporan', [
                'pelaporan_id' => $pelaporan->id,
                'error' => $e->getMessage(),
            ]);
            throw new ServiceException('Gagal membatalkan semua invoice: ' . $e->getMessage());
        }
    }

    public static function cancelInvoice(Invoice $invoice)
    {
        try {
            $sipayService = new SipayService();
            // Cancel the invoice in Sipay
            $sipayService->cancelInvoice($invoice);
            // Update the invoice status to expired
            $invoice->update(['payment_status' => 'expired']);
        } catch (Exception $e) {
            Log::error('Failed to cancel invoice', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
            throw new ServiceException('Gagal membatalkan invoice: ' . $e->getMessage());
        }
    }

    public static function generateInvoice(Pelaporan $pelaporan)
    {
        try {
            if ($pelaporan->invoices()->where('payment_status', 'paid')->exists()) {
                return null;
            }

            // Generate invoice number and receipt number
            $existingInvoice = $pelaporan->invoices()->where('payment_status', 'pending')->first();

            if ($existingInvoice) {
                // check expires at
                if (now()->greaterThan($existingInvoice->expires_at)) {
                    $existingInvoice->update(['payment_status' => 'expired']);
                }else{
                    return $existingInvoice;
                }
            }

            $pelaporan->load([
                'denda',
                'bunga',
                'sptpd',
            ]);

            if(now()->lessThan($pelaporan->batas_pembayaran)) {
                $expires_at = $pelaporan->batas_pembayaran;
            }else{
                $monthDiffBetweenBatasPembayaran = $pelaporan->batas_pembayaran->diffInMonths(now());
                $expires_at = $pelaporan->batas_pembayaran->copy()->addMonthsNoOverflow($monthDiffBetweenBatasPembayaran);
            }

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
        } catch (Exception $e) {
            Log::error('Failed to generate invoice', [
                'pelaporan_id' => $pelaporan->id,
                'error' => $e->getMessage(),
            ]);
            throw new ServiceException('Gagal membuat invoice: ' . $e->getMessage());
        }

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
