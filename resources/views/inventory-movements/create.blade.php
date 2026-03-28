@extends('layouts.app', ['title' => 'Nuevo movimiento', 'heading' => 'Nuevo movimiento de inventario', 'subheading' => 'Registra entradas, salidas o ajustes manuales para dejar trazabilidad completa del stock.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('inventory-movements.store') }}">
            @csrf
            <div class="grid grid-2">
                <div class="field">
                    <label for="product_id">Producto</label>
                    <select id="product_id" name="product_id" required>
                        <option value="">Selecciona un producto</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected((string) old('product_id', $selectedProductId) === (string) $product->id)>{{ $product->name }} ({{ $product->sku }}) | Stock: {{ $product->stock }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="movement_type">Tipo de movimiento</label>
                    <select id="movement_type" name="movement_type" required>
                        <option value="restock" @selected(old('movement_type', 'restock') === 'restock')>Reabasto</option>
                        <option value="manual_adjustment" @selected(old('movement_type') === 'manual_adjustment')>Ajuste manual</option>
                        <option value="return" @selected(old('movement_type') === 'return')>Devolucion</option>
                        <option value="sync_correction" @selected(old('movement_type') === 'sync_correction')>Correccion de sync</option>
                    </select>
                </div>
                <div class="field">
                    <label for="direction">Direccion</label>
                    <select id="direction" name="direction" required>
                        <option value="in" @selected(old('direction', 'in') === 'in')>Entrada</option>
                        <option value="out" @selected(old('direction') === 'out')>Salida</option>
                        <option value="adjustment" @selected(old('direction') === 'adjustment')>Ajuste a stock final</option>
                    </select>
                </div>
                <div class="field">
                    <label for="quantity">Cantidad</label>
                    <input id="quantity" type="number" min="0" name="quantity" value="{{ old('quantity') }}" required>
                    <p class="mt-2 text-xs text-stone-500">Si eliges `ajuste`, esta cantidad se toma como stock final deseado.</p>
                </div>
                <div class="field">
                    <label for="unit_cost">Costo unitario</label>
                    <input id="unit_cost" type="number" step="0.01" min="0" name="unit_cost" value="{{ old('unit_cost') }}">
                </div>
                <div class="field">
                    <label for="reference">Referencia</label>
                    <input id="reference" type="text" name="reference" value="{{ old('reference') }}" placeholder="Factura, folio, nota o motivo">
                </div>
                <div class="field">
                    <label for="device_id">Dispositivo</label>
                    <select id="device_id" name="device_id">
                        <option value="">Sin dispositivo</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @selected((string) old('device_id') === (string) $device->id)>{{ $device->name }} ({{ $device->device_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="user_id">Usuario responsable</label>
                    <select id="user_id" name="user_id">
                        <option value="">Usuario actual</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }}{{ $user->role ? ' · '.$user->role->code : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="sale_id">Venta relacionada</label>
                    <select id="sale_id" name="sale_id">
                        <option value="">Sin venta relacionada</option>
                        @foreach ($sales as $sale)
                            <option value="{{ $sale->id }}" @selected((string) old('sale_id') === (string) $sale->id)>{{ $sale->sale_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="source">Origen</label>
                    <select id="source" name="source">
                        <option value="server" @selected(old('source', 'server') === 'server')>Servidor</option>
                        <option value="pos" @selected(old('source') === 'pos')>POS</option>
                        <option value="system" @selected(old('source') === 'system')>Sistema</option>
                    </select>
                </div>
                <div class="field xl:col-span-2">
                    <label for="occurred_at">Fecha y hora</label>
                    <input id="occurred_at" type="datetime-local" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d\\TH:i')) }}">
                </div>
            </div>

            <div class="field">
                <label for="notes">Notas</label>
                <textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button class="btn btn-primary" type="submit">Guardar movimiento</button>
                <a class="btn btn-secondary" href="{{ route('inventory-movements.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
