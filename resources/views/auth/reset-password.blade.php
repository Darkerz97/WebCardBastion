@extends('layouts.auth', ['title' => 'Nueva contrasena | Card Bastion'])

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Nueva contrasena</p>
    <h1 class="mt-3 text-3xl font-black uppercase tracking-[0.06em] text-stone-900">Restablecer acceso</h1>
    <p class="mt-3 text-sm leading-7 text-stone-600">Define una nueva contrasena para volver a entrar a tu cuenta.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
        </div>

        <div class="field">
            <label for="password">Nueva contrasena</label>
            <input id="password" type="password" name="password" required>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirmar contrasena</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button class="btn btn-primary w-full" type="submit">Guardar nueva contrasena</button>
    </form>
@endsection
