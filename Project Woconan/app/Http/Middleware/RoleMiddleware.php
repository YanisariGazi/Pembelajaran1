<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // public function handle($request, Closure $next, ...$roles)
    // {
    //     // Periksa apakah pengguna memiliki salah satu peran yang diizinkan
    //     if ($request->user() && in_array($request->user()->role, $roles)) {
    //         return $next($request);
    //     }

    //     // Jika pengguna tidak memiliki peran yang diizinkan, kembalikan respons Forbidden
    //     return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengakses halaman ini'], 403);
    // }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user || !$user->hasRole($roles)) {
            return response()->json(['message' => 'Maaf, kamu tidak memiliki izin untuk mengakses halaman ini'], 403);
        }

        return $next($request);
    }
}

