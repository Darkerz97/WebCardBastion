@extends('layouts.app', ['title' => 'Detalle de cierre', 'heading' => 'Detalle de cierre de caja', 'subheading' => 'Revision completa de montos, diferencia, origen y responsable del cierre.'])

@section('content')
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="panel">
            <div class="flex flex-wrap gap-2">
                <span class="badge">{{ $cashClosure->status }}</span>
                <span class="badge">{{ $cashClosure->source }}</span>
            </div>

            <dl class="mt-6 grid gap-4 text-sm">
                <div><dt class="font-semibold text-stone-500">UUID</dt><dd class="mt-1 text-stone-900">{{ $cashClosure->uuid }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Dispositivo</dt><dd class="mt-1 text-stone-900">{{ $cashClosure->device?->name ?? 'Sin dispositivo' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Codigo dispositivo</dt><dd class="mt-1 text-stone-900">{{ $cashClosure->device?->device_code ?? 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Usuario</dt><dd class="mt-1 text-stone-900">{{ $cashClosure->user?->name ?? 'Sin usuario' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Rol</dt><dd class="mt-1 text-stone-900">{{ $cashClosure->user?->role?->code ?? 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Monto de apertura</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->opening_amount, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Ventas efectivo</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->cash_sales, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Ventas tarjeta</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->card_sales, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Ventas transferencia</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->transfer_sales, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Total ventas</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->total_sales, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Monto esperado</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->expected_amount, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Monto de cierre</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->closing_amount, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Diferencia</dt><dd class="mt-1 text-stone-900">${{ number_format($cashClosure->difference, 2) }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Apertura</dt><dd class="mt-1 text-stone-900">{{ optional($cashClosure->opened_at)->format('d/m/Y H:i') ?: 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Cierre</dt><dd class="mt-1 text-stone-900">{{ optional($cashClosure->closed_at)->format('d/m/Y H:i') ?: 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Generado cliente</dt><dd class="mt-1 text-stone-900">{{ optional($cashClosure->client_generated_at)->format('d/m/Y H:i') ?: 'N/D' }}</dd></div>
                <div><dt class="font-semibold text-stone-500">Recibido servidor</dt><dd class="mt-1 text-stone-900">{{ optional($cashClosure->received_at)->format('d/m/Y H:i') ?: 'N/D' }}</dd></div>
            </dl>

            <div class="mt-4">
                <strong>Notas</strong>
                <p class="muted mt-2">{{ $cashClosure->notes ?: 'Sin notas registradas.' }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel">
                <p class="section-kicker">Conciliacion</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Actualizar estatus</h2>
                <form method="POST" action="{{ route('cash-closures.status.update', $cashClosure) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="field">
                        <label for="status">Nuevo estatus</label>
                        <select id="status" name="status">
                            <option value="open" @selected($cashClosure->status === 'open')>Abierto</option>
                            <option value="closed" @selected($cashClosure->status === 'closed')>Cerrado</option>
                            <option value="reconciled" @selected($cashClosure->status === 'reconciled')>Conciliado</option>
                        </select>
                    </div>
                    <button class="btn btn-secondary w-full" type="submit">Actualizar estatus</button>
                </form>
            </div>

            <div class="panel">
                <p class="section-kicker">Resumen</p>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-[0.05em] text-stone-900">Lectura rapida</h2>
                <div class="mt-5 space-y-3">
                    <div class="rounded-2xl border border-stone-200 px-4 py-4">
                        <p class="font-semibold text-stone-900">Esperado</p>
                        <p class="mt-2 text-sm text-stone-500">${{ number_format($cashClosure->expected_amount, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 px-4 py-4">
                        <p class="font-semibold text-stone-900">Cierre real</p>
                        <p class="mt-2 text-sm text-stone-500">${{ number_format($cashClosure->closing_amount, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-stone-200 px-4 py-4">
                        <p class="font-semibold text-stone-900">Diferencia</p>
                        <p class="mt-2 text-sm text-stone-500">${{ number_format($cashClosure->difference, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
