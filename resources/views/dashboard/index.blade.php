@extends('layouts.app', ['title' => 'Dashboard', 'heading' => 'Dashboard', 'subheading' => 'Operacion central para tienda, jugadores y panel administrativo.'])

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
        <article class="metric-card">
            <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Categorias</p>
            <p class="mt-3 text-3xl font-black text-stone-900">{{ $metrics['categories'] }}</p>
        </article>
        <article class="metric-card">
            <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Productos</p>
            <p class="mt-3 text-3xl font-black text-stone-900">{{ $metrics['products'] }}</p>
            <p class="mt-2 text-sm text-stone-500">Publicados: {{ $metrics['published_products'] }}</p>
        </article>
        <article class="metric-card">
            <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Ventas hoy</p>
            <p class="mt-3 text-3xl font-black text-stone-900">{{ $metrics['sales_today'] }}</p>
            <p class="mt-2 text-sm text-stone-500">${{ number_format($metrics['amount_today'], 2) }}</p>
        </article>
        <article class="metric-card">
            <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Clientes</p>
            <p class="mt-3 text-3xl font-black text-stone-900">{{ $metrics['customers'] }}</p>
        </article>
        <article class="metric-card">
            <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Preventas</p>
            <p class="mt-3 text-3xl font-black text-stone-900">{{ $metrics['preorders'] }}</p>
        </article>
        <article class="metric-card">
            <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Cierres</p>
            <p class="mt-3 text-3xl font-black text-stone-900">{{ $metrics['cash_closures'] }}</p>
        </article>
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="table-shell">
            <div class="flex items-center justify-between border-b border-stone-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-black uppercase tracking-[0.08em] text-stone-900">Ventas recientes</h2>
                    <p class="text-sm text-stone-500">Ultima actividad operativa del sistema.</p>
                </div>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentSales as $sale)
                        <tr>
                            <td><a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a></td>
                            <td>{{ $sale->customer?->name ?? 'Publico general' }}</td>
                            <td>${{ number_format($sale->total, 2) }}</td>
                            <td>{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-stone-500">Aun no hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="panel">
            <h2 class="text-lg font-black uppercase tracking-[0.08em] text-stone-900">Stock bajo</h2>
            <p class="mt-2 text-sm text-stone-500">Productos que conviene reabastecer antes de abrir la tienda al publico.</p>
            <div class="mt-5 space-y-3">
                @forelse ($lowStockProducts as $product)
                    <div class="rounded-2xl border border-stone-200 px-4 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $product->name }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ $product->sku }}</p>
                            </div>
                            <span class="badge">{{ $product->stock }} piezas</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-stone-500">No hay alertas de inventario por ahora.</p>
                @endforelse
            </div>
        </div>
    </section>

    @if (auth()->user()?->hasRole(\App\Models\User::ROLE_ADMIN))
        <section class="mt-6">
            <div class="grid gap-6 xl:grid-cols-2">
                <div class="panel">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="section-kicker">Personalizacion</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Contenido editable del sitio</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600">
                                Administra el branding, textos comerciales y mensajes visibles de la tienda publica desde una sola pantalla de configuracion.
                            </p>
                        </div>
                        <a class="btn btn-primary" href="{{ route('site-settings.edit') }}">Editar contenido</a>
                    </div>
                </div>
                <div class="panel">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="section-kicker">Editorial</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Vlog y articulos</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600">
                                Crea entradas, sube imagenes de portada y administra comentarios desde una seccion exclusiva para administradores.
                            </p>
                        </div>
                        <a class="btn btn-primary" href="{{ route('articles.index') }}">Gestionar articulos</a>
                    </div>
                </div>
                <div class="panel xl:col-span-2">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="section-kicker">Redes sociales</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Conecta Facebook, Instagram y TikTok</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600">
                                Define los enlaces para seguir las cuentas oficiales y pega los codigos embed de albumes o publicaciones recientes para mostrarlos en la home publica.
                            </p>
                        </div>
                        <a class="btn btn-primary" href="{{ route('site-settings.edit') }}#social-media">Configurar redes</a>
                    </div>
                </div>
                <div class="panel xl:col-span-2">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="section-kicker">Preventas</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Reserva productos y registra abonos</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600">
                                Crea preventas ligadas a clientes, consulta saldo pendiente, registra nuevos abonos y lleva seguimiento de entrega desde una sola pantalla.
                            </p>
                        </div>
                        <a class="btn btn-primary" href="{{ route('preorders.index') }}">Gestionar preventas</a>
                    </div>
                </div>
                <div class="panel xl:col-span-2">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="section-kicker">Cierres de caja</p>
                            <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Consulta montos, diferencias y conciliacion</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-7 text-stone-600">
                                Revisa cierres por dispositivo o usuario, registra cierres manuales y actualiza su estatus de conciliacion desde el panel admin.
                            </p>
                        </div>
                        <a class="btn btn-primary" href="{{ route('cash-closures.index') }}">Gestionar cierres</a>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
