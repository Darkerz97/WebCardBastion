<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin | Card Bastion' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <div class="grid min-h-screen lg:grid-cols-[280px_1fr]">
        <aside class="border-r border-[color:var(--color-line)] bg-[linear-gradient(180deg,rgba(14,18,27,0.98),rgba(18,18,18,0.98))] px-6 py-8 text-stone-200">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-[color:var(--color-line)] bg-[linear-gradient(180deg,rgba(33,33,33,1),rgba(17,17,17,1))] text-lg font-black tracking-[0.25em] text-[color:var(--color-brand-500)] shadow-[0_14px_28px_rgba(0,0,0,0.28)]">CB</span>
                <div>
                    <div class="text-base font-black uppercase tracking-[0.25em] text-[color:var(--color-brand-500)]">Card Bastion</div>
                    <div class="text-xs uppercase tracking-[0.2em] text-[color:var(--color-ink-soft)]">Admin Suite</div>
                </div>
            </a>

            <nav class="mt-10 space-y-2 text-sm">
                <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('dashboard') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('dashboard') }}">Dashboard</a>
                @if (auth()->user()?->hasRole([\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_MANAGER]))
                    <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('categories.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('categories.index') }}">Categorias</a>
                    <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('products.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('products.index') }}">Productos</a>
                    <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('tournaments.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('tournaments.index') }}">Torneos</a>
                @endif
                @if (auth()->user()?->hasRole(\App\Models\User::ROLE_ADMIN))
                    <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('articles.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('articles.index') }}">Articulos</a>
                    <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('site-settings.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('site-settings.edit') }}">Contenido</a>
                @endif
                <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('customers.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('customers.index') }}">Clientes</a>
                <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('sales.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('sales.index') }}">Ventas</a>
                <a class="block rounded-2xl border px-4 py-3 transition {{ request()->routeIs('preorders.*') ? 'border-[color:var(--color-brand-300)] bg-[color:var(--color-brand-500)] text-[color:var(--color-night)]' : 'border-transparent bg-white/5 text-stone-300 hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white' }}" href="{{ route('preorders.index') }}">Preventas</a>
                <a class="block rounded-2xl border border-transparent bg-white/5 px-4 py-3 text-stone-300 transition hover:border-[color:var(--color-line)] hover:bg-white/10 hover:text-white" href="{{ route('store.home') }}">Ver tienda</a>
            </nav>

            <div class="mt-10 rounded-3xl border border-[color:var(--color-line)] bg-white/5 p-4">
                <p class="text-xs uppercase tracking-[0.22em] text-[color:var(--color-ink-soft)]">Sesion actual</p>
                <p class="mt-3 text-lg font-semibold text-white">{{ auth()->user()?->name }}</p>
                <p class="text-sm text-[color:var(--color-ink-soft)]">{{ auth()->user()?->role?->name }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button class="btn btn-secondary w-full" type="submit">Cerrar sesion</button>
                </form>
            </div>
        </aside>

        <main class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">{{ $eyebrow ?? 'Panel administrativo' }}</p>
                        <h1 class="mt-2 text-3xl font-black tracking-[0.06em] text-[color:var(--color-ink)]">{{ $heading ?? 'Card Bastion' }}</h1>
                        <p class="mt-2 max-w-3xl text-sm leading-7 text-[color:var(--color-ink-soft)]">{{ $subheading ?? 'Operacion central del ecosistema Card Bastion.' }}</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-semibold">Hay datos por corregir.</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
