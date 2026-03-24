@extends('layouts.auth', ['title' => 'Registro | Card Bastion'])

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Crear cuenta</p>
    <h1 class="mt-3 text-3xl font-black uppercase tracking-[0.06em] text-stone-900">Portal del jugador</h1>
    <p class="mt-3 text-sm leading-7 text-stone-600">Registra tu cuenta para comprar en la tienda y preparar tu historial competitivo.</p>

    <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
        @csrf
        <div class="field">
            <label for="name">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="field">
                <label for="email">Correo</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="field">
                <label for="phone">Telefono</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}">
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div class="field">
                <label for="password">Contrasena</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirmar contrasena</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
        </div>
        <button class="btn btn-primary w-full" type="submit">Crear mi cuenta</button>
    </form>

    <p class="mt-6 text-sm text-stone-600">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-semibold text-[color:var(--color-brand-600)]">Inicia sesion</a>
    </p>
@endsection
