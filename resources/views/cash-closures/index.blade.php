@extends('layouts.app', ['title' => 'Cierres de caja', 'heading' => 'Cierres de caja', 'subheading' => 'Consulta cierres, diferencias y responsables por dispositivo o usuario.'])

@section('content')
    <div class="space-y-6">
        <div class="panel">
            <form method="GET" class="grid gap-4 lg:grid-cols-[1fr_1fr_180px_180px_180px_auto]">
                <div class="field">
                    <label for="device_id">Dispositivo</label>
                    <select id="device_id" name="device_id">
                        <option value="">Todos</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @selected((string) request('device_id') === (string) $device->id)>{{ $device->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="user_id">Usuario</label>
                    <select id="user_id" name="user_id">
                        <option value="">Todos</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="status">Estatus</label>
                    <select id="status" name="status">
                        <option value="">Todos</option>
                        <option value="open" @selected(request('status') === 'open')>Abierto</option>
                        <option value="closed" @selected(request('status') === 'closed')>Cerrado</option>
                        <option value="reconciled" @selected(request('status') === 'reconciled')>Conciliado</option>
                    </select>
                </div>
                <div class="field"><label for="date_from">Desde</label><input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}"></div>
                <div class="field"><label for="date_to">Hasta</label><input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}"></div>
                <button class="btn btn-secondary self-end" type="submit">Filtrar</button>
            </form>
        </div>

        <div class="flex justify-end">
            <a class="btn btn-primary" href="{{ route('cash-closures.create') }}">Nuevo cierre</a>
        </div>

        <div class="table-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Dispositivo</th>
                        <th>Usuario</th>
                        <th>Estatus</th>
                        <th>Ventas</th>
                        <th>Esperado</th>
                        <th>Cierre</th>
                        <th>Diferencia</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cashClosures as $cashClosure)
                        <tr>
                            <td><a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('cash-closures.show', $cashClosure) }}">{{ $cashClosure->uuid }}</a></td>
                            <td>{{ $cashClosure->device?->name ?? 'Sin dispositivo' }}</td>
                            <td>{{ $cashClosure->user?->name ?? 'Sin usuario' }}</td>
                            <td><span class="badge">{{ $cashClosure->status }}</span></td>
                            <td>${{ number_format($cashClosure->total_sales, 2) }}</td>
                            <td>${{ number_format($cashClosure->expected_amount, 2) }}</td>
                            <td>${{ number_format($cashClosure->closing_amount, 2) }}</td>
                            <td>${{ number_format($cashClosure->difference, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-stone-500">No hay cierres registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $cashClosures->links() }}</div>
    </div>
@endsection
