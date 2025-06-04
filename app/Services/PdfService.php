<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Pelaporan;
use Barryvdh\DomPDF\PDF;

class PdfService
{
    public static function generateBuktiBayar(Pelaporan $pelaporan, $nama_perusahaan)
    {
        if (!$pelaporan->is_paid) {
            throw new ServiceException('Pelaporan belum dibayar.');
        }


        $invoice = $pelaporan->invoices->where('payment_status', 'paid')->first();


        if (!$invoice) {
            throw new ServiceException('Invoice tidak ditemukan.');
        }

        if ($nama_perusahaan) {
            $pelaporan->load(['penjualan' => function ($query) use ($nama_perusahaan) {
                $query->where('pembeli', $nama_perusahaan);
            }]);
        } else {
            $pelaporan->load('penjualan');
        }

        $pelaporan->penjualan_group = $pelaporan->penjualan->groupBy('jenis_bbm_id');
        $pdf = app(PDF::class)->loadView('pdf.bukti-bayar', [
            'pelaporan' => $pelaporan,
            'invoice' => $invoice,
        ]);

        $pdf->setPaper('A4');

        return $pdf;
    }

    public static function generateSspd(Pelaporan $pelaporan)
    {
        if (!$pelaporan->is_verified || !$pelaporan->is_sptpd_approved) {
            throw new ServiceException('Pelaporan belum diverifikasi atau SPTPD belum disetujui.');
        }

        // Format data for the PDF
        $pelaporan->load(['penjualan', 'sptpd', 'user.userDetail']);
        $pelaporan->data_formatted = $pelaporan
            ->penjualan
            ->groupBy('kode_jenis_bbm')
            ->mapWithKeys(function ($penjualan, $jenis_bbm_id) {
                $nama_jenis_bbm = $penjualan->first()->nama_jenis_bbm;

                $volume = 0;
                $dpp = 0;
                $pbbkb = 0;

                $penjualan->each(function ($item) use (&$volume, &$dpp, &$pbbkb) {
                    $volume += $item->volume;
                    $dpp += $item->dpp;
                    $pbbkb += $item->pbbkb_sistem;
                });

                return collect([
                    $nama_jenis_bbm => collect([
                        'volume' => $volume,
                        'dpp' => $dpp,
                        'pbbkb' => $pbbkb
                    ])
                ]);
            });

        $pelaporan->total_volume = $pelaporan->data_formatted->values()->sum('volume');
        $pelaporan->total_dpp = $pelaporan->data_formatted->values()->sum('dpp');
        $pelaporan->total_pbbkb = $pelaporan->data_formatted->values()->sum('pbbkb');

        // Calculate total sanctions
        $total_sanksi = $pelaporan->denda->sum('denda') +
            ($pelaporan->bunga->sum('bunga') * $pelaporan->sptpd->total_pbbkb);

        // Calculate grand total
        $grand_total = $pelaporan->total_pbbkb + $total_sanksi;

        $pdf = app(PDF::class)->loadView('pdf.sspd', [
            'pelaporan' => $pelaporan,
            'total_sanksi' => $total_sanksi,
            'grand_total' => $grand_total,
        ]);

        // Set to landscape orientation
        $pdf->setPaper('A4', 'landscape');

        return $pdf;
    }
}
