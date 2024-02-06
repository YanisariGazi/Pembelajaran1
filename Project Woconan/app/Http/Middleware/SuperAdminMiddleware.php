<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle($request, Closure $next)
    // {
    //     if ($request->user() && $request->user()->role === 'super_admin') {
    //         return $next($request);
    //     }

    //     return response()->json(['message' => 'Maaf, akses ditolak. Anda harus menjadi super admin untuk mengakses ini.'], 403);
    // }
}
