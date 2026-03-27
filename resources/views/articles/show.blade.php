@extends('layouts.app', ['title' => $article->title, 'heading' => $article->title, 'subheading' => 'Vista previa editorial, metadatos y comentarios recibidos.'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
        <div class="panel">
            @if ($article->cover_image_url)
                <div class="overflow-hidden rounded-3xl bg-stone-100">
                    <img class="aspect-[16/8] w-full object-cover" src="{{ $article->cover_image_url }}" alt="{{ $article->title }}">
                </div>
            @endif

            <div class="mt-6 flex flex-wrap gap-2">
                <span class="badge">{{ $article->is_published ? 'Publicado' : 'Borrador' }}</span>
                <span class="badge">{{ $article->allow_comments ? 'Comentarios activos' : 'Comentarios cerrados' }}</span>
            </div>

            @if ($article->excerpt)
                <p class="mt-5 text-lg leading-8 text-stone-600">{{ $article->excerpt }}</p>
            @endif

            <div class="mt-6 whitespace-pre-line text-sm leading-8 text-stone-700">{{ $article->content }}</div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a class="btn btn-primary" href="{{ route('articles.edit', $article) }}">Editar entrada</a>
                @if ($article->is_published)
                    <a class="btn btn-secondary" href="{{ route('blog.show', $article) }}" target="_blank">Ver publica</a>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel">
                <h2 class="text-lg font-black uppercase tracking-[0.08em] text-stone-900">Resumen</h2>
                <dl class="mt-5 grid gap-4 text-sm">
                    <div><dt class="font-semibold text-stone-500">Slug</dt><dd class="mt-1 text-stone-900">{{ $article->slug }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Autor</dt><dd class="mt-1 text-stone-900">{{ $article->author?->name ?? 'Sin autor' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Publicacion</dt><dd class="mt-1 text-stone-900">{{ optional($article->published_at)->format('d/m/Y H:i') ?: 'Sin fecha' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Comentarios</dt><dd class="mt-1 text-stone-900">{{ $article->comments->count() }}</dd></div>
                </dl>
            </div>

            <div class="panel">
                <div>
                    <h2 class="text-lg font-black uppercase tracking-[0.08em] text-stone-900">Comentarios</h2>
                    <p class="mt-2 text-sm text-stone-500">Mensajes recibidos en esta publicacion.</p>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($article->comments as $comment)
                        <div class="rounded-2xl border border-stone-200 px-4 py-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $comment->display_name }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-stone-500">{{ $comment->guest_email ?: ($comment->user?->email ?? 'Usuario registrado') }}</p>
                                </div>
                                <form method="POST" action="{{ route('articles.comments.destroy', [$article, $comment]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit" onclick="return confirm('¿Eliminar comentario?')">Eliminar</button>
                                </form>
                            </div>
                            <p class="mt-4 whitespace-pre-line text-sm leading-7 text-stone-700">{{ $comment->body }}</p>
                            <p class="mt-3 text-xs uppercase tracking-[0.22em] text-stone-500">{{ $comment->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Esta entrada todavia no tiene comentarios.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
