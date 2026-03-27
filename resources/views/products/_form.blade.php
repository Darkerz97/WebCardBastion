<div class="grid gap-5 lg:grid-cols-2">
    <div class="field">
        <label for="name">Nombre</label>
        <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required>
    </div>
    <div class="field">
        <label for="slug">Slug</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="Se genera automaticamente si lo dejas vacio">
    </div>
    <div class="field">
        <label for="sku">SKU</label>
        <input id="sku" type="text" name="sku" value="{{ old('sku', $product->sku) }}" required>
    </div>
    <div class="field">
        <label for="barcode">Codigo de barras</label>
        <input id="barcode" type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}">
    </div>
    <div class="field">
        <label for="category_id">Categoria</label>
        <select id="category_id" name="category_id">
            <option value="">Sin categoria</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="short_description">Resumen corto</label>
        <input id="short_description" type="text" maxlength="280" name="short_description" value="{{ old('short_description', $product->short_description) }}">
    </div>
    <div class="field">
        <label for="cost">Costo</label>
        <input id="cost" type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $product->cost ?? 0) }}" required>
    </div>
    <div class="field">
        <label for="price">Precio</label>
        <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? 0) }}" required>
    </div>
    <div class="field">
        <label for="stock">Stock</label>
        <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required>
    </div>
    <div class="field">
        <label for="cover_image">Imagen portada</label>
        <input id="cover_image" type="file" name="cover_image" accept="image/*">
    </div>
    <div class="field lg:col-span-2">
        <label for="gallery_images">Galeria</label>
        <input id="gallery_images" type="file" name="gallery_images[]" accept="image/*" multiple>
    </div>
    <div class="field lg:col-span-2">
        <label for="description">Descripcion completa</label>
        <textarea id="description" name="description" rows="6">{{ old('description', $product->description) }}</textarea>
    </div>
    <div class="field">
        <label for="active">Activo</label>
        <select id="active" name="active" required>
            <option value="1" @selected(old('active', $product->active ?? true))>Si</option>
            <option value="0" @selected((string) old('active', (int) ($product->active ?? true)) === '0')>No</option>
        </select>
    </div>
    <div class="field">
        <label for="featured">Destacado</label>
        <select id="featured" name="featured" required>
            <option value="1" @selected(old('featured', $product->featured ?? false))>Si</option>
            <option value="0" @selected((string) old('featured', (int) ($product->featured ?? false)) === '0')>No</option>
        </select>
    </div>
    <div class="field">
        <label for="publish_to_store">Visible en tienda</label>
        <select id="publish_to_store" name="publish_to_store" required>
            <option value="1" @selected(old('publish_to_store', $product->publish_to_store ?? true))>Si</option>
            <option value="0" @selected((string) old('publish_to_store', (int) ($product->publish_to_store ?? true)) === '0')>No</option>
        </select>
    </div>
</div>

@if ($product->exists && $product->images->isNotEmpty())
    <div class="mt-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.22em] text-stone-500">Galeria actual</h3>
            <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">
            @foreach ($product->images as $image)
                <img class="aspect-square w-full rounded-2xl border border-stone-200 object-cover" src="{{ $image->url }}" alt="{{ $image->alt_text ?: $product->name }}">
            @endforeach
        </div>
    </div>
@endif

<div class="mt-6 flex flex-wrap gap-3">
    <button class="btn btn-primary" type="submit">Guardar producto</button>
    <a class="btn btn-secondary" href="{{ route('products.index') }}">Cancelar</a>
</div>
