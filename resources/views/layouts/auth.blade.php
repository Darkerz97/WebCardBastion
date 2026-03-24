<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Acceso | Card Bastion' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hero-glow min-h-screen">
    <main class="mx-auto grid min-h-screen max-w-7xl items-center px-4 py-12 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
        <section class="hidden pr-12 lg:block">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Card Bastion Platform</p>
            <h1 class="mt-5 max-w-xl text-5xl font-black uppercase leading-none tracking-[0.08em] text-stone-900">
                Tienda virtual, portal de jugadores y operacion central.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-stone-600">
                Una sola base para catalogo, ventas, comunidad y panel administrativo, lista para Hostinger compartido.
            </p>
        </section>

        <section class="panel mx-auto w-full max-w-xl p-8 sm:p-10">
            @yield('content')
        </section>
    </main>
</body>
</html>
