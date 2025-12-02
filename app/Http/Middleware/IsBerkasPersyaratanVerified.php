<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsBerkasPersyaratanVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->userDetail?->is_verified) {
            return $next($request);
        }else{
            return redirect()->route('profile.index')->with('error', 'Berkas persyaratan belum selesai');
        }
    }
}
