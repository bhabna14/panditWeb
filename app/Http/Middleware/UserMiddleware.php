<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('users')->check()) {
            // If not a superadmin, redirect or return forbidden response
            return redirect()->route('userlogin'); // Example: Redirect to login page
        }
        return $next($request);
    }

    // public function handle($request, Closure $next, ...$guards)
    //     {
    //         $this->authenticate($request, $guards);

    //         return $next($request);
    //     }

    //     protected function authenticate($request, array $guards)
    //     {
    //         if (empty($guards)) {
    //             $guards = [null];
    //         }

    //         foreach ($guards as $guard) {
    //             if (Auth::guard($guard)->check()) {
    //                 return $this->auth->shouldUse($guard);
    //             }
    //         }

    //         $this->unauthenticated($request, $guards);
    //     }

}
