@extends('layouts.app', ['title' => 'Categorias', 'heading' => 'Categorias', 'subheading' => 'Base del ecommerce para ordenar catalogo, filtros y navegacion.'])

@section('content')
    <div class="space-y-6">
        <div class="panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <form method="GET" class="grid gap-4 sm:grid-cols-[1fr_auto] lg:w-[26rem]">
                    <div class="field">
                        <label for="search">Buscar</label>
                        <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nombre de categoria">
                    </div>
                    <button class="btn btn-secondary" type="submit">Filtrar</button>
                </form>
                <a class="btn btn-primary" href="{{ route('categories.create') }}">Nueva categoria</a>
            </div>
        </div>

        <div class="table-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Slug</th>
                        <th>Productos</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>
                                <p class="font-semibold text-stone-900">{{ $category->name }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ $category->description ?: 'Sin descripcion' }}</p>
                            </td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td><span class="badge">{{ $category->active ? 'Activa' : 'Inactiva' }}</span></td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a class="btn btn-secondary" href="{{ route('categories.edit', $category) }}">Editar</a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit" onclick="return confirm('¿Eliminar categoria?')">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-stone-500">No hay categorias registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $categories->links() }}</div>
    </div>
@endsection
