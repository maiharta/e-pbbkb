<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\CutiService;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $cutis = Cuti::all();
        return view('pages.admin.master-data.cuti.index', compact('cutis'));
    }

    public function create()
    {
        return view('pages.admin.master-data.cuti.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date:Y-m-d',
            'deskripsi' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            if (Cuti::where('tanggal', $request->tanggal)->exists()) {
                return redirect()->back()->with('error', 'Tanggal telah tersedia. Silahkan update deskripsi')->withInput();
            }
            Cuti::create([
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
            ]);

            $tanggal_carbon = Carbon::createFromFormat('Y-m-d', $request->tanggal);
            CutiService::updateBatasPelaporan($tanggal_carbon->bulan, $tanggal_carbon->tahun);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Hubungi Administrator');
        }

        return redirect()->route('master-data.cuti.index')->with('success', 'Cuti berhasil ditambahkan');
    }

    public function edit(Request $request, $ulid)
    {
        $cuti = Cuti::where('ulid', $ulid)->firstOrFail();
        return view('pages.admin.master-data.cuti.edit', compact('cuti'));
    }

    public function update(Request $request, $ulid)
    {
        $request->validate([
            'tanggal' => 'required|date:Y-m-d|unique:cutis,tanggal,' . $ulid . ',ulid',
            'deskripsi' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $cuti = Cuti::where('ulid', $ulid)->firstOrFail();
            $tanggal_before_carbon = Carbon::createFromFormat('Y-m-d', $cuti->tanggal);
            $tanggal_after_carbon = Carbon::createFromFormat('Y-m-d', $request->tanggal);


            $cuti->update([
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->deskripsi,
            ]);

            if ($tanggal_after_carbon->month != $tanggal_before_carbon->month || $tanggal_after_carbon->year != $tanggal_before_carbon->year) {
                CutiService::updateBatasPelaporan($tanggal_before_carbon->month, $tanggal_before_carbon->year);
                CutiService::updateBatasPelaporan($tanggal_after_carbon->month, $tanggal_after_carbon->year);
            } else {
                CutiService::updateBatasPelaporan($tanggal_after_carbon->month, $tanggal_after_carbon->year);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Hubungi Administrator');
        }

        return redirect()->route('master-data.cuti.index')->with('success', 'Cuti berhasil diubah');
    }
}
