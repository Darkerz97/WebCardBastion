@extends('layouts.app', ['title' => 'Clientes', 'heading' => 'Clientes', 'subheading' => 'Base central para ventas, rewards y e-commerce futuro.'])

@section('content')
    <div class="panel">
        <div class="actions" style="justify-content:space-between; margin-bottom:18px; align-items:flex-start;">
            <div>
                <strong>Carga masiva</strong>
                <div class="muted" style="margin-top:6px;">Descarga la plantilla CSV, llenala en Excel y vuelve a subirla para crear o actualizar clientes por correo o telefono.</div>
            </div>
            <div class="actions">
                <a class="btn secondary" href="{{ route('customers.template') }}">Descargar plantilla</a>
                <form method="POST" action="{{ route('customers.import') }}" enctype="multipart/form-data" class="actions">
                    @csrf
                    <input type="file" name="file" accept=".csv,text/csv" style="max-width:260px;">
                    <button class="btn" type="submit">Importar clientes</button>
                </form>
            </div>
        </div>
        <form method="GET" class="search-bar">
            <div class="field" style="margin:0;"><label for="search">Buscar</label><input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, telefono o correo"></div>
            <div class="field" style="margin:0;">
                <label for="active">Estado</label>
                <select id="active" name="active">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('active') === '1')>Activos</option>
                    <option value="0" @selected(request('active') === '0')>Inactivos</option>
                </select>
            </div>
            <button class="btn secondary" type="submit">Filtrar</button>
        </form>
        <div class="actions" style="justify-content:flex-end; margin-bottom:14px;"><a class="btn" href="{{ route('customers.create') }}">Nuevo cliente</a></div>
        <table>
            <thead><tr><th>Nombre</th><th>Cuenta</th><th>Telefono</th><th>Correo</th><th>Saldo</th><th>Compras</th><th></th></tr></thead>
            <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></td>
                    <td>{{ $customer->user_id ? 'Vinculada' : 'Sin cuenta' }}</td>
                    <td>{{ $customer->phone ?: 'N/D' }}</td>
                    <td>{{ $customer->email ?: 'N/D' }}</td>
                    <td>${{ number_format($customer->credit_balance, 2) }}</td>
                    <td>{{ $customer->sales_count }}</td>
                    <td class="actions">
                        <a class="btn secondary" href="{{ route('customers.edit', $customer) }}">Editar</a>
                        <form class="inline" method="POST" action="{{ route('customers.destroy', $customer) }}">@csrf @method('DELETE') <button class="btn danger" type="submit" onclick="return confirm('¿Eliminar cliente?')">Eliminar</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No hay clientes registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $customers->links() }}</div>
    </div>
@endsection
