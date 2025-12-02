<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Rules\SafePdfRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        $kabupaten = Kabupaten::all();
        $user_detail = auth()->user()->userDetail;
        $is_user_readonly = $user_detail ? $user_detail->is_user_readonly : false;
        return view('pages.profile.index', compact(
            'kabupaten',
            'user_detail',
            'is_user_readonly'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'name' => 'required',
            'npwpd' => 'required',
            'nomor_telepon' => 'required',
            'alamat' => 'required',
            'berkas' => ['required', 'max:2048', 'mimes:pdf', new SafePdfRule()],
        ],[
            'max' => 'Berkas yang diunggah tidak boleh lebih dari 2MB'
        ])->validate();

        DB::beginTransaction();
        try {
            $berkas = $request->file('berkas');
            $berkasName = $berkas->hashName();
            $filepath_berkas_persyaratan = 'berkas/' . $berkasName;

            $user = auth()->user()->update([
                'name' => $request->name,
            ]);

            $user_detail = auth()->user()->userDetail;
            if ($user_detail) {
                if ($user_detail->is_user_readonly) {
                    return redirect()->back()->with('error', 'Profile belum diverifikasi oleh admin');
                }
                if ($user_detail->catatan_revisi) {
                    $user_detail->update([
                        'kabupaten_id' => $request->kabupaten_id,
                        'npwpd' => $request->npwpd,
                        'nomor_telepon' => $request->nomor_telepon,
                        'alamat' => $request->alamat,
                        'filepath_berkas_persyaratan' => $filepath_berkas_persyaratan,
                        'is_user_readonly' => true,
                        'catatan_revisi' => null
                    ]);
                }
            }else{
                $user_detail = auth()->user()->userDetail()->create([
                    'kabupaten_id' => $request->kabupaten_id,
                    'npwpd' => $request->npwpd,
                    'nomor_telepon' => $request->nomor_telepon,
                    'alamat' => $request->alamat,
                    'is_user_readonly' => true,
                    'filepath_berkas_persyaratan' => $filepath_berkas_persyaratan,
                ]);
            }

            $berkas->storeAs('public/berkas', $berkasName);

            DB::commit();
            return redirect()->back()->with('success', 'Profile berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Profile gagal dibuat');
        }
    }
}
