<?php

namespace App\Http\Controllers\Operator;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                    foreach ($category->groupBy('jenis_bbm_id') as $item) {
                        $item_unique = $item->first();
                        $pbbkb = $item->sum('dpp') * ($item_unique->persentase_tarif_jenis_bbm + $item_unique->persentase_tarif_sektor) / 100;

                        $subtotal_volume += $item->sum('volume');
                        $subtotal_dpp += $item->sum('dpp');
                        $subtotal_pbbkb += $pbbkb;

                        $items->push(collect([
                            'nama_jenis_bbm' => $item_unique->nama_jenis_bbm,
                            'persentase_tarif' => $item_unique->persentase_tarif_jenis_bbm + $item_unique->persentase_tarif_sektor,
                            'volume' => $item->sum('volume'),
                            'dpp' => $item->sum('dpp'),
                            'pbbkb' => $pbbkb
                        ]));
                    }
                    $categories->put(
                        $is_subsidi ? 'Subsidi' : 'Umum',
                        collect([
                            'items' => $items,
                            'subtotal' => collect([
                                'volume' => $subtotal_volume,
                                'dpp' => $subtotal_dpp,
                                'pbbkb' => $subtotal_pbbkb
                            ])
                        ])
                    );
                }
                return [
                    $nama_sektor => $categories
                ];
            });

        return view('pages.operator.pelaporan.sptpd.index', compact('pelaporan'));
    }

    public function cancel(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::with(['penjualan'])
            ->where('ulid', $ulid)
            ->where('is_verified', true)
            ->where('is_sptpd_approved', false)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        $pelaporan->update([
            'is_verified' => false,
            'verfied_at' => null,
            'is_sent_to_admin' => false,
            'is_sptpd_canceled' => true,
            'catatan_revisi' => 'Pemohon melakukan pembatalan SPTPD dan melakukan verifikasi ulang'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil melakukan pembatalan SPTPD'
        ]);
    }

    public function approve(Request $request, $ulid)
    {
        $request->validate([
            'nomor_sptpd' => 'required',
        ]);

        $pelaporan = Pelaporan::with(['penjualan'])
            ->where('ulid', $ulid)
            ->where('is_verified', true)
            ->where('is_sptpd_approved', false)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $pelaporan->update([
                'is_sptpd_approved' => true,
            ]);

            $pelaporan->sptpd()->create([
                'nomor' => $request->nomor_sptpd,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server. Hubungi administrator'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil menyimpan surat pernyataan'
        ]);
    }
}
