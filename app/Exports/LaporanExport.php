<?php

namespace App\Exports;

use App\Models\Kabupaten;
use App\Models\Pelaporan;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanExport implements FromView, ShouldAutoSize
{
    protected $periode_awal;
    protected $periode_akhir;
    protected $kabupaten_ids;

    protected $data;

    public function __construct($periode_awal = null, $periode_akhir = null, $kabupaten_ids = [])
    {
        $this->periode_awal = $periode_awal;
        $this->periode_akhir = $periode_akhir;
        $this->kabupaten_ids = $kabupaten_ids;

        // check every kabupaten_ids exists in database
        if (!empty($kabupaten_ids)) {
            $kabupatens = Kabupaten::whereIn('id', $kabupaten_ids)->get();
            if ($kabupatens->count() !== count($kabupaten_ids)) {
                throw new \Exception('Some kabupaten_ids do not exist in the database.');
            }
        }

        $data = Pelaporan::with([
            'pembelian',
            'penjualan' => function ($query) {
                if (!empty($this->kabupaten_ids)) {
                    $query->whereIn('kabupaten_id', $this->kabupaten_ids);
                }
            },
            'user',
            'denda',
            'bunga',
            'sptpd'
        ])
            ->where('is_paid', true);

        // Apply date filtering logic
        if ($this->periode_awal) {
            // Parse periode_awal (format: month-year, e.g. "6-2023")
            list($startMonth, $startYear) = explode('-', $this->periode_awal);

            if ($this->periode_akhir) {
                // Parse periode_akhir (format: month-year, e.g. "6-2023")
                list($endMonth, $endYear) = explode('-', $this->periode_akhir);

                // Filter between periode_awal and periode_akhir
                $data->where(function ($query) use ($startMonth, $startYear, $endMonth, $endYear) {
                    $query->whereBetween('tahun', [$startYear, $endYear])
                        ->where(function ($query) use ($startYear, $startMonth, $endYear, $endMonth) {
                            // For same year, filter by month
                            if ($startYear == $endYear) {
                                $query->where('tahun', $startYear)
                                    ->whereBetween('bulan', [$startMonth, $endMonth]);
                            }
                            // For different years
                            else {
                                $query->where(function ($q) use ($startYear, $startMonth) {
                                    // Starting year, month >= startMonth
                                    $q->where('tahun', $startYear)
                                        ->where('bulan', '>=', $startMonth);
                                })->orWhere(function ($q) use ($endYear, $endMonth) {
                                    // Ending year, month <= endMonth
                                    $q->where('tahun', $endYear)
                                        ->where('bulan', '<=', $endMonth);
                                })->orWhere(function ($q) use ($startYear, $endYear) {
                                    // Years in between
                                    $q->whereBetween('tahun', [$startYear + 1, $endYear - 1]);
                                });
                            }
                        });
                });
            } else {
                // If no periode_akhir, filter from periode_awal to now
                $currentMonth = Carbon::now()->month;
                $currentYear = Carbon::now()->year;

                $data->where(function ($query) use ($startMonth, $startYear, $currentMonth, $currentYear) {
                    $query->whereBetween('tahun', [$startYear, $currentYear])
                        ->orWhere(function ($query) use ($startYear, $startMonth, $currentYear, $currentMonth) {
                            // For same year, filter by month
                            if ($startYear == $currentYear) {
                                $query->where('tahun', $startYear)
                                    ->whereBetween('bulan', [$startMonth, $currentMonth]);
                            }
                            // For different years
                            else {
                                $query->where(function ($q) use ($startYear, $startMonth) {
                                    // Starting year, month >= startMonth
                                    $q->where('tahun', $startYear)
                                        ->where('bulan', '>=', $startMonth);
                                })->orWhere(function ($q) use ($currentYear, $currentMonth) {
                                    // Current year, month <= currentMonth
                                    $q->where('tahun', $currentYear)
                                        ->where('bulan', '<=', $currentMonth);
                                })->orWhere(function ($q) use ($startYear, $currentYear) {
                                    // Years in between
                                    $q->whereBetween('tahun', [$startYear + 1, $currentYear - 1]);
                                });
                            }
                        });
                });
            }
        }

        $this->data = $data->get()
            // groupBy user
            ->groupBy(function ($item) {
                return $item->user->id;
            })->map(function ($items, $user) {
                // Get all unique jenis BBM IDs across all pelaporans for this user
                $allJenisBbmIds = $items->flatMap(function ($item) {
                    return collect($item->pembelian->pluck('jenis_bbm_id'))
                        ->merge($item->penjualan->pluck('jenis_bbm_id'))
                        ->unique();
                })->unique()->values();

                // Calculate totals by jenis BBM for this user
                $jenisBbmTotals = $allJenisBbmIds->mapWithKeys(function ($jenisBbmId) use ($items) {
                    // Get all pelaporan items that have this jenis BBM
                    $relevantPelaporans = $items->filter(function ($pelaporan) use ($jenisBbmId) {
                        return $pelaporan->pembelian->where('jenis_bbm_id', $jenisBbmId)->isNotEmpty() ||
                            $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->isNotEmpty();
                    });

                    // Calculate totals
                    $totalVolumePembelian = $relevantPelaporans->sum(function ($pelaporan) use ($jenisBbmId) {
                        return $pelaporan->pembelian->where('jenis_bbm_id', $jenisBbmId)->sum('volume');
                    });

                    $totalVolumePenjualan = $relevantPelaporans->sum(function ($pelaporan) use ($jenisBbmId) {
                        return $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->sum('volume');
                    });

                    $totalPbbkbPenjualan = $relevantPelaporans->sum(function ($pelaporan) use ($jenisBbmId) {
                        return $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->sum('pbbkb_sistem');
                    });

                    $totalDppPenjualan = $relevantPelaporans->sum(function ($pelaporan) use ($jenisBbmId) {
                        return $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->sum('dpp');
                    });


                    // Get the jenis BBM name from any pelaporan that has it
                    $namaJenisBbm = 'Unknown';
                    foreach ($relevantPelaporans as $pelaporan) {
                        $pembelianWithJenisBbm = $pelaporan->pembelian->where('jenis_bbm_id', $jenisBbmId)->first();
                        if ($pembelianWithJenisBbm) {
                            $namaJenisBbm = $pembelianWithJenisBbm->nama_jenis_bbm;
                            break;
                        }

                        $penjualanWithJenisBbm = $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->first();
                        if ($penjualanWithJenisBbm) {
                            $namaJenisBbm = $penjualanWithJenisBbm->nama_jenis_bbm;
                            break;
                        }
                    }

                    return [
                        $jenisBbmId => [
                            'nama_jenis_bbm' => $namaJenisBbm,
                            'total_volume_pembelian' => $totalVolumePembelian,
                            'total_volume_penjualan' => $totalVolumePenjualan,
                            'total_pbbkb_penjualan' => $totalPbbkbPenjualan,
                            'total_dpp_penjualan' => $totalDppPenjualan,
                        ]
                    ];
                });

                // Calculate company totals across all pelaporans
                $totalSanksiPerusahaan = 0;
                $totalSubtotalPerusahaan = 0;

                // Sum all sanksi for this company
                $totalSanksiPerusahaan = $items->sum(function ($pelaporan) {
                    return $pelaporan->denda->sum('denda') +
                        ($pelaporan->bunga->sum('bunga') * ($pelaporan->sptpd ? $pelaporan->sptpd->total_pbbkb : 0));
                });

                // Sum all subtotals (PBBKB + sanksi) for this company
                $totalSubtotalPerusahaan = $items->sum(function ($pelaporan) {
                    $sanksi = $pelaporan->denda->sum('denda') +
                        ($pelaporan->bunga->sum('bunga') * ($pelaporan->sptpd ? $pelaporan->sptpd->total_pbbkb : 0));
                    $pbbkb = $pelaporan->sptpd ? $pelaporan->sptpd->total_pbbkb : 0;
                    return $sanksi + $pbbkb;
                });

                return [
                    'user' => $items->first()->user,
                    'jenis_bbm_totals' => $jenisBbmTotals, // Add the totals by jenis BBM
                    'total_sanksi_perusahaan' => $totalSanksiPerusahaan, // Total sanksi for company
                    'total_subtotal_perusahaan' => $totalSubtotalPerusahaan, // Total subtotal for company
                    'pelaporans' => $items
                        ->filter(function ($item) {
                            return $item->is_paid;
                        })
                        ->map(function ($pelaporan) {
                            // Get all jenis_bbm_ids from both pembelian and penjualan
                            $jenisBbmIds = collect($pelaporan->pembelian->pluck('jenis_bbm_id'))
                                ->merge($pelaporan->penjualan->pluck('jenis_bbm_id'))
                                ->unique()
                                ->values();

                            // Calculate total sanksi for this pelaporan
                            $totalSanksi = $pelaporan->denda->sum('denda') +
                                $pelaporan->bunga->sum('bunga');

                            // Calculate total PBBKB for this pelaporan
                            $totalPbbkb = $pelaporan->sptpd ? $pelaporan->sptpd->total_pbbkb : 0;

                            // Calculate subtotal for this pelaporan
                            $subtotal = $totalPbbkb + $totalSanksi;

                            // Group both types by jenis_bbm_id
                            $bbmGroups = $jenisBbmIds->mapWithKeys(function ($jenisBbmId) use ($pelaporan, $totalSanksi, $totalPbbkb) {
                                // Calculate the PBBKB for this jenis BBM
                                $pbbkbForThisBbm = $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->sum('pbbkb_sistem');

                                // Calculate the proportion of sanksi for this BBM type based on its PBBKB contribution
                                $sanksiForThisBbm = 0;
                                if ($totalPbbkb > 0) {
                                    $proportion = $pbbkbForThisBbm / $totalPbbkb;
                                    $sanksiForThisBbm = $totalSanksi * $proportion;
                                }

                                // Calculate subtotal for this BBM type
                                $subtotalForThisBbm = $pbbkbForThisBbm + $sanksiForThisBbm;

                                return [
                                    $jenisBbmId => [
                                        'pembelian' => $pelaporan->pembelian->where('jenis_bbm_id', $jenisBbmId)->values(),
                                        'penjualan' => $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->values(),
                                        'volume_pembelian' => $pelaporan->pembelian->where('jenis_bbm_id', $jenisBbmId)->sum('volume'),
                                        'volume_penjualan' => $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->sum('volume'),
                                        'pbbkb_penjualan' => $pbbkbForThisBbm,
                                        'dpp_penjualan' => $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->sum('dpp'),
                                        'sanksi' => $sanksiForThisBbm,
                                        'subtotal' => $subtotalForThisBbm,
                                        'nama_jenis_bbm' => $pelaporan->pembelian->where('jenis_bbm_id', $jenisBbmId)->first()
                                            ? $pelaporan->pembelian->where('jenis_bbm_id', $jenisBbmId)->first()->nama_jenis_bbm
                                            : ($pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->first()
                                                ? $pelaporan->penjualan->where('jenis_bbm_id', $jenisBbmId)->first()->nama_jenis_bbm
                                                : 'Unknown')
                                    ]
                                ];
                            })->filter(function ($group) {
                                // Filter out empty groups
                                return $group['pembelian']->isNotEmpty() || $group['penjualan']->isNotEmpty();
                            });

                            return [
                                'id' => $pelaporan->id,
                                'bulan' => $pelaporan->bulan,
                                'tahun' => $pelaporan->tahun,
                                'kabupaten' => $pelaporan->kabupaten,
                                'bbm_groups' => $bbmGroups,
                                // Keep the original data if needed
                                'pembelians_raw' => $pelaporan->pembelian->groupBy('jenis_bbm_id'),
                                'penjualans_raw' => $pelaporan->penjualan->groupBy('jenis_bbm_id'),
                                'sanksi' => $totalSanksi,
                                'subtotal' => $subtotal
                            ];
                        }),
                ];
            });
        // })
        // ->dd();
        // Removed the ->dd() to allow export to proceed
    }


    public function view(): View
    {
        return view('exports.laporan', [
            'data' => $this->data
        ]);
    }
}
