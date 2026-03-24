@extends('layouts.app', ['title' => 'Nueva venta', 'heading' => 'Nueva venta', 'subheading' => 'Registro manual de venta con ítems y pagos iniciales.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('sales.store') }}">
            @csrf
            <div class="grid grid-2">
                <div class="field">
                    <label for="customer_id">Cliente</label>
                    <select id="customer_id" name="customer_id">
                        <option value="">Público general</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="device_id">Dispositivo</label>
                    <select id="device_id" name="device_id">
                        <option value="">Sin dispositivo</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @selected(old('device_id') == $device->id)>{{ $device->name }} ({{ $device->device_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="status">Estatus</label>
                    <select id="status" name="status" required>
                        <option value="completed" @selected(old('status', 'completed') === 'completed')>Completed</option>
                        <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                        <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                    </select>
                </div>
                <div class="field"><label for="discount">Descuento</label><input id="discount" type="number" name="discount" step="0.01" min="0" value="{{ old('discount', 0) }}"></div>
            </div>
            <h3>Ítems</h3>
            @for ($i = 0; $i < 3; $i++)
                <div class="grid grid-2" style="margin-bottom: 12px; padding: 12px; border: 1px dashed #d8c7b3; border-radius: 12px;">
                    <div class="field" style="margin:0;">
                        <label>Producto {{ $i + 1 }}</label>
                        <select name="items[{{ $i }}][product_id]">
                            <option value="">Selecciona un producto</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old("items.$i.product_id") == $product->id)>{{ $product->name }} | Stock: {{ $product->stock }} | ${{ number_format($product->price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="margin:0;"><label>Cantidad</label><input type="number" min="1" name="items[{{ $i }}][quantity]" value="{{ old("items.$i.quantity", $i === 0 ? 1 : '') }}"></div>
                    <div class="field" style="margin:0;"><label>Precio unitario opcional</label><input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]" value="{{ old("items.$i.unit_price") }}"></div>
                </div>
            @endfor
            <h3>Pago inicial</h3>
            <div class="grid grid-2">
                <div class="field">
                    <label for="payment_method">Método</label>
                    <select id="payment_method" name="payments[0][method]">
                        <option value="">Sin pago inicial</option>
                        <option value="cash" @selected(old('payments.0.method') === 'cash')>Cash</option>
                        <option value="card" @selected(old('payments.0.method') === 'card')>Card</option>
                        <option value="transfer" @selected(old('payments.0.method') === 'transfer')>Transfer</option>
                        <option value="credit" @selected(old('payments.0.method') === 'credit')>Credit</option>
                        <option value="mixed" @selected(old('payments.0.method') === 'mixed')>Mixed</option>
                    </select>
                </div>
                <div class="field"><label for="payment_amount">Monto</label><input id="payment_amount" type="number" step="0.01" min="0" name="payments[0][amount]" value="{{ old('payments.0.amount') }}"></div>
                <div class="field"><label for="payment_reference">Referencia</label><input id="payment_reference" type="text" name="payments[0][reference]" value="{{ old('payments.0.reference') }}"></div>
                <div class="field"><label for="payment_notes">Notas</label><input id="payment_notes" type="text" name="payments[0][notes]" value="{{ old('payments.0.notes') }}"></div>
            </div>
            <div class="actions"><button class="btn" type="submit">Registrar venta</button><a class="btn secondary" href="{{ route('sales.index') }}">Cancelar</a></div>
        </form>
    </div>
@endsection
