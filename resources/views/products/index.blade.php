@extends('layouts.app', ['title' => 'Productos', 'heading' => 'Productos', 'subheading' => 'Inventario central y catalogo publico del ecommerce.'])

@section('content')
    <div class="space-y-6">
        <div class="panel space-y-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[color:var(--color-brand-500)]">Carga masiva</p>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-stone-600">Descarga la plantilla CSV, llenala en Excel y vuelve a subirla para crear o actualizar productos por SKU, categoria, slug y visibilidad en tienda.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a class="btn btn-secondary" href="{{ route('products.template') }}">Descargar plantilla</a>
                    <a class="btn btn-primary" href="{{ route('products.create') }}">Nuevo producto</a>
                </div>
            </div>

            <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-[1fr_auto]">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv">
                <button class="btn btn-secondary" type="submit">Importar productos</button>
            </form>

            <form method="GET" class="grid gap-4 lg:grid-cols-[1fr_240px_220px_auto]">
                <div class="field">
                    <label for="search">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, slug, SKU, carta o set">
                </div>
                <div class="field">
                    <label for="category_id">Categoria</label>
                    <select id="category_id" name="category_id">
                        <option value="">Todas</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="active">Estado</label>
                    <select id="active" name="active">
                        <option value="">Todos</option>
                        <option value="1" @selected(request('active') === '1')>Activos</option>
                        <option value="0" @selected(request('active') === '0')>Inactivos</option>
                    </select>
                </div>
                <button class="btn btn-secondary self-end" type="submit">Filtrar</button>
            </form>
        </div>

        <div class="table-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Categoria</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Tienda</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                <a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                                <p class="mt-1 text-sm text-stone-500">{{ $product->sku }} · {{ $product->slug }}</p>
                                @if ($product->card_name || $product->game || $product->set_name)
                                    <p class="mt-1 text-sm text-stone-500">{{ $product->card_name ?: $product->name }}{{ $product->game ? ' · '.$product->game : '' }}{{ $product->set_name ? ' · '.$product->set_name : '' }}</p>
                                @endif
                            </td>
                            <td>{{ $product->product_type ?: 'normal' }}</td>
                            <td>{{ $product->categoryModel?->name ?? 'Sin categoria' }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                {{ $product->stock }}
                                @if ($product->stock <= ($product->min_stock ?? 0))
                                    <p class="mt-1 text-xs text-amber-700">Min: {{ $product->min_stock }}</p>
                                @endif
                            </td>
                            <td>
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
                            </td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a class="btn btn-secondary" href="{{ route('products.edit', $product) }}">Editar</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit" onclick="return confirm('¿Eliminar producto?')">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-stone-500">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $products->links() }}</div>
    </div>
@endsection
