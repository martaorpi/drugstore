<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBackpackAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! backpack_auth()->check()) {
            return redirect()->guest(backpack_url('login'));
        }

        return $next($request);
    }
}
