<?php

namespace App\Http\Controllers\Admin\Verifikasi;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::whereHas('userDetail', function ($query) {
            $query->where('is_verified', false)
                ->where('is_user_readonly', true);
        })->get();

        return view('pages.admin.verifikasi.user.index', compact('users'));
    }

    public function show(Request $request, $ulid)
    {
        $user = User::whereHas('userDetail', function ($query) {
            $query->where('is_verified', false)
                ->where('is_user_readonly', true);
        })->where('ulid', $ulid)->first();

        if (!$user) {
            return redirect()->route('admin.verifikasi.user.index')->with('error', 'User tidak ditemukan');
        }
        return view('pages.admin.verifikasi.user.show', compact('user'));
    }

    public function revisi(Request $request)
    {
        $request->validate([
            'ulid' => 'required',
            'catatan_revisi' => 'required'
        ]);

        $user = User::whereHas('userDetail', function ($query) {
            $query->where('is_verified', false)
                ->where('is_user_readonly', true);
        })->where('ulid', $request->ulid)->firstOr(function () {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        });

        DB::beginTransaction();
        try{
            $user->userDetail->update([
                'catatan_revisi' => $request->catatan_revisi,
                'is_user_readonly' => false
            ]);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Catatan revisi berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan catatan revisi'
            ]);
        }
    }

    public function approve(Request $request)
    {
        $request->validate([
            'ulid' => 'required'
        ]);

        $user = User::whereHas('userDetail', function ($query) {
            $query->where('is_verified', false)
                ->where('is_user_readonly', true);
        })->where('ulid', $request->ulid)->firstOr(function () {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ]);
        });

        DB::beginTransaction();
        try{
            $user->userDetail->update([
                'catatan_revisi' => null,
                'is_verified' => true,
                'verified_at' => now()
            ]);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diverifikasi'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat verifikasi user'
            ]);
        }
    }
}
