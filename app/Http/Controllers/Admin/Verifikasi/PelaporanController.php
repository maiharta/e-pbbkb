<?php

namespace App\Http\Controllers\Admin\Verifikasi;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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
        $pelaporan = Pelaporan::with(['user', 'penjualan', 'pembelian'])
            ->where('ulid', $ulid)->where('is_sent_to_admin', true)
            ->where('is_verified', false)
            ->firstOrFail();
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

        try{
            $pelaporan->update([
                'catatan_revisi' => $request->catatan_revisi,
                'is_sent_to_admin' => false,
                'is_sptpd_canceled' => false,
            ]);

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
            'ulid' => 'required'
        ]);

        $pelaporan = Pelaporan::where('ulid', $request->ulid)
            ->where('is_sent_to_admin', true)
            ->where('is_verified', false)
            ->firstOrFail();

        try {
            $pelaporan->update([
                'catatan_revisi' => null,
                'is_verified' => true,
                'verified_at' => now(),
                'is_sptpd_canceled' => false,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil melakukan validasi permohonan'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat melakukan validasi. Hubungi administrator'
            ]);
        }
    }
}
