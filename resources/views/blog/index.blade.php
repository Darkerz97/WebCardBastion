@extends('layouts.public', ['title' => 'Articulos | '.($siteSettings?->site_name ?? 'Card Bastion')])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="panel overflow-hidden">
            <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
                <div>
                    <p class="section-kicker">Vlog y articulos</p>
                    <h1 class="mt-3 text-4xl font-black uppercase leading-[0.96] tracking-[0.05em] text-stone-900 sm:text-5xl">Historias, noticias y contenido para la comunidad Card Bastion.</h1>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-stone-600">Publicaciones sobre torneos, lanzamientos, comunidad, consejos de compra y novedades del ecosistema.</p>
                    <form method="GET" class="mt-6 grid gap-3 sm:grid-cols-[1fr_auto]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por tema, titulo o palabra clave">
                        <button class="btn btn-primary" type="submit">Buscar</button>
                    </form>
                </div>

                @if ($featuredArticle)
                    <a href="{{ route('blog.show', $featuredArticle) }}" class="catalog-card block">
                        <div class="aspect-[4/3] overflow-hidden rounded-[22px] bg-stone-100">
                            @if ($featuredArticle->cover_image_url)
                                <img src="{{ $featuredArticle->cover_image_url }}" alt="{{ $featuredArticle->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,rgba(238,216,191,0.85),rgba(246,240,232,1))] px-6 text-center text-sm font-semibold uppercase tracking-[0.25em] text-stone-500">{{ $featuredArticle->title }}</div>
                            @endif
                        </div>
                        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.24em] text-stone-500">Destacado</p>
                        <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">{{ $featuredArticle->title }}</h2>
                        <p class="mt-3 text-sm leading-7 text-stone-600">{{ $featuredArticle->excerpt ?: \Illuminate\Support\Str::limit($featuredArticle->content, 170) }}</p>
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($articles as $article)
                <article class="catalog-card overflow-hidden">
                    <a href="{{ route('blog.show', $article) }}" class="block">
                        <div class="aspect-[4/3] overflow-hidden rounded-[22px] bg-stone-100">
                            @if ($article->cover_image_url)
                                <img src="{{ $article->cover_image_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,rgba(238,216,191,0.85),rgba(246,240,232,1))] px-6 text-center text-sm font-semibold uppercase tracking-[0.25em] text-stone-500">{{ $article->title }}</div>
                            @endif
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <p class="text-xs uppercase tracking-[0.24em] text-stone-500">{{ optional($article->published_at)->format('d/m/Y') ?: 'Sin fecha' }}</p>
                            <span class="badge">{{ $article->approved_comments_count }} comentarios</span>
                        </div>
                        <h2 class="mt-3 text-xl font-black uppercase tracking-[0.04em] text-stone-900">{{ $article->title }}</h2>
                        <p class="mt-3 text-sm leading-7 text-stone-600">{{ $article->excerpt ?: \Illuminate\Support\Str::limit($article->content, 160) }}</p>
                        <p class="mt-4 text-xs uppercase tracking-[0.22em] text-[color:var(--color-brand-600)]">Por {{ $article->author?->name ?? 'Card Bastion' }}</p>
                    </a>
                </article>
            @empty
                <div class="panel md:col-span-2 xl:col-span-3">
                    <p class="text-sm text-stone-500">Todavia no hay publicaciones disponibles.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $articles->links() }}</div>
    </section>
@endsection
