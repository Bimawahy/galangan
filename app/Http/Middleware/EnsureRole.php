<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless(in_array($request->user()?->activeRole(), $roles, true), 403, 'Role Anda tidak boleh mengakses halaman ini.');

        return $next($request);
    }
}
