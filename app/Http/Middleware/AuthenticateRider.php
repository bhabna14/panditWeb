<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateRider
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('rider-api')->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}

