<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenHasAbility
{
    public function handle(Request $request, Closure $next, string ...$abilities): Response
    {
        $user = $request->user();

        abort_unless($user, 401, 'Autenticacion requerida.');

        if ($abilities === []) {
            return $next($request);
        }

        $hasAbility = collect($abilities)->contains(fn (string $ability): bool => $user->tokenCan($ability) || $user->tokenCan('*'));

        abort_unless($hasAbility, 403, 'El token no tiene permisos para esta operacion.');

        return $next($request);
    }
}
