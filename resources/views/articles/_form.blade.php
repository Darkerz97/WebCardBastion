<div class="grid gap-5 lg:grid-cols-2">
    <div class="field lg:col-span-2">
        <label for="title">Titulo</label>
        <input id="title" type="text" name="title" value="{{ old('title', $article->title) }}" required>
    </div>
    <div class="field">
        <label for="slug">Slug</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $article->slug) }}" placeholder="Se genera automaticamente si lo dejas vacio">
    </div>
    <div class="field">
        <label for="published_at">Fecha de publicacion</label>
        <input id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}">
    </div>
    <div class="field lg:col-span-2">
        <label for="excerpt">Resumen</label>
        <textarea id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $article->excerpt) }}</textarea>
    </div>
    <div class="field lg:col-span-2">
        <label for="content">Contenido</label>
        <textarea id="content" name="content" rows="14" required>{{ old('content', $article->content) }}</textarea>
    </div>
    <div class="field">
        <label for="cover_image">Imagen de portada</label>
        <input id="cover_image" type="file" name="cover_image" accept="image/*">
    </div>
    <div class="field">
        <label for="is_published">Publicada</label>
        <select id="is_published" name="is_published" required>
            <option value="1" @selected(old('is_published', $article->is_published ?? true))>Si</option>
            <option value="0" @selected((string) old('is_published', (int) ($article->is_published ?? true)) === '0')>No</option>
        </select>
    </div>
    <div class="field">
        <label for="allow_comments">Permitir comentarios</label>
        <select id="allow_comments" name="allow_comments" required>
            <option value="1" @selected(old('allow_comments', $article->allow_comments ?? true))>Si</option>
            <option value="0" @selected((string) old('allow_comments', (int) ($article->allow_comments ?? true)) === '0')>No</option>
        </select>
    </div>
    @if ($article->cover_image_url)
        <div class="field">
            <label for="remove_cover_image">Portada actual</label>
            <div class="overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
                <img src="{{ $article->cover_image_url }}" alt="{{ $article->title }}" class="aspect-[4/3] w-full object-cover">
            </div>
            <label class="mt-3 flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remove_cover_image" value="1" @checked(old('remove_cover_image'))>
                Eliminar imagen actual
            </label>
        </div>
    @endif
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button class="btn btn-primary" type="submit">Guardar entrada</button>
    <a class="btn btn-secondary" href="{{ route('articles.index') }}">Cancelar</a>
</div>
