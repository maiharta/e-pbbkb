<?php

namespace App\Http\Controllers;

use App\Models\Sptpd;
use App\Models\Invoice;
use App\Models\Pelaporan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->is_admin) {
            return redirect()->route('pelaporan.index');
        }

        return view('pages.dashboard.index');
    }

    public function getStats(Request $request)
    {
        // Get the requested year or default to current year
        $year = $request->input('year', date('Y'));

        // Get total PBBKB from paid invoices for the selected year
        $totalPbbkb = Sptpd::whereHas('pelaporan', function ($query) {
            $query->where('is_paid', true);
        })
            // ->whereYear('tanggal', $year)
            ->sum('total_pbbkb');

        // Format total PBBKB in Indonesian "juta/miliar" format
        $formattedPbbkb = $this->formatToIndonesianScale($totalPbbkb);

        // Get total verified Wapu
        $totalWapu = User::whereHas('userDetail', function ($query) {
            $query->where('is_verified', true);
        })->count();

        // Get count of verified inputs
        $verifiedInputs = Pelaporan::where('is_verified', true)->count();

        // Get count of ongoing inputs
        $ongoingInputs = Pelaporan::where('is_sent_to_admin', true)
            ->where('is_verified', false)
            ->count();

        // Generate chart data for paid PBBKB by month for the selected year
        $chartData = $this->generateChartData($year);

        return response()->json([
            'totalPbbkb' => $totalPbbkb,
            'formattedPbbkb' => $formattedPbbkb,
            'totalWapu' => $totalWapu,
            'verifiedInputs' => $verifiedInputs,
            'ongoingInputs' => $ongoingInputs,
            'chartData' => $chartData
        ]);
    }

    private function generateChartData($year)
    {
        // Indonesian month names
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Initialize arrays for all months with zero values
        $months = array_values($monthNames);
        $values = array_fill(0, 12, 0);

        // Get monthly PBBKB totals from paid invoices for the specified year
        $monthlyData = Sptpd::select(
            DB::raw('MONTH(sptpds.tanggal) as month'),
            DB::raw('SUM(sptpds.total_pbbkb) as total')
        )
            ->join('pelaporans', 'sptpds.pelaporan_id', '=', 'pelaporans.id')
            ->where('pelaporans.is_paid', true)
            ->whereYear('sptpds.tanggal', $year)
            ->groupBy(DB::raw('MONTH(sptpds.tanggal)'))
            ->orderBy('month')
            ->get();

        // Fill in the data for months that have values
        foreach ($monthlyData as $data) {
            // Adjust for zero-based array (months are 1-based)
            $index = $data->month - 1;
            $values[$index] = (float) $data->total;
        }

        return [
            'months' => $months,
            'values' => $values
        ];
    }

    private function formatToIndonesianScale($number)
    {
        if ($number >= 1000000000) {
            // Format to miliar (billion)
            $value = number_format($number / 1000000000, 1, ',', '.');
            return $value . ' miliar';
        } elseif ($number >= 1000000) {
            // Format to juta (million)
            $value = number_format($number / 1000000, 1, ',', '.');
            return $value . ' juta';
        } elseif ($number >= 1000) {
            // Format to ribu (thousand)
            $value = number_format($number / 1000, 1, ',', '.');
            return $value . ' ribu';
        } else {
            // Format smaller numbers
            return number_format($number, 0, ',', '.');
        }
    }
}
