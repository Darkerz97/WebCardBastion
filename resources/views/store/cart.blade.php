@extends('layouts.public', ['title' => 'Carrito | Card Bastion'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[color:var(--color-brand-500)]">Carrito</p>
                <h1 class="mt-3 text-4xl font-black uppercase tracking-[0.06em] text-stone-900">Prepara tu pedido</h1>
            </div>
            <a class="btn btn-secondary" href="{{ route('store.catalog') }}">Seguir comprando</a>
        </div>

        <div class="mt-8 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="panel">
                <div class="space-y-4">
                    @forelse ($cartItems as $item)
                        <div class="rounded-3xl border border-stone-200 p-4">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $item['product']->name }}</p>
                                    <p class="mt-1 text-sm text-stone-500">{{ $item['product']->sku }}</p>
                                    <p class="mt-2 text-sm text-stone-600">${{ number_format($item['unit_price'], 2) }} c/u</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-3">
                                        @csrf
                                        @method('PATCH')
                                        <input class="w-24 rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm" type="number" name="quantity" min="0" max="{{ max($item['product']->stock, 1) }}" value="{{ $item['quantity'] }}">
                                        <button class="btn btn-secondary" type="submit">Actualizar</button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Quitar</button>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-3 text-right text-sm font-semibold text-stone-900">
                                Subtotal: ${{ number_format($item['line_total'], 2) }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Tu carrito esta vacio por ahora.</p>
                    @endforelse
                </div>
            </div>

            <aside class="panel h-fit">
                <h2 class="text-xl font-black uppercase tracking-[0.08em] text-stone-900">Resumen</h2>
                <dl class="mt-6 space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-stone-500">Productos</dt>
                        <dd class="font-semibold text-stone-900">{{ $cartItems->sum('quantity') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-stone-500">Subtotal</dt>
                        <dd class="font-semibold text-stone-900">${{ number_format($cartSubtotal, 2) }}</dd>
                    </div>
                </dl>

                <div class="mt-6 space-y-3">
                    @auth
                        <a class="btn btn-primary w-full {{ $cartItems->isEmpty() ? 'pointer-events-none opacity-50' : '' }}" href="{{ route('checkout.create') }}">Ir al checkout</a>
                    @else
                        <a class="btn btn-primary w-full" href="{{ route('login') }}">Inicia sesion para comprar</a>
                    @endauth
                    <a class="btn btn-secondary w-full" href="{{ route('store.catalog') }}">Volver a tienda</a>
                </div>
            </aside>
        </div>
    </section>
@endsection
