<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Pelaporan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePelaporanIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pelaporan = Pelaporan::where('ulid', $request->ulid)->where('is_verified', true)->first();
        if (!$pelaporan) {
            return redirect()->back()->with('error', 'Data belum terverifikasi. Tidak dapat melanjutkan aksi');
        }
        return $next($request);
    }
}
