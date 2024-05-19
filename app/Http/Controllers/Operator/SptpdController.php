<?php

namespace App\Http\Controllers\Operator;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SptpdController extends Controller
{
    public function index(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::with(['penjualan'])
            ->where('ulid', $ulid)
            ->where('is_verified', true)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        $pelaporan->data_formatted = $pelaporan
            ->penjualan
            ->groupBy('nama_sektor')
            ->mapWithKeys(function ($penjualan, $nama_sektor) {
                $categories = collect();
                foreach ($penjualan->groupBy('is_subsidi') as $is_subsidi => $category) {

                    $items = collect();
                    $subtotal_volume = 0;
                    $subtotal_dpp = 0;
                    $subtotal_pbbkb = 0;
                    foreach ($category->groupBy('jenis_bbm_id') as $jenis_bbm_id => $item) {
                        $item_unique = $item->first();
                        $pbbkb = $item->sum('dpp') * $item_unique->persentase_tarif_jenis_bbm + $item_unique->persentase_tarif_sektor / 100;

                        $subtotal_volume += $item->sum('volume');
                        $subtotal_dpp += $item->sum('dpp');
                        $subtotal_pbbkb += $pbbkb;

                        $items->push([
                            'nama_jenis_bbm' => $item_unique->nama_jenis_bbm,
                            'persentase_tarif' => $item_unique->persentase_tarif_jenis_bbm + $item_unique->persentase_tarif_sektor,
                            'volume' => $item->sum('volume'),
                            'dpp' => $item->sum('dpp'),
                            'pbbkb' => $pbbkb
                        ]);
                    }

                    $categories->push([
                        $is_subsidi ? 'Subsidi' : 'Umum' => [
                            'items' => $items,
                            'subtotal' => [
                                'volume' => $subtotal_volume,
                                'dpp' => $subtotal_dpp,
                                'pbbkb' => $subtotal_pbbkb
                            ]
                        ]
                    ]);
                }
                return [
                    $nama_sektor => $categories
                ];
            });

        return view('pages.operator.pelaporan.sptpd.index', compact('pelaporan'));
    }
}
