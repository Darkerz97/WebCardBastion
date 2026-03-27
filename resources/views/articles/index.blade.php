@extends('layouts.app', ['title' => 'Articulos', 'heading' => 'Articulos y vlog', 'subheading' => 'Entradas editoriales, anuncios y contenido publico administrado solo por admins.'])

@section('content')
    <div class="space-y-6">
        <div class="panel space-y-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[color:var(--color-brand-500)]">Editorial</p>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-stone-600">Crea entradas del vlog o articulos con portada, resumen y contenido largo para informar a la comunidad desde Card Bastion.</p>
                </div>
                <a class="btn btn-primary" href="{{ route('articles.create') }}">Nueva entrada</a>
            </div>

            <form method="GET" class="grid gap-4 lg:grid-cols-[1fr_220px_auto]">
                <div class="field">
                    <label for="search">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Titulo, slug o contenido">
                </div>
                <div class="field">
                    <label for="published">Estado</label>
                    <select id="published" name="published">
                        <option value="">Todos</option>
                        <option value="1" @selected(request('published') === '1')>Publicados</option>
                        <option value="0" @selected(request('published') === '0')>Borradores</option>
                    </select>
                </div>
                <button class="btn btn-secondary self-end" type="submit">Filtrar</button>
            </form>
        </div>

        <div class="table-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Entrada</th>
                        <th>Autor</th>
                        <th>Estado</th>
                        <th>Comentarios</th>
                        <th>Publicacion</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $article)
                        <tr>
                            <td>
                                <a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('articles.show', $article) }}">{{ $article->title }}</a>
                                <p class="mt-1 text-sm text-stone-500">{{ $article->slug }}</p>
                            </td>
                            <td>{{ $article->author?->name ?? 'Sin autor' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge">{{ $article->is_published ? 'Publicado' : 'Borrador' }}</span>
                                    @if (! $article->allow_comments)
                                        <span class="badge">Sin comentarios</span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $article->comments_count }}</td>
                            <td>{{ optional($article->published_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}</td>
                            <td>
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a class="btn btn-secondary" href="{{ route('articles.edit', $article) }}">Editar</a>
                                    <form method="POST" action="{{ route('articles.destroy', $article) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit" onclick="return confirm('¿Eliminar entrada?')">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-stone-500">No hay articulos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $articles->links() }}</div>
    </div>
@endsection
