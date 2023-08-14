<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$levels)
    {
        if(in_array($request->user()->level, $levels)){
            return $next($request);
        }
        return response()->json([
            'message' => 'Mohon Maaf Anda Bukan Role nya'
        ], Response::HTTP_FORBIDDEN);
    }
}
