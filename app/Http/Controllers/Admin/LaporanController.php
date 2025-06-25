<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kabupaten;
use Illuminate\Http\Request;
use App\Exports\LaporanExport;
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
}
