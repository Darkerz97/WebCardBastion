<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ApiLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(ApiLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'active' => true])) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no son válidas.'],
            ]);
        }

        /** @var User $user */
        $user = User::query()->with('role')->where('email', $credentials['email'])->firstOrFail();
        abort_unless($user->hasRole([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_CASHIER]), 403, 'Tu cuenta no tiene acceso a la API del POS.');
        $user->forceFill(['last_login_at' => now()])->save();

        $token = $user->createToken(
            $request->string('device_name')->toString() ?: 'api-client',
            $this->abilitiesFor($user),
        )->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'user' => new UserResource($user),
        ], 'Sesión iniciada correctamente.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse(null, 'Sesión cerrada correctamente.');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()?->loadMissing('role');

        return $this->successResponse(new UserResource($user), 'Perfil obtenido correctamente.');
    }

    private function abilitiesFor(User $user): array
    {
        if ($user->hasRole(User::ROLE_ADMIN)) {
            return ['*'];
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            return [
                'sync:heartbeat',
                'sync:read',
                'sync:upload-sales',
                'products:read',
                'products:write',
                'stock:write',
                'categories:read',
                'customers:read',
                'customers:write',
                'devices:read',
                'devices:write',
                'sales:read',
                'sales:write',
                'payments:write',
            ];
        }

        return [
            'sync:heartbeat',
            'sync:read',
            'sync:upload-sales',
            'products:read',
            'stock:write',
            'categories:read',
            'customers:read',
            'customers:write',
            'devices:read',
            'sales:read',
            'sales:write',
            'payments:write',
        ];
    }
}
