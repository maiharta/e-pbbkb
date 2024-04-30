<?php

namespace App\Http\Controllers\Operator;

use App\Models\Sektor;
use App\Models\JenisBbm;
use App\Models\Kabupaten;
use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PenjualanController extends Controller
{
    public function index(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();
        $penjualans = $pelaporan->penjualan()->with(['kabupaten', 'sektor', 'jenisBbm'])->get();
        return view('pages.operator.pelaporan.penjualan.index', compact('pelaporan', 'penjualans'));
    }

    public function create(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();
        $kabupatens = Kabupaten::all();
        $sektors = Sektor::all();
        $jenis_bbms = JenisBbm::all();
        return view('pages.operator.pelaporan.penjualan.create', compact(
            'pelaporan',
            'kabupatens',
            'sektors',
            'jenis_bbms'
        ));
    }

    public function store(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();
        $request->validate([
            'pembeli' => 'required',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'sektor_id' => 'required|exists:sektors,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
            'dpp' => 'required'
        ]);

        try {
            $sektor = Sektor::where('id', $request->sektor_id)->first();
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $pelaporan->penjualan()->create([
                'kabupaten_id' => $request->kabupaten_id,
                'sektor_id' => $request->sektor_id,
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'kode_sektor' => $sektor->kode,
                'nama_sektor' => $sektor->nama,
                'persentase_tarif_sektor' => $sektor->persentase_tarif,
                'pembeli' => $request->pembeli,
                'volume' => $request->volume,
                'dpp' => $request->dpp,
            ]);

            return redirect()->route('pelaporan.penjualan.index', $pelaporan->ulid)->with('success', 'Berhasil menambahkan data penjualan');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Hubungi administrator');
        }
    }

    public function edit(Request $request, $ulid, $penjualan)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)
            ->whereHas('penjualan', function ($query) use ($penjualan) {
                $query->where('ulid', $penjualan);
            })
            ->firstOrFail();
        $penjualan = Penjualan::where('ulid', $penjualan)->firstOrFail();
        $kabupatens = Kabupaten::all();
        $sektors = Sektor::all();
        $jenis_bbms = JenisBbm::all();

        return view('pages.operator.pelaporan.penjualan.edit', compact(
            'pelaporan',
            'penjualan',
            'kabupatens',
            'sektors',
            'jenis_bbms'
        ));
    }
    public function update(Request $request, $ulid, $penjualan)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)
            ->whereHas('penjualan', function ($query) use ($penjualan) {
                $query->where('ulid', $penjualan);
            })
            ->firstOrFail();
        $penjualan = Penjualan::where('ulid', $penjualan)->firstOrFail();

        $request->validate([
            'pembeli' => 'required',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'sektor_id' => 'required|exists:sektors,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
            'dpp' => 'required'
        ]);

        try {
            $sektor = Sektor::where('id', $request->sektor_id)->first();
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $penjualan->update([
                'kabupaten_id' => $request->kabupaten_id,
                'sektor_id' => $request->sektor_id,
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'kode_sektor' => $sektor->kode,
                'nama_sektor' => $sektor->nama,
                'persentase_tarif_sektor' => $sektor->persentase_tarif,
                'pembeli' => $request->pembeli,
                'volume' => $request->volume,
                'dpp' => $request->dpp,
            ]);
            return redirect()->route('pelaporan.penjualan.index', $pelaporan->ulid)->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Hubungi administrator');
        }
    }

    public function destroy(Request $request, $ulid, $penjualan)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)
            ->whereHas('penjualan', function ($query) use ($penjualan) {
                $query->where('ulid', $penjualan);
            })
            ->firstOrFail();
        $penjualan = Penjualan::where('ulid', $penjualan)->firstOrFail();

        $penjualan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
