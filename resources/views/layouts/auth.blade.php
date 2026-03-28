<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Acceso | '.($siteSettings?->site_name ?? 'Card Bastion') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hero-glow min-h-screen" data-auth-page="true">
    <main class="mx-auto grid min-h-screen max-w-7xl items-center px-4 py-12 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
        <section class="hidden pr-12 lg:block">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">{{ $siteSettings?->site_name ?? 'Card Bastion' }} Platform</p>
            <h1 class="mt-5 max-w-xl text-5xl font-black uppercase leading-none tracking-[0.08em] text-[color:var(--color-ink)]">
                Tienda virtual, portal de jugadores y operacion central.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-[color:var(--color-ink-soft)]">
                {{ $siteSettings?->site_tagline ?: 'Una sola base para catalogo, ventas, comunidad y panel administrativo, lista para Hostinger compartido.' }}
            </p>
        </section>

        <section class="panel mx-auto w-full max-w-xl p-8 sm:p-10">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-semibold">Revisa los datos del formulario.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</body>
</html>
