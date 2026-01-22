<?php

namespace App\Http\Controllers\Operator;

use App\Models\Sektor;
use App\Models\JenisBbm;
use App\Models\Kabupaten;
use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Imports\Operator\Pelaporan\PenjualanImport;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exports\Operator\Pelaporan\TemplateImportPenjualanExport;
use App\Models\PelaporanNote;

class PenjualanController extends Controller
{
    public function index(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();
        $penjualans = $pelaporan->penjualan()->get();
        $jenis_bbms = JenisBbm::all();
        $sektors = Sektor::all();
        $kabupatens = Kabupaten::all();
        $transaction_total = $penjualans->count();
        $volume_total = number_format($penjualans->sum('volume'), 0, ',', '.');
        $dpp_total = 'Rp. ' . number_format($penjualans->sum('dpp'), 0, ',', '.');
        $pbbkb_total = 'Rp. ' . number_format($penjualans->sum('pbbkb'), 0, ',', '.');
        return view('pages.operator.pelaporan.penjualan.index', compact(
            'pelaporan',
            'jenis_bbms',
            'sektors',
            'kabupatens',
            'transaction_total',
            'volume_total',
            'dpp_total',
            'pbbkb_total'
        ));
    }

    public function create(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();
        $sektors = Sektor::all();
        $jenis_bbms = JenisBbm::all();
        $kabupatens = Kabupaten::all();
        return view('pages.operator.pelaporan.penjualan.create', compact(
            'pelaporan',
            'sektors',
            'jenis_bbms',
            'kabupatens'
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
            'dpp' => 'required',
            'alamat' => 'required',
            'tanggal' => 'required|date:Y-m-d',
            'nomor_kuitansi' => 'required',
            'pbbkb' => 'required',
            'lokasi_penyaluran' => 'required|in:depot,TBBM',
            'is_wajib_pajak' => 'required|boolean'
        ]);

        try {
            $sektor = Sektor::where('id', $request->sektor_id)->first();
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $pelaporan->penjualan()->create([
                'sektor_id' => $request->sektor_id,
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kabupaten_id' => $request->kabupaten_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'kode_sektor' => $sektor->kode,
                'nama_sektor' => $sektor->nama,
                'persentase_pengenaan_sektor' => $sektor->persentase_pengenaan,
                'pembeli' => $request->pembeli,
                'volume' => $request->volume,
                'dpp' => $request->dpp,
                'alamat' => $request->alamat,
                'tanggal' => $request->tanggal,
                'nomor_kuitansi' => $request->nomor_kuitansi,
                'pbbkb' => $request->pbbkb,
                'lokasi_penyaluran' => $request->lokasi_penyaluran,
                'is_wajib_pajak' => $request->is_wajib_pajak
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
            'sektors',
            'jenis_bbms',
            'kabupatens'
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
            'sektor_id' => 'required|exists:sektors,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'jenis_bbm_id' => 'required|exists:jenis_bbms,id',
            'volume' => 'required',
            'dpp' => 'required',
            'alamat' => 'required',
            'tanggal' => 'required|date:Y-m-d',
            'nomor_kuitansi' => 'required',
            'pbbkb' => 'required',
            'lokasi_penyaluran' => 'required|in:depot,TBBM',
            'is_wajib_pajak' => 'required|boolean'
        ]);

        try {
            $sektor = Sektor::where('id', $request->sektor_id)->first();
            $jenis_bbm = JenisBbm::where('id', $request->jenis_bbm_id)->first();

            $penjualan->update([
                'sektor_id' => $request->sektor_id,
                'jenis_bbm_id' => $request->jenis_bbm_id,
                'kabupaten_id' => $request->kabupaten_id,
                'kode_jenis_bbm' => $jenis_bbm->kode,
                'nama_jenis_bbm' => $jenis_bbm->nama,
                'is_subsidi' => $jenis_bbm->is_subsidi,
                'persentase_tarif_jenis_bbm' => $jenis_bbm->persentase_tarif,
                'kode_sektor' => $sektor->kode,
                'nama_sektor' => $sektor->nama,
                'persentase_pengenaan_sektor' => $sektor->persentase_pengenaan,
                'pembeli' => $request->pembeli,
                'volume' => $request->volume,
                'dpp' => $request->dpp,
                'alamat' => $request->alamat,
                'tanggal' => $request->tanggal,
                'nomor_kuitansi' => $request->nomor_kuitansi,
                'pbbkb' => $request->pbbkb,
                'lokasi_penyaluran' => $request->lokasi_penyaluran,
                'is_wajib_pajak' => $request->is_wajib_pajak
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

        Log::info('Deleting penjualan id: ' . $penjualan->id);
        // delete all relation
        PelaporanNote::where('penjualan_id', $penjualan->id)->forceDelete();

        $penjualan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function downloadTemplateImport(Request $request)
    {
        return Excel::download(
            new TemplateImportPenjualanExport(),
            'Template Import Penjualan ' . date('d-m-Y') . '.xlsx'
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
            Excel::import(new PenjualanImport($pelaporan), $file);
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
            if (!Storage::exists('public/error-import-validation/penjualan')) {
                Storage::makeDirectory('public/error-import-validation/penjualan');
            }

            $filepath = storage_path('app/public/error-import-validation/penjualan/');
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
                'file' => Storage::url('public/error-import-validation/penjualan/' . $filename),
            ]);
        }

        return redirect()->route('pelaporan.penjualan.index', $ulid)->with('success', 'Import data berhasil');
    }

    public function resetData(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)
            ->where('ulid', $ulid)
            ->firstOrFail();

        // Check if pelaporan is already sent to admin
        if ($pelaporan->is_sent_to_admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat menghapus data. Pelaporan sudah dikirim ke admin.'
            ], 403);
        }

        try {
            $count = $pelaporan->penjualan()->count();
            $pelaporan->penjualan()->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Berhasil menghapus {$count} data penjualan"
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server. Hubungi administrator'
            ], 500);
        }
    }

    public function table(Request $request, $ulid)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = Penjualan::with(['jenisBbm', 'sektor', 'pelaporan'])->whereHas('pelaporan', function ($query) use ($ulid) {
                $query->where('ulid', $ulid)->where('user_id', auth()->user()->id);
            });

            // Total records
            $totalRecords = $query->count();

            // Filter records
            if ($search) {
                $query = $query->where(function ($query) use ($search) {
                    $query->where('nomor_kuitansi', 'like', "%{$search}%");
                });

                // filtered records count
                $totalFiltered = $query->count();
            } else {
                $totalFiltered = $totalRecords;
            }

            // filter by jenis_bbm_id
            if ($request->has('jenis_bbm_id') && !empty($request->input('jenis_bbm_id'))) {
                $query = $query->where('jenis_bbm_id', $request->input('jenis_bbm_id'));
            }
            // filter by sektor_id
            if ($request->has('sektor_id') && !empty($request->input('sektor_id'))) {
                $query = $query->where('sektor_id', $request->input('sektor_id'));
            }
            // filter by kabupaten_id
            if ($request->has('kabupaten_id') && !empty($request->input('kabupaten_id'))) {
                $query = $query->where('kabupaten_id', $request->input('kabupaten_id'));
            }

            // Offset and limit
            if ($start != 0 || $length != -1) {
                $query = $query->offset($start)
                    ->limit($length);
            }

            // Get data
            $records = $query
                ->get()
                ->map(function ($order) {
                    if ($order->is_wajib_pajak) {
                        $is_wajib_pajak = '<span class="w-100 badge bg-success">Ya</span>';
                    } else {
                        $is_wajib_pajak = '<span class="w-100 badge bg-secondary">Tidak</span>';
                    }

                    // action
                    $action = '';
                    if (!$order->pelaporan->is_sent_to_admin) {
                        $action .= '<div class="dropdown">
                                        <button aria-expanded="false"
                                                class="btn"
                                                data-bs-toggle="dropdown"
                                                id="dropdownMenuButton1"
                                                type="button">
                                            <i class="isax isax-more"></i>
                                        </button>
                                        <ul aria-labelledby="dropdownMenuButton1"
                                            class="dropdown-menu">
                                            <li><a class="dropdown-item"
                                                   href="' . route('pelaporan.penjualan.edit', ['penjualan' => $order->ulid, 'ulid' => $order->pelaporan->ulid]) . '">Edit</a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item"
                                                        onclick="hapus(\'' . route('pelaporan.penjualan.destroy', ['penjualan' => $order->ulid, 'ulid' => $order->pelaporan->ulid]) . '\')">
                                                    Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>';
                    } else {
                        $action .= 'Tidak ada aksi';
                    }
                    return [
                        'pembeli' => $order->pembeli,
                        'nomor_kuitansi' => $order->nomor_kuitansi,
                        'tanggal' => $order->tanggal_formatted,
                        'jenis_bbm' => $order->jenisBbm->nama . ' - ' . ($order->jenisBbm->is_subsidi ? 'Subsidi' : 'Non Subsidi'),
                        'sektor' => $order->sektor->nama,
                        'volume' => number_format($order->volume, 0, ',', '.'),
                        'dpp' => 'Rp. ' . number_format($order->dpp, 2, ',', '.'),
                        'is_wajib_pajak' => $is_wajib_pajak,
                        'pbbkb' => 'Rp. ' . number_format($order->pbbkb, 2, ',', '.'),
                        'is_pbbkb_match' => $order->is_pbbkb_match,
                        'pbbkb_sistem' => 'Rp. ' . number_format($order->pbbkb_sistem, 2, ',', '.'),
                        'action' => $action,
                    ];
                });

            // JSON response
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalFiltered,
                'data' => $records,
            ]);
        }

    }
}
