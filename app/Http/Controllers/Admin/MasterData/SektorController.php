<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Models\Sektor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SektorController extends Controller
{
    public function index(Request $request)
    {
        $sektors = Sektor::all();
        return view('pages.admin.master-data.sektor.index', compact('sektors'));
    }

    public function create()
    {
        return view('pages.admin.master-data.sektor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'persentase_tarif' => 'required|numeric|min:0|max:100',
        ]);

        try{
            if(Sektor::where('kode', $request->kode)->exists()){
                return redirect()->back()->with('error', 'Kode sektor telah digunakan')->withInput();
            }
            Sektor::create([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'persentase_tarif' => $request->persentase_tarif,
            ]);
        }catch(\Exception $e){
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Hubungi Administrator');
        }

        return redirect()->route('master-data.sektor.index')->with('success', 'Sektor berhasil ditambahkan');
    }

    public function edit(Request $request, $ulid)
    {
        $sektor = Sektor::where('ulid', $ulid)->firstOrFail();
        return view('pages.admin.master-data.sektor.edit', compact('sektor'));
    }

    public function update(Request $request, $ulid)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:sektors,kode,' . $ulid . ',ulid',
            'nama' => 'required|string|max:255',
            'persentase_tarif' => 'required|numeric|min:0|max:100',
        ]);

        try{
            $sektor = Sektor::where('ulid', $ulid)->firstOrFail();
            $sektor->update([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'persentase_tarif' => $request->persentase_tarif,
            ]);
        }catch(\Exception $e){
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Hubungi Administrator');
        }

        return redirect()->route('master-data.sektor.index')->with('success', 'Sektor berhasil diubah');
    }

    // public function destroy(Request $request, $ulid)
    // {
    //     try{
    //         $sektor = Sektor::where('ulid', $ulid)->firstOrFail();
    //         $sektor->delete();
    //     }catch(\Exception $e){
    //         Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
    //         return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Hubungi Administrator');
    //     }

    //     return redirect()->route('master-data.sektor.index');
    // }
}
