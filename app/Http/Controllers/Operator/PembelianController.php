<?php

namespace App\Http\Controllers\Operator;

use App\Models\Sektor;
use App\Models\JenisBbm;
use App\Models\Kabupaten;
use App\Models\Pelaporan;
use App\Models\Pembelian;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Imports\Operator\Pelaporan\PembelianImport;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exports\Operator\Pelaporan\TemplateImportPembelianExport;

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
        $jenis_bbms = JenisBbm::all();
        return view('pages.operator.pelaporan.pembelian.create', compact(
            'pelaporan',
            'jenis_bbms'
        ));
    }

    public function store(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)
            ->where('ulid', $ulid)
            ->firstOrFail();
        $request->validate([
            'penjual' => 'required',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
            'sisa_volume' => 'required',
            'nomor_kuitansi' => 'required',
            'tanggal' => 'required|date:Y-m-d',
            'alamat' => 'required',
        ]);

        try {
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $pelaporan->pembelian()->create([
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'penjual' => $request->penjual,
                'volume' => $request->volume,
                'sisa_volume' => $request->sisa_volume,
                'nomor_kuitansi' => $request->nomor_kuitansi,
                'tanggal' => $request->tanggal,
                'alamat' => $request->alamat,
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
        $jenis_bbms = JenisBbm::all();

        return view('pages.operator.pelaporan.pembelian.edit', compact(
            'pelaporan',
            'pembelian',
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
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
            'sisa_volume' => 'required',
            'nomor_kuitansi' => 'required',
            'tanggal' => 'required|date:Y-m-d',
            'alamat' => 'required',
        ]);

        try {
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $pembelian->update([
                'sektor_id' => $request->sektor_id,
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'penjual' => $request->penjual,
                'volume' => $request->volume,
                'sisa_volume' => $request->sisa_volume,
                'nomor_kuitansi' => $request->nomor_kuitansi,
                'tanggal' => $request->tanggal,
                'alamat' => $request->alamat,
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

    public function downloadTemplateImport(Request $request)
    {
        return Excel::download(
            new TemplateImportPembelianExport(),
            'Template Import Pembelian ' . date('d-m-Y') . '.xlsx'
        );
    }

    public function import(Request $request, $ulid)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $pelaporan = Pelaporan::where('ulid', $ulid)->firstOrFail();

        $file = $request->file('file');
        try {
            Excel::import(new PembelianImport($pelaporan), $file);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = collect();
            foreach ($failures as $failure) {
                if (!$errors->has($failure->row())) {
                    $errors->put($failure->row(), collect([
                        $failure->attribute() => implode(", ", $failure->errors()),
                    ]));
                } else {
                    $errors->get($failure->row())->put($failure->attribute(), implode(", ", $failure->errors()));
                }
            }

            // mkdir if folder not exist storage/app/public/error-import-validation
            if (!Storage::exists('public/error-import-validation/pembelian')) {
                Storage::makeDirectory('public/error-import-validation/pembelian');
            }

            $filepath = storage_path('app/public/error-import-validation/pembelian/');
            $filename = Str::uuid() . '.txt';
            $file = fopen($filepath . $filename, 'w');
            foreach ($errors as $key => $error) {
                fwrite($file, "Baris " . $key . " : " . PHP_EOL);
                foreach ($error as $key => $value) {
                    fwrite($file, "- " . $key . " : " . $value . PHP_EOL);
                }
            }
            fclose($file);

            return redirect()->back()->with('error-validation', [
                'message' => 'Import gagal, silahkan download dan cek file pesan error',
                'file' => Storage::url('public/error-import-validation/pembelian/' . $filename),
            ]);
        }

        return redirect()->route('pelaporan.pembelian.index', $ulid)->with('success', 'Import data berhasil');
    }
}
