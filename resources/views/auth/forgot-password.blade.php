@extends('layouts.auth', ['title' => 'Recuperar contrasena | Card Bastion'])

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Recuperacion</p>
    <h1 class="mt-3 text-3xl font-black uppercase tracking-[0.06em] text-stone-900">Recuperar contrasena</h1>
    <p class="mt-3 text-sm leading-7 text-stone-600">Escribe el correo de tu cuenta y te enviaremos un enlace para restablecer el acceso.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf
        <div class="field">
            <label for="email">Correo</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>

        <button class="btn btn-primary w-full" type="submit">Enviar enlace de recuperacion</button>
    </form>

    <a
        href="{{ route('login') }}"
        class="mt-4 inline-flex w-full items-center justify-center rounded-full border border-[color:var(--color-brand-200)] bg-white px-5 py-3 text-sm font-semibold text-[color:var(--color-brand-600)] transition hover:border-[color:var(--color-brand-400)] hover:bg-[color:var(--color-brand-50)]"
    >
        Volver al login
    </a>
@endsection
