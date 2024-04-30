<?php

namespace App\Http\Controllers\Operator;

use App\Models\Sektor;
use App\Models\JenisBbm;
use App\Models\Kabupaten;
use App\Models\Pelaporan;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PembelianController extends Controller
{
    public function index(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();
        $pembelians = $pelaporan->pembelian()->get();
        return view('pages.operator.pelaporan.pembelian.index', compact('pelaporan', 'pembelians'));
    }

    public function create(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();
        $kabupatens = Kabupaten::all();
        $sektors = Sektor::all();
        $jenis_bbms = JenisBbm::all();
        return view('pages.operator.pelaporan.pembelian.create', compact(
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
            'penjual' => 'required',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
        ]);

        try {
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $pelaporan->pembelian()->create([
                'kabupaten_id' => $request->kabupaten_id,
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'penjual' => $request->penjual,
                'volume' => $request->volume,
            ]);

            return redirect()->route('pelaporan.pembelian.index', $pelaporan->ulid)->with('success', 'Berhasil menambahkan data pembelian');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Hubungi administrator');
        }
    }

    public function edit(Request $request, $ulid, $pembelian)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)
            ->whereHas('pembelian', function ($query) use ($pembelian) {
                $query->where('ulid', $pembelian);
            })
            ->firstOrFail();
        $pembelian = Pembelian::where('ulid', $pembelian)->firstOrFail();
        $kabupatens = Kabupaten::all();
        $sektors = Sektor::all();
        $jenis_bbms = JenisBbm::all();

        return view('pages.operator.pelaporan.pembelian.edit', compact(
            'pelaporan',
            'pembelian',
            'kabupatens',
            'sektors',
            'jenis_bbms'
        ));
    }
    public function update(Request $request, $ulid, $pembelian)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)
            ->whereHas('pembelian', function ($query) use ($pembelian) {
                $query->where('ulid', $pembelian);
            })
            ->firstOrFail();
        $pembelian = Pembelian::where('ulid', $pembelian)->firstOrFail();

        $request->validate([
            'penjual' => 'required',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required'
        ]);

        try {
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $pembelian->update([
                'kabupaten_id' => $request->kabupaten_id,
                'sektor_id' => $request->sektor_id,
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'penjual' => $request->penjual,
                'volume' => $request->volume,
            ]);
            return redirect()->route('pelaporan.pembelian.index', $pelaporan->ulid)->with('success', 'Data berhasil diubah');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server. Hubungi administrator');
        }
    }

    public function destroy(Request $request, $ulid, $pembelian)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)
            ->whereHas('pembelian', function ($query) use ($pembelian) {
                $query->where('ulid', $pembelian);
            })
            ->firstOrFail();
        $pembelian = Pembelian::where('ulid', $pembelian)->firstOrFail();

        try {
            $pembelian->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server. Hubungi administrator'
            ], 500);
        }
    }
}
