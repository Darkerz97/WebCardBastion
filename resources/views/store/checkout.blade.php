@extends('layouts.public', ['title' => 'Checkout | Card Bastion'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-6 xl:grid-cols-[1fr_0.95fr]">
            <div class="panel">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Checkout</p>
                <h1 class="mt-3 text-4xl font-black uppercase tracking-[0.06em] text-stone-900">Finalizar compra</h1>
                <p class="mt-3 text-sm leading-7 text-stone-600">Esta fase registra el pedido y el pago inicial dentro del sistema sin depender de pasarelas externas.</p>

                <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="field">
                            <label for="contact_name">Nombre</label>
                            <input id="contact_name" type="text" name="contact_name" value="{{ old('contact_name', $user->name) }}" required>
                        </div>
                        <div class="field">
                            <label for="contact_phone">Telefono</label>
                            <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone', $user->phone) }}">
                        </div>
                    </div>
                    <div class="field">
                        <label for="contact_email">Correo</label>
                        <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email', $user->email) }}" required>
                    </div>
                    <div class="field">
                        <label for="payment_method">Metodo de pago</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="card" @selected(old('payment_method') === 'card')>Tarjeta</option>
                            <option value="transfer" @selected(old('payment_method') === 'transfer')>Transferencia</option>
                            <option value="credit" @selected(old('payment_method') === 'credit')>Credito</option>
                            <option value="cash" @selected(old('payment_method') === 'cash')>Efectivo contra entrega</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="notes">Notas del pedido</label>
                        <textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-full" type="submit">Confirmar pedido</button>
                </form>
            </div>

            <aside class="panel h-fit">
                <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Resumen de compra</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($cartItems as $item)
                        <div class="flex items-start justify-between gap-4 rounded-2xl border border-stone-200 px-4 py-4">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $item['product']->name }}</p>
                                <p class="mt-1 text-sm text-stone-500">Cantidad: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="font-semibold text-stone-900">${{ number_format($item['line_total'], 2) }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-stone-200 pt-4">
                    <span class="text-sm text-stone-500">Total</span>
                    <span class="text-2xl font-black text-[color:var(--color-brand-600)]">${{ number_format($cartSubtotal, 2) }}</span>
                </div>
            </aside>
        </div>
    </section>
@endsection
