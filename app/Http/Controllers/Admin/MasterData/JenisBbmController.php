<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Models\JenisBbm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class JenisBbmController extends Controller
{
    public function index(Request $request)
    {
        $jenis_bbms = JenisBbm::all();
        return view('pages.admin.master-data.jenis-bbm.index', compact('jenis_bbms'));
    }

    public function create()
    {
        return view('pages.admin.master-data.jenis-bbm.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'is_subsidi' => 'required|in:0,1', // Add this line
            'persentase_tarif' => 'required|numeric|min:0|max:100',
        ]);

        try {
            if (JenisBbm::where('kode', $request->kode)->exists()) {
                return redirect()->back()->with('error', 'Kode Jenis BBM telah digunakan')->withInput();
            }
            JenisBbm::create([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'is_subsidi' => $request->is_subsidi, // Add this line
                'persentase_tarif' => $request->persentase_tarif,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Hubungi Administrator');
        }

        return redirect()->route('master-data.jenis-bbm.index')->with('success', 'Jenis BBM berhasil ditambahkan');
    }

    public function edit(Request $request, $ulid)
    {
        $jenis_bbm = JenisBbm::where('ulid', $ulid)->firstOrFail();
        return view('pages.admin.master-data.jenis-bbm.edit', compact('jenis_bbm'));
    }

    public function update(Request $request, $ulid)
    {
        $request->validate([
            'kode' => 'required|string|max:255|unique:jenis_bbms,kode,' . $ulid . ',ulid',
            'nama' => 'required|string|max:255',
            'is_subsidi' => 'required|in:0,1', // Add this line
            'persentase_tarif' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $jenis_bbm = JenisBbm::where('ulid', $ulid)->firstOrFail();
            $jenis_bbm->update([
                'kode' => $request->kode,
                'nama' => $request->nama,
                'is_subsidi' => $request->is_subsidi, // Add this line
                'persentase_tarif' => $request->persentase_tarif,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem. Hubungi Administrator');
        }

        return redirect()->route('master-data.jenis-bbm.index')->with('success', 'Jenis BBM berhasil diubah');
    }
}
