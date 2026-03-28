<div class="grid grid-2">
    <div class="field">
        <label for="customer_id">Cliente</label>
        <select id="customer_id" name="customer_id">
            <option value="">Sin cliente</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label for="expected_release_date">Fecha esperada de entrega</label>
        <input id="expected_release_date" type="datetime-local" name="expected_release_date" value="{{ old('expected_release_date') }}">
    </div>
    <div class="field">
        <label for="discount">Descuento</label>
        <input id="discount" type="number" step="0.01" min="0" name="discount" value="{{ old('discount', 0) }}">
    </div>
    <div class="field">
        <label for="status">Estatus inicial</label>
        <select id="status" name="status">
            <option value="pending" @selected(old('status', 'pending') === 'pending')>Pendiente</option>
            <option value="partially_paid" @selected(old('status') === 'partially_paid')>Abonada</option>
            <option value="paid" @selected(old('status') === 'paid')>Pagada</option>
        </select>
    </div>
</div>

<div class="field">
    <label for="notes">Notas</label>
    <textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
</div>

<h3>Productos de la preventa</h3>
@for ($i = 0; $i < 4; $i++)
    <div class="grid grid-2" style="margin-bottom: 12px; padding: 12px; border: 1px dashed #d8c7b3; border-radius: 12px;">
        <div class="field" style="margin:0;">
            <label>Producto {{ $i + 1 }}</label>
            <select name="items[{{ $i }}][product_id]">
                <option value="">Producto libre / snapshot manual</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(old("items.$i.product_id") == $product->id)>{{ $product->name }} | {{ $product->sku }} | ${{ number_format($product->price, 2) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field" style="margin:0;">
            <label>Nombre manual</label>
            <input type="text" name="items[{{ $i }}][product_name]" value="{{ old("items.$i.product_name") }}" placeholder="Usa esto si el producto aun no existe">
        </div>
        <div class="field" style="margin:0;">
            <label>Cantidad</label>
            <input type="number" min="1" name="items[{{ $i }}][quantity]" value="{{ old("items.$i.quantity", $i === 0 ? 1 : '') }}">
        </div>
        <div class="field" style="margin:0;">
            <label>Precio unitario</label>
            <input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="{{ old("items.$i.unit_price") }}">
        </div>
    </div>
@endfor

<h3>Abono inicial opcional</h3>
<div class="grid grid-2">
    <div class="field">
        <label for="payment_method">Metodo</label>
        <select id="payment_method" name="payments[0][method]">
            <option value="">Sin abono inicial</option>
            <option value="cash" @selected(old('payments.0.method') === 'cash')>Efectivo</option>
            <option value="card" @selected(old('payments.0.method') === 'card')>Tarjeta</option>
            <option value="transfer" @selected(old('payments.0.method') === 'transfer')>Transferencia</option>
            <option value="credit" @selected(old('payments.0.method') === 'credit')>Credito</option>
            <option value="mixed" @selected(old('payments.0.method') === 'mixed')>Mixto</option>
        </select>
    </div>
    <div class="field">
        <label for="payment_amount">Monto</label>
        <input id="payment_amount" type="number" step="0.01" min="0" name="payments[0][amount]" value="{{ old('payments.0.amount') }}">
    </div>
    <div class="field">
        <label for="payment_reference">Referencia</label>
        <input id="payment_reference" type="text" name="payments[0][reference]" value="{{ old('payments.0.reference') }}">
    </div>
    <div class="field">
        <label for="payment_notes">Notas del abono</label>
        <input id="payment_notes" type="text" name="payments[0][notes]" value="{{ old('payments.0.notes') }}">
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <button class="btn btn-primary" type="submit">Guardar preventa</button>
    <a class="btn btn-secondary" href="{{ route('preorders.index') }}">Cancelar</a>
</div>
