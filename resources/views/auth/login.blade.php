@extends('layouts.auth', ['title' => 'Login | Card Bastion'])

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Acceso</p>
    <h1 class="mt-3 text-3xl font-black uppercase tracking-[0.06em] text-stone-900">Entrar a Card Bastion</h1>
    <p class="mt-3 text-sm leading-7 text-stone-600">Administra la operacion o entra a tu cuenta de jugador desde el mismo proyecto.</p>

    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
        @csrf
        <div class="field">
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="field">
            <label for="password">Contrasena</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button class="btn btn-primary w-full" type="submit">Entrar</button>
    </form>

    <div class="mt-6 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-4 text-sm text-stone-600">
        <p class="font-semibold text-stone-800">Demo rapido</p>
        <p class="mt-2">Admin: <code>admin@cardbastion.test</code> / <code>password</code></p>
        <p>Jugador: <code>player@cardbastion.test</code> / <code>password</code></p>
    </div>

    <p class="mt-6 text-sm text-stone-600">
        ¿Eres jugador nuevo?
        <a href="{{ route('register') }}" class="font-semibold text-[color:var(--color-brand-600)]">Crea tu cuenta</a>
    </p>
@endsection
