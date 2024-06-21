<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PanditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (Auth::guard('pandits')->check()) {
            return $next($request);
        }

        return redirect('pandit.otp');  // Redirect to home or login page if not super admin
    }
}
