<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiUserCanAccessPos
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $allowedRoles = $roles !== [] ? $roles : [
            User::ROLE_ADMIN,
            User::ROLE_MANAGER,
            User::ROLE_CASHIER,
        ];

        abort_unless($user && $user->hasRole($allowedRoles), 403, 'Tu cuenta no tiene acceso a la API del POS.');

        return $next($request);
    }
}
