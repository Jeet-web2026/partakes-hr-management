<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsurePartakerSessionExists
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasSession = Session::has('partaker_user_data')
            && Session::has('partaker_user_uin')
            && Session::has('partaker_access_token');

        if (! $hasSession) {
            return response()->view('errors.session-expired', [], 419);
        }

        return $next($request);
    }
}