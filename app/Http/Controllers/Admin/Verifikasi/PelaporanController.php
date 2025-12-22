<?php

namespace App\Http\Controllers\Admin\Verifikasi;

use App\Models\Pelaporan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PelaporanApprovedMail;
use App\Mail\PelaporanRevisionMail;

class PelaporanController extends Controller
{
    public function index()
    {
        $pelaporans = Pelaporan::with(['user'])->where('is_sent_to_admin', true)->where('is_verified', false)->get();
        return view('pages.admin.verifikasi.pelaporan.index', compact(
            'pelaporans'
        ));
    }

    public function show(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::with(['user', 'penjualan', 'pembelian', 'pelaporanNote'])
            ->where('ulid', $ulid)->where('is_sent_to_admin', true)
            ->where('is_verified', false)
            ->firstOrFail();

        $pelaporan->data_pembelian_terakhir = $pelaporan->pembelian->groupBy('jenis_bbm_id')->map(function ($item) {
            $total_volume = $item->sum('volume');
            $item = $item->sortByDesc('tanggal')->first();
            $item->total_volume = $total_volume;
            return $item;
        })->values();

        $pelaporan->data_penjualan_terakhir = $pelaporan->penjualan->groupBy('jenis_bbm_id')->map(function ($item) {
            $total_volume = $item->sum('volume');
            $item = $item->first();
            $item->total_volume = $total_volume;
            return $item;
        })->values();


        return view('pages.admin.verifikasi.pelaporan.show', compact(
            'pelaporan'
        ));
    }

    public function revisi(Request $request)
    {
        $request->validate([
            'catatan_revisi' => 'required',
            'ulid' => 'required'
        ]);

        $pelaporan = Pelaporan::where('ulid', $request->ulid)
            ->where('is_sent_to_admin', true)
            ->where('is_verified', false)
            ->firstOrFail();

        try {
            $pelaporan->update([
                'catatan_revisi' => $request->catatan_revisi,
                'is_sent_to_admin' => false,
                'is_sptpd_canceled' => false,
            ]);

            // Send email notification
            Mail::to($pelaporan->user->email)->send(new PelaporanRevisionMail($pelaporan, $request->catatan_revisi));

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil melakukan revisi permohonan'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan catatan revisi. Hubungi administrator'
            ]);
        }
    }

    public function approve(Request $request)
    {
        $request->validate([
            'ulid' => 'required',
        ]);

        $pelaporan = Pelaporan::with('pelaporanNote')
            ->where('ulid', $request->ulid)
            ->where('is_sent_to_admin', true)
            ->where('is_verified', false)
            ->firstOrFail();

        if ($pelaporan->pelaporanNote->where('is_active', true)->where('status', 'danger')->count() != 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan validasi. Terdapat data pelaporan yang belum sesuai'
            ]);
        }

        DB::beginTransaction();
        try {
            $pelaporan->update([
                'catatan_revisi' => null,
                'is_verified' => true,
                'verified_at' => now(),
                'is_sptpd_canceled' => false,
            ]);

            // Send email notification
            Mail::to($pelaporan->user->email)->send(new PelaporanApprovedMail($pelaporan));

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil melakukan validasi permohonan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat melakukan validasi. Hubungi administrator'
            ]);
        }
    }

    public function penjualanTable(Request $request, $ulid)
    {
        if ($request->ajax()) {
            $start = $request->input('start');
            $length = $request->input('length');
            $draw = $request->input('draw');
            $search = $request->input('search');

            // Query
            $query = Penjualan::with(['jenisBbm', 'sektor'])->whereHas('pelaporan', function ($query) use ($ulid) {
                $query->where('ulid', $ulid)->where('is_sent_to_admin', true)->where('is_verified', false);
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

            // Offset and limit
            if ($start != 0 || $length != -1) {
                $query = $query->offset($start)
                    ->limit($length);
            }

            // Get data
            $records = $query
                ->get()
                ->map(function ($order) {
                    if($order->is_wajib_pajak){
                        $is_wajib_pajak = '<span class="w-100 badge bg-success">Ya</span>';
                    } else {
                        $is_wajib_pajak = '<span class="w-100 badge bg-secondary">Tidak</span>';
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
                        'pbbkb_sistem' => 'Rp. ' . number_format($order->pbbkb_sistem, 2, ',', '.'),
                        'is_pbbkb_match' => $order->pbbkb == $order->pbbkb_sistem
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
