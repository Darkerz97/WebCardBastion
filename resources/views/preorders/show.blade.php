@extends('layouts.app', ['title' => $preorder->preorder_number, 'heading' => $preorder->preorder_number, 'subheading' => 'Seguimiento de cliente, productos reservados y abonos registrados.'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="panel">
                <div class="flex flex-wrap gap-2">
                    <span class="badge">{{ $preorder->status }}</span>
                    <span class="badge">{{ $preorder->source }}</span>
                </div>

                <dl class="mt-6 grid gap-4 text-sm">
                    <div><dt class="font-semibold text-stone-500">Cliente</dt><dd class="mt-1 text-stone-900">{{ $preorder->customer?->name ?? 'Sin cliente' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Cuenta vinculada</dt><dd class="mt-1 text-stone-900">{{ $preorder->customer?->user?->name ?? 'Sin cuenta' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Telefono</dt><dd class="mt-1 text-stone-900">{{ $preorder->customer?->phone ?? 'N/D' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Correo</dt><dd class="mt-1 text-stone-900">{{ $preorder->customer?->email ?? 'N/D' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Subtotal</dt><dd class="mt-1 text-stone-900">${{ number_format($preorder->subtotal, 2) }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Descuento</dt><dd class="mt-1 text-stone-900">${{ number_format($preorder->discount, 2) }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Total</dt><dd class="mt-1 text-stone-900">${{ number_format($preorder->total, 2) }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Abonado</dt><dd class="mt-1 text-stone-900">${{ number_format($preorder->amount_paid, 2) }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Saldo pendiente</dt><dd class="mt-1 text-stone-900">${{ number_format($preorder->amount_due, 2) }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">Entrega esperada</dt><dd class="mt-1 text-stone-900">{{ optional($preorder->expected_release_date)->format('d/m/Y H:i') ?: 'Pendiente' }}</dd></div>
                    <div><dt class="font-semibold text-stone-500">UUID</dt><dd class="mt-1 text-stone-900">{{ $preorder->uuid }}</dd></div>
                </dl>

                <div class="mt-4">
                    <strong>Notas</strong>
                    <p class="muted mt-2">{{ $preorder->notes ?: 'Sin notas registradas.' }}</p>
                </div>
            </div>

            <div class="panel">
                <h2 class="text-lg font-black uppercase tracking-[0.08em] text-stone-900">Items reservados</h2>
                <table class="mt-4">
                    <thead><tr><th>Producto</th><th>SKU</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr></thead>
                    <tbody>
                    @forelse ($preorder->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->product_sku ?: ($item->product?->sku ?? 'Manual') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_price, 2) }}</td>
                            <td>${{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">Sin items registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <h2 class="text-lg font-black uppercase tracking-[0.08em] text-stone-900">Historial de abonos</h2>
                <table class="mt-4">
                    <thead><tr><th>Metodo</th><th>Monto</th><th>Referencia</th><th>Notas</th><th>Fecha</th></tr></thead>
                    <tbody>
                    @forelse ($preorder->payments as $payment)
                        <tr>
                            <td>{{ $payment->method }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->reference ?: 'N/D' }}</td>
                            <td>{{ $payment->notes ?: 'N/D' }}</td>
                            <td>{{ optional($payment->paid_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">Aun no hay abonos registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel">
                <p class="section-kicker">Seguimiento</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Cambiar estatus</h2>
                <form method="POST" action="{{ route('preorders.status.update', $preorder) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="field">
                        <label for="status">Nuevo estatus</label>
                        <select id="status" name="status">
                            <option value="pending" @selected($preorder->status === 'pending')>Pendiente</option>
                            <option value="partially_paid" @selected($preorder->status === 'partially_paid')>Abonada</option>
                            <option value="paid" @selected($preorder->status === 'paid')>Pagada</option>
                            <option value="delivered" @selected($preorder->status === 'delivered')>Entregada</option>
                            <option value="cancelled" @selected($preorder->status === 'cancelled')>Cancelada</option>
                        </select>
                    </div>
                    <button class="btn btn-secondary w-full" type="submit">Actualizar estatus</button>
                </form>
            </div>

            <div class="panel">
                <p class="section-kicker">Abonos</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Registrar nuevo abono</h2>
                <form method="POST" action="{{ route('preorders.payments.store', $preorder) }}" class="mt-5 space-y-4">
                    @csrf
                    <div class="field">
                        <label for="method">Metodo</label>
                        <select id="method" name="method">
                            <option value="cash">Efectivo</option>
                            <option value="card">Tarjeta</option>
                            <option value="transfer">Transferencia</option>
                            <option value="credit">Credito</option>
                            <option value="mixed">Mixto</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="amount">Monto</label>
                        <input id="amount" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required>
                    </div>
                    <div class="field">
                        <label for="reference">Referencia</label>
                        <input id="reference" type="text" name="reference" value="{{ old('reference') }}">
                    </div>
                    <div class="field">
                        <label for="paid_at">Fecha del abono</label>
                        <input id="paid_at" type="datetime-local" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d\\TH:i')) }}">
                    </div>
                    <div class="field">
                        <label for="payment_notes">Notas</label>
                        <textarea id="payment_notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-full" type="submit">Guardar abono</button>
                </form>
            </div>

            <div class="panel">
                <p class="section-kicker">Cliente asociado</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Seguimiento comercial</h2>
                <p class="mt-3 text-sm leading-7 text-stone-600">
                    Esta preventa pertenece a {{ $preorder->customer?->name ?? 'un registro sin cliente asignado' }}. Usa esta vista para revisar cuanto ha abonado, cuanto falta por liquidar y cuando deberia entregarse.
                </p>
                @if ($preorder->customer)
                    <a class="btn btn-secondary mt-5 w-full" href="{{ route('customers.show', $preorder->customer) }}">Ver cliente</a>
                @endif
            </div>
        </div>
    </div>
@endsection
