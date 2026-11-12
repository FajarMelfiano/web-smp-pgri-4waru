<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user('web');

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
