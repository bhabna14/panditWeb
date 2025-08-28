<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     if ($request->user() && $request->user()->role === 'admin') {
    //         return $next($request);
    //     }

    //     abort(403, 'Unauthorized action.');
    // }
    // public function handle(Request $request, Closure $next, $role)
    // {
        // Check if the user is authenticated
        // if (!Auth::check()) {
        //     // If user is not authenticated, redirect to login page
        //     return redirect()->route('login');
        // }

        // // Get the authenticated user
        // $user = Auth::user();

        // // Check if the user has the required role
        // if ($user->role !== $role) {
        //     // If not, redirect to home page or show unauthorized error page
        //     abort(403, 'Unauthorized action.');
        // }
    //     if (!Auth::guard('admins')->check()) {
    //         // If not a superadmin, redirect or return forbidden response
    //         return redirect()->route('login'); // Example: Redirect to login page
    //     }

    //     return $next($request);
    // }


    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated with the 'admins' guard
        if (!Auth::guard('admins')->check()) {
            // If not authenticated, redirect to the login page
            return redirect()->route('adminlogin');
        }
    
        // Proceed with the request if authenticated
        return $next($request);
    }
}
