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

                $fullPath = storage_path('app/public/' . $file);

                if (!file_exists($fullPath)) {
                    return abort(404);
                }
                if (request()->user()->isAdmin() || request()->user()->id === $user->id) {
                    return response()->download($fullPath);
                } else {
                    return abort(403);
                }
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
    }
}
