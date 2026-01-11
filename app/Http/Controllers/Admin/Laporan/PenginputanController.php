<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Models\Pelaporan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PdfService;

class PenginputanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelaporan::with(['user']);

        // Filter by user
        $user_id = $request->input('user_id');
        if ($user_id) {
            $query->where('user_id', $user_id);
        }

        // Filter by status
        $status = $request->input('status');
        if ($status === 'verified') {
            $query->where('is_verified', true);
        } elseif ($status === 'ongoing') {
            $query->where('is_verified', false)
                  ->where('is_expired', false);
        }
        // If status is empty or null, show all (no filter applied)

        // Filter by periode
        $periode_awal = $request->input('periode_awal');
        $periode_akhir = $request->input('periode_akhir');

        if ($periode_awal && $periode_akhir) {
            list($bulan_awal, $tahun_awal) = explode('-', $periode_awal);
            list($bulan_akhir, $tahun_akhir) = explode('-', $periode_akhir);

            $query->where(function ($q) use ($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
                $q->where(function ($subQ) use ($tahun_awal, $tahun_akhir) {
                    $subQ->whereBetween('tahun', [$tahun_awal, $tahun_akhir]);
                });

                if ($tahun_awal == $tahun_akhir) {
                    $q->whereBetween('bulan', [$bulan_awal, $bulan_akhir]);
                } else {
                    $q->where(function ($dateQ) use ($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir) {
                        $dateQ->where(function ($startQ) use ($tahun_awal, $bulan_awal) {
                            $startQ->where('tahun', $tahun_awal)
                                   ->where('bulan', '>=', $bulan_awal);
                        })
                        ->orWhere(function ($betweenQ) use ($tahun_awal, $tahun_akhir) {
                            $betweenQ->where('tahun', '>', $tahun_awal)
                                     ->where('tahun', '<', $tahun_akhir);
                        })
                        ->orWhere(function ($endQ) use ($tahun_akhir, $bulan_akhir) {
                            $endQ->where('tahun', $tahun_akhir)
                                 ->where('bulan', '<=', $bulan_akhir);
                        });
                    });
                }
            });
        }

        $pelaporans = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        // Get verified operators for user filter
        $users = User::whereHas('userDetail', function ($query) {
            $query->where('is_verified', true);
        })
        ->whereHas('roles', function ($query) {
            $query->where('name', 'operator');
        })
        ->orderBy('name')
        ->get();

        return view('pages.admin.laporan.penginputan.index', compact('pelaporans', 'users'));
    }

    public function showSptpd($ulid)
    {
        $pelaporan = Pelaporan::with(['penjualan'])
            ->where('ulid', $ulid)
            ->where('is_verified', true)
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

                        $item->map(function ($i) use (&$subtotal_volume, &$subtotal_dpp, &$subtotal_pbbkb) {
                            $subtotal_volume += $i->volume;
                            $subtotal_dpp += $i->dpp * $i->volume;
                            $subtotal_pbbkb += $i->pbbkb_sistem;
                        });

                        $items->push(collect([
                            'nama_jenis_bbm' => $item_unique->nama_jenis_bbm,
                            'persentase_tarif' => $item_unique->persentase_tarif_jenis_bbm / 100 * $item_unique->persentase_pengenaan_sektor / 100,
                            'volume' => $item->sum('volume'),
                            'dpp' => $item->sum(function ($i) {
                                return $i->dpp * $i->volume;
                            }),
                            'pbbkb' => $item->sum('pbbkb_sistem')
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

        return view('pages.admin.laporan.penginputan.sptpd', compact('pelaporan'));
    }

    public function downloadSptpd($ulid)
    {
        $pelaporan = Pelaporan::where('ulid', $ulid)
            ->where('is_sptpd_approved', true)
            ->firstOrFail();

        $pdf = PdfService::generateSptpd($pelaporan);

        return $pdf->download('SPTPD-' . $pelaporan->ulid . '.pdf');
    }

    public function showSspd($ulid)
    {
        $pelaporan = Pelaporan::with(['bunga', 'denda', 'penjualan' => function ($query) {
            $query->with(['sektor', 'jenisBbm']);
        }])
            ->where('ulid', $ulid)
            ->where('is_verified', true)
            ->where('is_sptpd_approved', true)
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
        return view('pages.admin.laporan.penginputan.sspd', compact('pelaporan', 'list_nama_pembeli'));
    }

    public function downloadSspd($ulid)
    {
        $pelaporan = Pelaporan::where('ulid', $ulid)
            ->where('is_verified', true)
            ->where('is_sptpd_approved', true)
            ->firstOrFail();

        $pdf = PdfService::generateSspd($pelaporan);

        return $pdf->download('SSPD-' . $pelaporan->ulid . '.pdf');
    }

    public function showPenjualan($ulid)
    {
        $pelaporan = Pelaporan::where('ulid', $ulid)->firstOrFail();
        $penjualans = $pelaporan->penjualan()->with(['sektor', 'jenisBbm'])->get();
        return view('pages.admin.laporan.penginputan.penjualan', compact('pelaporan', 'penjualans'));
    }

    public function showPembelian($ulid)
    {
        $pelaporan = Pelaporan::where('ulid', $ulid)->firstOrFail();
        $pembelians = $pelaporan->pembelian()->get();
        return view('pages.admin.laporan.penginputan.pembelian', compact('pelaporan', 'pembelians'));
    }

    public function showInvoices($ulid)
    {
        $pelaporan = Pelaporan::with(['invoices'])
            ->where('ulid', $ulid)
            ->firstOrFail();
        return view('pages.admin.laporan.penginputan.invoices', compact('pelaporan'));
    }
}
