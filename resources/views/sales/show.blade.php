@extends('layouts.app', ['title' => $sale->sale_number, 'heading' => $sale->sale_number, 'subheading' => 'Detalle completo de venta, ítems y pagos.'])

@section('content')
    <div class="card-stack">
        <div class="panel">
            <div class="grid grid-2">
                <div><strong>Cliente:</strong> {{ $sale->customer?->name ?? 'Público general' }}</div>
                <div><strong>Usuario:</strong> {{ $sale->user?->name ?? 'N/D' }}</div>
                <div><strong>Dispositivo:</strong> {{ $sale->device?->name ?? 'N/D' }}</div>
                <div><strong>Fecha:</strong> {{ optional($sale->sold_at)->format('d/m/Y H:i') }}</div>
                <div><strong>Estatus:</strong> {{ $sale->status }}</div>
                <div><strong>Pago:</strong> {{ $sale->payment_status }}</div>
                <div><strong>Subtotal:</strong> ${{ number_format($sale->subtotal, 2) }}</div>
                <div><strong>Descuento:</strong> ${{ number_format($sale->discount, 2) }}</div>
                <div><strong>Total:</strong> ${{ number_format($sale->total, 2) }}</div>
                <div><strong>UUID:</strong> <span class="muted">{{ $sale->uuid }}</span></div>
            </div>
        </div>
        <div class="panel">
            <h3 style="margin-top:0;">Ítems</h3>
            <table>
                <thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unitario</th><th>Total línea</th></tr></thead>
                <tbody>@foreach ($sale->items as $item)<tr><td>{{ $item->product?->name ?? 'Producto eliminado' }}</td><td>{{ $item->quantity }}</td><td>${{ number_format($item->unit_price, 2) }}</td><td>${{ number_format($item->line_total, 2) }}</td></tr>@endforeach</tbody>
            </table>
        </div>
        <div class="panel">
            <h3 style="margin-top:0;">Pagos</h3>
            <table>
                <thead><tr><th>Método</th><th>Monto</th><th>Referencia</th><th>Notas</th><th>Fecha</th></tr></thead>
                <tbody>
                @forelse ($sale->payments as $payment)
                    <tr><td>{{ $payment->method }}</td><td>${{ number_format($payment->amount, 2) }}</td><td>{{ $payment->reference ?: 'N/D' }}</td><td>{{ $payment->notes ?: 'N/D' }}</td><td>{{ optional($payment->paid_at)->format('d/m/Y H:i') }}</td></tr>
                @empty
                    <tr><td colspan="5" class="muted">Sin pagos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
