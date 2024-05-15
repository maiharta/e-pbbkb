<?php

namespace App\Http\Middleware;

use App\Models\Pelaporan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePelaporanIsNotSendToAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pelaporan = Pelaporan::where('ulid', $request->ulid)->where('is_sent_to_admin', true)->first();
        if($pelaporan){
            return redirect()->back()->with('error', 'Data telah terkirim ke admin. Tidak dapat melakukan perubahan kecuali revisi');
        }
        return $next($request);
    }
}
