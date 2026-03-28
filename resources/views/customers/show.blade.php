@extends('layouts.app', ['title' => $customer->name, 'heading' => $customer->name, 'subheading' => 'Detalle del cliente y ultimas compras.'])

@section('content')
    <div class="panel card-stack">
        <div class="grid grid-2">
            <div><strong>Cuenta vinculada:</strong> {{ $customer->user?->name ?? 'Sin cuenta asociada' }}</div>
            <div><strong>Rol:</strong> {{ $customer->user?->role?->code ?? 'N/D' }}</div>
            <div><strong>Telefono:</strong> {{ $customer->phone ?: 'N/D' }}</div>
            <div><strong>Correo:</strong> {{ $customer->email ?: 'N/D' }}</div>
            <div><strong>Saldo a favor:</strong> ${{ number_format($customer->credit_balance, 2) }}</div>
            <div><strong>Estado:</strong> {{ $customer->active ? 'Activo' : 'Inactivo' }}</div>
        </div>
        <div><strong>Notas</strong><p class="muted">{{ $customer->notes ?: 'Sin notas registradas.' }}</p></div>
        <div>
            <strong>Preordenes recientes</strong>
            <table>
                <thead><tr><th>Folio</th><th>Estatus</th><th>Total</th><th>Lanzamiento</th></tr></thead>
                <tbody>
                @forelse ($customer->preorders as $preorder)
                    <tr>
                        <td>{{ $preorder->preorder_number }}</td>
                        <td>{{ $preorder->status }}</td>
                        <td>${{ number_format($preorder->total, 2) }}</td>
                        <td>{{ optional($preorder->expected_release_date)->format('d/m/Y H:i') ?: 'Pendiente' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">Sin preordenes registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <strong>Ultimas ventas</strong>
            <table>
                <thead><tr><th>Folio</th><th>Total</th><th>Estatus</th><th>Fecha</th></tr></thead>
                <tbody>
                @forelse ($customer->sales as $sale)
                    <tr>
                        <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a></td>
                        <td>${{ number_format($sale->total, 2) }}</td>
                        <td>{{ $sale->status }}</td>
                        <td>{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">Sin compras registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
