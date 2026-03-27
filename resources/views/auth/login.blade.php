@extends('layouts.auth', ['title' => 'Login | Card Bastion'])

@section('content')
    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Acceso</p>
    <h1 class="mt-3 text-3xl font-black uppercase tracking-[0.06em] text-stone-900">Entrar a Card Bastion</h1>
    <p class="mt-3 text-sm leading-7 text-stone-600">Accede de forma segura al panel administrativo o a tu cuenta de jugador desde una sola plataforma.</p>

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
        <div class="flex justify-end">
            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[color:var(--color-brand-600)] transition hover:text-[color:var(--color-brand-500)]">
                Olvide mi contrasena
            </a>
        </div>
        <button class="btn btn-primary w-full" type="submit">Entrar</button>
    </form>

    <a
        href="{{ route('register') }}"
        class="mt-4 inline-flex w-full items-center justify-center rounded-full border border-[color:var(--color-brand-200)] bg-white px-5 py-3 text-sm font-semibold text-[color:var(--color-brand-600)] transition hover:border-[color:var(--color-brand-400)] hover:bg-[color:var(--color-brand-50)]"
    >
        Registrarme como nuevo usuario
    </a>

    <div class="mt-6 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-4 text-sm text-stone-600">
        <p class="font-semibold text-stone-800">Acceso protegido</p>
        <p class="mt-2">Si no recuerdas tu contrasena, utiliza la opcion de recuperacion para restablecer el acceso a tu cuenta.</p>
    </div>

    <p class="mt-6 text-sm text-stone-600">
        ¿Eres jugador nuevo?
        <a href="{{ route('register') }}" class="font-semibold text-[color:var(--color-brand-600)]">Crea tu cuenta aqui</a>
    </p>
@endsection
