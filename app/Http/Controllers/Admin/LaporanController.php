<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kabupaten;
use Illuminate\Http\Request;
use App\Exports\LaporanExport;
use App\Exports\PelaporanExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $kabupatens = Kabupaten::all();

        return view('pages.admin.laporan.index', compact(
            'kabupatens',
        ));
    }

    public function exportExcel(Request $request)
    {
        // dd($request->all());
        $periode_awal = request('periode_awal', null);
        $periode_akhir = request('periode_akhir', null);
        $kabupaten_ids = request('kabupaten_id', null);

        return Excel::download(
            new LaporanExport($periode_awal, $periode_akhir, $kabupaten_ids),
            'test.xlsx'
        );
    }

    public function exportPelaporanExcel(Request $request)
    {
        $periode_awal = request('periode_awal_pelaporan', null);
        $periode_akhir = request('periode_akhir_pelaporan', null);
        $status = request('status', null);

        $filename = 'export_pelaporan_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new PelaporanExport($periode_awal, $periode_akhir, $status),
            $filename
        );
    }
}
