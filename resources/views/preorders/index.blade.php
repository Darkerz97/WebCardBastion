@extends('layouts.app', ['title' => 'Preventas', 'heading' => 'Preventas', 'subheading' => 'Seguimiento de reservas, clientes asociados y saldo pendiente.'])

@section('content')
    <div class="space-y-6">
        <div class="panel">
            <form method="GET" class="grid gap-4 lg:grid-cols-[1fr_220px_180px_180px_auto]">
                <div class="field">
                    <label for="customer_id">Cliente</label>
                    <select id="customer_id" name="customer_id">
                        <option value="">Todos</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) request('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="status">Estatus</label>
                    <select id="status" name="status">
                        <option value="">Todos</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pendiente</option>
                        <option value="partially_paid" @selected(request('status') === 'partially_paid')>Abonada</option>
                        <option value="paid" @selected(request('status') === 'paid')>Pagada</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelada</option>
                        <option value="delivered" @selected(request('status') === 'delivered')>Entregada</option>
                    </select>
                </div>
                <div class="field"><label for="date_from">Desde</label><input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}"></div>
                <div class="field"><label for="date_to">Hasta</label><input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}"></div>
                <button class="btn btn-secondary self-end" type="submit">Filtrar</button>
            </form>
        </div>

        <div class="flex justify-end">
            <a class="btn btn-primary" href="{{ route('preorders.create') }}">Nueva preventa</a>
        </div>

        <div class="table-shell">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Estatus</th>
                        <th>Total</th>
                        <th>Abonado</th>
                        <th>Saldo</th>
                        <th>Entrega esperada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($preorders as $preorder)
                        <tr>
                            <td><a class="font-semibold text-[color:var(--color-brand-600)]" href="{{ route('preorders.show', $preorder) }}">{{ $preorder->preorder_number }}</a></td>
                            <td>{{ $preorder->customer?->name ?? 'Sin cliente' }}</td>
                            <td><span class="badge">{{ $preorder->status }}</span></td>
                            <td>${{ number_format($preorder->total, 2) }}</td>
                            <td>${{ number_format($preorder->amount_paid, 2) }}</td>
                            <td>${{ number_format($preorder->amount_due, 2) }}</td>
                            <td>{{ optional($preorder->expected_release_date)->format('d/m/Y H:i') ?: 'Pendiente' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-stone-500">No hay preventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $preorders->links() }}</div>
    </div>
@endsection
