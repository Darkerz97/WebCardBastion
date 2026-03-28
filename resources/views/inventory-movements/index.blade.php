@extends('layouts.app', ['title' => 'Movimientos de inventario', 'heading' => 'Movimientos de inventario', 'subheading' => 'Audita entradas, salidas, ajustes y correcciones para entender quien movio el stock y cuando ocurrio.'])

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <article class="metric-card">
                <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Movimientos</p>
                <p class="mt-3 text-3xl font-black text-stone-900">{{ $summary['movements'] }}</p>
            </article>
            <article class="metric-card">
                <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Productos auditados</p>
                <p class="mt-3 text-3xl font-black text-stone-900">{{ $summary['products'] }}</p>
            </article>
            <article class="metric-card">
                <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Entradas</p>
                <p class="mt-3 text-3xl font-black text-stone-900">{{ number_format($summary['entries']) }}</p>
            </article>
            <article class="metric-card">
                <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Salidas</p>
                <p class="mt-3 text-3xl font-black text-stone-900">{{ number_format($summary['exits']) }}</p>
            </article>
            <article class="metric-card">
                <p class="text-xs uppercase tracking-[0.24em] text-stone-500">Ajustes</p>
                <p class="mt-3 text-3xl font-black text-stone-900">{{ $summary['adjustments'] }}</p>
            </article>
        </section>

        <div class="panel">
            <form method="GET" class="grid gap-4 xl:grid-cols-[1.2fr_1fr_1fr_180px_180px_180px_180px_auto]">
                <div class="field">
                    <label for="search">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Producto, SKU, UUID, referencia o nota">
                </div>
                <div class="field">
                    <label for="product_id">Producto</label>
                    <select id="product_id" name="product_id">
                        <option value="">Todos</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) request('product_id') === (string) $product->id)>{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="user_id">Usuario</label>
                    <select id="user_id" name="user_id">
                        <option value="">Todos</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="movement_type">Tipo</label>
                    <select id="movement_type" name="movement_type">
                        <option value="">Todos</option>
                        <option value="sale" @selected(request('movement_type') === 'sale')>Venta</option>
                        <option value="restock" @selected(request('movement_type') === 'restock')>Reabasto</option>
                        <option value="manual_adjustment" @selected(request('movement_type') === 'manual_adjustment')>Ajuste manual</option>
                        <option value="return" @selected(request('movement_type') === 'return')>Devolucion</option>
                        <option value="sync_correction" @selected(request('movement_type') === 'sync_correction')>Correccion sync</option>
                    </select>
                </div>
                <div class="field">
                    <label for="direction">Direccion</label>
                    <select id="direction" name="direction">
                        <option value="">Todas</option>
                        <option value="in" @selected(request('direction') === 'in')>Entrada</option>
                        <option value="out" @selected(request('direction') === 'out')>Salida</option>
                        <option value="adjustment" @selected(request('direction') === 'adjustment')>Ajuste</option>
                    </select>
                </div>
                <div class="field">
                    <label for="source">Origen</label>
                    <select id="source" name="source">
                        <option value="">Todos</option>
                        <option value="server" @selected(request('source') === 'server')>Servidor</option>
                        <option value="pos" @selected(request('source') === 'pos')>POS</option>
                        <option value="system" @selected(request('source') === 'system')>Sistema</option>
                    </select>
                </div>
                <div class="field">
                    <label for="date_from">Desde</label>
                    <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="field">
                    <label for="date_to">Hasta</label>
                    <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
                <button class="btn btn-secondary self-end" type="submit">Filtrar</button>
            </form>
        </div>

        <div class="flex justify-end">
            <a class="btn btn-primary" href="{{ route('inventory-movements.create', request()->filled('product_id') ? ['product_id' => request('product_id')] : []) }}">Nuevo movimiento</a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
            <div class="table-shell">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Direccion</th>
                            <th>Cantidad</th>
                            <th>Antes / despues</th>
                            <th>Responsable</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr>
                                <td>
                                    <a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('inventory-movements.show', $movement) }}">{{ $movement->product?->name ?? 'Producto eliminado' }}</a>
                                    <p class="mt-1 text-sm text-stone-500">{{ $movement->product?->sku ?? 'Sin SKU' }}</p>
                                </td>
                                <td>
                                    <span class="badge">{{ $movement->movement_type }}</span>
                                    @if ($movement->reference)
                                        <p class="mt-1 text-sm text-stone-500">{{ $movement->reference }}</p>
                                    @endif
                                </td>
                                <td>{{ $movement->direction }}</td>
                                <td>{{ $movement->quantity }}</td>
                                <td>{{ $movement->stock_before }} -> {{ $movement->stock_after }}</td>
                                <td>
                                    {{ $movement->user?->name ?? 'Sistema' }}
                                    <p class="mt-1 text-sm text-stone-500">{{ $movement->device?->name ?? $movement->source }}</p>
                                </td>
                                <td>{{ optional($movement->occurred_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-stone-500">No hay movimientos que coincidan con los filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <p class="section-kicker">Auditoria</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Productos a revisar</h2>
                <p class="mt-3 text-sm leading-7 text-stone-600">
                    Aqui aparecen productos con stock bajo o con actividad reciente para que puedas revisar diferencias y movimientos inusuales.
                </p>

                <div class="mt-5 space-y-3">
                    @forelse ($auditProducts as $product)
                        <div class="rounded-2xl border border-stone-200 px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $product->name }}</p>
                                    <p class="mt-1 text-sm text-stone-500">{{ $product->sku }}</p>
                                </div>
                                <span class="badge">{{ $product->inventory_movements_count }} movs</span>
                            </div>
                            <p class="mt-3 text-sm text-stone-500">Stock actual: {{ $product->stock }} | Minimo: {{ $product->min_stock }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a class="btn btn-secondary" href="{{ route('inventory-movements.index', ['product_id' => $product->id]) }}">Auditar</a>
                                <a class="btn btn-secondary" href="{{ route('products.show', $product) }}">Ver producto</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">No hay productos destacados para auditoria por ahora.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div>{{ $movements->links() }}</div>
    </div>
@endsection
