<div class="grid grid-2">
    <div class="field"><label for="name">Nombre</label><input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required></div>
    <div class="field"><label for="sku">SKU</label><input id="sku" type="text" name="sku" value="{{ old('sku', $product->sku) }}" required></div>
    <div class="field"><label for="barcode">Código de barras</label><input id="barcode" type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"></div>
    <div class="field"><label for="category">Categoría</label><input id="category" type="text" name="category" value="{{ old('category', $product->category) }}"></div>
    <div class="field"><label for="cost">Costo</label><input id="cost" type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $product->cost ?? 0) }}" required></div>
    <div class="field"><label for="price">Precio</label><input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? 0) }}" required></div>
    <div class="field"><label for="stock">Stock</label><input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required></div>
    <div class="field"><label for="image_path">Ruta de imagen</label><input id="image_path" type="text" name="image_path" value="{{ old('image_path', $product->image_path) }}"></div>
</div>
<div class="field"><label for="description">Descripción</label><textarea id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea></div>
<div class="field">
    <label for="active">Activo</label>
    <select id="active" name="active" required>
        <option value="1" @selected(old('active', $product->active ?? true))>Sí</option>
        <option value="0" @selected(! old('active', $product->active ?? true))>No</option>
    </select>
</div>
<div class="actions"><button class="btn" type="submit">Guardar producto</button><a class="btn secondary" href="{{ route('products.index') }}">Cancelar</a></div>
