<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pelaporan;
use App\Models\User;
use Illuminate\Http\Request;

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

        // Get total PBBKB from paid invoices based on pelaporan year
        $totalPbbkb = Pelaporan::query()
            ->join('invoices', 'pelaporans.id', '=', 'invoices.pelaporan_id')
            ->where('invoices.year', $year)
            ->where('invoices.payment_status', 'paid')
            ->sum('invoices.amount');

        // Format total PBBKB in Indonesian "juta/miliar" format
        $formattedPbbkb = number_format($totalPbbkb);

        // Get total verified Wapu (filtered by userDetail->verified_at year)
        $totalWapu = User::whereHas('userDetail', function ($query) use ($year) {
            $query->where('is_verified', true);
        })
            // role operator HasRole 'operator'
            ->whereHas('roles', function ($query) {
                $query->where('name', 'operator');
            })
            ->count();

        // Get count of verified inputs (filtered by pelaporan->tahun)
        $verifiedInputs = Pelaporan::where('is_verified', true)
            ->where('tahun', $year)
            ->count();

        // Get count of ongoing inputs (filtered by pelaporan->tahun)
        $ongoingInputs = Pelaporan::where('is_expired', false)
            ->where('is_verified', false)
            ->where('tahun', $year)
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

        // Get monthly PBBKB totals from paid invoices based on invoice month/year
        $monthlyData = Pelaporan::withWhereHas('invoices', function ($query) use ($year) {
            $query->where('payment_status', 'paid')
                ->selectRaw('SUM(amount) as total');
        })
            ->where('tahun', $year)
            ->groupBy('month')
            ->get();

        // Fill in the data for months that have values
        foreach ($monthlyData as $data) {
            $month = (int) $data->month;
            // $index = $month - 1;

            if ($month >= 1 && $month <= 12) {
                $values[$month] = (float) $data->invoices->first()->total;
            }
        }

        return [
            'months' => $months,
            'values' => $values
        ];
    }
}
