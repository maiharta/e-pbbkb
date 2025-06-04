<?php

namespace App\Http\Controllers\Operator;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PdfService;

class SspdController extends Controller
{
    public function index(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::with(['bunga', 'denda', 'penjualan' => function ($query) {
            $query->with(['sektor', 'jenisBbm']);
        }])
            ->where('ulid', $ulid)
            ->where('is_verified', true)
            ->where('is_sptpd_approved', true)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

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

        $list_nama_pembeli = $pelaporan->penjualan->pluck('pembeli')->unique()->sort()->values();
        return view('pages.operator.pelaporan.sspd.index', compact('pelaporan', 'list_nama_pembeli'));
    }

    public function downloadBuktiBayar(Request $request, $ulid)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
        ]);

        $pelaporan = Pelaporan::where('ulid', $ulid)
            ->where('user_id', auth()->user()->id)
            ->where('is_paid', true)
            ->firstOrFail();

        $pdf = PdfService::generateBuktiBayar(
            $pelaporan,
            $request->nama_perusahaan
        );

        return $pdf->download('bukti-bayar-' . $pelaporan->ulid . '-' . $request->nama_perusahaan . '.pdf');
    }

    public function downloadSspd(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('ulid', $ulid)
            ->where('user_id', auth()->user()->id)
            ->where('is_verified', true)
            ->where('is_sptpd_approved', true)
            ->firstOrFail();

        $pdf = PdfService::generateSspd($pelaporan);

        return $pdf->download('SSPD-' . $pelaporan->ulid . '.pdf');
    }
}
