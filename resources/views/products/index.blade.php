@extends('layouts.app', ['title' => 'Productos', 'heading' => 'Productos', 'subheading' => 'Catálogo central para POS, panel y sincronización.'])

@section('content')
    <div class="panel">
        <div class="actions" style="justify-content:space-between; margin-bottom:18px; align-items:flex-start;">
            <div>
                <strong>Carga masiva</strong>
                <div class="muted" style="margin-top:6px;">Descarga la plantilla CSV, llenala en Excel y vuelve a subirla para crear o actualizar productos por SKU.</div>
            </div>
            <div class="actions">
                <a class="btn secondary" href="{{ route('products.template') }}">Descargar plantilla</a>
                <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" class="actions">
                    @csrf
                    <input type="file" name="file" accept=".csv,text/csv" style="max-width:260px;">
                    <button class="btn" type="submit">Importar productos</button>
                </form>
            </div>
        </div>
        <form method="GET" class="search-bar">
            <div class="field" style="margin:0;"><label for="search">Buscar</label><input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, SKU o barcode"></div>
            <div class="field" style="margin:0;">
                <label for="active">Estado</label>
                <select id="active" name="active">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('active') === '1')>Activos</option>
                    <option value="0" @selected(request('active') === '0')>Inactivos</option>
                </select>
            </div>
            <button class="btn secondary" type="submit">Filtrar</button>
        </form>
        <div class="actions" style="justify-content:flex-end; margin-bottom:14px;"><a class="btn" href="{{ route('products.create') }}">Nuevo producto</a></div>
        <table>
            <thead><tr><th>Nombre</th><th>SKU</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category ?: 'Sin categoría' }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td><span class="badge">{{ $product->active ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="actions">
                        <a class="btn secondary" href="{{ route('products.edit', $product) }}">Editar</a>
                        <form class="inline" method="POST" action="{{ route('products.destroy', $product) }}">@csrf @method('DELETE') <button class="btn danger" type="submit" onclick="return confirm('¿Eliminar producto?')">Eliminar</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No hay productos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $products->links() }}</div>
    </div>
@endsection
