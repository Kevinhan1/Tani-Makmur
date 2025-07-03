<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IpProtect
{
    public function handle(Request $request, Closure $next)
    {
        $allowedIps = explode(',', env('ALLOWED_IPS'));
        $clientIp = $request->ip();

        if (!in_array($clientIp, $allowedIps)) {
            abort(404, 'This page could not be found.');
        }

        return $next($request);
    }
}
