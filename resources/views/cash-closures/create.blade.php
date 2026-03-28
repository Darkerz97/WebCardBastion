@extends('layouts.app', ['title' => 'Nuevo cierre de caja', 'heading' => 'Nuevo cierre de caja', 'subheading' => 'Registra un cierre manual, asignalo a un usuario y dispositivo, y deja lista la conciliacion.'])

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('cash-closures.store') }}">
            @csrf
            <div class="grid grid-2">
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
                        <option value="">Sin usuario</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }}{{ $user->role ? ' · '.$user->role->code : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="opening_amount">Monto de apertura</label>
                    <input id="opening_amount" type="number" step="0.01" min="0" name="opening_amount" value="{{ old('opening_amount', 0) }}">
                </div>
                <div class="field">
                    <label for="cash_sales">Ventas en efectivo</label>
                    <input id="cash_sales" type="number" step="0.01" min="0" name="cash_sales" value="{{ old('cash_sales', 0) }}">
                </div>
                <div class="field">
                    <label for="card_sales">Ventas con tarjeta</label>
                    <input id="card_sales" type="number" step="0.01" min="0" name="card_sales" value="{{ old('card_sales', 0) }}">
                </div>
                <div class="field">
                    <label for="transfer_sales">Ventas por transferencia</label>
                    <input id="transfer_sales" type="number" step="0.01" min="0" name="transfer_sales" value="{{ old('transfer_sales', 0) }}">
                </div>
                <div class="field">
                    <label for="total_sales">Total de ventas</label>
                    <input id="total_sales" type="number" step="0.01" min="0" name="total_sales" value="{{ old('total_sales') }}" placeholder="Opcional, se calcula si lo dejas vacio">
                </div>
                <div class="field">
                    <label for="expected_amount">Monto esperado en caja</label>
                    <input id="expected_amount" type="number" step="0.01" min="0" name="expected_amount" value="{{ old('expected_amount') }}" placeholder="Opcional, se calcula si lo dejas vacio">
                </div>
                <div class="field">
                    <label for="closing_amount">Monto de cierre</label>
                    <input id="closing_amount" type="number" step="0.01" min="0" name="closing_amount" value="{{ old('closing_amount') }}" required>
                </div>
                <div class="field">
                    <label for="difference">Diferencia</label>
                    <input id="difference" type="number" step="0.01" name="difference" value="{{ old('difference') }}" placeholder="Opcional, se calcula si lo dejas vacio">
                </div>
                <div class="field">
                    <label for="status">Estatus</label>
                    <select id="status" name="status">
                        <option value="closed" @selected(old('status', 'closed') === 'closed')>Cerrado</option>
                        <option value="open" @selected(old('status') === 'open')>Abierto</option>
                        <option value="reconciled" @selected(old('status') === 'reconciled')>Conciliado</option>
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
                <div class="field">
                    <label for="opened_at">Fecha de apertura</label>
                    <input id="opened_at" type="datetime-local" name="opened_at" value="{{ old('opened_at') }}">
                </div>
                <div class="field">
                    <label for="closed_at">Fecha de cierre</label>
                    <input id="closed_at" type="datetime-local" name="closed_at" value="{{ old('closed_at', now()->format('Y-m-d\\TH:i')) }}">
                </div>
            </div>

            <div class="field">
                <label for="notes">Notas</label>
                <textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button class="btn btn-primary" type="submit">Guardar cierre</button>
                <a class="btn btn-secondary" href="{{ route('cash-closures.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
