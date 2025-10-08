<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DownloadFileController extends Controller
{
    public function download(Request $request)
    {
        $request->validate([
            'uid' => 'required',
            'type' => 'required|in:berkas,laporan,profile_syarat',
        ]);

        switch ($request->type) {
            case 'berkas':
                // $filePath = storage_path('app/public/berkas/' . $file);
                break;
            case 'laporan':
                // $filePath = storage_path('app/public/laporan/' . $file);
                break;
            case 'profile_syarat':
                $user = User::where('ulid', $request->uid)->firstOrFail();
                $file = $user->userDetail->filepath_berkas_persyaratan;

                if (!file_exists(storage_path($file))) {
                    return response()->json(['message' => 'File not found'], 404);
                }
                if(request()->user()->isAdmin() || request()->user()->id === $user->id){
                    return response()->download(storage_path($file));
                }else{
                    return response()->json(['message' => 'Unauthorized'], 403);
                }
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
    }
}
