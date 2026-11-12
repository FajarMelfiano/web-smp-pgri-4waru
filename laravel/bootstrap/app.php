<?php

use App\Http\Middleware\EnsureUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserRole::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request): string {
            if ($request->is('ppdb/portal*') || $request->is('ppdb/keluar')) {
                return route('ppdb.masuk');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request): string {
            if (Auth::guard('ppdb')->check() && ($request->is('ppdb/masuk*') || $request->is('ppdb/daftar*'))) {
                return route('ppdb.portal');
            }

            return route('admin');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
