<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Pelaporan;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public static function generateBuktiBayar(Pelaporan $pelaporan, $nama_perusahaan, $npwp = null)
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
            'npwp' => $npwp,
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

        $pelaporan->penjualan = $pelaporan->penjualan->groupBy('kode_jenis_bbm')->map(function ($items, $kode_jenis_bbm) {
            $firstItem = $items->first();
            return collect([
                'nama_jenis_bbm' => $firstItem->nama_jenis_bbm,
                'volume' => $items->sum('volume'),
                'dpp' => $items->sum(function ($i) {
                    return $i->dpp * $i->volume;
                }),
                'pbbkb' => $items->sum('pbbkb_sistem'),
            ]);
        });

        $pdf = app(PDF::class)->loadView('pdf.sspd', [
            'pelaporan' => $pelaporan,
        ]);

        // Set to landscape orientation
        $pdf->setPaper('A4', 'potrait');

        $pdf->save(Storage::disk('public')->path( 'sspd.pdf'));

        return $pdf;
    }

    public static function generateSptpd(Pelaporan $pelaporan)
    {
        if (!$pelaporan->is_sptpd_approved) {
            throw new ServiceException('SPTPD belum disetujui.');
        }

        // Load necessary relationships
        $pelaporan->load(['penjualan', 'sptpd', 'user.userDetail']);

        // Format data for the PDF
        $pelaporan->penjualan_by_jenis = $pelaporan->penjualan->groupBy('kode_jenis_bbm')->map(function ($items, $kode_jenis_bbm) {
            $firstItem = $items->first();
            return collect([
                'nama_jenis_bbm' => $firstItem->nama_jenis_bbm,
                'volume' => $items->sum('volume'),
                'dpp' => $items->sum(function ($i) {
                    return $i->dpp * $i->volume;
                }),
                'pbbkb' => $items->sum('pbbkb_sistem'),
            ]);
        });

        $pelaporan->penjualan_by_sektor = $pelaporan->penjualan->groupBy('kode_sektor')->map(function ($items, $kode_sektor) {
            return $items->groupBy('kode_jenis_bbm')->map(function ($jenisItems, $kode_jenis_bbm) {
                $firstItem = $jenisItems->first();
                return collect([
                    'nama_sektor' => $firstItem->nama_sektor,
                    'nama_jenis_bbm' => $firstItem->nama_jenis_bbm,
                    'persentase_pengenaan_sektor' => $firstItem->persentase_tarif_jenis_bbm,
                    'volume' => $jenisItems->sum('volume'),
                    'dpp' => $jenisItems->sum(function ($i) {
                        return $i->dpp * $i->volume;
                    }),
                    'pbbkb' => $jenisItems->sum('pbbkb_sistem'),
                ]);
            })->values();
        });

        // dd($pelaporan->penjualan_by_sektor);

        $pdf = app(PDF::class)->loadView('pdf.sptpd', [
            'pelaporan' => $pelaporan,
        ]);

        // Set to landscape orientation
        $pdf->setPaper('A4', 'landscape');

        $pdf->save(Storage::disk('public')->path('sptpd.pdf'));

        return $pdf;
    }
}
