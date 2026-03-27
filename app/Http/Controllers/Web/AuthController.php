<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PlayerRegisterRequest;
use App\Models\Customer;
use App\Http\Requests\Auth\WebLoginRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function createRegister(): View
    {
        return view('auth.register');
    }

    public function store(WebLoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt([
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'active' => true,
        ], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Las credenciales proporcionadas no son validas.']);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->forceFill(['last_login_at' => now()])->save();

        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function register(PlayerRegisterRequest $request): RedirectResponse
    {
        $playerRole = Role::query()->where('code', User::ROLE_PLAYER)->firstOrFail();

        $user = User::query()->create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->validated('namee'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password' => $request->validated('password'),
            'role_id' => $playerRole->id,
            'active' => true,
            'email_verified_at' => now(),
        ]);

        Customer::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'uuid' => (string) Str::uuid(),
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'notes' => null,
                'credit_balance' => 0,
                'active' => true,
            ],
        );

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('account.dashboard')->with('success', 'Tu cuenta de jugador ya esta lista.');
    }

    public function destroy(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesion cerrada correctamente.');
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->hasRole(User::ROLE_PLAYER)) {
            return redirect()->intended(route('account.dashboard'))->with('success', 'Bienvenido a tu portal de jugador.');
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Bienvenido de nuevo.');
    }
}
