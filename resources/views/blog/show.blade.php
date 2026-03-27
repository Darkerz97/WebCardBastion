@extends('layouts.public', ['title' => $article->title.' | '.($siteSettings?->site_name ?? 'Card Bastion')])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 xl:grid-cols-[1fr_0.88fr]">
            <article class="panel overflow-hidden">
                @if ($article->cover_image_url)
                    <div class="overflow-hidden rounded-3xl bg-stone-100">
                        <img src="{{ $article->cover_image_url }}" alt="{{ $article->title }}" class="aspect-[16/8] w-full object-cover">
                    </div>
                @endif

                <p class="mt-6 text-xs font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">{{ optional($article->published_at)->format('d/m/Y') ?: 'Sin fecha' }}</p>
                <h1 class="mt-3 text-4xl font-black uppercase leading-[1.02] tracking-[0.05em] text-stone-900">{{ $article->title }}</h1>
                <p class="mt-4 text-sm uppercase tracking-[0.22em] text-stone-500">Por {{ $article->author?->name ?? 'Card Bastion' }}</p>

                @if ($article->excerpt)
                    <p class="mt-6 text-lg leading-8 text-stone-600">{{ $article->excerpt }}</p>
                @endif

                <div class="mt-8 whitespace-pre-line text-base leading-8 text-stone-700">{{ $article->content }}</div>
            </article>

            <div class="space-y-6">
                <div class="panel">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Comentarios</h2>
                            <p class="mt-2 text-sm text-stone-500">Comparte tu opinion como visitante o jugador registrado.</p>
                        </div>
                        <span class="badge">{{ $article->approvedComments->count() }} visibles</span>
                    </div>

                    @if ($article->allow_comments)
                        <form method="POST" action="{{ route('blog.comments.store', $article) }}" class="mt-6 space-y-4">
                            @csrf
                            @guest
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="field">
                                        <label for="guest_name">Nombre</label>
                                        <input id="guest_name" type="text" name="guest_name" value="{{ old('guest_name') }}" required>
                                    </div>
                                    <div class="field">
                                        <label for="guest_email">Correo</label>
                                        <input id="guest_email" type="email" name="guest_email" value="{{ old('guest_email') }}" required>
                                    </div>
                                </div>
                            @else
                                <div class="rounded-2xl border border-[color:var(--color-line)] bg-[color:var(--color-brand-50)] px-4 py-4 text-sm text-stone-600">
                                    Comentando como <span class="font-semibold text-stone-900">{{ auth()->user()->name }}</span>.
                                </div>
                            @endguest
                            <div class="field">
                                <label for="body">Comentario</label>
                                <textarea id="body" name="body" rows="5" required>{{ old('body') }}</textarea>
                            </div>
                            <button class="btn btn-primary" type="submit">Publicar comentario</button>
                        </form>
                    @else
                        <p class="mt-5 text-sm text-stone-500">Los comentarios estan cerrados para esta publicacion.</p>
                    @endif

                    <div class="mt-8 space-y-4">
                        @forelse ($article->approvedComments as $comment)
                            <div class="rounded-2xl border border-stone-200 px-4 py-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-stone-900">{{ $comment->display_name }}</p>
                                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-stone-500">{{ $comment->created_at?->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-stone-700">{{ $comment->body }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-stone-500">Todavia no hay comentarios en este articulo.</p>
                        @endforelse
                    </div>
                </div>

                <div class="panel">
                    <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Mas publicaciones</h2>
                    <div class="mt-5 space-y-3">
                        @forelse ($relatedArticles as $related)
                            <a href="{{ route('blog.show', $related) }}" class="block rounded-2xl border border-stone-200 px-4 py-4 transition hover:border-[color:var(--color-brand-300)] hover:bg-[color:var(--color-brand-50)]">
                                <p class="font-semibold text-stone-900">{{ $related->title }}</p>
                                <p class="mt-2 text-sm leading-7 text-stone-600">{{ $related->excerpt ?: \Illuminate\Support\Str::limit($related->content, 110) }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-stone-500">No hay mas publicaciones relacionadas por ahora.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
