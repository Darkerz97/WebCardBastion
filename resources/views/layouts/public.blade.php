<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Card Bastion' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hero-glow min-h-screen">
    <header class="sticky top-0 z-30 border-b border-[color:var(--color-line)] bg-[rgba(11,16,24,0.88)] backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <div class="surface-outline flex flex-wrap items-center justify-between gap-4 rounded-[28px] px-4 py-3 sm:px-5 lg:flex-nowrap lg:px-6">
                <a href="{{ route('store.home') }}" class="flex min-w-0 items-center gap-3 rounded-[30px] border border-[color:var(--color-line)] bg-[linear-gradient(180deg,rgba(35,29,21,0.32),rgba(11,16,24,0.14))] px-4 py-3 shadow-[0_16px_34px_rgba(0,0,0,0.22)]">
                    <span class="inline-flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-[color:var(--color-line)] bg-[linear-gradient(180deg,rgba(33,33,33,1),rgba(17,17,17,1))] shadow-[0_12px_30px_rgba(0,0,0,0.24)]">
                        <img src="{{ asset('cardbastion-logo.png') }}" alt="Card Bastion" class="h-10 w-10 object-contain">
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-base font-black uppercase tracking-[0.32em] text-[color:var(--color-brand-500)]">Card Bastion</span>
                        <span class="block truncate text-xs tracking-[0.28em] text-[color:var(--color-ink-soft)]">Boutique TCG + Player Hub</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-2 rounded-full border border-[color:var(--color-line)] bg-[rgba(24,24,24,0.86)] px-2 py-2 text-sm font-medium text-[color:var(--color-ink-soft)] lg:flex">
                    <a href="{{ route('store.catalog') }}" class="rounded-full px-4 py-2 transition hover:bg-[color:var(--color-brand-50)] hover:text-[color:var(--color-brand-600)]">Tienda</a>
                    <a href="{{ route('store.catalog') }}#catalogo" class="rounded-full px-4 py-2 transition hover:bg-[color:var(--color-brand-50)] hover:text-[color:var(--color-brand-600)]">Catálogo</a>
                    @auth
                        <a href="{{ route('account.tournaments.index') }}" class="rounded-full px-4 py-2 transition hover:bg-[color:var(--color-brand-50)] hover:text-[color:var(--color-brand-600)]">Torneos</a>
                        <a href="{{ route('account.dashboard') }}" class="rounded-full px-4 py-2 transition hover:bg-[color:var(--color-brand-50)] hover:text-[color:var(--color-brand-600)]">Mi cuenta</a>
                        @if (auth()->user()?->isBackofficeUser())
                            <a href="{{ route('dashboard') }}" class="rounded-full px-4 py-2 transition hover:bg-[color:var(--color-brand-50)] hover:text-[color:var(--color-brand-600)]">Admin</a>
                        @endif
                    @endauth
                </nav>

                <div class="flex w-full flex-wrap items-center justify-end gap-2 sm:gap-3 lg:w-auto">
                    <a class="btn border-[color:var(--color-brand-200)] bg-[color:var(--color-brand-50)] px-4 text-[color:var(--color-brand-700)] shadow-[0_10px_22px_rgba(145,71,26,0.12)] hover:border-[color:var(--color-brand-300)] hover:bg-[color:var(--color-brand-100)]" href="{{ route('cart.index') }}">
                        Carrito
                        <span class="ml-2 inline-flex min-w-7 items-center justify-center rounded-full bg-[color:var(--color-brand-600)] px-2 py-1 text-xs font-bold text-white">{{ $cartItemCount ?? 0 }}</span>
                    </a>
                @auth
                    <span class="hidden text-sm text-[color:var(--color-ink-soft)] sm:inline">{{ auth()->user()->name }}</span>
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
