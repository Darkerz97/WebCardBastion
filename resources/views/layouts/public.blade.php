<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Card Bastion' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hero-glow min-h-screen">
    <header class="border-b border-stone-200/80 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('store.home') }}" class="flex items-center gap-3">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-[color:var(--color-night)] text-lg font-black tracking-[0.25em] text-amber-300">CB</span>
                <span>
                    <span class="block text-base font-black uppercase tracking-[0.28em] text-stone-900">Card Bastion</span>
                    <span class="block text-xs tracking-[0.22em] text-stone-500">Store + Player Hub</span>
                </span>
            </a>

            <nav class="hidden items-center gap-5 text-sm font-medium text-stone-600 lg:flex">
                <a href="{{ route('store.catalog') }}" class="transition hover:text-[color:var(--color-brand-600)]">Tienda</a>
                <a href="{{ route('cart.index') }}" class="transition hover:text-[color:var(--color-brand-600)]">Carrito</a>
                @auth
                    <a href="{{ route('account.tournaments.index') }}" class="transition hover:text-[color:var(--color-brand-600)]">Torneos</a>
                    <a href="{{ route('account.dashboard') }}" class="transition hover:text-[color:var(--color-brand-600)]">Mi cuenta</a>
                    @if (auth()->user()?->isBackofficeUser())
                        <a href="{{ route('dashboard') }}" class="transition hover:text-[color:var(--color-brand-600)]">Admin</a>
                    @endif
                @endauth
            </nav>

            <div class="flex items-center gap-3">
                <a class="btn btn-secondary" href="{{ route('cart.index') }}">Carrito ({{ $cartItemCount ?? 0 }})</a>
                @auth
                    <span class="hidden text-sm text-stone-500 sm:inline">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-secondary" type="submit">Salir</button>
                    </form>
                @else
                    <a class="btn btn-secondary" href="{{ route('login') }}">Entrar</a>
                    <a class="btn btn-primary" href="{{ route('register') }}">Crear cuenta</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    <p class="font-semibold">Hay datos por corregir.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
