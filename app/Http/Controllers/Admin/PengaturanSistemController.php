<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\PengaturanSistem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\CutiService;
use Illuminate\Support\Facades\Artisan;

class PengaturanSistemController extends Controller
{
    public function index()
    {
        $pengaturan_sistem = PengaturanSistem::all();
        return view('pages.admin.pengaturan-sistem.index', compact(
            'pengaturan_sistem'
        ));
    }
    public function update(Request $request)
    {
        $validated = $request->validate([
            'batas_pelaporan' => 'required',
            'batas_pembayaran' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $old_values = PengaturanSistem::whereIn('key', array_keys($validated))->get();
            foreach ($validated as $key => $value) {
                PengaturanSistem::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            // update seluruh pelaporan when batas not same
            if ($old_values->where('key', 'batas_pembayaran')->first()->value != $request->batas_pembayaran || $old_values->where('key', 'batas_pelaporan')->first()->value != $request->batas_pelaporan) {
                CutiService::updateAllPelaporan();
            }

            if ($old_values->where('key',))
                DB::commit();
            Artisan::call('optimize:clear');
            return back()->with('success', 'Pengaturan berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return back()->with('error', 'Pengaturan gagal diubah');
        }
    }
}
