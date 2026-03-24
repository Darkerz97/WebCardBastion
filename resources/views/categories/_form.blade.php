<div class="grid gap-5 lg:grid-cols-2">
    <div class="field">
        <label for="name">Nombre</label>
        <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required>
    </div>
    <div class="field">
        <label for="slug">Slug</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="Se genera automaticamente si lo dejas vacio">
    </div>
    <div class="field lg:col-span-2">
        <label for="description">Descripcion</label>
        <textarea id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
    </div>
    <div class="field">
        <label for="image">Imagen</label>
        <input id="image" type="file" name="image" accept="image/*">
        @if ($category->image_path)
            <p class="text-xs text-stone-500">Imagen actual: {{ $category->image_path }}</p>
        @endif
    </div>
    <div class="field">
        <label for="sort_order">Orden</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
    </div>
    <div class="field">
        <label for="active">Activa</label>
        <select id="active" name="active" required>
            <option value="1" @selected(old('active', $category->active ?? true))>Si</option>
            <option value="0" @selected((string) old('active', (int) ($category->active ?? true)) === '0')>No</option>
        </select>
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button class="btn btn-primary" type="submit">Guardar categoria</button>
    <a class="btn btn-secondary" href="{{ route('categories.index') }}">Cancelar</a>
</div>
