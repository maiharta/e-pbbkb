<?php

namespace App\Exports;

use App\Models\Pelaporan;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PelaporanExport implements FromView, ShouldAutoSize
{
    protected $periode_awal;
    protected $periode_akhir;
    protected $status;

    protected $data;

    public function __construct($periode_awal = null, $periode_akhir = null, $status = null)
    {
        $this->periode_awal = $periode_awal;
        $this->periode_akhir = $periode_akhir;
        $this->status = $status;

        $data = Pelaporan::with([
            'user.userDetail',
            'pembelian',
            'penjualan',
        ]);

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
                        ->where(function ($query) use ($startYear, $startMonth, $currentYear, $currentMonth) {
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

        // Apply status filter
        if ($this->status === 'verified') {
            $data->where('is_verified', true);
        } elseif ($this->status === 'ongoing') {
            $data->where('is_verified', false)
                ->where('is_expired', false);
        }

        $this->data = $data->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();
    }

    public function view(): View
    {
        return view('exports.pelaporan', [
            'data' => $this->data,
            'periode_awal' => $this->periode_awal,
            'periode_akhir' => $this->periode_akhir,
            'status' => $this->status,
        ]);
    }
}
