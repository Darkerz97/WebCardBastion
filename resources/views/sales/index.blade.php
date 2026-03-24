@extends('layouts.app', ['title' => 'Ventas', 'heading' => 'Ventas', 'subheading' => 'Consulta central de tickets y pagos.'])

@section('content')
    <div class="panel">
        <div class="actions" style="justify-content:space-between; margin-bottom:18px; align-items:flex-start;">
            <div>
                <strong>Carga masiva</strong>
                <div class="muted" style="margin-top:6px;">Descarga la plantilla CSV, llenala en Excel y sube varias ventas a la vez. Repite el mismo sale_number si una venta lleva varios productos.</div>
            </div>
            <div class="actions">
                <a class="btn secondary" href="{{ route('sales.template') }}">Descargar plantilla</a>
                <form method="POST" action="{{ route('sales.import') }}" enctype="multipart/form-data" class="actions">
                    @csrf
                    <input type="file" name="file" accept=".csv,text/csv" style="max-width:260px;">
                    <button class="btn" type="submit">Importar ventas</button>
                </form>
            </div>
        </div>
        <form method="GET" class="search-bar" style="grid-template-columns: repeat(4, minmax(0, 1fr)) auto;">
            <div class="field" style="margin:0;">
                <label for="customer_id">Cliente</label>
                <select id="customer_id" name="customer_id">
                    <option value="">Todos</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="margin:0;">
                <label for="status">Estatus</label>
                <select id="status" name="status">
                    <option value="">Todos</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
            </div>
            <div class="field" style="margin:0;"><label for="date_from">Desde</label><input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}"></div>
            <div class="field" style="margin:0;"><label for="date_to">Hasta</label><input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}"></div>
            <button class="btn secondary" type="submit">Filtrar</button>
        </form>
        <div class="actions" style="justify-content:flex-end; margin-bottom:14px;"><a class="btn" href="{{ route('sales.create') }}">Nueva venta</a></div>
        <table>
            <thead><tr><th>Folio</th><th>Cliente</th><th>Usuario</th><th>Total</th><th>Pago</th><th>Fecha</th></tr></thead>
            <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a></td>
                    <td>{{ $sale->customer?->name ?? 'Público general' }}</td>
                    <td>{{ $sale->user?->name ?? 'N/D' }}</td>
                    <td>${{ number_format($sale->total, 2) }}</td>
                    <td><span class="badge">{{ $sale->payment_status }}</span></td>
                    <td>{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No hay ventas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $sales->links() }}</div>
    </div>
@endsection
