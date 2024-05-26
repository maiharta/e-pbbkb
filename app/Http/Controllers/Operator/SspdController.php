<?php

namespace App\Http\Controllers\Operator;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SspdController extends Controller
{
    public function index(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::with(['penjualan' => function ($query) {
            $query->with(['sektor', 'jenisBbm']);
        }])
            ->where('ulid', $ulid)
            ->where('is_verified', true)
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
                    $pbbkb += $item->dpp * ($item->persentase_tarif_jenis_bbm + $item->persentase_tarif_sektor) / 100;
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

        return view('pages.operator.pelaporan.sspd.index', compact('pelaporan'));
    }
}
