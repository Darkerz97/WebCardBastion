@extends('layouts.app', ['title' => $product->name, 'heading' => $product->name, 'subheading' => 'Detalle del producto dentro del panel administrativo.'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1fr_0.95fr]">
        <div class="panel">
            <div class="aspect-[4/3] rounded-3xl bg-stone-100">
                @if ($product->primary_image_url)
                    <img class="h-full w-full rounded-3xl object-cover" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}">
                @else
                    <div class="flex h-full items-center justify-center rounded-3xl bg-gradient-to-br from-amber-100 to-stone-200 text-center text-sm font-semibold uppercase tracking-[0.25em] text-stone-500">{{ $product->name }}</div>
                @endif
            </div>

            @if ($product->images->isNotEmpty())
                <div class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($product->images as $image)
                        <img class="aspect-square w-full rounded-2xl border border-stone-200 object-cover" src="{{ $image->url }}" alt="{{ $image->alt_text ?: $product->name }}">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="panel">
            <div class="flex flex-wrap gap-2">
                @if ($product->publish_to_store)
                    <span class="badge">Publicado</span>
                @endif
                @if ($product->featured)
                    <span class="badge">Destacado</span>
                @endif
                @if ($product->stock <= ($product->min_stock ?? 0))
                    <span class="badge">Stock bajo</span>
                @endif
            </div>

            <dl class="mt-6 grid gap-4 text-sm">
                <div><dt class="font-semibold text-stone-500">Tipo</dt><dd class="mt-1 text-stone-900">{{ $product->product_type ?: 'normal' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Categoria</dt><dd class="mt-1 text-stone-900">{{ $product->categoryModel?->name ?? 'Sin categoria' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">SKU</dt><dd class="mt-1 text-stone-900">{{ $product->sku }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Codigo de barras</dt><dd class="mt-1 text-stone-900">{{ $product->barcode ?: 'Sin barcode' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Slug</dt><dd class="mt-1 text-stone-900">{{ $product->slug }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Precio</dt><dd class="mt-1 text-stone-900">${{ number_format($product->price, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Costo</dt><dd class="mt-1 text-stone-900">${{ number_format($product->cost, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Stock</dt><dd class="mt-1 text-stone-900">{{ $product->stock }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Stock minimo</dt><dd class="mt-1 text-stone-900">{{ $product->min_stock }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Juego</dt><dd class="mt-1 text-stone-900">{{ $product->game ?: 'No definido' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Nombre de carta</dt><dd class="mt-1 text-stone-900">{{ $product->card_name ?: 'No aplica' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Set</dt><dd class="mt-1 text-stone-900">{{ $product->set_name ?: 'No definido' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Codigo set</dt><dd class="mt-1 text-stone-900">{{ $product->set_code ?: 'No definido' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Numero coleccion</dt><dd class="mt-1 text-stone-900">{{ $product->collector_number ?: 'No definido' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Acabado</dt><dd class="mt-1 text-stone-900">{{ $product->finish ?: 'No definido' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Idioma</dt><dd class="mt-1 text-stone-900">{{ $product->language ?: 'No definido' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Condicion</dt><dd class="mt-1 text-stone-900">{{ $product->card_condition ?: 'No definida' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Resumen</dt><dd class="mt-1 text-stone-900">{{ $product->short_description ?: 'Sin resumen corto' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Descripcion</dt><dd class="mt-1 text-stone-900">{{ $product->description ?: 'Sin descripcion' }}</dd></div>
            </dl>

            <div class="mt-6 flex flex-wrap gap-3">
                <a class="btn btn-primary" href="{{ route('products.edit', $product) }}">Editar producto</a>
                <a class="btn btn-secondary" href="{{ route('inventory-movements.index', ['product_id' => $product->id]) }}">Auditar inventario</a>
                <a class="btn btn-secondary" href="{{ route('inventory-movements.create', ['product_id' => $product->id]) }}">Registrar movimiento</a>
                @if ($product->publish_to_store)
                    <a class="btn btn-secondary" href="{{ route('store.products.show', $product) }}" target="_blank">Ver en tienda</a>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 panel">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="section-kicker">Inventario</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Historial reciente del producto</h2>
                <p class="mt-3 text-sm leading-7 text-stone-600">
                    Revisa rapidamente los ultimos movimientos que afectaron el stock de este articulo y entra al detalle cuando necesites auditarlo.
                </p>
            </div>
            <a class="btn btn-primary" href="{{ route('inventory-movements.index', ['product_id' => $product->id]) }}">Ver historial completo</a>
        </div>

        <div class="mt-6 table-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Antes / despues</th>
                        <th>Responsable</th>
                        <th>Referencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->inventoryMovements as $movement)
                        <tr>
                            <td>
                                <a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('inventory-movements.show', $movement) }}">{{ optional($movement->occurred_at)->format('d/m/Y H:i') }}</a>
                            </td>
                            <td>{{ $movement->movement_type }} · {{ $movement->direction }}</td>
                            <td>{{ $movement->stock_before }} -> {{ $movement->stock_after }}</td>
                            <td>{{ $movement->user?->name ?? 'Sistema' }}</td>
                            <td>{{ $movement->reference ?: 'Sin referencia' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-stone-500">Este producto aun no tiene movimientos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
