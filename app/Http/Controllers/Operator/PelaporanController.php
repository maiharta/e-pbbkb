<?php

namespace App\Http\Controllers\Operator;

use App\Models\Pelaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PelaporanService;

class PelaporanController extends Controller
{
    public function index()
    {
        $pelaporans = Pelaporan::where('user_id', auth()->user()->id)->get();
        return view('pages.operator.pelaporan.index', compact(
            'pelaporans'
        ));
    }

    public function send(Request $request, $ulid)
    {
        $pelaporan = Pelaporan::where('user_id', auth()->user()->id)->where('ulid', $ulid)->firstOrFail();

        // generate pbbkb sistem
        PelaporanService::generatePbbkbSistem($pelaporan);

        // generate note pelaporan
        PelaporanService::generateNote($pelaporan);

        try {
            $pelaporan->update([
                'is_sent_to_admin' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil mengirimkan pelaporan'
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server. Hubungi administrator'
            ]);
        }
    }
}
