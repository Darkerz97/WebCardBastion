@extends('layouts.app', ['title' => 'Dashboard', 'heading' => 'Dashboard', 'subheading' => 'Resumen operativo del servidor central.'])

@section('content')
    <div class="grid grid-5" style="margin-bottom: 20px;">
        <div class="metric"><small>Total productos</small><strong>{{ $metrics['products'] }}</strong></div>
        <div class="metric"><small>Total clientes</small><strong>{{ $metrics['customers'] }}</strong></div>
        <div class="metric"><small>Total ventas</small><strong>{{ $metrics['sales'] }}</strong></div>
        <div class="metric"><small>Ventas de hoy</small><strong>{{ $metrics['sales_today'] }}</strong></div>
        <div class="metric"><small>Monto vendido hoy</small><strong>${{ number_format($metrics['amount_today'], 2) }}</strong></div>
    </div>
    <div class="panel">
        <h2 style="margin-top: 0;">Ventas recientes</h2>
        <table>
            <thead><tr><th>Folio</th><th>Cliente</th><th>Usuario</th><th>Total</th><th>Estatus</th><th>Fecha</th></tr></thead>
            <tbody>
            @forelse ($recentSales as $sale)
                <tr>
                    <td><a href="{{ route('sales.show', $sale) }}">{{ $sale->sale_number }}</a></td>
                    <td>{{ $sale->customer?->name ?? 'Público general' }}</td>
                    <td>{{ $sale->user?->name ?? 'N/D' }}</td>
                    <td>${{ number_format($sale->total, 2) }}</td>
                    <td><span class="badge">{{ $sale->status }} / {{ $sale->payment_status }}</span></td>
                    <td>{{ optional($sale->sold_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Aún no hay ventas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
