@extends('layouts.app', ['title' => 'Detalle de movimiento', 'heading' => 'Detalle de movimiento de inventario', 'subheading' => 'Consulta el contexto completo del ajuste, la trazabilidad del producto y el cambio exacto de stock.'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="panel">
            <div class="flex flex-wrap gap-2">
                <span class="badge">{{ $inventoryMovement->movement_type }}</span>
                <span class="badge">{{ $inventoryMovement->direction }}</span>
                <span class="badge">{{ $inventoryMovement->source }}</span>
            </div>

            <dl class="mt-6 grid gap-4 text-sm">
                <div><dt class="font-semibold text-stone-500">UUID</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->uuid }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Producto</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->product?->name ?? 'Producto eliminado' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">SKU</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->product?->sku ?? 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Cantidad</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->quantity }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Stock antes</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->stock_before }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Stock despues</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->stock_after }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Costo unitario</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->unit_cost !== null ? '$'.number_format($inventoryMovement->unit_cost, 2) : 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Referencia</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->reference ?: 'Sin referencia' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Venta relacionada</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->sale?->sale_number ?? 'Sin venta relacionada' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Usuario</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->user?->name ?? 'Sistema' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Rol</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->user?->role?->code ?? 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Dispositivo</dt><dd class="mt-1 text-stone-900">{{ $inventoryMovement->device?->name ?? 'Sin dispositivo' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Fecha del movimiento</dt><dd class="mt-1 text-stone-900">{{ optional($inventoryMovement->occurred_at)->format('d/m/Y H:i') ?: 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Generado cliente</dt><dd class="mt-1 text-stone-900">{{ optional($inventoryMovement->client_generated_at)->format('d/m/Y H:i') ?: 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Recibido servidor</dt><dd class="mt-1 text-stone-900">{{ optional($inventoryMovement->received_at)->format('d/m/Y H:i') ?: 'N/D' }}</dd></div>
            </dl>

            <div class="mt-4">
                <strong>Notas</strong>
                <p class="muted mt-2">{{ $inventoryMovement->notes ?: 'Sin notas registradas.' }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel">
                <p class="section-kicker">Producto</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Acciones rapidas</h2>
                <div class="mt-5 flex flex-wrap gap-3">
                    @if ($inventoryMovement->product)
                        <a class="btn btn-primary" href="{{ route('inventory-movements.index', ['product_id' => $inventoryMovement->product_id]) }}">Ver auditoria del producto</a>
                        <a class="btn btn-secondary" href="{{ route('products.show', $inventoryMovement->product) }}">Ver producto</a>
                        <a class="btn btn-secondary" href="{{ route('inventory-movements.create', ['product_id' => $inventoryMovement->product_id]) }}">Nuevo ajuste</a>
                    @endif
                </div>
            </div>

            <div class="panel">
                <p class="section-kicker">Historial</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Movimientos recientes del mismo producto</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($relatedMovements as $movement)
                        <div class="rounded-2xl border border-stone-200 px-4 py-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $movement->movement_type }}</p>
                                    <p class="mt-1 text-sm text-stone-500">{{ optional($movement->occurred_at)->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="badge">{{ $movement->stock_before }} -> {{ $movement->stock_after }}</span>
                            </div>
                            <p class="mt-3 text-sm text-stone-500">{{ $movement->reference ?: ($movement->user?->name ?? 'Sistema') }}</p>
                            <div class="mt-3">
                                <a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('inventory-movements.show', $movement) }}">Abrir detalle</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">No hay mas movimientos recientes para este producto.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
