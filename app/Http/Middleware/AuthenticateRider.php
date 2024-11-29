<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateRider extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('rider-api')->check()) {
            return $this->auth->shouldUse('rider-api');
        }

        $this->unauthenticated($request, ['rider-api']);
    }
}

